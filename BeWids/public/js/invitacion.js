document.addEventListener('DOMContentLoaded',inicio);
let participantes;

function inicio(){
    let btnparticipantes = document.querySelector('.btnPart');
    if(btnparticipantes[0]){
        btnparticipantes.forEach(e=>e.addEventListener('click', aniadirParticipante));
        participantes = btnparticipantes.map(e=>e.value);

    }
}

function aniadirParticipante(evt){
    if(participantes.contains(evt.target.value))
        location.href = '/aniadirPar?par='+evt.target.value
}