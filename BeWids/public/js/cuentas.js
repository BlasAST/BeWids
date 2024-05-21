document.addEventListener('DOMContentLoaded',iniciar);

function iniciar(){
    botones = document.querySelectorAll('.reembolso button');
    botones[0] && botones.foreach(e=>e.addEventListener('click',Reembolsar));
}

function reembolsar(){
    
}