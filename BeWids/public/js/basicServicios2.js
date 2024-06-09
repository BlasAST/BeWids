document.addEventListener('DOMContentLoaded',iniciar);

let seccion;
let selected;
let secciones;

function iniciar(){
    // Modificado para que si se usa livewire busque en generico
    secciones = document.querySelectorAll('main > section, .generico > div');
    document.querySelectorAll('header > div > div').forEach(e=>e.addEventListener('click', categoria));
    seccion = document.querySelector('.mostrar');
    selected = document.querySelector('.selected')
    document.querySelector('.btnVolver').addEventListener('click', volverPortal);
}

function volverPortal(){
    window.location.href='/portal';
}
function categoria(evt){
    if(window.location.pathname=='/calendario')return
    seccion.classList.remove('mostrar');
    seccion.style.display = 'none';
    selected.classList.remove('selected');
    selected.style.border='0';
    secciones.forEach(e=>{
        if(e.id + "Cat" == evt.currentTarget.id){
            e.classList.add('mostrar');
            seccion = e;
        }
    });
    evt.currentTarget.firstElementChild.classList.add('selected');
    selected = evt.currentTarget.firstElementChild
    seccion.style.display='flex';
    selected.style.borderBottom ='4px solid white';
}