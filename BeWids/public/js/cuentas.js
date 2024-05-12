document.addEventListener('DOMContentLoaded',iniciar);

let cat;
let selected;
let secciones;

function iniciar(){
    
    secciones = document.querySelectorAll('main > div:not(:first-child)');
    document.querySelectorAll('.categorias span').forEach(e=>e.addEventListener('click', categoria));
    cat = document.querySelector('.mostrar');
    selected = document.querySelector('.selected')
    

}
function categoria(evt){
    cat.classList.remove('mostrar');
    selected.classList.remove('selected');
    secciones.forEach(e=>{
        if(e.classList.contains(evt.currentTarget.id)){
            e.classList.add('mostrar');
            cat = e;
        }
    });
    evt.currentTarget.firstElementChild.classList.add('selected');
    selected = evt.currentTarget.firstElementChild
}
