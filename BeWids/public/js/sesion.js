console.log('JS iniciado');
document.addEventListener('DOMContentLoaded', iniciar);
let inputs
let divIniciar
let divCrear
let divMostrar
let inputUsuario
let inputCorreoI
let inputCorreoC
let inputCorreo2C
let inputPassI
let inputPassC
let inputPass2C
let errorCrear
let errorIniciar
let error
let idTemp;
let evtTemp;

function iniciar(){
    inputs = document.querySelectorAll('input');
    inputUsuario = document.querySelector('.crear input[name="name"]')
    inputCorreoC = document.querySelector('.crear input[name="email"]')
    inputCorreo2C = document.querySelector('.crear input[name="email2"]')
    inputPassC = document.querySelector('.crear input[name="password"]')
    inputPass2C = document.querySelector('.crear input[name="pass2"]')
    inputPassI = document.querySelector('.inicio input[name="password"]')
    inputCorreoI = document.querySelector('.inicio input[name="email"]')
    errorCrear = document.querySelector('.crear .error');
    errorIniciar = document.querySelector('.inicio .error');




    console.log(inputs)
    inputs.forEach(e=>e.addEventListener('focus',inputFocus));
    inputs.forEach(e=>e.addEventListener('blur',inputBlur));
    divIniciar = document.querySelector('.inicio');
    divCrear = document.querySelector('.crear');
    divMostrar = document.querySelector('.mostrar');
    let cerrar = document.querySelectorAll('.cerrarSesion');
    cerrar[0] && cerrar[0].addEventListener('click',cerrarSesion);
    document.querySelectorAll('.ojo').forEach(e=>e.addEventListener('click',contraseña));
    document.querySelectorAll('.botonIniciar').forEach(e=>e.addEventListener('click',cambiar));
    document.querySelectorAll('.botonCrear').forEach(e=>e.addEventListener('click',cambiar))
    document.querySelectorAll('.error').forEach(e=>{if(e.innerText)erroneo(e.parentElement)})
    document.querySelectorAll('.pass').forEach(e=>e.addEventListener('input',validarPass));
    forms = document.querySelectorAll('form');
    forms[0].addEventListener('submit',validarIniciar);
    forms[1].addEventListener('submit',validarCrear);


}

function validarCrear(evt){
    evt.preventDefault();
    error = '';
    if(validarCorreo()){
        if(validarContrasenia())
            evt.target.submit();
    }
    errorCrear.innerText = error;
}
function validarIniciar(evt){
    evt.preventDefault();
    error = '';
    if( ! /^[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*@[a-zA-Z]+\.[a-zA-Z]{2,3}$/.test(inputCorreoI.value))
        error = 'Formato correo no válido'
    if(! /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(inputPassI.value))
        error = (error && error + ' y formato contraseña no válido') || 'Formato contraseña no valido'
    if(error)
        errorIniciar.innerText = error;
    else
        evt.target.submit();
}
function validarCorreo(){
    if(! /^[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*@[a-zA-Z]+\.[a-zA-Z]{2,3}$/.test(inputCorreoC.value)){
        error = 'Formato correo no valido';
        return false
    }
    if(inputCorreoC.value != inputCorreo2C.value){
        error = 'Los correos no coinciden';
        return false
    }
    return true 
}
function validarContrasenia(){
    if(! /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(inputPassC.value)){
        error = 'Formato contraseña no valido';
        return false
    }
    if(inputPassC.value != inputPass2C.value){
        error = 'Las contraseñas no coinciden';
        return false
    }
    return true 
}

function validarPass(evt){
    let valor = evt.target.value;
    if(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(valor)){
        evt.target.parentElement.style.borderColor = 'green';
        evt.target.nextElementSibling.style.borderColor = 'green';
    }else{
        evt.target.parentElement.style.borderColor = 'red';
        evt.target.nextElementSibling.style.borderColor = 'red';
    }
    idTemp && evtTemp == evt.target && clearTimeout(idTemp)
    idTemp = setTimeout(()=>{
        evt.target.parentElement.style.borderColor = 'white';
        evt.target.nextElementSibling.style.borderColor = 'white';
        idTemp = null;
    },3000)
    evtTemp = evt.target;
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
    let input = evt.target.parentElement.previousElementSibling;
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
    window.location.href = '/cuenta/cerrar';
}

function inputFocus(evt){
    if(evt.target.type == 'submit' || evt.target.type == 'checkbox')return
    evt.target.nextElementSibling.style.borderStyle = 'none';
    evt.target.parentElement.style.border = '2px solid white'
}
function inputBlur(evt){
    if(evt.target.type == 'submit' || evt.target.type == 'checkbox')return
    evt.target.nextElementSibling.style.borderStyle = 'solid';
    evt.target.parentElement.style.border = 'none'
}