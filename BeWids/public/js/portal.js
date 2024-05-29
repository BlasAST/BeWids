document.addEventListener('DOMContentLoaded',iniciar);

function iniciar(){
    document.querySelector('.btnGastos').addEventListener('click',irGastos);
    document.querySelector('.btnCE').addEventListener('click',irChat);
    document.querySelector('.btnCE2').addEventListener('click',irEncuestas);
    document.querySelector('.btnEv').addEventListener('click',irEvento);
    document.querySelector('.btnInvitacion').addEventListener('click',irInvitacion);
    document.querySelector('.closeSession').addEventListener('click',volverPerfil);
    
}

function irGastos(){
    window.location.href = '/contabilidad';
}
 function irChat(){
    window.location.href = '/chat';
 }
 function irEncuestas(){
    window.location.href="/encuestas";
 }
 function irEvento(){
    window.location.href = '/eventos';
 }
 function irInvitacion(){
   window.location.href = '/crearEnlace';
 }
 function volverPerfil(){
   window.location.href='/perfil';
 }