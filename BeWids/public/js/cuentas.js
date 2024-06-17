document.addEventListener('DOMContentLoaded',iniciar);
window.addEventListener("beforeunload",salir);

let contenedores;
let inputsG
let checkBoxes
let positivos;
let negativos;


function iniciar(){
    //añadimos eventListeners y variables globales
    let botones = document.querySelectorAll('.reembolso button');
    botones[0] && botones.forEach(e=>e.addEventListener('click',reembolsar));
    let botonesNot = document.querySelectorAll('button[data-action]');
    botonesNot[0]&& botonesNot.forEach(e=>e.addEventListener('click', responderNotificacion));
    contenedores = document.querySelectorAll('section');
    let gastos = document.querySelectorAll('.gasto');
    gastos[0] && gastos.forEach(e=>e.addEventListener('click', abrirGasto));
    let form = document.querySelector('.formGasto');
    form && form.addEventListener('submit', crearGasto)
    inputsG = document.querySelectorAll('.inputG');
    checkBoxes = document.querySelectorAll('.checkBoxes');
    positivos = document.querySelectorAll('.positivo');
    negativos = document.querySelectorAll('.negativo');
    window.addEventListener('resize',grafResponsive);
    grafResponsive();

}

function grafResponsive(){
    if (window.innerWidth >= 768) {
        [...positivos].forEach(e=>{
            e.style.backgroundImage = "linear-gradient(to top, #4465B8,#2B2C30 "+e.dataset.porcentaje+"%, #2B2C30 100%)";
            e.firstElementChild.style.bottom=e.dataset.porcentaje+ "%";
            e.firstElementChild.style.left="50%";


        });
        [...negativos].forEach(e=>{
            e.style.backgroundImage = "linear-gradient(to top, #D63865,#2B2C30 "+e.dataset.porcentaje+"%, #2B2C30 100%)"
            e.firstElementChild.style.bottom=e.dataset.porcentaje+ "%";
            e.firstElementChild.style.left="50%";


        });
    } else {
        [...positivos].forEach(e=>{
            e.style.backgroundImage = "linear-gradient(to right, #4465B8,#2B2C30 "+e.dataset.porcentaje+"%, #2B2C30 100%)"
            e.firstElementChild.style.left=Number(e.dataset.porcentaje) - 5 + "%";
            e.firstElementChild.style.bottom="50%";

            

        });
        [...negativos].forEach(e=>{
            e.style.backgroundImage = "linear-gradient(to right, #D63865,#2B2C30 "+e.dataset.porcentaje+"%, #2B2C30 100%)"
            e.firstElementChild.style.left=Number(e.dataset.porcentaje) - 5 + "%";
            e.firstElementChild.style.bottom="50%";


        });
    }
}

function crearGasto(evt){
    //validación para añadir gasto
    evt.preventDefault();
    let inputsRellenos = [...inputsG].reduce((acc,e)=>{
        return (acc && e.value.trim())
    },true)
    let checkBoxRellenos = [...checkBoxes].reduce((acc,e)=>{
        return (acc || e.checked)
    },false)
    if(inputsRellenos && checkBoxRellenos){
        evt.target.submit();
    }else{
        evt.target.lastElementChild.classList.remove('hidden');
        evt.target.lastElementChild.classList.add('flex');

    }
       

}

function abrirGasto(evt){
    //mostrar más info del gasto
    let extra = evt.currentTarget.lastElementChild
    if(extra.classList.contains('hidden')){
        extra.classList.remove('hidden');
        extra.classList.add('flex');
        evt.currentTarget.classList.remove('bg-colorCabera')
        evt.currentTarget.classList.add('bg-colorComplem')
    }else{
        extra.classList.remove('flex');
        extra.classList.add('hidden');
        evt.currentTarget.classList.remove('bg-colorComplem')
        evt.currentTarget.classList.add('bg-colorCabera')

    }
}

function reembolsar(evt){
    //solicitar reembolso siempre y cuando no esté ya solicitado
    !evt.target.nextElementSibling && evt.target.firstElementChild.submit();
}

function responderNotificacion(evt){
    let form = evt.target.parentElement.lastElementChild;
    form.lastElementChild.value = evt.target.getAttribute('data-action');
    form.submit();
    
}

async function salir(){
    //guardar pestaña en la que se sale para que al entrar se abra en esa
    let actual;
    contenedores.forEach(e=>{
      if(e.classList.contains('mostrar'))
        actual = e.id;
    });
    let referencia = window.location.href;
    referencia = referencia.replaceAll('http://127.0.0.1:8000/','');

    if(actual){
      const formData = new FormData();
      formData.append('actual', actual);
      formData.append('pagina', referencia);
      formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));  // Añade el token CSRF

      navigator.sendBeacon('/salir', formData);

    }
    
  }