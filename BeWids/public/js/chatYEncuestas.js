document.addEventListener('DOMContentLoaded',iniciar);

let botones;
let menus;
function iniciar(){
    abrirApartadosChat();
    rechazarChat();
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

