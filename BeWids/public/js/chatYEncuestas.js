document.addEventListener('DOMContentLoaded',iniciar);
let hijos;
function iniciar(){
    hijos=document.querySelectorAll('.menu > button');
    hijos.forEach(hijo=>hijo.addEventListener('click',mostrarChat));
}
function mostrarChat(evt){
    anteriores=document.querySelectorAll(".select")
    anteriores.forEach(anterior=>anterior.className='')
    evt.target.className='select';
}
