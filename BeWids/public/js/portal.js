document.addEventListener('DOMContentLoaded',iniciar);

function iniciar(){
    document.querySelector('.btnGastos').addEventListener('click',irGastos);
    document.querySelector('.btnCE').addEventListener('click',irChatYEncuestas);
    document.querySelector('.btnEv').addEventListener('click',irEvento);

}

function irGastos(){
    window.location.href = '/contabilidad';
}
 function irChatYEncuestas(){
    window.location.href = '/chat';
 }
 function irEvento(){
    window.location.href = '/eventos';
 }