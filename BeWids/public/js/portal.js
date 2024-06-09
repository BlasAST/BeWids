document.addEventListener('DOMContentLoaded',iniciar);
let participantes;
let nombreNuevo;


function iniciar(){
    document.querySelector('.btnGastos').addEventListener('click',irGastos);
    document.querySelector('.btnCE').addEventListener('click',irChat);
    document.querySelector('.btnCE2').addEventListener('click',irEncuestas);
    document.querySelector('.btnEv').addEventListener('click',irEvento);
    document.querySelector('.btnInvitacion').addEventListener('click',irInvitacion);
    document.querySelector('.closeSession').addEventListener('click',volverPerfil);
    document.querySelectorAll('.btn').forEach(e=>e.addEventListener('click', redireccionar));



   //INVITACIONES
    let btnNuevo = document.querySelector('.btnNuevo');
    btnNuevo && btnNuevo.addEventListener('click',nuevoParticipante);
    nombreNuevo = document.querySelector('.nombreNuevo');
    let btnParticipantes = document.querySelectorAll('.btnPart');
    if(btnParticipantes[0]){
        btnParticipantes.forEach(e=>e.addEventListener('click', aniadirParticipante));
        participantes = [...btnParticipantes].map(e=>e.value);

    }
    
}
function nuevoParticipante(){
   if(nombreNuevo.value)
      location.href = '/aniadirPar?par='+nombreNuevo.value

}
function aniadirParticipante(evt){
   if(participantes.includes(evt.target.value))
       location.href = '/aniadirPar?par='+evt.target.value
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

 function redireccionar(evt){

   window.location.href='/'+evt.target.value;

 }