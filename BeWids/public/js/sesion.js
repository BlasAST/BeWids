console.log('JS iniciado');
document.addEventListener('DOMContentLoaded', iniciar);
var inputs
function iniciar(){
    inputs = document.querySelectorAll('input');
    console.log(inputs)
    inputs.forEach(e=>e.addEventListener('focus',inputFocus));
    inputs.forEach(e=>e.addEventListener('blur',inputBlur));
    var cerrar = document.querySelectorAll('.cerrar');
    cerrar[0] && cerrar[0].addEventListener('click',cerrarSesion);
    document.querySelectorAll('.ojo').forEach(e=>e.addEventListener('click',contraseña));
}

function contraseña(evt){
    var input = evt.target.parentElement.previousElementSibling;
    if(input.type == 'password'){
        input.type = 'text';
        evt.target.style.backgroundImage = "url('../imagenes/imagenesSesion/ojoC.png')";
        input.select();
    }else{
        input.type = 'password';
        evt.target.style.backgroundImage = "url('../imagenes/imagenesSesion/ojoA.png')";
        input.select();
    }
}

function cerrarSesion(){
    window.location.href = 'logOut';
}

function inputFocus(evt){
    evt.target.nextElementSibling.style.borderStyle = 'none';
    evt.target.parentElement.style.border = '2px solid white'
}
function inputBlur(evt){
    evt.target.nextElementSibling.style.borderStyle = 'solid';
    evt.target.parentElement.style.border = 'none'
}