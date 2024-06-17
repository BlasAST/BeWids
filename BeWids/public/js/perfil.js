addEventListener('DOMContentLoaded', iniciar)

let contParticipantes = 1;
let formPortal;
let participantes = [];

function iniciar() {
    //añadimos eventListeners y damos valor a variables globales
    let perfil = document.querySelector('.perfil');
    let inputsPerfil = perfil.querySelectorAll('.formPerfil input');
    inputsPerfil.forEach(inpu => inpu.style.display = 'none')
    let sesiones = document.querySelector('.sesiones');
    let botonP = document.querySelector('.bperfil')
    let botonS = document.querySelector('.bsesiones');
    document.querySelector('.crearPortal').addEventListener('click',mostrarForm);
    document.querySelector('.sesiones form button').addEventListener('click',aniadirParticipante);
    formPortal = document.querySelector('.formPortal');
    formPortal.addEventListener('submit',crearPortal);
    document.querySelectorAll('.portal').forEach(e=>e.addEventListener('click',enviarPortal));
    participantes.push(document.querySelector('.nombreP'));

    move(botonS, botonP);
    moveSettings();
    info(botonP,perfil, inputsPerfil);
}
function crearPortal(evt){
    //validamos los datos antes de crear el portal
    //comprobamos que no se llamen igual algun participante
    evt.preventDefault()
    let usados = [];
    let correcto = true;
    participantes.forEach(e=>{
        if(usados.includes(e.value))
            correcto = false
        else
            usados.push(e.value);            
    })
    if(correcto)
        evt.target.submit()
    else
        evt.target.lastElementChild.previousElementSibling.style.display = 'flex';
}

function enviarPortal(evt){
    //abrimos el portal
    evt.target.firstElementChild.submit()
}

function mostrarForm(evt){
    //variamos la visibilidad del formulario de creación de portal
    if(evt.target.innerText == 'Crear Portal'){
        formPortal.style.display = 'flex';
        evt.target.innerText = 'Cancelar';
    }else{
        formPortal.style.display = 'none';
        evt.target.innerText = 'Crear Portal';
    }
}

function move(botonS, botonP) {
    //cambiamos el contenido de la página entre info perfil y portales
    let sesiones = document.querySelector('.sesiones');
    let perfil = document.querySelector('.perfil')
    botonS.addEventListener('click', () => {
        sesiones.style.display = 'flex';
        perfil.style.display = 'none';
        botonP.style.display = 'block'
        botonS.style.display = 'none';
    })
    botonP.addEventListener('click', () => {
        sesiones.style.display = 'none';
        perfil.style.display = 'block';
        botonP.style.display = 'none'
        botonS.style.display = 'block';
    })
}

function moveSettings() {
    //abrir ajustes de perfil
    let bajustes = document.querySelector('.bajustes');
    let ajustes = document.querySelector('.ajustes');
    bajustes.addEventListener('click', () => {
        ajustes.classList.toggle('mostrar');
    })
}

function info(botonP ,perfil, inputs) {
    //mostrar inputs para modificar info usuario
    let boton = document.querySelector('.editar');
    boton.addEventListener('click', () => {
    boton.parentElement.parentElement.classList.remove('mostrar');
    let formulario=document.querySelector('form');
    let boto;
    if(!perfil.querySelector('.guardar')){
        boto=document.createElement('button');
        let texto=document.createTextNode('Guardar');
        boto.appendChild(texto);
        boto.type='submit';
        boto.className='guardar';
        perfil.appendChild(boto);
    }
    inputs.forEach(input=>input.style.display='block');
    boto.addEventListener('click',function(evt){guardarInformacion(evt,inputs)});
    })
  
}


function guardarInformacion(evt,inputs){
    //validación de datos antes de cambiar info usuario
    evt.preventDefault();
    let formularioVali = true;
    // for (let input of inputs) {
    //     if (contieneCaracteresEspeciales(input.value)) {
    //         alert('Uno o más campos contienen caracteres no permitidos');
    //         formularioVali = false;
    //         break;
    //     }
    // }
    if (formularioVali) {
        console.log('Datos correctos');
        evt.target.form.submit();
    }
}
function contieneCaracteresEspeciales(cadena) {
    var expresionRegular = /[!@#$%^&*()+\=\[\]{};':"\\|,<>\/?]/;
    return expresionRegular.test(cadena);
}

function aniadirParticipante(evt){
    //crear input para añadir mas participantes
    let [label,input] = crearInput();
    evt.target.parentElement.insertBefore(label,evt.target.previousElementSibling);
    evt.target.parentElement.insertBefore(input,evt.target.previousElementSibling)
    participantes.push(input);
}
function crearInput(){
    let label = document.createElement('label');
    label.appendChild(document.createTextNode('Añadir participante nº'+contParticipantes++))
    label.setAttribute('for','participantes[]');
    let nodo = document.createElement('input');
    nodo.setAttribute('name','participantes[]');
    nodo.setAttribute('type','text');
    return [label,nodo];
}