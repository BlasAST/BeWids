document.addEventListener('DOMContentLoaded',iniciar);

function iniciar(){
    document.querySelector('.icoPerfil').addEventListener('click',sesion);
    document.querySelectorAll('.botonesSesion button').forEach(e=>e.addEventListener('click',sesion))
}

function sesion(evt){
    if(evt.target.className == 'icoPerfil')
        window.location.href = '/perfil';
    if(evt.target.innerText == 'Iniciar Sesión')
        window.location.href = '/perfil/iniciar';
    if(evt.target.innerText == 'Registrarse')
        window.location.href = '/perfil/registrar'
}