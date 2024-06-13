addEventListener('DOMContentLoaded', () => {
    iniciar();
})

let contParticipantes = 1;
let formPortal;
let participantes = [];

function iniciar() {
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
    evt.target.firstElementChild.submit()
}

function mostrarForm(evt){
    if(evt.target.innerText == 'Crear Portal'){
        formPortal.style.display = 'flex';
        evt.target.innerText = 'Cancelar';
    }else{
        formPortal.style.display = 'none';
        evt.target.innerText = 'Crear Portal';
    }
}

function move(botonS, botonP) {

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
    let bajustes = document.querySelector('.bajustes');
    let ajustes = document.querySelector('.ajustes');
    bajustes.addEventListener('click', () => {
        ajustes.classList.toggle('mostrar');
    })
}

function info(botonP ,perfil, inputs) {
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
    evt.preventDefault();
    let formularioVali = true;
    for (let input of inputs) {
        if (contieneCaracteresEspeciales(input.value)) {
            alert('Uno o más campos contienen caracteres no permitidos');
            formularioVali = false;
            break;
        }
    }
    if (formularioVali) {
        console.log('Datos correctos');
        evt.target.form.submit();
    }
}
function contieneCaracteresEspeciales(cadena) {
    var expresionRegular = /[!@#$%^&*()+\=\[\]{};':"\\|,.<>\/?]/;
    return expresionRegular.test(cadena);
}

function aniadirParticipante(evt){
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