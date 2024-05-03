addEventListener('DOMContentLoaded', () => {
    iniciar();
})

function iniciar() {
    let perfil = document.querySelector('.perfil');
    let inputsPerfil = perfil.querySelectorAll('input');
    inputsPerfil.forEach(inpu => inpu.style.display = 'none')
    let sesiones = document.querySelector('.sesiones');
    let botonP = document.querySelector('.bperfil')
    let botonS = document.querySelector('.bsesiones');

    move(botonS, botonP);
    moveSettings();
    info(botonP, perfil, inputsPerfil);
}


function move(botonS, botonP) {

    let sesiones = document.querySelector('.sesiones');
    let perfil = document.querySelector('.perfil')
    botonS.addEventListener('click', () => {
        sesiones.style.display = 'block';
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

function info(botonP, perfil, inputs) {
    let boton = document.querySelector('.editar');
    boton.addEventListener('click', () => {
        boton.parentElement.parentElement.classList.remove('mostrar');
        let padre = document.createElement('form');
        padre.appendChild(perfil);
        botonP.parentElement.insertBefore(padre, botonP);
        inputs.forEach(input => input.style.display = 'block');
        let enviar = document.createElement('button');
        let tex = document.createTextNode('Guardar');
        enviar.appendChild(tex);
        padre.appendChild(enviar);
    })
}


function guardarInformacion(){
    
}
