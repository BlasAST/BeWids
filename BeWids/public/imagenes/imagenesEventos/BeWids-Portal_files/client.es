import "/node_modules/vite/dist/client/env.mjs";

class HMRContext {
    constructor(hmrClient, ownerPath) {
        this.hmrClient = hmrClient;
        this.ownerPath = ownerPath;
        if (!hmrClient.dataMap.has(ownerPath)) {
            hmrClient.dataMap.set(ownerPath, {});
        }
        // when a file is hot updated, a new context is created
        // clear its stale callbacks
        const mod = hmrClient.hotModulesMap.get(ownerPath);
        if (mod) {
            mod.callbacks = [];
        }
        // clear stale custom event listeners
        const staleListeners = hmrClient.ctxToListenersMap.get(ownerPath);
        if (staleListeners) {
            for (const [event, staleFns] of staleListeners) {
                const listeners = hmrClient.customListenersMap.get(event);
                if (listeners) {
                    hmrClient.customListenersMap.set(event, listeners.filter((l) => !staleFns.includes(l)));
                }
            }
        }
        this.newListeners = new Map();
        hmrClient.ctxToListenersMap.set(ownerPath, this.newListeners);
    }
    get data() {
        return this.hmrClient.dataMap.get(this.ownerPath);
    }
    accept(deps, callback) {
        if (typeof deps === 'function' || !deps) {
            // self-accept: hot.accept(() => {})
            this.acceptDeps([this.ownerPath], ([mod]) => deps === null || deps === void 0 ? void 0 : deps(mod));
        }
        else if (typeof deps === 'string') {
            // explicit deps
            this.acceptDeps([deps], ([mod]) => callback === null || callback === void 0 ? void 0 : callback(mod));
        }
        else if (Array.isArray(deps)) {
            this.acceptDeps(deps, callback);
        }
        else {
            throw new Error(`invalid hot.accept() usage.`);
        }
    }
    // export names (first arg) are irrelevant on the client side, they're
    // extracted in the server for propagation
    acceptExports(_, callback) {
        this.acceptDeps([this.ownerPath], ([mod]) => callback === null || callback === void 0 ? void 0 : callback(mod));
    }
    dispose(cb) {
        this.hmrClient.disposeMap.set(this.ownerPath, cb);
    }
    prune(cb) {
        this.hmrClient.pruneMap.set(this.ownerPath, cb);
    }
    // Kept for backward compatibility (#11036)
    // eslint-disable-next-line @typescript-eslint/no-empty-function
    decline() { }
    invalidate(message) {
        this.hmrClient.notifyListeners('vite:invalidate', {
            path: this.ownerPath,
            message,
        });
        this.send('vite:invalidate', { path: this.ownerPath, message });
        this.hmrClient.logger.debug(`[vite] invalidate ${this.ownerPath}${message ? `: ${message}` : ''}`);
    }
    on(event, cb) {
        const addToMap = (map) => {
            const existing = map.get(event) || [];
            existing.push(cb);
            map.set(event, existing);
        };
        addToMap(this.hmrClient.customListenersMap);
        addToMap(this.newListeners);
    }
    off(event, cb) {
        const removeFromMap = (map) => {
            const existing = map.get(event);
            if (existing === undefined) {
                return;
            }
            const pruned = existing.filter((l) => l !== cb);
            if (pruned.length === 0) {
                map.delete(event);
                return;
            }
            map.set(event, pruned);
        };
        removeFromMap(this.hmrClient.customListenersMap);
        removeFromMap(this.newListeners);
    }
    send(event, data) {
        this.hmrClient.messenger.send(JSON.stringify({ type: 'custom', event, data }));
    }
    acceptDeps(deps, callback = () => { }) {
        const mod = this.hmrClient.hotModulesMap.get(this.ownerPath) || {
            id: this.ownerPath,
            callbacks: [],
        };
        mod.callbacks.push({
            deps,
            fn: callback,
        });
        this.hmrClient.hotModulesMap.set(this.ownerPath, mod);
    }
}
class HMRMessenger {
    constructor(connection) {
        this.connection = connection;
        this.queue = [];
    }
    send(message) {
        this.queue.push(message);
        this.flush();
    }
    flush() {
        if (this.connection.isReady()) {
            this.queue.forEach((msg) => this.connection.send(msg));
            this.queue = [];
        }
    }
}
class HMRClient {
    constructor(logger, connection, 
    // This allows implementing reloading via different methods depending on the environment
    importUpdatedModule) {
        this.logger = logger;
        this.importUpdatedModule = importUpdatedModule;
        this.hotModulesMap = new Map();
        this.disposeMap = new Map();
        this.pruneMap = new Map();
        this.dataMap = new Map();
        this.customListenersMap = new Map();
        this.ctxToListenersMap = new Map();
        this.updateQueue = [];
        this.pendingUpdateQueue = false;
        this.messenger = new HMRMessenger(connection);
    }
    async notifyListeners(event, data) {
        const cbs = this.customListenersMap.get(event);
        if (cbs) {
            await Promise.allSettled(cbs.map((cb) => cb(data)));
        }
    }
    clear() {
        this.hotModulesMap.clear();
        this.disposeMap.clear();
        this.pruneMap.clear();
        this.dataMap.clear();
        this.customListenersMap.clear();
        this.ctxToListenersMap.clear();
    }
    // After an HMR update, some modules are no longer imported on the page
    // but they may have left behind side effects that need to be cleaned up
    // (.e.g style injections)
    async prunePaths(paths) {
        await Promise.all(paths.map((path) => {
            const disposer = this.disposeMap.get(path);
            if (disposer)
                return disposer(this.dataMap.get(path));
        }));
        paths.forEach((path) => {
            const fn = this.pruneMap.get(path);
            if (fn) {
                fn(this.dataMap.get(path));
            }
        });
    }
    warnFailedUpdate(err, path) {
        if (!err.message.includes('fetch')) {
            this.logger.error(err);
        }
        this.logger.error(`[hmr] Failed to reload ${path}. ` +
            `This could be due to syntax errors or importing non-existent ` +
            `modules. (see errors above)`);
    }
    /**
     * buffer multiple hot updates triggered by the same src change
     * so that they are invoked in the same order they were sent.
     * (otherwise the order may be inconsistent because of the http request round trip)
     */
    async queueUpdate(payload) {
        this.updateQueue.push(this.fetchUpdate(payload));
        if (!this.pendingUpdateQueue) {
            this.pendingUpdateQueue = true;
            await Promise.resolve();
            this.pendingUpdateQueue = false;
            const loading = [...this.updateQueue];
            this.updateQueue = [];
            (await Promise.all(loading)).forEach((fn) => fn && fn());
        }
    }
    async fetchUpdate(update) {
        const { path, acceptedPath } = update;
        const mod = this.hotModulesMap.get(path);
        if (!mod) {
            // In a code-splitting project,
            // it is common that the hot-updating module is not loaded yet.
            // https://github.com/vitejs/vite/issues/721
            return;
        }
        let fetchedModule;
        const isSelfUpdate = path === acceptedPath;
        // determine the qualified callbacks before we re-import the modules
        const qualifiedCallbacks = mod.callbacks.filter(({ deps }) => deps.includes(acceptedPath));
        if (isSelfUpdate || qualifiedCallbacks.length > 0) {
            const disposer = this.disposeMap.get(acceptedPath);
            if (disposer)
                await disposer(this.dataMap.get(acceptedPath));
            try {
                fetchedModule = await this.importUpdatedModule(update);
            }
            catch (e) {
                this.warnFailedUpdate(e, acceptedPath);
            }
        }
        return () => {
            for (const { deps, fn } of qualifiedCallbacks) {
                fn(deps.map((dep) => (dep === acceptedPath ? fetchedModule : undefined)));
            }
            const loggedPath = isSelfUpdate ? path : `${acceptedPath} via ${path}`;
            this.logger.debug(`[vite] hot updated: ${loggedPath}`);
        };
    }
}

const hmrConfigName = "vite.config.js";
const base$1 = "/" || '/';
// Create an element with provided attributes and optional children
function h(e, attrs = {}, ...children) {
    const elem = document.createElement(e);
    for (const [k, v] of Object.entries(attrs)) {
        elem.setAttribute(k, v);
    }
    elem.append(...children);
    return elem;
}
// set :host styles to make playwright detect the element as visible
const templateStyle = /*css*/ `
:host {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 99999;
  --monospace: 'SFMono-Regular', Consolas,
  'Liberation Mono', Menlo, Courier, monospace;
  --red: #ff5555;
  --yellow: #e2aa53;
  --purple: #cfa4ff;
  --cyan: #2dd9da;
  --dim: #c9c9c9;

  --window-background: #181818;
  --window-color: #d8d8d8;
}

.backdrop {
  position: fixed;
  z-index: 99999;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow-y: scroll;
  margin: 0;
  background: rgba(0, 0, 0, 0.66);
}

.window {
  font-family: var(--monospace);
  line-height: 1.5;
  max-width: 80vw;
  color: var(--window-color);
  box-sizing: border-box;
  margin: 30px auto;
  padding: 2.5vh 4vw;
  position: relative;
  background: var(--window-background);
  border-radius: 6px 6px 8px 8px;
  box-shadow: 0 19px 38px rgba(0,0,0,0.30), 0 15px 12px rgba(0,0,0,0.22);
  overflow: hidden;
  border-top: 8px solid var(--red);
  direction: ltr;
  text-align: left;
}

pre {
  font-family: var(--monospace);
  font-size: 16px;
  margin-top: 0;
  margin-bottom: 1em;
  overflow-x: scroll;
  scrollbar-width: none;
}

pre::-webkit-scrollbar {
  display: none;
}

pre.frame::-webkit-scrollbar {
  display: block;
  height: 5px;
}

pre.frame::-webkit-scrollbar-thumb {
  background: #999;
  border-radius: 5px;
}

pre.frame {
  scrollbar-width: thin;
}

.message {
  line-height: 1.3;
  font-weight: 600;
  white-space: pre-wrap;
}

.message-body {
  color: var(--red);
}

.plugin {
  color: var(--purple);
}

.file {
  color: var(--cyan);
  margin-bottom: 0;
  white-space: pre-wrap;
  word-break: break-all;
}

.frame {
  color: var(--yellow);
}

.stack {
  font-size: 13px;
  color: var(--dim);
}

.tip {
  font-size: 13px;
  color: #999;
  border-top: 1px dotted #999;
  padding-top: 13px;
  line-height: 1.8;
}

code {
  font-size: 13px;
  font-family: var(--monospace);
  color: var(--yellow);
}

.file-link {
  text-decoration: underline;
  cursor: pointer;
}

kbd {
  line-height: 1.5;
  font-family: ui-monospace, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
  font-size: 0.75rem;
  font-weight: 700;
  background-color: rgb(38, 40, 44);
  color: rgb(166, 167, 171);
  padding: 0.15rem 0.3rem;
  border-radius: 0.25rem;
  border-width: 0.0625rem 0.0625rem 0.1875rem;
  border-style: solid;
  border-color: rgb(54, 57, 64);
  border-image: initial;
}
`;
// Error Template
const createTemplate = () => h('div', { class: 'backdrop', part: 'backdrop' }, h('div', { class: 'window', part: 'window' }, h('pre', { class: 'message', part: 'message' }, h('span', { class: 'plugin', part: 'plugin' }), h('span', { class: 'message-body', part: 'message-body' })), h('pre', { class: 'file', part: 'file' }), h('pre', { class: 'frame', part: 'frame' }), h('pre', { class: 'stack', part: 'stack' }), h('div', { class: 'tip', part: 'tip' }, 'Click outside, press ', h('kbd', {}, 'Esc'), ' key, or fix the code to dismiss.', h('br'), 'You can also disable this overlay by setting ', h('code', { part: 'config-option-name' }, 'server.hmr.overlay'), ' to ', h('code', { part: 'config-option-value' }, 'false'), ' in ', h('code', { part: 'config-file-name' }, hmrConfigName), '.')), h('style', {}, templateStyle));
const fileRE = /(?:[a-zA-Z]:\\|\/).*?:\d+:\d+/g;
const codeframeRE = /^(?:>?\s*\d+\s+\|.*|\s+\|\s*\^.*)\r?\n/gm;
// Allow `ErrorOverlay` to extend `HTMLElement` even in environments where
// `HTMLElement` was not originally defined.
const { HTMLElement = class {
} } = globalThis;
class ErrorOverlay extends HTMLElement {
    constructor(err, links = true) {
        var _a;
        super();
        this.root = this.attachShadow({ mode: 'open' });
        this.root.appendChild(createTemplate());
        codeframeRE.lastIndex = 0;
        const hasFrame = err.frame && codeframeRE.test(err.frame);
        const message = hasFrame
            ? err.message.replace(codeframeRE, '')
            : err.message;
        if (err.plugin) {
            this.text('.plugin', `[plugin:${err.plugin}] `);
        }
        this.text('.message-body', message.trim());
        const [file] = (((_a = err.loc) === null || _a === void 0 ? void 0 : _a.file) || err.id || 'unknown file').split(`?`);
        if (err.loc) {
            this.text('.file', `${file}:${err.loc.line}:${err.loc.column}`, links);
        }
        else if (err.id) {
            this.text('.file', file);
        }
        if (hasFrame) {
            this.text('.frame', err.frame.trim());
        }
        this.text('.stack', err.stack, links);
        this.root.querySelector('.window').addEventListener('click', (e) => {
            e.stopPropagation();
        });
        this.addEventListener('click', () => {
            this.close();
        });
        this.closeOnEsc = (e) => {
            if (e.key === 'Escape' || e.code === 'Escape') {
                this.close();
            }
        };
        document.addEventListener('keydown', this.closeOnEsc);
    }
    text(selector, text, linkFiles = false) {
        const el = this.root.querySelector(selector);
        if (!linkFiles) {
            el.textContent = text;
        }
        else {
            let curIndex = 0;
            let match;
            fileRE.lastIndex = 0;
            while ((match = fileRE.exec(text))) {
                const { 0: file, index } = match;
                if (index != null) {
                    const frag = text.slice(curIndex, index);
                    el.appendChild(document.createTextNode(frag));
                    const link = document.createElement('a');
                    link.textContent = file;
                    link.className = 'file-link';
                    link.onclick = () => {
                        fetch(new URL(`${base$1}__open-in-editor?file=${encodeURIComponent(file)}`, import.meta.url));
                    };
                    el.appendChild(link);
                    curIndex += frag.length + file.length;
                }
            }
        }
    }
    close() {
        var _a;
        (_a = this.parentNode) === null || _a === void 0 ? void 0 : _a.removeChild(this);
        document.removeEventListener('keydown', this.closeOnEsc);
    }
}
const overlayId = 'vite-error-overlay';
const { customElements } = globalThis; // Ensure `customElements` is defined before the next line.
if (customElements && !customElements.get(overlayId)) {
    customElements.define(overlayId, ErrorOverlay);
}

var _a;
console.debug('[vite] connecting...');
const importMetaUrl = new URL(import.meta.url);
// use server configuration, then fallback to inference
const serverHost = "localhost:undefined/";
const socketProtocol = null || (importMetaUrl.protocol === 'https:' ? 'wss' : 'ws');
const hmrPort = null;
const socketHost = `${null || importMetaUrl.hostname}:${hmrPort || importMetaUrl.port}${"/"}`;
const directSocketHost = "localhost:undefined/";
const base = "/" || '/';
let socket;
try {
    let fallback;
    // only use fallback when port is inferred to prevent confusion
    if (!hmrPort) {
        fallback = () => {
            // fallback to connecting directly to the hmr server
            // for servers which does not support proxying websocket
            socket = setupWebSocket(socketProtocol, directSocketHost, () => {
                const currentScriptHostURL = new URL(import.meta.url);
                const currentScriptHost = currentScriptHostURL.host +
                    currentScriptHostURL.pathname.replace(/@vite\/client$/, '');
                console.error('[vite] failed to connect to websocket.\n' +
                    'your current setup:\n' +
                    `  (browser) ${currentScriptHost} <--[HTTP]--> ${serverHost} (server)\n` +
                    `  (browser) ${socketHost} <--[WebSocket (failing)]--> ${directSocketHost} (server)\n` +
                    'Check out your Vite / network configuration and https://vitejs.dev/config/server-options.html#server-hmr .');
            });
            socket.addEventListener('open', () => {
                console.info('[vite] Direct websocket connection fallback. Check out https://vitejs.dev/config/server-options.html#server-hmr to remove the previous connection error.');
            }, { once: true });
        };
    }
    socket = setupWebSocket(socketProtocol, socketHost, fallback);
}
catch (error) {
    console.error(`[vite] failed to connect to websocket (${error}). `);
}
function setupWebSocket(protocol, hostAndPath, onCloseWithoutOpen) {
    const socket = new WebSocket(`${protocol}://${hostAndPath}`, 'vite-hmr');
    let isOpened = false;
    socket.addEventListener('open', () => {
        isOpened = true;
        notifyListeners('vite:ws:connect', { webSocket: socket });
    }, { once: true });
    // Listen for messages
    socket.addEventListener('message', async ({ data }) => {
        handleMessage(JSON.parse(data));
    });
    // ping server
    socket.addEventListener('close', async ({ wasClean }) => {
        if (wasClean)
            return;
        if (!isOpened && onCloseWithoutOpen) {
            onCloseWithoutOpen();
            return;
        }
        notifyListeners('vite:ws:disconnect', { webSocket: socket });
        if (hasDocument) {
            console.log(`[vite] server connection lost. polling for restart...`);
            await waitForSuccessfulPing(protocol, hostAndPath);
            location.reload();
        }
    });
    return socket;
}
function cleanUrl(pathname) {
    const url = new URL(pathname, 'http://vitejs.dev');
    url.searchParams.delete('direct');
    return url.pathname + url.search;
}
let isFirstUpdate = true;
const outdatedLinkTags = new WeakSet();
const debounceReload = (time) => {
    let timer;
    return () => {
        if (timer) {
            clearTimeout(timer);
            timer = null;
        }
        timer = setTimeout(() => {
            location.reload();
        }, time);
    };
};
const pageReload = debounceReload(50);
const hmrClient = new HMRClient(console, {
    isReady: () => socket && socket.readyState === 1,
    send: (message) => socket.send(message),
}, async function importUpdatedModule({ acceptedPath, timestamp, explicitImportRequired, isWithinCircularImport, }) {
    const [acceptedPathWithoutQuery, query] = acceptedPath.split(`?`);
    const importPromise = import(
    /* @vite-ignore */
    base +
        acceptedPathWithoutQuery.slice(1) +
        `?${explicitImportRequired ? 'import&' : ''}t=${timestamp}${query ? `&${query}` : ''}`);
    if (isWithinCircularImport) {
        importPromise.catch(() => {
            console.info(`[hmr] ${acceptedPath} failed to apply HMR as it's within a circular import. Reloading page to reset the execution order. ` +
                `To debug and break the circular import, you can run \`vite --debug hmr\` to log the circular dependency path if a file change triggered it.`);
            pageReload();
        });
    }
    return await importPromise;
});
async function handleMessage(payload) {
    switch (payload.type) {
        case 'connected':
            console.debug(`[vite] connected.`);
            hmrClient.messenger.flush();
            // proxy(nginx, docker) hmr ws maybe caused timeout,
            // so send ping package let ws keep alive.
            setInterval(() => {
                if (socket.readyState === socket.OPEN) {
                    socket.send('{"type":"ping"}');
                }
            }, 30000);
            break;
        case 'update':
            notifyListeners('vite:beforeUpdate', payload);
            if (hasDocument) {
                // if this is the first update and there's already an error overlay, it
                // means the page opened with existing server compile error and the whole
                // module script failed to load (since one of the nested imports is 500).
                // in this case a normal update won't work and a full reload is needed.
                if (isFirstUpdate && hasErrorOverlay()) {
                    window.location.reload();
                    return;
                }
                else {
                    if (enableOverlay) {
                        clearErrorOverlay();
                    }
                    isFirstUpdate = false;
                }
            }
            await Promise.all(payload.updates.map(async (update) => {
                if (update.type === 'js-update') {
                    return hmrClient.queueUpdate(update);
                }
                // css-update
                // this is only sent when a css file referenced with <link> is updated
                const { path, timestamp } = update;
                const searchUrl = cleanUrl(path);
                // can't use querySelector with `[href*=]` here since the link may be
                // using relative paths so we need to use link.href to grab the full
                // URL for the include check.
                const el = Array.from(document.querySelectorAll('link')).find((e) => !outdatedLinkTags.has(e) && cleanUrl(e.href).includes(searchUrl));
                if (!el) {
                    return;
                }
                const newPath = `${base}${searchUrl.slice(1)}${searchUrl.includes('?') ? '&' : '?'}t=${timestamp}`;
                // rather than swapping the href on the existing tag, we will
                // create a new link tag. Once the new stylesheet has loaded we
                // will remove the existing link tag. This removes a Flash Of
                // Unstyled Content that can occur when swapping out the tag href
                // directly, as the new stylesheet has not yet been loaded.
                return new Promise((resolve) => {
                    const newLinkTag = el.cloneNode();
                    newLinkTag.href = new URL(newPath, el.href).href;
                    const removeOldEl = () => {
                        el.remove();
                        console.debug(`[vite] css hot updated: ${searchUrl}`);
                        resolve();
                    };
                    newLinkTag.addEventListener('load', removeOldEl);
                    newLinkTag.addEventListener('error', removeOldEl);
                    outdatedLinkTags.add(el);
                    el.after(newLinkTag);
                });
            }));
            notifyListeners('vite:afterUpdate', payload);
            break;
        case 'custom': {
            notifyListeners(payload.event, payload.data);
            break;
        }
        case 'full-reload':
            notifyListeners('vite:beforeFullReload', payload);
            if (hasDocument) {
                if (payload.path && payload.path.endsWith('.html')) {
                    // if html file is edited, only reload the page if the browser is
                    // currently on that page.
                    const pagePath = decodeURI(location.pathname);
                    const payloadPath = base + payload.path.slice(1);
                    if (pagePath === payloadPath ||
                        payload.path === '/index.html' ||
                        (pagePath.endsWith('/') && pagePath + 'index.html' === payloadPath)) {
                        pageReload();
                    }
                    return;
                }
                else {
                    pageReload();
                }
            }
            break;
        case 'prune':
            notifyListeners('vite:beforePrune', payload);
            await hmrClient.prunePaths(payload.paths);
            break;
        case 'error': {
            notifyListeners('vite:error', payload);
            if (hasDocument) {
                const err = payload.err;
                if (enableOverlay) {
                    createErrorOverlay(err);
                }
                else {
                    console.error(`[vite] Internal Server Error\n${err.message}\n${err.stack}`);
                }
            }
            break;
        }
        default: {
            const check = payload;
            return check;
        }
    }
}
function notifyListeners(event, data) {
    hmrClient.notifyListeners(event, data);
}
const enableOverlay = true;
const hasDocument = 'document' in globalThis;
function createErrorOverlay(err) {
    clearErrorOverlay();
    document.body.appendChild(new ErrorOverlay(err));
}
function clearErrorOverlay() {
    document.querySelectorAll(overlayId).forEach((n) => n.close());
}
function hasErrorOverlay() {
    return document.querySelectorAll(overlayId).length;
}
async function waitForSuccessfulPing(socketProtocol, hostAndPath, ms = 1000) {
    const pingHostProtocol = socketProtocol === 'wss' ? 'https' : 'http';
    const ping = async () => {
        // A fetch on a websocket URL will return a successful promise with status 400,
        // but will reject a networking error.
        // When running on middleware mode, it returns status 426, and an cors error happens if mode is not no-cors
        try {
            await fetch(`${pingHostProtocol}://${hostAndPath}`, {
                mode: 'no-cors',
                headers: {
                    // Custom headers won't be included in a request with no-cors so (ab)use one of the
                    // safelisted headers to identify the ping request
                    Accept: 'text/x-vite-ping',
                },
            });
            return true;
        }
        catch { }
        return false;
    };
    if (await ping()) {
        return;
    }
    await wait(ms);
    // eslint-disable-next-line no-constant-condition
    while (true) {
        if (document.visibilityState === 'visible') {
            if (await ping()) {
                break;
            }
            await wait(ms);
        }
        else {
            await waitForWindowShow();
        }
    }
}
function wait(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}
function waitForWindowShow() {
    return new Promise((resolve) => {
        const onChange = async () => {
            if (document.visibilityState === 'visible') {
                resolve();
                document.removeEventListener('visibilitychange', onChange);
            }
        };
        document.addEventListener('visibilitychange', onChange);
    });
}
const sheetsMap = new Map();
// collect existing style elements that may have been inserted during SSR
// to avoid FOUC or duplicate styles
if ('document' in globalThis) {
    document
        .querySelectorAll('style[data-vite-dev-id]')
        .forEach((el) => {
        sheetsMap.set(el.getAttribute('data-vite-dev-id'), el);
    });
}
const cspNonce = 'document' in globalThis
    ? (_a = document.querySelector('meta[property=csp-nonce]')) === null || _a === void 0 ? void 0 : _a.nonce
    : undefined;
// all css imports should be inserted at the same position
// because after build it will be a single css file
let lastInsertedStyle;
function updateStyle(id, content) {
    let style = sheetsMap.get(id);
    if (!style) {
        style = document.createElement('style');
        style.setAttribute('type', 'text/css');
        style.setAttribute('data-vite-dev-id', id);
        style.textContent = content;
        if (cspNonce) {
            style.setAttribute('nonce', cspNonce);
        }
        if (!lastInsertedStyle) {
            document.head.appendChild(style);
            // reset lastInsertedStyle after async
            // because dynamically imported css will be splitted into a different file
            setTimeout(() => {
                lastInsertedStyle = undefined;
            }, 0);
        }
        else {
            lastInsertedStyle.insertAdjacentElement('afterend', style);
        }
        lastInsertedStyle = style;
    }
    else {
        style.textContent = content;
    }
    sheetsMap.set(id, style);
}
function removeStyle(id) {
    const style = sheetsMap.get(id);
    if (style) {
        document.head.removeChild(style);
        sheetsMap.delete(id);
    }
}
function createHotContext(ownerPath) {
    return new HMRContext(hmrClient, ownerPath);
}
/**
 * urls here are dynamic import() urls that couldn't be statically analyzed
 */
function injectQuery(url, queryToInject) {
    // skip urls that won't be handled by vite
    if (url[0] !== '.' && url[0] !== '/') {
        return url;
    }
    // can't use pathname from URL since it may be relative like ../
    const pathname = url.replace(/[?#].*$/, '');
    const { search, hash } = new URL(url, 'http://vitejs.dev');
    return `${pathname}?${queryToInject}${search ? `&` + search.slice(1) : ''}${hash || ''}`;
}

export { ErrorOverlay, createHotContext, injectQuery, removeStyle, updateStyle };
                                   

//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiY2xpZW50Lm1qcyIsInNvdXJjZXMiOlsiaG1yLnRzIiwib3ZlcmxheS50cyIsImNsaWVudC50cyJdLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgdHlwZSB7IFVwZGF0ZSB9IGZyb20gJ3R5cGVzL2htclBheWxvYWQnXG5pbXBvcnQgdHlwZSB7IE1vZHVsZU5hbWVzcGFjZSwgVml0ZUhvdENvbnRleHQgfSBmcm9tICd0eXBlcy9ob3QnXG5pbXBvcnQgdHlwZSB7IEluZmVyQ3VzdG9tRXZlbnRQYXlsb2FkIH0gZnJvbSAndHlwZXMvY3VzdG9tRXZlbnQnXG5cbnR5cGUgQ3VzdG9tTGlzdGVuZXJzTWFwID0gTWFwPHN0cmluZywgKChkYXRhOiBhbnkpID0+IHZvaWQpW10+XG5cbmludGVyZmFjZSBIb3RNb2R1bGUge1xuICBpZDogc3RyaW5nXG4gIGNhbGxiYWNrczogSG90Q2FsbGJhY2tbXVxufVxuXG5pbnRlcmZhY2UgSG90Q2FsbGJhY2sge1xuICAvLyB0aGUgZGVwZW5kZW5jaWVzIG11c3QgYmUgZmV0Y2hhYmxlIHBhdGhzXG4gIGRlcHM6IHN0cmluZ1tdXG4gIGZuOiAobW9kdWxlczogQXJyYXk8TW9kdWxlTmFtZXNwYWNlIHwgdW5kZWZpbmVkPikgPT4gdm9pZFxufVxuXG5leHBvcnQgaW50ZXJmYWNlIEhNUkxvZ2dlciB7XG4gIGVycm9yKG1zZzogc3RyaW5nIHwgRXJyb3IpOiB2b2lkXG4gIGRlYnVnKC4uLm1zZzogdW5rbm93bltdKTogdm9pZFxufVxuXG5leHBvcnQgaW50ZXJmYWNlIEhNUkNvbm5lY3Rpb24ge1xuICAvKipcbiAgICogQ2hlY2tlZCBiZWZvcmUgc2VuZGluZyBtZXNzYWdlcyB0byB0aGUgY2xpZW50LlxuICAgKi9cbiAgaXNSZWFkeSgpOiBib29sZWFuXG4gIC8qKlxuICAgKiBTZW5kIG1lc3NhZ2UgdG8gdGhlIGNsaWVudC5cbiAgICovXG4gIHNlbmQobWVzc2FnZXM6IHN0cmluZyk6IHZvaWRcbn1cblxuZXhwb3J0IGNsYXNzIEhNUkNvbnRleHQgaW1wbGVtZW50cyBWaXRlSG90Q29udGV4dCB7XG4gIHByaXZhdGUgbmV3TGlzdGVuZXJzOiBDdXN0b21MaXN0ZW5lcnNNYXBcblxuICBjb25zdHJ1Y3RvcihcbiAgICBwcml2YXRlIGhtckNsaWVudDogSE1SQ2xpZW50LFxuICAgIHByaXZhdGUgb3duZXJQYXRoOiBzdHJpbmcsXG4gICkge1xuICAgIGlmICghaG1yQ2xpZW50LmRhdGFNYXAuaGFzKG93bmVyUGF0aCkpIHtcbiAgICAgIGhtckNsaWVudC5kYXRhTWFwLnNldChvd25lclBhdGgsIHt9KVxuICAgIH1cblxuICAgIC8vIHdoZW4gYSBmaWxlIGlzIGhvdCB1cGRhdGVkLCBhIG5ldyBjb250ZXh0IGlzIGNyZWF0ZWRcbiAgICAvLyBjbGVhciBpdHMgc3RhbGUgY2FsbGJhY2tzXG4gICAgY29uc3QgbW9kID0gaG1yQ2xpZW50LmhvdE1vZHVsZXNNYXAuZ2V0KG93bmVyUGF0aClcbiAgICBpZiAobW9kKSB7XG4gICAgICBtb2QuY2FsbGJhY2tzID0gW11cbiAgICB9XG5cbiAgICAvLyBjbGVhciBzdGFsZSBjdXN0b20gZXZlbnQgbGlzdGVuZXJzXG4gICAgY29uc3Qgc3RhbGVMaXN0ZW5lcnMgPSBobXJDbGllbnQuY3R4VG9MaXN0ZW5lcnNNYXAuZ2V0KG93bmVyUGF0aClcbiAgICBpZiAoc3RhbGVMaXN0ZW5lcnMpIHtcbiAgICAgIGZvciAoY29uc3QgW2V2ZW50LCBzdGFsZUZuc10gb2Ygc3RhbGVMaXN0ZW5lcnMpIHtcbiAgICAgICAgY29uc3QgbGlzdGVuZXJzID0gaG1yQ2xpZW50LmN1c3RvbUxpc3RlbmVyc01hcC5nZXQoZXZlbnQpXG4gICAgICAgIGlmIChsaXN0ZW5lcnMpIHtcbiAgICAgICAgICBobXJDbGllbnQuY3VzdG9tTGlzdGVuZXJzTWFwLnNldChcbiAgICAgICAgICAgIGV2ZW50LFxuICAgICAgICAgICAgbGlzdGVuZXJzLmZpbHRlcigobCkgPT4gIXN0YWxlRm5zLmluY2x1ZGVzKGwpKSxcbiAgICAgICAgICApXG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9XG5cbiAgICB0aGlzLm5ld0xpc3RlbmVycyA9IG5ldyBNYXAoKVxuICAgIGhtckNsaWVudC5jdHhUb0xpc3RlbmVyc01hcC5zZXQob3duZXJQYXRoLCB0aGlzLm5ld0xpc3RlbmVycylcbiAgfVxuXG4gIGdldCBkYXRhKCk6IGFueSB7XG4gICAgcmV0dXJuIHRoaXMuaG1yQ2xpZW50LmRhdGFNYXAuZ2V0KHRoaXMub3duZXJQYXRoKVxuICB9XG5cbiAgYWNjZXB0KGRlcHM/OiBhbnksIGNhbGxiYWNrPzogYW55KTogdm9pZCB7XG4gICAgaWYgKHR5cGVvZiBkZXBzID09PSAnZnVuY3Rpb24nIHx8ICFkZXBzKSB7XG4gICAgICAvLyBzZWxmLWFjY2VwdDogaG90LmFjY2VwdCgoKSA9PiB7fSlcbiAgICAgIHRoaXMuYWNjZXB0RGVwcyhbdGhpcy5vd25lclBhdGhdLCAoW21vZF0pID0+IGRlcHM/Lihtb2QpKVxuICAgIH0gZWxzZSBpZiAodHlwZW9mIGRlcHMgPT09ICdzdHJpbmcnKSB7XG4gICAgICAvLyBleHBsaWNpdCBkZXBzXG4gICAgICB0aGlzLmFjY2VwdERlcHMoW2RlcHNdLCAoW21vZF0pID0+IGNhbGxiYWNrPy4obW9kKSlcbiAgICB9IGVsc2UgaWYgKEFycmF5LmlzQXJyYXkoZGVwcykpIHtcbiAgICAgIHRoaXMuYWNjZXB0RGVwcyhkZXBzLCBjYWxsYmFjaylcbiAgICB9IGVsc2Uge1xuICAgICAgdGhyb3cgbmV3IEVycm9yKGBpbnZhbGlkIGhvdC5hY2NlcHQoKSB1c2FnZS5gKVxuICAgIH1cbiAgfVxuXG4gIC8vIGV4cG9ydCBuYW1lcyAoZmlyc3QgYXJnKSBhcmUgaXJyZWxldmFudCBvbiB0aGUgY2xpZW50IHNpZGUsIHRoZXkncmVcbiAgLy8gZXh0cmFjdGVkIGluIHRoZSBzZXJ2ZXIgZm9yIHByb3BhZ2F0aW9uXG4gIGFjY2VwdEV4cG9ydHMoXG4gICAgXzogc3RyaW5nIHwgcmVhZG9ubHkgc3RyaW5nW10sXG4gICAgY2FsbGJhY2s6IChkYXRhOiBhbnkpID0+IHZvaWQsXG4gICk6IHZvaWQge1xuICAgIHRoaXMuYWNjZXB0RGVwcyhbdGhpcy5vd25lclBhdGhdLCAoW21vZF0pID0+IGNhbGxiYWNrPy4obW9kKSlcbiAgfVxuXG4gIGRpc3Bvc2UoY2I6IChkYXRhOiBhbnkpID0+IHZvaWQpOiB2b2lkIHtcbiAgICB0aGlzLmhtckNsaWVudC5kaXNwb3NlTWFwLnNldCh0aGlzLm93bmVyUGF0aCwgY2IpXG4gIH1cblxuICBwcnVuZShjYjogKGRhdGE6IGFueSkgPT4gdm9pZCk6IHZvaWQge1xuICAgIHRoaXMuaG1yQ2xpZW50LnBydW5lTWFwLnNldCh0aGlzLm93bmVyUGF0aCwgY2IpXG4gIH1cblxuICAvLyBLZXB0IGZvciBiYWNrd2FyZCBjb21wYXRpYmlsaXR5ICgjMTEwMzYpXG4gIC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBAdHlwZXNjcmlwdC1lc2xpbnQvbm8tZW1wdHktZnVuY3Rpb25cbiAgZGVjbGluZSgpOiB2b2lkIHt9XG5cbiAgaW52YWxpZGF0ZShtZXNzYWdlOiBzdHJpbmcpOiB2b2lkIHtcbiAgICB0aGlzLmhtckNsaWVudC5ub3RpZnlMaXN0ZW5lcnMoJ3ZpdGU6aW52YWxpZGF0ZScsIHtcbiAgICAgIHBhdGg6IHRoaXMub3duZXJQYXRoLFxuICAgICAgbWVzc2FnZSxcbiAgICB9KVxuICAgIHRoaXMuc2VuZCgndml0ZTppbnZhbGlkYXRlJywgeyBwYXRoOiB0aGlzLm93bmVyUGF0aCwgbWVzc2FnZSB9KVxuICAgIHRoaXMuaG1yQ2xpZW50LmxvZ2dlci5kZWJ1ZyhcbiAgICAgIGBbdml0ZV0gaW52YWxpZGF0ZSAke3RoaXMub3duZXJQYXRofSR7bWVzc2FnZSA/IGA6ICR7bWVzc2FnZX1gIDogJyd9YCxcbiAgICApXG4gIH1cblxuICBvbjxUIGV4dGVuZHMgc3RyaW5nPihcbiAgICBldmVudDogVCxcbiAgICBjYjogKHBheWxvYWQ6IEluZmVyQ3VzdG9tRXZlbnRQYXlsb2FkPFQ+KSA9PiB2b2lkLFxuICApOiB2b2lkIHtcbiAgICBjb25zdCBhZGRUb01hcCA9IChtYXA6IE1hcDxzdHJpbmcsIGFueVtdPikgPT4ge1xuICAgICAgY29uc3QgZXhpc3RpbmcgPSBtYXAuZ2V0KGV2ZW50KSB8fCBbXVxuICAgICAgZXhpc3RpbmcucHVzaChjYilcbiAgICAgIG1hcC5zZXQoZXZlbnQsIGV4aXN0aW5nKVxuICAgIH1cbiAgICBhZGRUb01hcCh0aGlzLmhtckNsaWVudC5jdXN0b21MaXN0ZW5lcnNNYXApXG4gICAgYWRkVG9NYXAodGhpcy5uZXdMaXN0ZW5lcnMpXG4gIH1cblxuICBvZmY8VCBleHRlbmRzIHN0cmluZz4oXG4gICAgZXZlbnQ6IFQsXG4gICAgY2I6IChwYXlsb2FkOiBJbmZlckN1c3RvbUV2ZW50UGF5bG9hZDxUPikgPT4gdm9pZCxcbiAgKTogdm9pZCB7XG4gICAgY29uc3QgcmVtb3ZlRnJvbU1hcCA9IChtYXA6IE1hcDxzdHJpbmcsIGFueVtdPikgPT4ge1xuICAgICAgY29uc3QgZXhpc3RpbmcgPSBtYXAuZ2V0KGV2ZW50KVxuICAgICAgaWYgKGV4aXN0aW5nID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgcmV0dXJuXG4gICAgICB9XG4gICAgICBjb25zdCBwcnVuZWQgPSBleGlzdGluZy5maWx0ZXIoKGwpID0+IGwgIT09IGNiKVxuICAgICAgaWYgKHBydW5lZC5sZW5ndGggPT09IDApIHtcbiAgICAgICAgbWFwLmRlbGV0ZShldmVudClcbiAgICAgICAgcmV0dXJuXG4gICAgICB9XG4gICAgICBtYXAuc2V0KGV2ZW50LCBwcnVuZWQpXG4gICAgfVxuICAgIHJlbW92ZUZyb21NYXAodGhpcy5obXJDbGllbnQuY3VzdG9tTGlzdGVuZXJzTWFwKVxuICAgIHJlbW92ZUZyb21NYXAodGhpcy5uZXdMaXN0ZW5lcnMpXG4gIH1cblxuICBzZW5kPFQgZXh0ZW5kcyBzdHJpbmc+KGV2ZW50OiBULCBkYXRhPzogSW5mZXJDdXN0b21FdmVudFBheWxvYWQ8VD4pOiB2b2lkIHtcbiAgICB0aGlzLmhtckNsaWVudC5tZXNzZW5nZXIuc2VuZChcbiAgICAgIEpTT04uc3RyaW5naWZ5KHsgdHlwZTogJ2N1c3RvbScsIGV2ZW50LCBkYXRhIH0pLFxuICAgIClcbiAgfVxuXG4gIHByaXZhdGUgYWNjZXB0RGVwcyhcbiAgICBkZXBzOiBzdHJpbmdbXSxcbiAgICBjYWxsYmFjazogSG90Q2FsbGJhY2tbJ2ZuJ10gPSAoKSA9PiB7fSxcbiAgKTogdm9pZCB7XG4gICAgY29uc3QgbW9kOiBIb3RNb2R1bGUgPSB0aGlzLmhtckNsaWVudC5ob3RNb2R1bGVzTWFwLmdldCh0aGlzLm93bmVyUGF0aCkgfHwge1xuICAgICAgaWQ6IHRoaXMub3duZXJQYXRoLFxuICAgICAgY2FsbGJhY2tzOiBbXSxcbiAgICB9XG4gICAgbW9kLmNhbGxiYWNrcy5wdXNoKHtcbiAgICAgIGRlcHMsXG4gICAgICBmbjogY2FsbGJhY2ssXG4gICAgfSlcbiAgICB0aGlzLmhtckNsaWVudC5ob3RNb2R1bGVzTWFwLnNldCh0aGlzLm93bmVyUGF0aCwgbW9kKVxuICB9XG59XG5cbmNsYXNzIEhNUk1lc3NlbmdlciB7XG4gIGNvbnN0cnVjdG9yKHByaXZhdGUgY29ubmVjdGlvbjogSE1SQ29ubmVjdGlvbikge31cblxuICBwcml2YXRlIHF1ZXVlOiBzdHJpbmdbXSA9IFtdXG5cbiAgcHVibGljIHNlbmQobWVzc2FnZTogc3RyaW5nKTogdm9pZCB7XG4gICAgdGhpcy5xdWV1ZS5wdXNoKG1lc3NhZ2UpXG4gICAgdGhpcy5mbHVzaCgpXG4gIH1cblxuICBwdWJsaWMgZmx1c2goKTogdm9pZCB7XG4gICAgaWYgKHRoaXMuY29ubmVjdGlvbi5pc1JlYWR5KCkpIHtcbiAgICAgIHRoaXMucXVldWUuZm9yRWFjaCgobXNnKSA9PiB0aGlzLmNvbm5lY3Rpb24uc2VuZChtc2cpKVxuICAgICAgdGhpcy5xdWV1ZSA9IFtdXG4gICAgfVxuICB9XG59XG5cbmV4cG9ydCBjbGFzcyBITVJDbGllbnQge1xuICBwdWJsaWMgaG90TW9kdWxlc01hcCA9IG5ldyBNYXA8c3RyaW5nLCBIb3RNb2R1bGU+KClcbiAgcHVibGljIGRpc3Bvc2VNYXAgPSBuZXcgTWFwPHN0cmluZywgKGRhdGE6IGFueSkgPT4gdm9pZCB8IFByb21pc2U8dm9pZD4+KClcbiAgcHVibGljIHBydW5lTWFwID0gbmV3IE1hcDxzdHJpbmcsIChkYXRhOiBhbnkpID0+IHZvaWQgfCBQcm9taXNlPHZvaWQ+PigpXG4gIHB1YmxpYyBkYXRhTWFwID0gbmV3IE1hcDxzdHJpbmcsIGFueT4oKVxuICBwdWJsaWMgY3VzdG9tTGlzdGVuZXJzTWFwOiBDdXN0b21MaXN0ZW5lcnNNYXAgPSBuZXcgTWFwKClcbiAgcHVibGljIGN0eFRvTGlzdGVuZXJzTWFwID0gbmV3IE1hcDxzdHJpbmcsIEN1c3RvbUxpc3RlbmVyc01hcD4oKVxuXG4gIHB1YmxpYyBtZXNzZW5nZXI6IEhNUk1lc3NlbmdlclxuXG4gIGNvbnN0cnVjdG9yKFxuICAgIHB1YmxpYyBsb2dnZXI6IEhNUkxvZ2dlcixcbiAgICBjb25uZWN0aW9uOiBITVJDb25uZWN0aW9uLFxuICAgIC8vIFRoaXMgYWxsb3dzIGltcGxlbWVudGluZyByZWxvYWRpbmcgdmlhIGRpZmZlcmVudCBtZXRob2RzIGRlcGVuZGluZyBvbiB0aGUgZW52aXJvbm1lbnRcbiAgICBwcml2YXRlIGltcG9ydFVwZGF0ZWRNb2R1bGU6ICh1cGRhdGU6IFVwZGF0ZSkgPT4gUHJvbWlzZTxNb2R1bGVOYW1lc3BhY2U+LFxuICApIHtcbiAgICB0aGlzLm1lc3NlbmdlciA9IG5ldyBITVJNZXNzZW5nZXIoY29ubmVjdGlvbilcbiAgfVxuXG4gIHB1YmxpYyBhc3luYyBub3RpZnlMaXN0ZW5lcnM8VCBleHRlbmRzIHN0cmluZz4oXG4gICAgZXZlbnQ6IFQsXG4gICAgZGF0YTogSW5mZXJDdXN0b21FdmVudFBheWxvYWQ8VD4sXG4gICk6IFByb21pc2U8dm9pZD5cbiAgcHVibGljIGFzeW5jIG5vdGlmeUxpc3RlbmVycyhldmVudDogc3RyaW5nLCBkYXRhOiBhbnkpOiBQcm9taXNlPHZvaWQ+IHtcbiAgICBjb25zdCBjYnMgPSB0aGlzLmN1c3RvbUxpc3RlbmVyc01hcC5nZXQoZXZlbnQpXG4gICAgaWYgKGNicykge1xuICAgICAgYXdhaXQgUHJvbWlzZS5hbGxTZXR0bGVkKGNicy5tYXAoKGNiKSA9PiBjYihkYXRhKSkpXG4gICAgfVxuICB9XG5cbiAgcHVibGljIGNsZWFyKCk6IHZvaWQge1xuICAgIHRoaXMuaG90TW9kdWxlc01hcC5jbGVhcigpXG4gICAgdGhpcy5kaXNwb3NlTWFwLmNsZWFyKClcbiAgICB0aGlzLnBydW5lTWFwLmNsZWFyKClcbiAgICB0aGlzLmRhdGFNYXAuY2xlYXIoKVxuICAgIHRoaXMuY3VzdG9tTGlzdGVuZXJzTWFwLmNsZWFyKClcbiAgICB0aGlzLmN0eFRvTGlzdGVuZXJzTWFwLmNsZWFyKClcbiAgfVxuXG4gIC8vIEFmdGVyIGFuIEhNUiB1cGRhdGUsIHNvbWUgbW9kdWxlcyBhcmUgbm8gbG9uZ2VyIGltcG9ydGVkIG9uIHRoZSBwYWdlXG4gIC8vIGJ1dCB0aGV5IG1heSBoYXZlIGxlZnQgYmVoaW5kIHNpZGUgZWZmZWN0cyB0aGF0IG5lZWQgdG8gYmUgY2xlYW5lZCB1cFxuICAvLyAoLmUuZyBzdHlsZSBpbmplY3Rpb25zKVxuICBwdWJsaWMgYXN5bmMgcHJ1bmVQYXRocyhwYXRoczogc3RyaW5nW10pOiBQcm9taXNlPHZvaWQ+IHtcbiAgICBhd2FpdCBQcm9taXNlLmFsbChcbiAgICAgIHBhdGhzLm1hcCgocGF0aCkgPT4ge1xuICAgICAgICBjb25zdCBkaXNwb3NlciA9IHRoaXMuZGlzcG9zZU1hcC5nZXQocGF0aClcbiAgICAgICAgaWYgKGRpc3Bvc2VyKSByZXR1cm4gZGlzcG9zZXIodGhpcy5kYXRhTWFwLmdldChwYXRoKSlcbiAgICAgIH0pLFxuICAgIClcbiAgICBwYXRocy5mb3JFYWNoKChwYXRoKSA9PiB7XG4gICAgICBjb25zdCBmbiA9IHRoaXMucHJ1bmVNYXAuZ2V0KHBhdGgpXG4gICAgICBpZiAoZm4pIHtcbiAgICAgICAgZm4odGhpcy5kYXRhTWFwLmdldChwYXRoKSlcbiAgICAgIH1cbiAgICB9KVxuICB9XG5cbiAgcHJvdGVjdGVkIHdhcm5GYWlsZWRVcGRhdGUoZXJyOiBFcnJvciwgcGF0aDogc3RyaW5nIHwgc3RyaW5nW10pOiB2b2lkIHtcbiAgICBpZiAoIWVyci5tZXNzYWdlLmluY2x1ZGVzKCdmZXRjaCcpKSB7XG4gICAgICB0aGlzLmxvZ2dlci5lcnJvcihlcnIpXG4gICAgfVxuICAgIHRoaXMubG9nZ2VyLmVycm9yKFxuICAgICAgYFtobXJdIEZhaWxlZCB0byByZWxvYWQgJHtwYXRofS4gYCArXG4gICAgICAgIGBUaGlzIGNvdWxkIGJlIGR1ZSB0byBzeW50YXggZXJyb3JzIG9yIGltcG9ydGluZyBub24tZXhpc3RlbnQgYCArXG4gICAgICAgIGBtb2R1bGVzLiAoc2VlIGVycm9ycyBhYm92ZSlgLFxuICAgIClcbiAgfVxuXG4gIHByaXZhdGUgdXBkYXRlUXVldWU6IFByb21pc2U8KCgpID0+IHZvaWQpIHwgdW5kZWZpbmVkPltdID0gW11cbiAgcHJpdmF0ZSBwZW5kaW5nVXBkYXRlUXVldWUgPSBmYWxzZVxuXG4gIC8qKlxuICAgKiBidWZmZXIgbXVsdGlwbGUgaG90IHVwZGF0ZXMgdHJpZ2dlcmVkIGJ5IHRoZSBzYW1lIHNyYyBjaGFuZ2VcbiAgICogc28gdGhhdCB0aGV5IGFyZSBpbnZva2VkIGluIHRoZSBzYW1lIG9yZGVyIHRoZXkgd2VyZSBzZW50LlxuICAgKiAob3RoZXJ3aXNlIHRoZSBvcmRlciBtYXkgYmUgaW5jb25zaXN0ZW50IGJlY2F1c2Ugb2YgdGhlIGh0dHAgcmVxdWVzdCByb3VuZCB0cmlwKVxuICAgKi9cbiAgcHVibGljIGFzeW5jIHF1ZXVlVXBkYXRlKHBheWxvYWQ6IFVwZGF0ZSk6IFByb21pc2U8dm9pZD4ge1xuICAgIHRoaXMudXBkYXRlUXVldWUucHVzaCh0aGlzLmZldGNoVXBkYXRlKHBheWxvYWQpKVxuICAgIGlmICghdGhpcy5wZW5kaW5nVXBkYXRlUXVldWUpIHtcbiAgICAgIHRoaXMucGVuZGluZ1VwZGF0ZVF1ZXVlID0gdHJ1ZVxuICAgICAgYXdhaXQgUHJvbWlzZS5yZXNvbHZlKClcbiAgICAgIHRoaXMucGVuZGluZ1VwZGF0ZVF1ZXVlID0gZmFsc2VcbiAgICAgIGNvbnN0IGxvYWRpbmcgPSBbLi4udGhpcy51cGRhdGVRdWV1ZV1cbiAgICAgIHRoaXMudXBkYXRlUXVldWUgPSBbXVxuICAgICAgOyhhd2FpdCBQcm9taXNlLmFsbChsb2FkaW5nKSkuZm9yRWFjaCgoZm4pID0+IGZuICYmIGZuKCkpXG4gICAgfVxuICB9XG5cbiAgcHJpdmF0ZSBhc3luYyBmZXRjaFVwZGF0ZSh1cGRhdGU6IFVwZGF0ZSk6IFByb21pc2U8KCgpID0+IHZvaWQpIHwgdW5kZWZpbmVkPiB7XG4gICAgY29uc3QgeyBwYXRoLCBhY2NlcHRlZFBhdGggfSA9IHVwZGF0ZVxuICAgIGNvbnN0IG1vZCA9IHRoaXMuaG90TW9kdWxlc01hcC5nZXQocGF0aClcbiAgICBpZiAoIW1vZCkge1xuICAgICAgLy8gSW4gYSBjb2RlLXNwbGl0dGluZyBwcm9qZWN0LFxuICAgICAgLy8gaXQgaXMgY29tbW9uIHRoYXQgdGhlIGhvdC11cGRhdGluZyBtb2R1bGUgaXMgbm90IGxvYWRlZCB5ZXQuXG4gICAgICAvLyBodHRwczovL2dpdGh1Yi5jb20vdml0ZWpzL3ZpdGUvaXNzdWVzLzcyMVxuICAgICAgcmV0dXJuXG4gICAgfVxuXG4gICAgbGV0IGZldGNoZWRNb2R1bGU6IE1vZHVsZU5hbWVzcGFjZSB8IHVuZGVmaW5lZFxuICAgIGNvbnN0IGlzU2VsZlVwZGF0ZSA9IHBhdGggPT09IGFjY2VwdGVkUGF0aFxuXG4gICAgLy8gZGV0ZXJtaW5lIHRoZSBxdWFsaWZpZWQgY2FsbGJhY2tzIGJlZm9yZSB3ZSByZS1pbXBvcnQgdGhlIG1vZHVsZXNcbiAgICBjb25zdCBxdWFsaWZpZWRDYWxsYmFja3MgPSBtb2QuY2FsbGJhY2tzLmZpbHRlcigoeyBkZXBzIH0pID0+XG4gICAgICBkZXBzLmluY2x1ZGVzKGFjY2VwdGVkUGF0aCksXG4gICAgKVxuXG4gICAgaWYgKGlzU2VsZlVwZGF0ZSB8fCBxdWFsaWZpZWRDYWxsYmFja3MubGVuZ3RoID4gMCkge1xuICAgICAgY29uc3QgZGlzcG9zZXIgPSB0aGlzLmRpc3Bvc2VNYXAuZ2V0KGFjY2VwdGVkUGF0aClcbiAgICAgIGlmIChkaXNwb3NlcikgYXdhaXQgZGlzcG9zZXIodGhpcy5kYXRhTWFwLmdldChhY2NlcHRlZFBhdGgpKVxuICAgICAgdHJ5IHtcbiAgICAgICAgZmV0Y2hlZE1vZHVsZSA9IGF3YWl0IHRoaXMuaW1wb3J0VXBkYXRlZE1vZHVsZSh1cGRhdGUpXG4gICAgICB9IGNhdGNoIChlKSB7XG4gICAgICAgIHRoaXMud2FybkZhaWxlZFVwZGF0ZShlLCBhY2NlcHRlZFBhdGgpXG4gICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuICgpID0+IHtcbiAgICAgIGZvciAoY29uc3QgeyBkZXBzLCBmbiB9IG9mIHF1YWxpZmllZENhbGxiYWNrcykge1xuICAgICAgICBmbihcbiAgICAgICAgICBkZXBzLm1hcCgoZGVwKSA9PiAoZGVwID09PSBhY2NlcHRlZFBhdGggPyBmZXRjaGVkTW9kdWxlIDogdW5kZWZpbmVkKSksXG4gICAgICAgIClcbiAgICAgIH1cbiAgICAgIGNvbnN0IGxvZ2dlZFBhdGggPSBpc1NlbGZVcGRhdGUgPyBwYXRoIDogYCR7YWNjZXB0ZWRQYXRofSB2aWEgJHtwYXRofWBcbiAgICAgIHRoaXMubG9nZ2VyLmRlYnVnKGBbdml0ZV0gaG90IHVwZGF0ZWQ6ICR7bG9nZ2VkUGF0aH1gKVxuICAgIH1cbiAgfVxufVxuIiwiaW1wb3J0IHR5cGUgeyBFcnJvclBheWxvYWQgfSBmcm9tICd0eXBlcy9obXJQYXlsb2FkJ1xuXG4vLyBpbmplY3RlZCBieSB0aGUgaG1yIHBsdWdpbiB3aGVuIHNlcnZlZFxuZGVjbGFyZSBjb25zdCBfX0JBU0VfXzogc3RyaW5nXG5kZWNsYXJlIGNvbnN0IF9fSE1SX0NPTkZJR19OQU1FX186IHN0cmluZ1xuXG5jb25zdCBobXJDb25maWdOYW1lID0gX19ITVJfQ09ORklHX05BTUVfX1xuY29uc3QgYmFzZSA9IF9fQkFTRV9fIHx8ICcvJ1xuXG4vLyBDcmVhdGUgYW4gZWxlbWVudCB3aXRoIHByb3ZpZGVkIGF0dHJpYnV0ZXMgYW5kIG9wdGlvbmFsIGNoaWxkcmVuXG5mdW5jdGlvbiBoKFxuICBlOiBzdHJpbmcsXG4gIGF0dHJzOiBSZWNvcmQ8c3RyaW5nLCBzdHJpbmc+ID0ge30sXG4gIC4uLmNoaWxkcmVuOiAoc3RyaW5nIHwgTm9kZSlbXVxuKSB7XG4gIGNvbnN0IGVsZW0gPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KGUpXG4gIGZvciAoY29uc3QgW2ssIHZdIG9mIE9iamVjdC5lbnRyaWVzKGF0dHJzKSkge1xuICAgIGVsZW0uc2V0QXR0cmlidXRlKGssIHYpXG4gIH1cbiAgZWxlbS5hcHBlbmQoLi4uY2hpbGRyZW4pXG4gIHJldHVybiBlbGVtXG59XG5cbi8vIHNldCA6aG9zdCBzdHlsZXMgdG8gbWFrZSBwbGF5d3JpZ2h0IGRldGVjdCB0aGUgZWxlbWVudCBhcyB2aXNpYmxlXG5jb25zdCB0ZW1wbGF0ZVN0eWxlID0gLypjc3MqLyBgXG46aG9zdCB7XG4gIHBvc2l0aW9uOiBmaXhlZDtcbiAgdG9wOiAwO1xuICBsZWZ0OiAwO1xuICB3aWR0aDogMTAwJTtcbiAgaGVpZ2h0OiAxMDAlO1xuICB6LWluZGV4OiA5OTk5OTtcbiAgLS1tb25vc3BhY2U6ICdTRk1vbm8tUmVndWxhcicsIENvbnNvbGFzLFxuICAnTGliZXJhdGlvbiBNb25vJywgTWVubG8sIENvdXJpZXIsIG1vbm9zcGFjZTtcbiAgLS1yZWQ6ICNmZjU1NTU7XG4gIC0teWVsbG93OiAjZTJhYTUzO1xuICAtLXB1cnBsZTogI2NmYTRmZjtcbiAgLS1jeWFuOiAjMmRkOWRhO1xuICAtLWRpbTogI2M5YzljOTtcblxuICAtLXdpbmRvdy1iYWNrZ3JvdW5kOiAjMTgxODE4O1xuICAtLXdpbmRvdy1jb2xvcjogI2Q4ZDhkODtcbn1cblxuLmJhY2tkcm9wIHtcbiAgcG9zaXRpb246IGZpeGVkO1xuICB6LWluZGV4OiA5OTk5OTtcbiAgdG9wOiAwO1xuICBsZWZ0OiAwO1xuICB3aWR0aDogMTAwJTtcbiAgaGVpZ2h0OiAxMDAlO1xuICBvdmVyZmxvdy15OiBzY3JvbGw7XG4gIG1hcmdpbjogMDtcbiAgYmFja2dyb3VuZDogcmdiYSgwLCAwLCAwLCAwLjY2KTtcbn1cblxuLndpbmRvdyB7XG4gIGZvbnQtZmFtaWx5OiB2YXIoLS1tb25vc3BhY2UpO1xuICBsaW5lLWhlaWdodDogMS41O1xuICBtYXgtd2lkdGg6IDgwdnc7XG4gIGNvbG9yOiB2YXIoLS13aW5kb3ctY29sb3IpO1xuICBib3gtc2l6aW5nOiBib3JkZXItYm94O1xuICBtYXJnaW46IDMwcHggYXV0bztcbiAgcGFkZGluZzogMi41dmggNHZ3O1xuICBwb3NpdGlvbjogcmVsYXRpdmU7XG4gIGJhY2tncm91bmQ6IHZhcigtLXdpbmRvdy1iYWNrZ3JvdW5kKTtcbiAgYm9yZGVyLXJhZGl1czogNnB4IDZweCA4cHggOHB4O1xuICBib3gtc2hhZG93OiAwIDE5cHggMzhweCByZ2JhKDAsMCwwLDAuMzApLCAwIDE1cHggMTJweCByZ2JhKDAsMCwwLDAuMjIpO1xuICBvdmVyZmxvdzogaGlkZGVuO1xuICBib3JkZXItdG9wOiA4cHggc29saWQgdmFyKC0tcmVkKTtcbiAgZGlyZWN0aW9uOiBsdHI7XG4gIHRleHQtYWxpZ246IGxlZnQ7XG59XG5cbnByZSB7XG4gIGZvbnQtZmFtaWx5OiB2YXIoLS1tb25vc3BhY2UpO1xuICBmb250LXNpemU6IDE2cHg7XG4gIG1hcmdpbi10b3A6IDA7XG4gIG1hcmdpbi1ib3R0b206IDFlbTtcbiAgb3ZlcmZsb3cteDogc2Nyb2xsO1xuICBzY3JvbGxiYXItd2lkdGg6IG5vbmU7XG59XG5cbnByZTo6LXdlYmtpdC1zY3JvbGxiYXIge1xuICBkaXNwbGF5OiBub25lO1xufVxuXG5wcmUuZnJhbWU6Oi13ZWJraXQtc2Nyb2xsYmFyIHtcbiAgZGlzcGxheTogYmxvY2s7XG4gIGhlaWdodDogNXB4O1xufVxuXG5wcmUuZnJhbWU6Oi13ZWJraXQtc2Nyb2xsYmFyLXRodW1iIHtcbiAgYmFja2dyb3VuZDogIzk5OTtcbiAgYm9yZGVyLXJhZGl1czogNXB4O1xufVxuXG5wcmUuZnJhbWUge1xuICBzY3JvbGxiYXItd2lkdGg6IHRoaW47XG59XG5cbi5tZXNzYWdlIHtcbiAgbGluZS1oZWlnaHQ6IDEuMztcbiAgZm9udC13ZWlnaHQ6IDYwMDtcbiAgd2hpdGUtc3BhY2U6IHByZS13cmFwO1xufVxuXG4ubWVzc2FnZS1ib2R5IHtcbiAgY29sb3I6IHZhcigtLXJlZCk7XG59XG5cbi5wbHVnaW4ge1xuICBjb2xvcjogdmFyKC0tcHVycGxlKTtcbn1cblxuLmZpbGUge1xuICBjb2xvcjogdmFyKC0tY3lhbik7XG4gIG1hcmdpbi1ib3R0b206IDA7XG4gIHdoaXRlLXNwYWNlOiBwcmUtd3JhcDtcbiAgd29yZC1icmVhazogYnJlYWstYWxsO1xufVxuXG4uZnJhbWUge1xuICBjb2xvcjogdmFyKC0teWVsbG93KTtcbn1cblxuLnN0YWNrIHtcbiAgZm9udC1zaXplOiAxM3B4O1xuICBjb2xvcjogdmFyKC0tZGltKTtcbn1cblxuLnRpcCB7XG4gIGZvbnQtc2l6ZTogMTNweDtcbiAgY29sb3I6ICM5OTk7XG4gIGJvcmRlci10b3A6IDFweCBkb3R0ZWQgIzk5OTtcbiAgcGFkZGluZy10b3A6IDEzcHg7XG4gIGxpbmUtaGVpZ2h0OiAxLjg7XG59XG5cbmNvZGUge1xuICBmb250LXNpemU6IDEzcHg7XG4gIGZvbnQtZmFtaWx5OiB2YXIoLS1tb25vc3BhY2UpO1xuICBjb2xvcjogdmFyKC0teWVsbG93KTtcbn1cblxuLmZpbGUtbGluayB7XG4gIHRleHQtZGVjb3JhdGlvbjogdW5kZXJsaW5lO1xuICBjdXJzb3I6IHBvaW50ZXI7XG59XG5cbmtiZCB7XG4gIGxpbmUtaGVpZ2h0OiAxLjU7XG4gIGZvbnQtZmFtaWx5OiB1aS1tb25vc3BhY2UsIE1lbmxvLCBNb25hY28sIENvbnNvbGFzLCBcIkxpYmVyYXRpb24gTW9ub1wiLCBcIkNvdXJpZXIgTmV3XCIsIG1vbm9zcGFjZTtcbiAgZm9udC1zaXplOiAwLjc1cmVtO1xuICBmb250LXdlaWdodDogNzAwO1xuICBiYWNrZ3JvdW5kLWNvbG9yOiByZ2IoMzgsIDQwLCA0NCk7XG4gIGNvbG9yOiByZ2IoMTY2LCAxNjcsIDE3MSk7XG4gIHBhZGRpbmc6IDAuMTVyZW0gMC4zcmVtO1xuICBib3JkZXItcmFkaXVzOiAwLjI1cmVtO1xuICBib3JkZXItd2lkdGg6IDAuMDYyNXJlbSAwLjA2MjVyZW0gMC4xODc1cmVtO1xuICBib3JkZXItc3R5bGU6IHNvbGlkO1xuICBib3JkZXItY29sb3I6IHJnYig1NCwgNTcsIDY0KTtcbiAgYm9yZGVyLWltYWdlOiBpbml0aWFsO1xufVxuYFxuXG4vLyBFcnJvciBUZW1wbGF0ZVxuY29uc3QgY3JlYXRlVGVtcGxhdGUgPSAoKSA9PlxuICBoKFxuICAgICdkaXYnLFxuICAgIHsgY2xhc3M6ICdiYWNrZHJvcCcsIHBhcnQ6ICdiYWNrZHJvcCcgfSxcbiAgICBoKFxuICAgICAgJ2RpdicsXG4gICAgICB7IGNsYXNzOiAnd2luZG93JywgcGFydDogJ3dpbmRvdycgfSxcbiAgICAgIGgoXG4gICAgICAgICdwcmUnLFxuICAgICAgICB7IGNsYXNzOiAnbWVzc2FnZScsIHBhcnQ6ICdtZXNzYWdlJyB9LFxuICAgICAgICBoKCdzcGFuJywgeyBjbGFzczogJ3BsdWdpbicsIHBhcnQ6ICdwbHVnaW4nIH0pLFxuICAgICAgICBoKCdzcGFuJywgeyBjbGFzczogJ21lc3NhZ2UtYm9keScsIHBhcnQ6ICdtZXNzYWdlLWJvZHknIH0pLFxuICAgICAgKSxcbiAgICAgIGgoJ3ByZScsIHsgY2xhc3M6ICdmaWxlJywgcGFydDogJ2ZpbGUnIH0pLFxuICAgICAgaCgncHJlJywgeyBjbGFzczogJ2ZyYW1lJywgcGFydDogJ2ZyYW1lJyB9KSxcbiAgICAgIGgoJ3ByZScsIHsgY2xhc3M6ICdzdGFjaycsIHBhcnQ6ICdzdGFjaycgfSksXG4gICAgICBoKFxuICAgICAgICAnZGl2JyxcbiAgICAgICAgeyBjbGFzczogJ3RpcCcsIHBhcnQ6ICd0aXAnIH0sXG4gICAgICAgICdDbGljayBvdXRzaWRlLCBwcmVzcyAnLFxuICAgICAgICBoKCdrYmQnLCB7fSwgJ0VzYycpLFxuICAgICAgICAnIGtleSwgb3IgZml4IHRoZSBjb2RlIHRvIGRpc21pc3MuJyxcbiAgICAgICAgaCgnYnInKSxcbiAgICAgICAgJ1lvdSBjYW4gYWxzbyBkaXNhYmxlIHRoaXMgb3ZlcmxheSBieSBzZXR0aW5nICcsXG4gICAgICAgIGgoJ2NvZGUnLCB7IHBhcnQ6ICdjb25maWctb3B0aW9uLW5hbWUnIH0sICdzZXJ2ZXIuaG1yLm92ZXJsYXknKSxcbiAgICAgICAgJyB0byAnLFxuICAgICAgICBoKCdjb2RlJywgeyBwYXJ0OiAnY29uZmlnLW9wdGlvbi12YWx1ZScgfSwgJ2ZhbHNlJyksXG4gICAgICAgICcgaW4gJyxcbiAgICAgICAgaCgnY29kZScsIHsgcGFydDogJ2NvbmZpZy1maWxlLW5hbWUnIH0sIGhtckNvbmZpZ05hbWUpLFxuICAgICAgICAnLicsXG4gICAgICApLFxuICAgICksXG4gICAgaCgnc3R5bGUnLCB7fSwgdGVtcGxhdGVTdHlsZSksXG4gIClcblxuY29uc3QgZmlsZVJFID0gLyg/OlthLXpBLVpdOlxcXFx8XFwvKS4qPzpcXGQrOlxcZCsvZ1xuY29uc3QgY29kZWZyYW1lUkUgPSAvXig/Oj4/XFxzKlxcZCtcXHMrXFx8Lip8XFxzK1xcfFxccypcXF4uKilcXHI/XFxuL2dtXG5cbi8vIEFsbG93IGBFcnJvck92ZXJsYXlgIHRvIGV4dGVuZCBgSFRNTEVsZW1lbnRgIGV2ZW4gaW4gZW52aXJvbm1lbnRzIHdoZXJlXG4vLyBgSFRNTEVsZW1lbnRgIHdhcyBub3Qgb3JpZ2luYWxseSBkZWZpbmVkLlxuY29uc3QgeyBIVE1MRWxlbWVudCA9IGNsYXNzIHt9IGFzIHR5cGVvZiBnbG9iYWxUaGlzLkhUTUxFbGVtZW50IH0gPSBnbG9iYWxUaGlzXG5leHBvcnQgY2xhc3MgRXJyb3JPdmVybGF5IGV4dGVuZHMgSFRNTEVsZW1lbnQge1xuICByb290OiBTaGFkb3dSb290XG4gIGNsb3NlT25Fc2M6IChlOiBLZXlib2FyZEV2ZW50KSA9PiB2b2lkXG5cbiAgY29uc3RydWN0b3IoZXJyOiBFcnJvclBheWxvYWRbJ2VyciddLCBsaW5rcyA9IHRydWUpIHtcbiAgICBzdXBlcigpXG4gICAgdGhpcy5yb290ID0gdGhpcy5hdHRhY2hTaGFkb3coeyBtb2RlOiAnb3BlbicgfSlcbiAgICB0aGlzLnJvb3QuYXBwZW5kQ2hpbGQoY3JlYXRlVGVtcGxhdGUoKSlcblxuICAgIGNvZGVmcmFtZVJFLmxhc3RJbmRleCA9IDBcbiAgICBjb25zdCBoYXNGcmFtZSA9IGVyci5mcmFtZSAmJiBjb2RlZnJhbWVSRS50ZXN0KGVyci5mcmFtZSlcbiAgICBjb25zdCBtZXNzYWdlID0gaGFzRnJhbWVcbiAgICAgID8gZXJyLm1lc3NhZ2UucmVwbGFjZShjb2RlZnJhbWVSRSwgJycpXG4gICAgICA6IGVyci5tZXNzYWdlXG4gICAgaWYgKGVyci5wbHVnaW4pIHtcbiAgICAgIHRoaXMudGV4dCgnLnBsdWdpbicsIGBbcGx1Z2luOiR7ZXJyLnBsdWdpbn1dIGApXG4gICAgfVxuICAgIHRoaXMudGV4dCgnLm1lc3NhZ2UtYm9keScsIG1lc3NhZ2UudHJpbSgpKVxuXG4gICAgY29uc3QgW2ZpbGVdID0gKGVyci5sb2M/LmZpbGUgfHwgZXJyLmlkIHx8ICd1bmtub3duIGZpbGUnKS5zcGxpdChgP2ApXG4gICAgaWYgKGVyci5sb2MpIHtcbiAgICAgIHRoaXMudGV4dCgnLmZpbGUnLCBgJHtmaWxlfToke2Vyci5sb2MubGluZX06JHtlcnIubG9jLmNvbHVtbn1gLCBsaW5rcylcbiAgICB9IGVsc2UgaWYgKGVyci5pZCkge1xuICAgICAgdGhpcy50ZXh0KCcuZmlsZScsIGZpbGUpXG4gICAgfVxuXG4gICAgaWYgKGhhc0ZyYW1lKSB7XG4gICAgICB0aGlzLnRleHQoJy5mcmFtZScsIGVyci5mcmFtZSEudHJpbSgpKVxuICAgIH1cbiAgICB0aGlzLnRleHQoJy5zdGFjaycsIGVyci5zdGFjaywgbGlua3MpXG5cbiAgICB0aGlzLnJvb3QucXVlcnlTZWxlY3RvcignLndpbmRvdycpIS5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIChlKSA9PiB7XG4gICAgICBlLnN0b3BQcm9wYWdhdGlvbigpXG4gICAgfSlcblxuICAgIHRoaXMuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCAoKSA9PiB7XG4gICAgICB0aGlzLmNsb3NlKClcbiAgICB9KVxuXG4gICAgdGhpcy5jbG9zZU9uRXNjID0gKGU6IEtleWJvYXJkRXZlbnQpID0+IHtcbiAgICAgIGlmIChlLmtleSA9PT0gJ0VzY2FwZScgfHwgZS5jb2RlID09PSAnRXNjYXBlJykge1xuICAgICAgICB0aGlzLmNsb3NlKClcbiAgICAgIH1cbiAgICB9XG5cbiAgICBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdrZXlkb3duJywgdGhpcy5jbG9zZU9uRXNjKVxuICB9XG5cbiAgdGV4dChzZWxlY3Rvcjogc3RyaW5nLCB0ZXh0OiBzdHJpbmcsIGxpbmtGaWxlcyA9IGZhbHNlKTogdm9pZCB7XG4gICAgY29uc3QgZWwgPSB0aGlzLnJvb3QucXVlcnlTZWxlY3RvcihzZWxlY3RvcikhXG4gICAgaWYgKCFsaW5rRmlsZXMpIHtcbiAgICAgIGVsLnRleHRDb250ZW50ID0gdGV4dFxuICAgIH0gZWxzZSB7XG4gICAgICBsZXQgY3VySW5kZXggPSAwXG4gICAgICBsZXQgbWF0Y2g6IFJlZ0V4cEV4ZWNBcnJheSB8IG51bGxcbiAgICAgIGZpbGVSRS5sYXN0SW5kZXggPSAwXG4gICAgICB3aGlsZSAoKG1hdGNoID0gZmlsZVJFLmV4ZWModGV4dCkpKSB7XG4gICAgICAgIGNvbnN0IHsgMDogZmlsZSwgaW5kZXggfSA9IG1hdGNoXG4gICAgICAgIGlmIChpbmRleCAhPSBudWxsKSB7XG4gICAgICAgICAgY29uc3QgZnJhZyA9IHRleHQuc2xpY2UoY3VySW5kZXgsIGluZGV4KVxuICAgICAgICAgIGVsLmFwcGVuZENoaWxkKGRvY3VtZW50LmNyZWF0ZVRleHROb2RlKGZyYWcpKVxuICAgICAgICAgIGNvbnN0IGxpbmsgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdhJylcbiAgICAgICAgICBsaW5rLnRleHRDb250ZW50ID0gZmlsZVxuICAgICAgICAgIGxpbmsuY2xhc3NOYW1lID0gJ2ZpbGUtbGluaydcbiAgICAgICAgICBsaW5rLm9uY2xpY2sgPSAoKSA9PiB7XG4gICAgICAgICAgICBmZXRjaChcbiAgICAgICAgICAgICAgbmV3IFVSTChcbiAgICAgICAgICAgICAgICBgJHtiYXNlfV9fb3Blbi1pbi1lZGl0b3I/ZmlsZT0ke2VuY29kZVVSSUNvbXBvbmVudChmaWxlKX1gLFxuICAgICAgICAgICAgICAgIGltcG9ydC5tZXRhLnVybCxcbiAgICAgICAgICAgICAgKSxcbiAgICAgICAgICAgIClcbiAgICAgICAgICB9XG4gICAgICAgICAgZWwuYXBwZW5kQ2hpbGQobGluaylcbiAgICAgICAgICBjdXJJbmRleCArPSBmcmFnLmxlbmd0aCArIGZpbGUubGVuZ3RoXG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9XG4gIH1cbiAgY2xvc2UoKTogdm9pZCB7XG4gICAgdGhpcy5wYXJlbnROb2RlPy5yZW1vdmVDaGlsZCh0aGlzKVxuICAgIGRvY3VtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoJ2tleWRvd24nLCB0aGlzLmNsb3NlT25Fc2MpXG4gIH1cbn1cblxuZXhwb3J0IGNvbnN0IG92ZXJsYXlJZCA9ICd2aXRlLWVycm9yLW92ZXJsYXknXG5jb25zdCB7IGN1c3RvbUVsZW1lbnRzIH0gPSBnbG9iYWxUaGlzIC8vIEVuc3VyZSBgY3VzdG9tRWxlbWVudHNgIGlzIGRlZmluZWQgYmVmb3JlIHRoZSBuZXh0IGxpbmUuXG5pZiAoY3VzdG9tRWxlbWVudHMgJiYgIWN1c3RvbUVsZW1lbnRzLmdldChvdmVybGF5SWQpKSB7XG4gIGN1c3RvbUVsZW1lbnRzLmRlZmluZShvdmVybGF5SWQsIEVycm9yT3ZlcmxheSlcbn1cbiIsImltcG9ydCB0eXBlIHsgRXJyb3JQYXlsb2FkLCBITVJQYXlsb2FkIH0gZnJvbSAndHlwZXMvaG1yUGF5bG9hZCdcbmltcG9ydCB0eXBlIHsgVml0ZUhvdENvbnRleHQgfSBmcm9tICd0eXBlcy9ob3QnXG5pbXBvcnQgdHlwZSB7IEluZmVyQ3VzdG9tRXZlbnRQYXlsb2FkIH0gZnJvbSAndHlwZXMvY3VzdG9tRXZlbnQnXG5pbXBvcnQgeyBITVJDbGllbnQsIEhNUkNvbnRleHQgfSBmcm9tICcuLi9zaGFyZWQvaG1yJ1xuaW1wb3J0IHsgRXJyb3JPdmVybGF5LCBvdmVybGF5SWQgfSBmcm9tICcuL292ZXJsYXknXG5pbXBvcnQgJ0B2aXRlL2VudidcblxuLy8gaW5qZWN0ZWQgYnkgdGhlIGhtciBwbHVnaW4gd2hlbiBzZXJ2ZWRcbmRlY2xhcmUgY29uc3QgX19CQVNFX186IHN0cmluZ1xuZGVjbGFyZSBjb25zdCBfX1NFUlZFUl9IT1NUX186IHN0cmluZ1xuZGVjbGFyZSBjb25zdCBfX0hNUl9QUk9UT0NPTF9fOiBzdHJpbmcgfCBudWxsXG5kZWNsYXJlIGNvbnN0IF9fSE1SX0hPU1ROQU1FX186IHN0cmluZyB8IG51bGxcbmRlY2xhcmUgY29uc3QgX19ITVJfUE9SVF9fOiBudW1iZXIgfCBudWxsXG5kZWNsYXJlIGNvbnN0IF9fSE1SX0RJUkVDVF9UQVJHRVRfXzogc3RyaW5nXG5kZWNsYXJlIGNvbnN0IF9fSE1SX0JBU0VfXzogc3RyaW5nXG5kZWNsYXJlIGNvbnN0IF9fSE1SX1RJTUVPVVRfXzogbnVtYmVyXG5kZWNsYXJlIGNvbnN0IF9fSE1SX0VOQUJMRV9PVkVSTEFZX186IGJvb2xlYW5cblxuY29uc29sZS5kZWJ1ZygnW3ZpdGVdIGNvbm5lY3RpbmcuLi4nKVxuXG5jb25zdCBpbXBvcnRNZXRhVXJsID0gbmV3IFVSTChpbXBvcnQubWV0YS51cmwpXG5cbi8vIHVzZSBzZXJ2ZXIgY29uZmlndXJhdGlvbiwgdGhlbiBmYWxsYmFjayB0byBpbmZlcmVuY2VcbmNvbnN0IHNlcnZlckhvc3QgPSBfX1NFUlZFUl9IT1NUX19cbmNvbnN0IHNvY2tldFByb3RvY29sID1cbiAgX19ITVJfUFJPVE9DT0xfXyB8fCAoaW1wb3J0TWV0YVVybC5wcm90b2NvbCA9PT0gJ2h0dHBzOicgPyAnd3NzJyA6ICd3cycpXG5jb25zdCBobXJQb3J0ID0gX19ITVJfUE9SVF9fXG5jb25zdCBzb2NrZXRIb3N0ID0gYCR7X19ITVJfSE9TVE5BTUVfXyB8fCBpbXBvcnRNZXRhVXJsLmhvc3RuYW1lfToke1xuICBobXJQb3J0IHx8IGltcG9ydE1ldGFVcmwucG9ydFxufSR7X19ITVJfQkFTRV9ffWBcbmNvbnN0IGRpcmVjdFNvY2tldEhvc3QgPSBfX0hNUl9ESVJFQ1RfVEFSR0VUX19cbmNvbnN0IGJhc2UgPSBfX0JBU0VfXyB8fCAnLydcblxubGV0IHNvY2tldDogV2ViU29ja2V0XG50cnkge1xuICBsZXQgZmFsbGJhY2s6ICgoKSA9PiB2b2lkKSB8IHVuZGVmaW5lZFxuICAvLyBvbmx5IHVzZSBmYWxsYmFjayB3aGVuIHBvcnQgaXMgaW5mZXJyZWQgdG8gcHJldmVudCBjb25mdXNpb25cbiAgaWYgKCFobXJQb3J0KSB7XG4gICAgZmFsbGJhY2sgPSAoKSA9PiB7XG4gICAgICAvLyBmYWxsYmFjayB0byBjb25uZWN0aW5nIGRpcmVjdGx5IHRvIHRoZSBobXIgc2VydmVyXG4gICAgICAvLyBmb3Igc2VydmVycyB3aGljaCBkb2VzIG5vdCBzdXBwb3J0IHByb3h5aW5nIHdlYnNvY2tldFxuICAgICAgc29ja2V0ID0gc2V0dXBXZWJTb2NrZXQoc29ja2V0UHJvdG9jb2wsIGRpcmVjdFNvY2tldEhvc3QsICgpID0+IHtcbiAgICAgICAgY29uc3QgY3VycmVudFNjcmlwdEhvc3RVUkwgPSBuZXcgVVJMKGltcG9ydC5tZXRhLnVybClcbiAgICAgICAgY29uc3QgY3VycmVudFNjcmlwdEhvc3QgPVxuICAgICAgICAgIGN1cnJlbnRTY3JpcHRIb3N0VVJMLmhvc3QgK1xuICAgICAgICAgIGN1cnJlbnRTY3JpcHRIb3N0VVJMLnBhdGhuYW1lLnJlcGxhY2UoL0B2aXRlXFwvY2xpZW50JC8sICcnKVxuICAgICAgICBjb25zb2xlLmVycm9yKFxuICAgICAgICAgICdbdml0ZV0gZmFpbGVkIHRvIGNvbm5lY3QgdG8gd2Vic29ja2V0LlxcbicgK1xuICAgICAgICAgICAgJ3lvdXIgY3VycmVudCBzZXR1cDpcXG4nICtcbiAgICAgICAgICAgIGAgIChicm93c2VyKSAke2N1cnJlbnRTY3JpcHRIb3N0fSA8LS1bSFRUUF0tLT4gJHtzZXJ2ZXJIb3N0fSAoc2VydmVyKVxcbmAgK1xuICAgICAgICAgICAgYCAgKGJyb3dzZXIpICR7c29ja2V0SG9zdH0gPC0tW1dlYlNvY2tldCAoZmFpbGluZyldLS0+ICR7ZGlyZWN0U29ja2V0SG9zdH0gKHNlcnZlcilcXG5gICtcbiAgICAgICAgICAgICdDaGVjayBvdXQgeW91ciBWaXRlIC8gbmV0d29yayBjb25maWd1cmF0aW9uIGFuZCBodHRwczovL3ZpdGVqcy5kZXYvY29uZmlnL3NlcnZlci1vcHRpb25zLmh0bWwjc2VydmVyLWhtciAuJyxcbiAgICAgICAgKVxuICAgICAgfSlcbiAgICAgIHNvY2tldC5hZGRFdmVudExpc3RlbmVyKFxuICAgICAgICAnb3BlbicsXG4gICAgICAgICgpID0+IHtcbiAgICAgICAgICBjb25zb2xlLmluZm8oXG4gICAgICAgICAgICAnW3ZpdGVdIERpcmVjdCB3ZWJzb2NrZXQgY29ubmVjdGlvbiBmYWxsYmFjay4gQ2hlY2sgb3V0IGh0dHBzOi8vdml0ZWpzLmRldi9jb25maWcvc2VydmVyLW9wdGlvbnMuaHRtbCNzZXJ2ZXItaG1yIHRvIHJlbW92ZSB0aGUgcHJldmlvdXMgY29ubmVjdGlvbiBlcnJvci4nLFxuICAgICAgICAgIClcbiAgICAgICAgfSxcbiAgICAgICAgeyBvbmNlOiB0cnVlIH0sXG4gICAgICApXG4gICAgfVxuICB9XG5cbiAgc29ja2V0ID0gc2V0dXBXZWJTb2NrZXQoc29ja2V0UHJvdG9jb2wsIHNvY2tldEhvc3QsIGZhbGxiYWNrKVxufSBjYXRjaCAoZXJyb3IpIHtcbiAgY29uc29sZS5lcnJvcihgW3ZpdGVdIGZhaWxlZCB0byBjb25uZWN0IHRvIHdlYnNvY2tldCAoJHtlcnJvcn0pLiBgKVxufVxuXG5mdW5jdGlvbiBzZXR1cFdlYlNvY2tldChcbiAgcHJvdG9jb2w6IHN0cmluZyxcbiAgaG9zdEFuZFBhdGg6IHN0cmluZyxcbiAgb25DbG9zZVdpdGhvdXRPcGVuPzogKCkgPT4gdm9pZCxcbikge1xuICBjb25zdCBzb2NrZXQgPSBuZXcgV2ViU29ja2V0KGAke3Byb3RvY29sfTovLyR7aG9zdEFuZFBhdGh9YCwgJ3ZpdGUtaG1yJylcbiAgbGV0IGlzT3BlbmVkID0gZmFsc2VcblxuICBzb2NrZXQuYWRkRXZlbnRMaXN0ZW5lcihcbiAgICAnb3BlbicsXG4gICAgKCkgPT4ge1xuICAgICAgaXNPcGVuZWQgPSB0cnVlXG4gICAgICBub3RpZnlMaXN0ZW5lcnMoJ3ZpdGU6d3M6Y29ubmVjdCcsIHsgd2ViU29ja2V0OiBzb2NrZXQgfSlcbiAgICB9LFxuICAgIHsgb25jZTogdHJ1ZSB9LFxuICApXG5cbiAgLy8gTGlzdGVuIGZvciBtZXNzYWdlc1xuICBzb2NrZXQuYWRkRXZlbnRMaXN0ZW5lcignbWVzc2FnZScsIGFzeW5jICh7IGRhdGEgfSkgPT4ge1xuICAgIGhhbmRsZU1lc3NhZ2UoSlNPTi5wYXJzZShkYXRhKSlcbiAgfSlcblxuICAvLyBwaW5nIHNlcnZlclxuICBzb2NrZXQuYWRkRXZlbnRMaXN0ZW5lcignY2xvc2UnLCBhc3luYyAoeyB3YXNDbGVhbiB9KSA9PiB7XG4gICAgaWYgKHdhc0NsZWFuKSByZXR1cm5cblxuICAgIGlmICghaXNPcGVuZWQgJiYgb25DbG9zZVdpdGhvdXRPcGVuKSB7XG4gICAgICBvbkNsb3NlV2l0aG91dE9wZW4oKVxuICAgICAgcmV0dXJuXG4gICAgfVxuXG4gICAgbm90aWZ5TGlzdGVuZXJzKCd2aXRlOndzOmRpc2Nvbm5lY3QnLCB7IHdlYlNvY2tldDogc29ja2V0IH0pXG5cbiAgICBpZiAoaGFzRG9jdW1lbnQpIHtcbiAgICAgIGNvbnNvbGUubG9nKGBbdml0ZV0gc2VydmVyIGNvbm5lY3Rpb24gbG9zdC4gcG9sbGluZyBmb3IgcmVzdGFydC4uLmApXG4gICAgICBhd2FpdCB3YWl0Rm9yU3VjY2Vzc2Z1bFBpbmcocHJvdG9jb2wsIGhvc3RBbmRQYXRoKVxuICAgICAgbG9jYXRpb24ucmVsb2FkKClcbiAgICB9XG4gIH0pXG5cbiAgcmV0dXJuIHNvY2tldFxufVxuXG5mdW5jdGlvbiBjbGVhblVybChwYXRobmFtZTogc3RyaW5nKTogc3RyaW5nIHtcbiAgY29uc3QgdXJsID0gbmV3IFVSTChwYXRobmFtZSwgJ2h0dHA6Ly92aXRlanMuZGV2JylcbiAgdXJsLnNlYXJjaFBhcmFtcy5kZWxldGUoJ2RpcmVjdCcpXG4gIHJldHVybiB1cmwucGF0aG5hbWUgKyB1cmwuc2VhcmNoXG59XG5cbmxldCBpc0ZpcnN0VXBkYXRlID0gdHJ1ZVxuY29uc3Qgb3V0ZGF0ZWRMaW5rVGFncyA9IG5ldyBXZWFrU2V0PEhUTUxMaW5rRWxlbWVudD4oKVxuXG5jb25zdCBkZWJvdW5jZVJlbG9hZCA9ICh0aW1lOiBudW1iZXIpID0+IHtcbiAgbGV0IHRpbWVyOiBSZXR1cm5UeXBlPHR5cGVvZiBzZXRUaW1lb3V0PiB8IG51bGxcbiAgcmV0dXJuICgpID0+IHtcbiAgICBpZiAodGltZXIpIHtcbiAgICAgIGNsZWFyVGltZW91dCh0aW1lcilcbiAgICAgIHRpbWVyID0gbnVsbFxuICAgIH1cbiAgICB0aW1lciA9IHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgbG9jYXRpb24ucmVsb2FkKClcbiAgICB9LCB0aW1lKVxuICB9XG59XG5jb25zdCBwYWdlUmVsb2FkID0gZGVib3VuY2VSZWxvYWQoNTApXG5cbmNvbnN0IGhtckNsaWVudCA9IG5ldyBITVJDbGllbnQoXG4gIGNvbnNvbGUsXG4gIHtcbiAgICBpc1JlYWR5OiAoKSA9PiBzb2NrZXQgJiYgc29ja2V0LnJlYWR5U3RhdGUgPT09IDEsXG4gICAgc2VuZDogKG1lc3NhZ2UpID0+IHNvY2tldC5zZW5kKG1lc3NhZ2UpLFxuICB9LFxuICBhc3luYyBmdW5jdGlvbiBpbXBvcnRVcGRhdGVkTW9kdWxlKHtcbiAgICBhY2NlcHRlZFBhdGgsXG4gICAgdGltZXN0YW1wLFxuICAgIGV4cGxpY2l0SW1wb3J0UmVxdWlyZWQsXG4gICAgaXNXaXRoaW5DaXJjdWxhckltcG9ydCxcbiAgfSkge1xuICAgIGNvbnN0IFthY2NlcHRlZFBhdGhXaXRob3V0UXVlcnksIHF1ZXJ5XSA9IGFjY2VwdGVkUGF0aC5zcGxpdChgP2ApXG4gICAgY29uc3QgaW1wb3J0UHJvbWlzZSA9IGltcG9ydChcbiAgICAgIC8qIEB2aXRlLWlnbm9yZSAqL1xuICAgICAgYmFzZSArXG4gICAgICAgIGFjY2VwdGVkUGF0aFdpdGhvdXRRdWVyeS5zbGljZSgxKSArXG4gICAgICAgIGA/JHtleHBsaWNpdEltcG9ydFJlcXVpcmVkID8gJ2ltcG9ydCYnIDogJyd9dD0ke3RpbWVzdGFtcH0ke1xuICAgICAgICAgIHF1ZXJ5ID8gYCYke3F1ZXJ5fWAgOiAnJ1xuICAgICAgICB9YFxuICAgIClcbiAgICBpZiAoaXNXaXRoaW5DaXJjdWxhckltcG9ydCkge1xuICAgICAgaW1wb3J0UHJvbWlzZS5jYXRjaCgoKSA9PiB7XG4gICAgICAgIGNvbnNvbGUuaW5mbyhcbiAgICAgICAgICBgW2htcl0gJHthY2NlcHRlZFBhdGh9IGZhaWxlZCB0byBhcHBseSBITVIgYXMgaXQncyB3aXRoaW4gYSBjaXJjdWxhciBpbXBvcnQuIFJlbG9hZGluZyBwYWdlIHRvIHJlc2V0IHRoZSBleGVjdXRpb24gb3JkZXIuIGAgK1xuICAgICAgICAgICAgYFRvIGRlYnVnIGFuZCBicmVhayB0aGUgY2lyY3VsYXIgaW1wb3J0LCB5b3UgY2FuIHJ1biBcXGB2aXRlIC0tZGVidWcgaG1yXFxgIHRvIGxvZyB0aGUgY2lyY3VsYXIgZGVwZW5kZW5jeSBwYXRoIGlmIGEgZmlsZSBjaGFuZ2UgdHJpZ2dlcmVkIGl0LmAsXG4gICAgICAgIClcbiAgICAgICAgcGFnZVJlbG9hZCgpXG4gICAgICB9KVxuICAgIH1cbiAgICByZXR1cm4gYXdhaXQgaW1wb3J0UHJvbWlzZVxuICB9LFxuKVxuXG5hc3luYyBmdW5jdGlvbiBoYW5kbGVNZXNzYWdlKHBheWxvYWQ6IEhNUlBheWxvYWQpIHtcbiAgc3dpdGNoIChwYXlsb2FkLnR5cGUpIHtcbiAgICBjYXNlICdjb25uZWN0ZWQnOlxuICAgICAgY29uc29sZS5kZWJ1ZyhgW3ZpdGVdIGNvbm5lY3RlZC5gKVxuICAgICAgaG1yQ2xpZW50Lm1lc3Nlbmdlci5mbHVzaCgpXG4gICAgICAvLyBwcm94eShuZ2lueCwgZG9ja2VyKSBobXIgd3MgbWF5YmUgY2F1c2VkIHRpbWVvdXQsXG4gICAgICAvLyBzbyBzZW5kIHBpbmcgcGFja2FnZSBsZXQgd3Mga2VlcCBhbGl2ZS5cbiAgICAgIHNldEludGVydmFsKCgpID0+IHtcbiAgICAgICAgaWYgKHNvY2tldC5yZWFkeVN0YXRlID09PSBzb2NrZXQuT1BFTikge1xuICAgICAgICAgIHNvY2tldC5zZW5kKCd7XCJ0eXBlXCI6XCJwaW5nXCJ9JylcbiAgICAgICAgfVxuICAgICAgfSwgX19ITVJfVElNRU9VVF9fKVxuICAgICAgYnJlYWtcbiAgICBjYXNlICd1cGRhdGUnOlxuICAgICAgbm90aWZ5TGlzdGVuZXJzKCd2aXRlOmJlZm9yZVVwZGF0ZScsIHBheWxvYWQpXG4gICAgICBpZiAoaGFzRG9jdW1lbnQpIHtcbiAgICAgICAgLy8gaWYgdGhpcyBpcyB0aGUgZmlyc3QgdXBkYXRlIGFuZCB0aGVyZSdzIGFscmVhZHkgYW4gZXJyb3Igb3ZlcmxheSwgaXRcbiAgICAgICAgLy8gbWVhbnMgdGhlIHBhZ2Ugb3BlbmVkIHdpdGggZXhpc3Rpbmcgc2VydmVyIGNvbXBpbGUgZXJyb3IgYW5kIHRoZSB3aG9sZVxuICAgICAgICAvLyBtb2R1bGUgc2NyaXB0IGZhaWxlZCB0byBsb2FkIChzaW5jZSBvbmUgb2YgdGhlIG5lc3RlZCBpbXBvcnRzIGlzIDUwMCkuXG4gICAgICAgIC8vIGluIHRoaXMgY2FzZSBhIG5vcm1hbCB1cGRhdGUgd29uJ3Qgd29yayBhbmQgYSBmdWxsIHJlbG9hZCBpcyBuZWVkZWQuXG4gICAgICAgIGlmIChpc0ZpcnN0VXBkYXRlICYmIGhhc0Vycm9yT3ZlcmxheSgpKSB7XG4gICAgICAgICAgd2luZG93LmxvY2F0aW9uLnJlbG9hZCgpXG4gICAgICAgICAgcmV0dXJuXG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgaWYgKGVuYWJsZU92ZXJsYXkpIHtcbiAgICAgICAgICAgIGNsZWFyRXJyb3JPdmVybGF5KClcbiAgICAgICAgICB9XG4gICAgICAgICAgaXNGaXJzdFVwZGF0ZSA9IGZhbHNlXG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIGF3YWl0IFByb21pc2UuYWxsKFxuICAgICAgICBwYXlsb2FkLnVwZGF0ZXMubWFwKGFzeW5jICh1cGRhdGUpOiBQcm9taXNlPHZvaWQ+ID0+IHtcbiAgICAgICAgICBpZiAodXBkYXRlLnR5cGUgPT09ICdqcy11cGRhdGUnKSB7XG4gICAgICAgICAgICByZXR1cm4gaG1yQ2xpZW50LnF1ZXVlVXBkYXRlKHVwZGF0ZSlcbiAgICAgICAgICB9XG5cbiAgICAgICAgICAvLyBjc3MtdXBkYXRlXG4gICAgICAgICAgLy8gdGhpcyBpcyBvbmx5IHNlbnQgd2hlbiBhIGNzcyBmaWxlIHJlZmVyZW5jZWQgd2l0aCA8bGluaz4gaXMgdXBkYXRlZFxuICAgICAgICAgIGNvbnN0IHsgcGF0aCwgdGltZXN0YW1wIH0gPSB1cGRhdGVcbiAgICAgICAgICBjb25zdCBzZWFyY2hVcmwgPSBjbGVhblVybChwYXRoKVxuICAgICAgICAgIC8vIGNhbid0IHVzZSBxdWVyeVNlbGVjdG9yIHdpdGggYFtocmVmKj1dYCBoZXJlIHNpbmNlIHRoZSBsaW5rIG1heSBiZVxuICAgICAgICAgIC8vIHVzaW5nIHJlbGF0aXZlIHBhdGhzIHNvIHdlIG5lZWQgdG8gdXNlIGxpbmsuaHJlZiB0byBncmFiIHRoZSBmdWxsXG4gICAgICAgICAgLy8gVVJMIGZvciB0aGUgaW5jbHVkZSBjaGVjay5cbiAgICAgICAgICBjb25zdCBlbCA9IEFycmF5LmZyb20oXG4gICAgICAgICAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsPEhUTUxMaW5rRWxlbWVudD4oJ2xpbmsnKSxcbiAgICAgICAgICApLmZpbmQoXG4gICAgICAgICAgICAoZSkgPT5cbiAgICAgICAgICAgICAgIW91dGRhdGVkTGlua1RhZ3MuaGFzKGUpICYmIGNsZWFuVXJsKGUuaHJlZikuaW5jbHVkZXMoc2VhcmNoVXJsKSxcbiAgICAgICAgICApXG5cbiAgICAgICAgICBpZiAoIWVsKSB7XG4gICAgICAgICAgICByZXR1cm5cbiAgICAgICAgICB9XG5cbiAgICAgICAgICBjb25zdCBuZXdQYXRoID0gYCR7YmFzZX0ke3NlYXJjaFVybC5zbGljZSgxKX0ke1xuICAgICAgICAgICAgc2VhcmNoVXJsLmluY2x1ZGVzKCc/JykgPyAnJicgOiAnPydcbiAgICAgICAgICB9dD0ke3RpbWVzdGFtcH1gXG5cbiAgICAgICAgICAvLyByYXRoZXIgdGhhbiBzd2FwcGluZyB0aGUgaHJlZiBvbiB0aGUgZXhpc3RpbmcgdGFnLCB3ZSB3aWxsXG4gICAgICAgICAgLy8gY3JlYXRlIGEgbmV3IGxpbmsgdGFnLiBPbmNlIHRoZSBuZXcgc3R5bGVzaGVldCBoYXMgbG9hZGVkIHdlXG4gICAgICAgICAgLy8gd2lsbCByZW1vdmUgdGhlIGV4aXN0aW5nIGxpbmsgdGFnLiBUaGlzIHJlbW92ZXMgYSBGbGFzaCBPZlxuICAgICAgICAgIC8vIFVuc3R5bGVkIENvbnRlbnQgdGhhdCBjYW4gb2NjdXIgd2hlbiBzd2FwcGluZyBvdXQgdGhlIHRhZyBocmVmXG4gICAgICAgICAgLy8gZGlyZWN0bHksIGFzIHRoZSBuZXcgc3R5bGVzaGVldCBoYXMgbm90IHlldCBiZWVuIGxvYWRlZC5cbiAgICAgICAgICByZXR1cm4gbmV3IFByb21pc2UoKHJlc29sdmUpID0+IHtcbiAgICAgICAgICAgIGNvbnN0IG5ld0xpbmtUYWcgPSBlbC5jbG9uZU5vZGUoKSBhcyBIVE1MTGlua0VsZW1lbnRcbiAgICAgICAgICAgIG5ld0xpbmtUYWcuaHJlZiA9IG5ldyBVUkwobmV3UGF0aCwgZWwuaHJlZikuaHJlZlxuICAgICAgICAgICAgY29uc3QgcmVtb3ZlT2xkRWwgPSAoKSA9PiB7XG4gICAgICAgICAgICAgIGVsLnJlbW92ZSgpXG4gICAgICAgICAgICAgIGNvbnNvbGUuZGVidWcoYFt2aXRlXSBjc3MgaG90IHVwZGF0ZWQ6ICR7c2VhcmNoVXJsfWApXG4gICAgICAgICAgICAgIHJlc29sdmUoKVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgbmV3TGlua1RhZy5hZGRFdmVudExpc3RlbmVyKCdsb2FkJywgcmVtb3ZlT2xkRWwpXG4gICAgICAgICAgICBuZXdMaW5rVGFnLmFkZEV2ZW50TGlzdGVuZXIoJ2Vycm9yJywgcmVtb3ZlT2xkRWwpXG4gICAgICAgICAgICBvdXRkYXRlZExpbmtUYWdzLmFkZChlbClcbiAgICAgICAgICAgIGVsLmFmdGVyKG5ld0xpbmtUYWcpXG4gICAgICAgICAgfSlcbiAgICAgICAgfSksXG4gICAgICApXG4gICAgICBub3RpZnlMaXN0ZW5lcnMoJ3ZpdGU6YWZ0ZXJVcGRhdGUnLCBwYXlsb2FkKVxuICAgICAgYnJlYWtcbiAgICBjYXNlICdjdXN0b20nOiB7XG4gICAgICBub3RpZnlMaXN0ZW5lcnMocGF5bG9hZC5ldmVudCwgcGF5bG9hZC5kYXRhKVxuICAgICAgYnJlYWtcbiAgICB9XG4gICAgY2FzZSAnZnVsbC1yZWxvYWQnOlxuICAgICAgbm90aWZ5TGlzdGVuZXJzKCd2aXRlOmJlZm9yZUZ1bGxSZWxvYWQnLCBwYXlsb2FkKVxuICAgICAgaWYgKGhhc0RvY3VtZW50KSB7XG4gICAgICAgIGlmIChwYXlsb2FkLnBhdGggJiYgcGF5bG9hZC5wYXRoLmVuZHNXaXRoKCcuaHRtbCcpKSB7XG4gICAgICAgICAgLy8gaWYgaHRtbCBmaWxlIGlzIGVkaXRlZCwgb25seSByZWxvYWQgdGhlIHBhZ2UgaWYgdGhlIGJyb3dzZXIgaXNcbiAgICAgICAgICAvLyBjdXJyZW50bHkgb24gdGhhdCBwYWdlLlxuICAgICAgICAgIGNvbnN0IHBhZ2VQYXRoID0gZGVjb2RlVVJJKGxvY2F0aW9uLnBhdGhuYW1lKVxuICAgICAgICAgIGNvbnN0IHBheWxvYWRQYXRoID0gYmFzZSArIHBheWxvYWQucGF0aC5zbGljZSgxKVxuICAgICAgICAgIGlmIChcbiAgICAgICAgICAgIHBhZ2VQYXRoID09PSBwYXlsb2FkUGF0aCB8fFxuICAgICAgICAgICAgcGF5bG9hZC5wYXRoID09PSAnL2luZGV4Lmh0bWwnIHx8XG4gICAgICAgICAgICAocGFnZVBhdGguZW5kc1dpdGgoJy8nKSAmJiBwYWdlUGF0aCArICdpbmRleC5odG1sJyA9PT0gcGF5bG9hZFBhdGgpXG4gICAgICAgICAgKSB7XG4gICAgICAgICAgICBwYWdlUmVsb2FkKClcbiAgICAgICAgICB9XG4gICAgICAgICAgcmV0dXJuXG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgcGFnZVJlbG9hZCgpXG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIGJyZWFrXG4gICAgY2FzZSAncHJ1bmUnOlxuICAgICAgbm90aWZ5TGlzdGVuZXJzKCd2aXRlOmJlZm9yZVBydW5lJywgcGF5bG9hZClcbiAgICAgIGF3YWl0IGhtckNsaWVudC5wcnVuZVBhdGhzKHBheWxvYWQucGF0aHMpXG4gICAgICBicmVha1xuICAgIGNhc2UgJ2Vycm9yJzoge1xuICAgICAgbm90aWZ5TGlzdGVuZXJzKCd2aXRlOmVycm9yJywgcGF5bG9hZClcbiAgICAgIGlmIChoYXNEb2N1bWVudCkge1xuICAgICAgICBjb25zdCBlcnIgPSBwYXlsb2FkLmVyclxuICAgICAgICBpZiAoZW5hYmxlT3ZlcmxheSkge1xuICAgICAgICAgIGNyZWF0ZUVycm9yT3ZlcmxheShlcnIpXG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgY29uc29sZS5lcnJvcihcbiAgICAgICAgICAgIGBbdml0ZV0gSW50ZXJuYWwgU2VydmVyIEVycm9yXFxuJHtlcnIubWVzc2FnZX1cXG4ke2Vyci5zdGFja31gLFxuICAgICAgICAgIClcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgYnJlYWtcbiAgICB9XG4gICAgZGVmYXVsdDoge1xuICAgICAgY29uc3QgY2hlY2s6IG5ldmVyID0gcGF5bG9hZFxuICAgICAgcmV0dXJuIGNoZWNrXG4gICAgfVxuICB9XG59XG5cbmZ1bmN0aW9uIG5vdGlmeUxpc3RlbmVyczxUIGV4dGVuZHMgc3RyaW5nPihcbiAgZXZlbnQ6IFQsXG4gIGRhdGE6IEluZmVyQ3VzdG9tRXZlbnRQYXlsb2FkPFQ+LFxuKTogdm9pZFxuZnVuY3Rpb24gbm90aWZ5TGlzdGVuZXJzKGV2ZW50OiBzdHJpbmcsIGRhdGE6IGFueSk6IHZvaWQge1xuICBobXJDbGllbnQubm90aWZ5TGlzdGVuZXJzKGV2ZW50LCBkYXRhKVxufVxuXG5jb25zdCBlbmFibGVPdmVybGF5ID0gX19ITVJfRU5BQkxFX09WRVJMQVlfX1xuY29uc3QgaGFzRG9jdW1lbnQgPSAnZG9jdW1lbnQnIGluIGdsb2JhbFRoaXNcblxuZnVuY3Rpb24gY3JlYXRlRXJyb3JPdmVybGF5KGVycjogRXJyb3JQYXlsb2FkWydlcnInXSkge1xuICBjbGVhckVycm9yT3ZlcmxheSgpXG4gIGRvY3VtZW50LmJvZHkuYXBwZW5kQ2hpbGQobmV3IEVycm9yT3ZlcmxheShlcnIpKVxufVxuXG5mdW5jdGlvbiBjbGVhckVycm9yT3ZlcmxheSgpIHtcbiAgZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbDxFcnJvck92ZXJsYXk+KG92ZXJsYXlJZCkuZm9yRWFjaCgobikgPT4gbi5jbG9zZSgpKVxufVxuXG5mdW5jdGlvbiBoYXNFcnJvck92ZXJsYXkoKSB7XG4gIHJldHVybiBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKG92ZXJsYXlJZCkubGVuZ3RoXG59XG5cbmFzeW5jIGZ1bmN0aW9uIHdhaXRGb3JTdWNjZXNzZnVsUGluZyhcbiAgc29ja2V0UHJvdG9jb2w6IHN0cmluZyxcbiAgaG9zdEFuZFBhdGg6IHN0cmluZyxcbiAgbXMgPSAxMDAwLFxuKSB7XG4gIGNvbnN0IHBpbmdIb3N0UHJvdG9jb2wgPSBzb2NrZXRQcm90b2NvbCA9PT0gJ3dzcycgPyAnaHR0cHMnIDogJ2h0dHAnXG5cbiAgY29uc3QgcGluZyA9IGFzeW5jICgpID0+IHtcbiAgICAvLyBBIGZldGNoIG9uIGEgd2Vic29ja2V0IFVSTCB3aWxsIHJldHVybiBhIHN1Y2Nlc3NmdWwgcHJvbWlzZSB3aXRoIHN0YXR1cyA0MDAsXG4gICAgLy8gYnV0IHdpbGwgcmVqZWN0IGEgbmV0d29ya2luZyBlcnJvci5cbiAgICAvLyBXaGVuIHJ1bm5pbmcgb24gbWlkZGxld2FyZSBtb2RlLCBpdCByZXR1cm5zIHN0YXR1cyA0MjYsIGFuZCBhbiBjb3JzIGVycm9yIGhhcHBlbnMgaWYgbW9kZSBpcyBub3Qgbm8tY29yc1xuICAgIHRyeSB7XG4gICAgICBhd2FpdCBmZXRjaChgJHtwaW5nSG9zdFByb3RvY29sfTovLyR7aG9zdEFuZFBhdGh9YCwge1xuICAgICAgICBtb2RlOiAnbm8tY29ycycsXG4gICAgICAgIGhlYWRlcnM6IHtcbiAgICAgICAgICAvLyBDdXN0b20gaGVhZGVycyB3b24ndCBiZSBpbmNsdWRlZCBpbiBhIHJlcXVlc3Qgd2l0aCBuby1jb3JzIHNvIChhYil1c2Ugb25lIG9mIHRoZVxuICAgICAgICAgIC8vIHNhZmVsaXN0ZWQgaGVhZGVycyB0byBpZGVudGlmeSB0aGUgcGluZyByZXF1ZXN0XG4gICAgICAgICAgQWNjZXB0OiAndGV4dC94LXZpdGUtcGluZycsXG4gICAgICAgIH0sXG4gICAgICB9KVxuICAgICAgcmV0dXJuIHRydWVcbiAgICB9IGNhdGNoIHt9XG4gICAgcmV0dXJuIGZhbHNlXG4gIH1cblxuICBpZiAoYXdhaXQgcGluZygpKSB7XG4gICAgcmV0dXJuXG4gIH1cbiAgYXdhaXQgd2FpdChtcylcblxuICAvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgbm8tY29uc3RhbnQtY29uZGl0aW9uXG4gIHdoaWxlICh0cnVlKSB7XG4gICAgaWYgKGRvY3VtZW50LnZpc2liaWxpdHlTdGF0ZSA9PT0gJ3Zpc2libGUnKSB7XG4gICAgICBpZiAoYXdhaXQgcGluZygpKSB7XG4gICAgICAgIGJyZWFrXG4gICAgICB9XG4gICAgICBhd2FpdCB3YWl0KG1zKVxuICAgIH0gZWxzZSB7XG4gICAgICBhd2FpdCB3YWl0Rm9yV2luZG93U2hvdygpXG4gICAgfVxuICB9XG59XG5cbmZ1bmN0aW9uIHdhaXQobXM6IG51bWJlcikge1xuICByZXR1cm4gbmV3IFByb21pc2UoKHJlc29sdmUpID0+IHNldFRpbWVvdXQocmVzb2x2ZSwgbXMpKVxufVxuXG5mdW5jdGlvbiB3YWl0Rm9yV2luZG93U2hvdygpIHtcbiAgcmV0dXJuIG5ldyBQcm9taXNlPHZvaWQ+KChyZXNvbHZlKSA9PiB7XG4gICAgY29uc3Qgb25DaGFuZ2UgPSBhc3luYyAoKSA9PiB7XG4gICAgICBpZiAoZG9jdW1lbnQudmlzaWJpbGl0eVN0YXRlID09PSAndmlzaWJsZScpIHtcbiAgICAgICAgcmVzb2x2ZSgpXG4gICAgICAgIGRvY3VtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoJ3Zpc2liaWxpdHljaGFuZ2UnLCBvbkNoYW5nZSlcbiAgICAgIH1cbiAgICB9XG4gICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcigndmlzaWJpbGl0eWNoYW5nZScsIG9uQ2hhbmdlKVxuICB9KVxufVxuXG5jb25zdCBzaGVldHNNYXAgPSBuZXcgTWFwPHN0cmluZywgSFRNTFN0eWxlRWxlbWVudD4oKVxuXG4vLyBjb2xsZWN0IGV4aXN0aW5nIHN0eWxlIGVsZW1lbnRzIHRoYXQgbWF5IGhhdmUgYmVlbiBpbnNlcnRlZCBkdXJpbmcgU1NSXG4vLyB0byBhdm9pZCBGT1VDIG9yIGR1cGxpY2F0ZSBzdHlsZXNcbmlmICgnZG9jdW1lbnQnIGluIGdsb2JhbFRoaXMpIHtcbiAgZG9jdW1lbnRcbiAgICAucXVlcnlTZWxlY3RvckFsbDxIVE1MU3R5bGVFbGVtZW50Pignc3R5bGVbZGF0YS12aXRlLWRldi1pZF0nKVxuICAgIC5mb3JFYWNoKChlbCkgPT4ge1xuICAgICAgc2hlZXRzTWFwLnNldChlbC5nZXRBdHRyaWJ1dGUoJ2RhdGEtdml0ZS1kZXYtaWQnKSEsIGVsKVxuICAgIH0pXG59XG5cbmNvbnN0IGNzcE5vbmNlID1cbiAgJ2RvY3VtZW50JyBpbiBnbG9iYWxUaGlzXG4gICAgPyBkb2N1bWVudC5xdWVyeVNlbGVjdG9yPEhUTUxNZXRhRWxlbWVudD4oJ21ldGFbcHJvcGVydHk9Y3NwLW5vbmNlXScpPy5ub25jZVxuICAgIDogdW5kZWZpbmVkXG5cbi8vIGFsbCBjc3MgaW1wb3J0cyBzaG91bGQgYmUgaW5zZXJ0ZWQgYXQgdGhlIHNhbWUgcG9zaXRpb25cbi8vIGJlY2F1c2UgYWZ0ZXIgYnVpbGQgaXQgd2lsbCBiZSBhIHNpbmdsZSBjc3MgZmlsZVxubGV0IGxhc3RJbnNlcnRlZFN0eWxlOiBIVE1MU3R5bGVFbGVtZW50IHwgdW5kZWZpbmVkXG5cbmV4cG9ydCBmdW5jdGlvbiB1cGRhdGVTdHlsZShpZDogc3RyaW5nLCBjb250ZW50OiBzdHJpbmcpOiB2b2lkIHtcbiAgbGV0IHN0eWxlID0gc2hlZXRzTWFwLmdldChpZClcbiAgaWYgKCFzdHlsZSkge1xuICAgIHN0eWxlID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnc3R5bGUnKVxuICAgIHN0eWxlLnNldEF0dHJpYnV0ZSgndHlwZScsICd0ZXh0L2NzcycpXG4gICAgc3R5bGUuc2V0QXR0cmlidXRlKCdkYXRhLXZpdGUtZGV2LWlkJywgaWQpXG4gICAgc3R5bGUudGV4dENvbnRlbnQgPSBjb250ZW50XG4gICAgaWYgKGNzcE5vbmNlKSB7XG4gICAgICBzdHlsZS5zZXRBdHRyaWJ1dGUoJ25vbmNlJywgY3NwTm9uY2UpXG4gICAgfVxuXG4gICAgaWYgKCFsYXN0SW5zZXJ0ZWRTdHlsZSkge1xuICAgICAgZG9jdW1lbnQuaGVhZC5hcHBlbmRDaGlsZChzdHlsZSlcblxuICAgICAgLy8gcmVzZXQgbGFzdEluc2VydGVkU3R5bGUgYWZ0ZXIgYXN5bmNcbiAgICAgIC8vIGJlY2F1c2UgZHluYW1pY2FsbHkgaW1wb3J0ZWQgY3NzIHdpbGwgYmUgc3BsaXR0ZWQgaW50byBhIGRpZmZlcmVudCBmaWxlXG4gICAgICBzZXRUaW1lb3V0KCgpID0+IHtcbiAgICAgICAgbGFzdEluc2VydGVkU3R5bGUgPSB1bmRlZmluZWRcbiAgICAgIH0sIDApXG4gICAgfSBlbHNlIHtcbiAgICAgIGxhc3RJbnNlcnRlZFN0eWxlLmluc2VydEFkamFjZW50RWxlbWVudCgnYWZ0ZXJlbmQnLCBzdHlsZSlcbiAgICB9XG4gICAgbGFzdEluc2VydGVkU3R5bGUgPSBzdHlsZVxuICB9IGVsc2Uge1xuICAgIHN0eWxlLnRleHRDb250ZW50ID0gY29udGVudFxuICB9XG4gIHNoZWV0c01hcC5zZXQoaWQsIHN0eWxlKVxufVxuXG5leHBvcnQgZnVuY3Rpb24gcmVtb3ZlU3R5bGUoaWQ6IHN0cmluZyk6IHZvaWQge1xuICBjb25zdCBzdHlsZSA9IHNoZWV0c01hcC5nZXQoaWQpXG4gIGlmIChzdHlsZSkge1xuICAgIGRvY3VtZW50LmhlYWQucmVtb3ZlQ2hpbGQoc3R5bGUpXG4gICAgc2hlZXRzTWFwLmRlbGV0ZShpZClcbiAgfVxufVxuXG5leHBvcnQgZnVuY3Rpb24gY3JlYXRlSG90Q29udGV4dChvd25lclBhdGg6IHN0cmluZyk6IFZpdGVIb3RDb250ZXh0IHtcbiAgcmV0dXJuIG5ldyBITVJDb250ZXh0KGhtckNsaWVudCwgb3duZXJQYXRoKVxufVxuXG4vKipcbiAqIHVybHMgaGVyZSBhcmUgZHluYW1pYyBpbXBvcnQoKSB1cmxzIHRoYXQgY291bGRuJ3QgYmUgc3RhdGljYWxseSBhbmFseXplZFxuICovXG5leHBvcnQgZnVuY3Rpb24gaW5qZWN0UXVlcnkodXJsOiBzdHJpbmcsIHF1ZXJ5VG9JbmplY3Q6IHN0cmluZyk6IHN0cmluZyB7XG4gIC8vIHNraXAgdXJscyB0aGF0IHdvbid0IGJlIGhhbmRsZWQgYnkgdml0ZVxuICBpZiAodXJsWzBdICE9PSAnLicgJiYgdXJsWzBdICE9PSAnLycpIHtcbiAgICByZXR1cm4gdXJsXG4gIH1cblxuICAvLyBjYW4ndCB1c2UgcGF0aG5hbWUgZnJvbSBVUkwgc2luY2UgaXQgbWF5IGJlIHJlbGF0aXZlIGxpa2UgLi4vXG4gIGNvbnN0IHBhdGhuYW1lID0gdXJsLnJlcGxhY2UoL1s/I10uKiQvLCAnJylcbiAgY29uc3QgeyBzZWFyY2gsIGhhc2ggfSA9IG5ldyBVUkwodXJsLCAnaHR0cDovL3ZpdGVqcy5kZXYnKVxuXG4gIHJldHVybiBgJHtwYXRobmFtZX0/JHtxdWVyeVRvSW5qZWN0fSR7c2VhcmNoID8gYCZgICsgc2VhcmNoLnNsaWNlKDEpIDogJyd9JHtcbiAgICBoYXNoIHx8ICcnXG4gIH1gXG59XG5cbmV4cG9ydCB7IEVycm9yT3ZlcmxheSB9XG4iXSwibmFtZXMiOlsiYmFzZSJdLCJtYXBwaW5ncyI6Ijs7TUFpQ2EsVUFBVSxDQUFBO0lBR3JCLFdBQ1UsQ0FBQSxTQUFvQixFQUNwQixTQUFpQixFQUFBO1FBRGpCLElBQVMsQ0FBQSxTQUFBLEdBQVQsU0FBUyxDQUFXO1FBQ3BCLElBQVMsQ0FBQSxTQUFBLEdBQVQsU0FBUyxDQUFRO1FBRXpCLElBQUksQ0FBQyxTQUFTLENBQUMsT0FBTyxDQUFDLEdBQUcsQ0FBQyxTQUFTLENBQUMsRUFBRTtZQUNyQyxTQUFTLENBQUMsT0FBTyxDQUFDLEdBQUcsQ0FBQyxTQUFTLEVBQUUsRUFBRSxDQUFDLENBQUE7QUFDckMsU0FBQTs7O1FBSUQsTUFBTSxHQUFHLEdBQUcsU0FBUyxDQUFDLGFBQWEsQ0FBQyxHQUFHLENBQUMsU0FBUyxDQUFDLENBQUE7QUFDbEQsUUFBQSxJQUFJLEdBQUcsRUFBRTtBQUNQLFlBQUEsR0FBRyxDQUFDLFNBQVMsR0FBRyxFQUFFLENBQUE7QUFDbkIsU0FBQTs7UUFHRCxNQUFNLGNBQWMsR0FBRyxTQUFTLENBQUMsaUJBQWlCLENBQUMsR0FBRyxDQUFDLFNBQVMsQ0FBQyxDQUFBO0FBQ2pFLFFBQUEsSUFBSSxjQUFjLEVBQUU7WUFDbEIsS0FBSyxNQUFNLENB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