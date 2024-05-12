document.addEventListener('DOMContentLoaded',iniciar);

function iniciar(){
    document.querySelector('.btnGastos').addEventListener('click',irGastos);
}

function irGastos(){
    window.location.href = '/contabilidad';
}