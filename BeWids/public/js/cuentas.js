document.addEventListener('DOMContentLoaded',iniciar);

function iniciar(){
    let botones = document.querySelectorAll('.reembolso button');
    botones[0] && botones.forEach(e=>e.addEventListener('click',reembolsar));
    let botonesNot = document.querySelectorAll('button[data-action]');
    botonesNot[0]&& botonesNot.forEach(e=>e.addEventListener('click', responderNotificacion));
}

function reembolsar(evt){
    !evt.target.nextElementSibling && evt.target.firstElementChild.submit();
}

function responderNotificacion(evt){
    let form = evt.target.parentElement.lastElementChild;
    form.lastElementChild.value = evt.target.getAttribute('data-action');
    form.submit();
    
}