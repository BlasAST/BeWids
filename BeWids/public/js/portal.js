document.addEventListener('DOMContentLoaded',iniciar);
let participantes;
let nombreNuevo;
let ajustes;
let contEnlace;
let contConfirm;
let contErrorElim;
let timer;

function iniciar(){
   //añadimos eventListeners y variables globales

   //  document.querySelector('.btnCE').addEventListener('click',irChat);
   //  document.querySelector('.btnCE2').addEventListener('click',irEncuestas);
    document.querySelector('.closeSession').addEventListener('click',volverPerfil);
    document.querySelectorAll('.btn').forEach(e=>e.addEventListener('click', redireccionar));
    document.querySelectorAll('.btnAjustes').forEach(e=>e.addEventListener('click', abrirCerrarAjustes));
    ajustes = document.querySelector('.ajustes');
    contEnlace = document.querySelector('.enlace');
    let enlace = document.querySelector('.btnEnlace');
    enlace && enlace.addEventListener('click',abrirEnlace);
    document.querySelector('.volverPortal').addEventListener('click',abrirEnlace);
    document.querySelector('.copiarEnlace').addEventListener('click',enlaceCopiado);
    document.querySelector('.btnAband').addEventListener('click', eliminarPart);
    document.querySelector('.btnCerrar').addEventListener('click', cerrarError);
    document.querySelector('.btnCancConfirm').addEventListener('click',cerrarConfirm)
    document.querySelector('.btnConfirm').addEventListener('click', abandonar)
    let check = document.querySelectorAll('input[type="checkbox"]');
    check[0] && check.forEach(e=>e.addEventListener('change',cambiarConf))


    contConfirm = document.querySelector('.confirmElim');
    contErrorElim = document.querySelector('.errorElim')




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

async function cambiarConf(evt){
   //cada vez que se modifica un checkbox de ajustes se solicita al servidor cambiarlo en la BD
   try {
      let response = await fetch('/cambiarConf?conf='+evt.currentTarget.checked+'&tipo='+evt.currentTarget.id);
      if(!response.ok){
          throw new Error('Error al cambiar ajustes');
      }
      let data = await response.json();

      if(data){
          console.log('ajustes modif')
      }else{
         console.log('ajustes NO modif')

      }
  } catch (error) {
      console.log('Error:', error);
  }
}

function abandonar(){
   location.href = '/eliminarPart'
}

function cerrarConfirm(){
   contConfirm.classList.remove('flex')
   contConfirm.classList.add('hidden') 
}

function cerrarError(){
   contErrorElim.classList.remove('flex')
   contErrorElim.classList.add('hidden') 
}

async function eliminarPart(evt){
   //Solicita la disponibilidad del participante para ser eliminando
   //Se tendra en cuenta si tiene deudas o no
   try {
       let response = await fetch('/comprobarCuentas');
       if(!response.ok){
           throw new Error('Error al comprobar participante');
       }
       let data = await response.json();

       if(data){
           contConfirm.classList.remove('hidden');
           contConfirm.classList.add('flex')
       }else{
            contErrorElim.classList.remove('hidden');
            contErrorElim.classList.add('flex')
       }


   } catch (error) {
       console.log('Error:', error);
   }

}

async function abrirEnlace(){
   //Muestra popUp de enlace de invitación
    if(contEnlace.classList.contains('hidden')){
       await pedirToken();
       contEnlace.classList.remove('hidden');
       contEnlace.classList.add('flex');
       timer=setTimeout(()=>{
          contEnlace.classList.remove('flex');
          contEnlace.classList.add('hidden');
       },10000)
    }else{
       contEnlace.classList.remove('flex');
       contEnlace.classList.add('hidden');
       clearTimeout(timer);
    }
}

async function pedirToken(){
   //Solicita el token de portal al servidor que generará uno nuevo
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

function enlaceCopiado(evt){
   //Copiar enlace al clickar
   let textoCopiado=evt.currentTarget.textContent;
   navigator.clipboard.writeText(textoCopiado);
   let mensaje=evt.currentTarget.parentElement.lastElementChild; 
   mensaje.classList.remove('hidden');
   setTimeout(()=>{
      console.log('borrando');
      mensaje.classList.add('hidden');
   },2000)
}

function abrirCerrarAjustes(evt){
   //Mostrar - Ocultar Ajustes
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

//  function irChat(){
//     window.location.href = '/chat';
//  }
//  function irEncuestas(){
//     window.location.href="/encuestas";
//  }

 function volverPerfil(){
   window.location.href='/perfil';
 }

 function redireccionar(evt){

   window.location.href='/'+evt.currentTarget.id;

 }