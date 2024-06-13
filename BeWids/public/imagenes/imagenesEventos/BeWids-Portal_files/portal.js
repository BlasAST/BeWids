document.addEventListener('DOMContentLoaded',iniciar);
let participantes;
let nombreNuevo;
let ajustes;
let contEnlace;


function iniciar(){
    document.querySelector('.btnCE').addEventListener('click',irChat);
    document.querySelector('.btnCE2').addEventListener('click',irEncuestas);
  
    document.querySelector('.closeSession').addEventListener('click',volverPerfil);
    document.querySelectorAll('.btn').forEach(e=>e.addEventListener('click', redireccionar));
    document.querySelectorAll('.btnAjustes').forEach(e=>e.addEventListener('click', abrirCerrarAjustes));
    ajustes = document.querySelector('.ajustes');
    document.querySelector('input[type="checkbox"]').addEventListener('change',e=>console.log(e));
    contEnlace = document.querySelector('.enlace');
    document.querySelector('.btnEnlace').addEventListener('click',abrirEnlace);
    document.querySelector('.volverPortal').addEventListener('click',abrirEnlace)



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

async function abrirEnlace(){
   if(contEnlace.classList.contains('hidden')){
      await pedirToken();
      contEnlace.classList.remove('hidden');
      contEnlace.classList.add('flex');
      setInterval(e=>{
         contEnlace.classList.remove('flex');
         contEnlace.classList.add('hidden');
      },10000)
   }else{
      contEnlace.classList.remove('flex');
      contEnlace.classList.add('hidden');
   }
}

async function pedirToken(){
   try {
      let response = await fetch('/crearEnlace');

      if (!response.ok) {
          throw new Error('Error al crear Enlace');
      }
      let data = await response.json();

      // Limpiar eventos existentes
      contEnlace.firstElementChild.lastElementChild.firstElementChild.innerText = "http://127.0.0.1:8000/invitacion/"+data;


  } catch (error) {
      console.error('Error:', error);
  }
}

function abrirCerrarAjustes(evt){
   if(ajustes.classList.contains('hidden')){
      ajustes.classList.remove('hidden');
      ajustes.classList.add('flex');
   }else{
      ajustes.classList.remove('flex');
      ajustes.classList.add('hidden');
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

   window.location.href='/'+evt.currentTarget.id;

 }