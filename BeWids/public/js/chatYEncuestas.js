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
    mostrarMenu();
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

function mostrarMenu(){
    let botonAbrir=document.querySelector('.open');
    let botonCerrar=document.querySelector('.close');
    let contenedor=document.querySelector('.lista');
    botonAbrir.addEventListener('click',()=>{
        contenedor.classList.add('flex');
        contenedor.classList.remove('hidden');
        botonCerrar.classList.remove('hidden');
        botonAbrir.classList.add('hidden');
        botonAbrir.parentElement.classList.remove('items-start');
    });
    botonCerrar.addEventListener('click',()=>{
        contenedor.classList.remove('flex');
        contenedor.classList.add('hidden');
        botonCerrar.classList.add('hidden');
        botonAbrir.classList.remove('hidden');
        botonAbrir.parentElement.classList.add('items-start');
    });
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
    
    if(window.innerWidth>400){
        scrollEncuestas();
    }
    coloresTablaEncuestas();
    mostrarFormulario();
    crearInputs();
    seleccionadosEnFormulario();
    botones();
    cambiarEncuestas();
    manejarVotacion();
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
        contenedor.scrollTop=contenedor.scrollHeight;
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
                participante.addEventListener('click', function () {
                    participante.setAttribute('required', 'true');
                })
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
let contenedorPadrePorcentaje;
let contenedorVotos;
function pintar(datos, tipo){
    contenedorPadre=document.querySelector('.muestraInfo');
    contenedorPadrePorcentaje=document.querySelector('.muestraInfo2');
    
    switch (tipo){
        case 'participantes':
            pintarParticipantes(datos,tipo);
            break;
        case 'descripcion':
            pintarDescripcion(datos,tipo);
            break;
        case 'opciones_votos':
            pintarVotos(datos);
            break;
    }
}
function pintarParticipantes(datos,tipo){
    contenedorPadre.parentElement.parentElement.classList.remove('hidden');
    contenedorPadrePorcentaje.parentElement.parentElement.classList.add('hidden');
    
    let contenido=`
    <h2 class="font-extralight text-3xl mt-2">${tipo.toUpperCase()}</h2>
    <ul class="w-[80%] h-[90%] flex flex-col justify-around items-center all-li::w-[15%] all-li:text-center">`
    datos=JSON.parse(datos);
    for(let i=0;i<datos.length;i++){
        contenido+=`<li class="bg-colorComplem p-2 rounded-2xl my-2">${datos[i]}</li>`;
    };
        contenido+=`</ul>`;
    contenedorPadre.innerHTML=contenido;
    cerrar();
}

function pintarDescripcion(datos,tipo){
    contenedorPadre.parentElement.parentElement.classList.remove('hidden');
    let contenido=`
    <h2 class="font-extralight text-white text-3xl mt-2">${tipo.toUpperCase()}</h2>
    <div class="scroll-y-auto p-10 hover:bg-colorComplem hover:text-white"> 
    <p>${datos}</p>
    </div>
    `;
    contenedorPadre.innerHTML=contenido;
    cerrar();
}
function pintarVotos(datos){
    contenedorPadrePorcentaje.parentElement.parentElement.classList.remove('hidden');
    contenedorPadre.parentElement.parentElement.classList.add('hidden');
    datos=JSON.parse(datos);
    
    let contenido=`
    <h2 class="absolute top-0 bg-colorComplem p-2 font-light">PORCENTAJES</h2>
    `;
    datos.forEach(dato=>{
        contenido+=`
        <div class="my-2 w-[80%] h-20 pb-5text-center">
            <h3 class=" p-2">${dato.opcion.toUpperCase()}: <span>${dato.porcentaje}</span></h3>
            <div class="bg-colorComplem w-[100%] h-[40%] rounded-3xl">
                <div class="bg-colorDetalles h-full  rounded-3xl block" style="width:${dato.porcentaje};"></div>
            </div>
        </div>
        `;
    });
    contenedorPadrePorcentaje.innerHTML=contenido;
    cerrar();
}


function cerrar(){
    let botonCerrar=document.querySelector('.btn-cerrar');
    let botonCerrar2=document.querySelector('.btn-cerrar2');
    botonCerrar.addEventListener('click',()=>{
        contenedorPadre.parentElement.parentElement.classList.add('hidden');
        contenedorPadrePorcentaje.parentElement.parentElement.classList.add('hidden');
    })
    botonCerrar2.addEventListener('click',()=>{
        contenedorPadre.parentElement.parentElement.classList.add('hidden');
        contenedorPadrePorcentaje.parentElement.parentElement.classList.add('hidden');
    })
}

function manejarVotacion(){
    let botonVotar=document.querySelectorAll('.mostrarVotacion');
    botonVotar.forEach(voto=>voto.addEventListener('click',pedirVotar));
}

async function pedirVotar(evt){
    let tipo = evt.currentTarget.value;
    let id=evt.currentTarget.parentElement.parentElement.lastElementChild.value;
    try {
        let response = await fetch('/pedirDatos?tipo=opciones_votos&id='+id);
        if (!response.ok) { throw new Error('Error al añadir el evento'); }
        let data = await response.json();
        pintarVotador(data,tipo);
    } catch (error) {
        console.error('Error:', error);
    }
    
}


function pintarVotador(datos,tipo){
    datos=JSON.parse(datos);
    contenedorVotos=document.querySelector('.opcionesVotacion');
    contenedorVotos.parentElement.parentElement.classList.remove('hidden');
    let contenido=``;
    datos.forEach(dato=>{
        contenido+=`
        <button class="elementoVotable w-full bg-colorComplem hover:bg-colorDetalles hover:text-white my-4 rounded-3xl" value="votacion">
                                <h2>${dato.opcion}</h2>
                                <h4>Votado por: ${dato.porcentaje}</h4>
                                <h6 class="hidden">${dato.id_encuesta}</h6>
        </button>
        `;
        contenedorVotos.innerHTML=contenido;
    });
    escucharVoto();
    cerrarVotacion();
}
function cerrarVotacion(){
    let botonCerrar3=document.querySelector('.btn-cerrar3');
    botonCerrar3.addEventListener('click',()=>{
        contenedorVotos.parentElement.parentElement.classList.add('hidden');
    })
}

function escucharVoto(){
    let botonesVoto=document.querySelectorAll('.elementoVotable');
    botonesVoto.forEach(boton=>boton.addEventListener('click',guardarVoto))
}

async function guardarVoto(evt){
    let valor=evt.currentTarget.querySelector('h2').textContent;
    let id=evt.currentTarget.querySelector('h6').textContent;
    try {
        let response = await fetch('/updateEncuesta?seleccion='+valor+'&id='+id);
        if (!response.ok) { throw new Error('Error al añadir el evento'); }
        let data = await response.json();
        pintarInfoEncuesta(data);
    } catch (error) {
        console.log(error);
    }

}


function pintarInfoEncuesta(datos){
    let respuesta=datos
    let contenido=`
        <div >
            <h2>${respuesta}</h2>
        </div>
    `;
    let elemento=document.querySelector('.containerMensaje');
    elemento.innerHTML=contenido;
    elemento.classList.remove('hidden');
    setTimeout(()=>{
        elemento.classList.add('hidden')
        elemento.parentElement.parentElement.classList.add('hidden')
        if(respuesta=='Tu voto a sido guardado correctamente'){
            ruta=window.location.pathname;
            window.location.href = ruta;
        }
    },3000);
}

 function  cambiarEncuestas(){
    let boton=document.querySelector('.cambioEncuestas');
    let tablaActuales=document.querySelector('.tablaNoFinalizados');
    let tablaFinalizados=document.querySelector('.tablaFinalizados');
    boton.addEventListener('click',function(){
        tablaActuales.classList.toggle('hidden');
        tablaActuales.classList.toggle('md:hidden');
        tablaFinalizados.classList.toggle('hidden');
        tablaFinalizados.classList.toggle('md:hidden');
        
        if (tablaActuales.classList.contains('hidden')) {
            boton.classList.add('bg-colorComplem');
            boton.classList.remove('bg-colorBarra2');
            boton.textContent = 'Encuestas activas';
        } else {
            boton.classList.add('bg-colorBarra2');
            boton.classList.remove('bg-colorComplem');
            boton.textContent = 'Encuestas finalizadas';
        }

    })

}