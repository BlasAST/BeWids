document.addEventListener('DOMContentLoaded',iniciar);

let botones;
let menus;
function iniciar(){
    abrirApartadosChat();
    rechazarChat();
    abrirEncuestaDefault();
}

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

function abrirEncuestaDefault(){
    let valor=window.location.pathname;
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
    
}

