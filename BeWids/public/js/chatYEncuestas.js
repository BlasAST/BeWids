document.addEventListener('DOMContentLoaded',iniciar);
function iniciar(){
    abrirApartadosChat();
    rechazarChat();
    abrirEncuestaDefault();
    scrollEncuestas();
    coloresTablaEncuestas();
    mostrarFormulario();
}

// ELEMENTOS DE EL CHAT

let botones;
let menus;
function abrirApartadosChat(){
    botones=document.querySelectorAll('.seleccionesChat > button:not(:first-child)');
    menus=document.querySelectorAll('.seleccionesChat > ul');
    botones.forEach(boton=>boton.addEventListener('click',abrirC));
    
}
function abrirC(evt){
    menus.forEach(menu=>{
        menu.classList.contains(evt.currentTarget.id)?menu.classList.toggle('hidden'):'';
    })
}
let seleccionado;
function rechazarChat(){
    seleccionado=document.querySelector('select');
    seleccionado.addEventListener('change',cierreSeleccionado)
    
}
function cierreSeleccionado(){
    let botonRechazo;

    setTimeout(()=>{
        botonRechazo=document.querySelector('.cancelar')
        botonRechazo.addEventListener('click',()=>{
           seleccionado.value=seleccionado.options[0].value;
        })
    },100)
    
}

// ELEMENTOS ENCUESTAS
let botonesE;
let partesCabecera;

function abrirEncuestaDefault(){
    // let valor=window.location.pathname;
    let chat=document.querySelector('#chat');
    let encuesta=document.querySelector('#encuestas');
    if(valor=='/encuestas'){
    chat.style.display='none';
    encuesta.style.display='flex';

    let categorias=document.querySelectorAll('.categoria > div > span')
    categorias[0].classList.remove('selected');
    categorias[0].classList.remove('border-b-4');
    categorias[0].classList.remove('border-white');
    
    categorias[1].classList.add('selected')
    categorias[1].classList.add('border-b-4')
    categorias[1].classList.add('border-white')
    }
    if (chat.classList.contains('selected')){
        chat.style.display='flex';
        encuesta.style.display='none';
    }
}

function scrollEncuestas(){
    botonesE=document.querySelector('.botonesEncuestas');
    let listadoEncuestas=document.querySelector('.listadoEncuestas');
    let ultimoScroll=0;

    listadoEncuestas.addEventListener('scroll',function(){
        let topeScroll=listadoEncuestas.scrollTop;
        if(topeScroll>ultimoScroll){
            botonesE.classList.add('hidden')
        }else{
            botonesE.classList.remove('hidden')
        }
        ultimoScroll=topeScroll;
    });

}
function coloresTablaEncuestas(){

    partesCabecera=document.querySelectorAll('thead>tr>th')
    partesCabecera.forEach(encabezado=>{
        encabezado.style.border='solid #541530 4px';
        encabezado.style.backgroundColor='#4465B8';
    })
    
}

function mostrarFormulario(){
    let botonCrearEncuestas=document.querySelector('.creadorEncuestas');
    let elemento=document.querySelector('.formEncuesta');
    botonCrearEncuestas.addEventListener('click',()=>{
        elemento.classList.toggle('hidden');
        elemento.classList.add('flex');
        botonCrearEncuestas.textContent='Cancelar Encuesta';
        botonCrearEncuestas.style.backgroundColor='#4465B8';

        if(elemento.classList.contains('hidden')){
            botonCrearEncuestas.textContent='Crear Encuesta';
            botonCrearEncuestas.style.backgroundColor='#541530';
            elemento.classList.remove('flex');
            botonesE.classList.remove('hidden');
        }
    })
}

