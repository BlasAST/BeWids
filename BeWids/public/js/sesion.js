console.log('JS iniciado');
document.addEventListener('DOMContentLoaded', iniciar);
var inputs
function iniciar(){
    inputs = document.querySelectorAll('input');
    console.log(inputs)
    inputs.forEach(e=>e.addEventListener('focus',inputFocus));
    inputs.forEach(e=>e.addEventListener('blur',inputBlur));
}

function inputFocus(evt){
    evt.target.nextElementSibling.style.borderStyle = 'none';
    evt.target.parentElement.style.border = '2px solid white'
}
function inputBlur(evt){
    evt.target.nextElementSibling.style.borderStyle = 'solid';
    evt.target.parentElement.style.border = 'none'
}