console.log('JS iniciado');
document.addEventListener('DOMContentLoaded', iniciar);
var inputs
var divIniciar
var divCrear
var divMostrar
function iniciar(){
    inputs = document.querySelectorAll('input');
    console.log(inputs)
    inputs.forEach(e=>e.addEventListener('focus',inputFocus));
    inputs.forEach(e=>e.addEventListener('blur',inputBlur));
    divIniciar = document.querySelector('.inicio');
    divCrear = document.querySelector('.crear');
    divMostrar = document.querySelector('.mostrar');
    var cerrar = document.querySelectorAll('.cerrarSesion');
    cerrar[0] && cerrar[0].addEventListener('click',cerrarSesion);
    document.querySelectorAll('.ojo').forEach(e=>e.addEventListener('click',contraseña));
    document.querySelectorAll('.botonIniciar').forEach(e=>e.addEventListener('click',cambiar));
    document.querySelectorAll('.botonCrear').forEach(e=>e.addEventListener('click',cambiar))
    document.querySelectorAll('.error').forEach(e=>{if(e.innerText)erroneo(e.parentElement)})

}
function erroneo(form){
    divMostrar.classList.remove('mostrar');
    divMostrar = form.parentElement;
    divMostrar.classList.add('mostrar');
}
function cambiar(evt){
    evt.preventDefault();
    divMostrar.classList.remove('mostrar')
    if(evt.target.className == 'botonIniciar'){
        divIniciar.classList.add('mostrar');
        divMostrar = divIniciar;
    }else{
        divCrear.classList.add('mostrar');
        divMostrar = divCrear;
    }
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
    window.location.href = '/perfil/cerrar';
}

function inputFocus(evt){
    evt.target.nextElementSibling.style.borderStyle = 'none';
    evt.target.parentElement.style.border = '2px solid white'
}
function inputBlur(evt){
    evt.target.nextElementSibling.style.borderStyle = 'solid';
    evt.target.parentElement.style.border = 'none'
}