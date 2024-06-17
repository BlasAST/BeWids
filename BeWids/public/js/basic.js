document.addEventListener('DOMContentLoaded',iniciar);

function iniciar(){
    document.querySelector('.icoPerfil').addEventListener('click',sesion);
    document.querySelectorAll('.botonesSesion button').forEach(e=>e.addEventListener('click',sesion))
    toggleMuestras();
    subir();
}

function sesion(evt){
    if(evt.target.className == 'icoPerfil')
        window.location.href = '/cuenta';
    if(evt.target.innerText == 'Iniciar Sesión')
        window.location.href = '/cuenta/iniciar';
    if(evt.target.innerText == 'Registrarse')
        window.location.href = '/cuenta/registrar'
}
let muestras;
function toggleMuestras(){
    let botones=document.querySelectorAll('.botonesMuestra > *');
    muestras=document.querySelectorAll('.muestraSeleccionada >*');
    botones.forEach(boton=>boton.addEventListener('click',mostrar));
}

function mostrar(evt){
    let busqueda=evt.target.classList.value;
    busqueda=busqueda.split('-');
    busqueda=busqueda[1]
    muestras.forEach(muestra=>{
        muestra.style.display='none';
        if(muestra.classList.contains(busqueda)){
            muestra.style.display='flex';
        }
    })
}

function subir(){
    let subir=document.querySelector('.subir');
    subir.addEventListener('click',()=>{
        window.scrollTo({
            top:0,
            behavior:'smooth'
        })
    });
}