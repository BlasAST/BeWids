document.addEventListener('DOMContentLoaded', iniciar);
function iniciar() {
    abrirApartadosChat();
    eventosListaChat();
    eventosEncuestas();
}

// ELEMENTOS DE EL CHAT

let botonesL;
let menusL;
let seleccionado;
let nuevoChat;
let botonHijoDiv;
function abrirApartadosChat() {
    botonesL = document.querySelectorAll('.seleccionesChat > button:not(:first-child)');
    botonHijoDiv = document.querySelector('.botonDiv >button');
    menusL = document.querySelectorAll('.seleccionesChat > ul');
    botonesL.forEach(boton => boton.addEventListener('click', abrirC));
    botonHijoDiv.addEventListener('click', abrirC);
}
function abrirC(evt) {
    menusL.forEach(menu => {
        menu.classList.contains(evt.currentTarget.id) ? menu.classList.toggle('hidden') : '';
    })
}
function eventosListaChat() {
    seleccionado = document.querySelector('select');

    seleccionado.addEventListener('change', () => {
        nuevoChat = document.querySelector('.newChat');
        nuevoChat.style.display = 'block';
        cambioSeleccionado();
    })
    crearNuevoGrupo();
}

function cambioSeleccionado() {
    let botonRechazo;
    let botonAceptar;
    let pMensaje;

    setTimeout(() => {
        botonRechazo = document.querySelector('.cancelar')
        botonRechazo.addEventListener('click', () => {
            seleccionado.value = seleccionado.options[0].value;
        });
        botonAceptar = document.querySelector('.aceptar')
        botonAceptar.addEventListener('click', () => {
            setTimeout(() => {
                seleccionado.value = seleccionado.options[0].value;
                pMensaje = document.querySelector('.mensajeNewChat');
                pMensaje.classList.remove('hidden');
                setTimeout(() => {
                    pMensaje.classList.add('hidden');
                }, 2500)
            }, 100)
        })


    }, 100)
}

function crearNuevoGrupo() {
    let formulario = document.querySelector('.newGroupForm');
    let boton = document.querySelector('.newGroup');
    let inputAll = document.querySelector('#all');
    let inputs = document.querySelectorAll('.creacionGrupo >input:not(#all)');
    let cierreFormulario = document.querySelector('.cierreFormGroup');

    boton.addEventListener('click', () => {
        menusL.forEach(menu => menu.classList.add('hidden'));
        formulario.classList.toggle('hidden');
        formulario.classList.toggle('flex');
    })

    inputAll.addEventListener('change', () => {
        if (inputAll.checked) {
            inputs.forEach(input => input.checked = false);
        }
    })
    inputs.forEach(input => input.addEventListener('change', () => {
        if (input.checked && inputAll.checked) {
            inputAll.checked = false;
        }
    }))
    cierreFormulario.addEventListener('click', () => {
        formulario.classList.add('hidden');
    })


}

function mostrarParticipantesChat() {
    let boton = document.querySelector('.mostrarListaParticipantes');
    let participantes = document.querySelector('.participantesList');
    boton.addEventListener('click', () => {
        participantes.classList.toggle('hidden');
    })


}
// mostrarListaParticipantes
function eventosEncuestas() {
    scrollEncuestas();
    coloresTablaEncuestas();
    mostrarFormulario();
    crearInputs();
    seleccionadosEnFormulario();
    botones()
}


// ELEMENTOS ENCUESTAS
let botonesE;
let partesCabecera;


function scrollEncuestas() {
    botonesE = document.querySelector('.botonesEncuestas');
    let listadoEncuestas = document.querySelector('.listadoEncuestas');
    let ultimoScroll = 0;

    listadoEncuestas.addEventListener('scroll', function () {
        let topeScroll = listadoEncuestas.scrollTop;
        if (topeScroll > ultimoScroll) {
            botonesE.classList.add('hidden')
        } else {
            botonesE.classList.remove('hidden')
        }
        ultimoScroll = topeScroll;
    });
}

function coloresTablaEncuestas() {
    partesCabecera = document.querySelectorAll('thead>tr>th')
    partesCabecera.forEach(encabezado => {
        encabezado.style.border = 'solid #541530 4px';
        encabezado.style.backgroundColor = '#4465B8';
    })
}

function mostrarFormulario() {
    let botonCrearEncuestas = document.querySelector('.creadorEncuestas');
    let elemento = document.querySelector('.formEncuesta');
    botonCrearEncuestas.addEventListener('click', () => {
        elemento.classList.toggle('hidden');
        elemento.classList.add('flex');
        botonCrearEncuestas.textContent = 'Cancelar Encuesta';
        botonCrearEncuestas.style.backgroundColor = '#4465B8';

        if (elemento.classList.contains('hidden')) {
            botonCrearEncuestas.textContent = 'Crear Encuesta';
            botonCrearEncuestas.style.backgroundColor = '#541530';
            elemento.classList.remove('flex');
            botonesE.classList.remove('hidden');
        }
    })
}
function crearInputs() {
    let boton = document.querySelector('.crearInputs');
    boton.addEventListener('click', function () {
        let input = document.createElement('input');
        input.setAttribute('type', 'text');
        input.setAttribute('placeholder', 'Opcion de encuesta');
        input.setAttribute('name', 'opciones_votos[]');
        input.classList.add('opciones_votos');
        let contenedor = document.querySelector('.opcionesContainer');
        contenedor.appendChild(input);
    });
}

function seleccionadosEnFormulario() {
    let votoAll = document.querySelector('#allParticipantes');
    let one2many = document.querySelector('#one2Many');
    let divO = document.querySelector('.seleccionados');
    let participantesSeleccionados = document.querySelectorAll('.individual');

    one2many.addEventListener('click', () => {
        divO.classList.toggle('hidden');
        votoAll.checked = false;
        votoAll.removeAttribute('required');
        participantesSeleccionados.forEach(participante => {
            participante.setAttribute('required', 'true');
            participante.addEventListener('click', function () {
                participantesSeleccionados.forEach(participante => participante.removeAttribute('required'));
            })
        });
    });

    votoAll.addEventListener('click', () => {
        one2many.checked = false;
        if (!divO.classList.contains('hidden')) {
            divO.classList.add('hidden');
        }
        votoAll.setAttribute('required', 'true');
        participantesSeleccionados.forEach(participante => participante.checked = false);
    })
}
function botones() {
    let botones = document.querySelectorAll('.btn-info');
    botones[0] && botones.forEach(boton => boton.addEventListener('click', pedirDatos))
}

async function pedirDatos(evt) {
    let tipo = evt.currentTarget.value;
    let id=evt.currentTarget.parentElement.parentElement.lastElementChild.value;
    try {
        let response = await fetch('/pedirDatos?tipo=' + tipo+'&id='+id);
        if (!response.ok) { throw new Error('Error al añadir el evento'); }
        let data = await response.json();
        pintar(data,tipo);
    } catch (error) {
        console.error('Error:', error);
    }
}

let contenedorPadre;
function pintar(datos, tipo){
    contenedorPadre=document.querySelector('.muestraInfo');
    
    switch (tipo){
        case 'participantes':
            pintarParticipantes(datos,tipo);
            break;
        case 'descripcion':
            pintarDescripcion(datos,tipo);
            break;
    }
}
function pintarParticipantes(datos,tipo){
    contenedorPadre.parentElement.parentElement.classList.remove('hidden');
    let contenido=`
    <h2 class="font-extralight text-3xl mt-2">${tipo.toUpperCase()}</h2>
    <ul class="w-[80%] h-[90%] flex flex-col justify-around items-center flex-wrap all-li:w-[25%] all-li:bg-colorComplem all-li:text-center">`
    datos=JSON.parse(datos);
    for(let i=0;i<datos.length;i++){
        contenido+=`<li>${datos[i]}</li>`;
    };
        contenido+=`</ul>`;
    contenedorPadre.innerHTML=contenido;
    cerrar();
}

function pintarDescripcion(datos,tipo){
    contenedorPadre.parentElement.parentElement.classList.remove('hidden');
    let contenido=`
    <h2 class="font-extralight text-3xl mt-2">${tipo.toUpperCase()}</h2>
    <div class="scroll-y-auto p-10"> 
    <p>${datos}</p>
    </div>
    `;
    contenedorPadre.innerHTML=contenido;
    cerrar();
}

function cerrar(){
    let botonCerrar=document.querySelector('.btn-cerrar');
    botonCerrar.addEventListener('click',()=>{
        contenedorPadre.parentElement.parentElement.classList.add('hidden');
    })
}

function pintarElementos(ele, attr, contenido) {
    const elemento = document.createElement(ele);
    for (let clave in attr) {
        elemento.setAttribute(clave, attr[clave]);
    }
    if (typeof contenido == 'string') {
        elemento.appendChild(document.createTextNode(contenido));
    }
    if (contenido instanceof HTMLElement) {
        elemento.appendChild(contenido);
    }
    if (Array.isArray(contenido)) {
        contenido.forEach(conte => elemento.appendChild(conte));
    }
    return elemento;
}
