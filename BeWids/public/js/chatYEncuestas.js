document.addEventListener('DOMContentLoaded',iniciar);
let hijos;
function iniciar(){
    hijos=document.querySelectorAll('.categorias > p');
    hijos.forEach(hijo=>{
        hijo.className='select'
        hijo.addEventListener('click',mostrarChat)});
}
function mostrarChat(evt){
   
    anteriores.forEach(anterior=>anterior.className='')
    evt.target.className='select';
}
