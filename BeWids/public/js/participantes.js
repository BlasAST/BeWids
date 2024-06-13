document.addEventListener('DOMContentLoaded', iniciar);
let formNuevo
let errorElim

function iniciar(){
    document.querySelector('.btnNuevoPart').addEventListener('click',abrirForm);
    formNuevo = document.querySelector('.formNuevo')
    let elim = document.querySelectorAll('.btnDel')
    elim[0] && elim.forEach(e=>e.addEventListener('click', eliminarPart));
    let desvin = document.querySelectorAll('.btnDes')
    desvin[0] && desvin.forEach(e=>e.addEventListener('click', desvincularPart));
    let ascend = document.querySelectorAll('.btnAsc')
    ascend[0] && ascend.forEach(e=>e.addEventListener('click', ascenderPart));
    errorElim = document.querySelector('.errorElim')
    document.querySelector('.btnCerrar').addEventListener('click', cerrarError);

}
function cerrarError(){
    errorElim.classList.remove('flex')
    errorElim.classList.add('hidden') 
}

function abrirForm(evt){
    if(formNuevo.classList.contains('hidden')){
        formNuevo.classList.remove('hidden')
        formNuevo.classList.add('flex');
        evt.target.innerText = 'Cancelar';
    }else{
        formNuevo.classList.remove('flex')
        formNuevo.classList.add('hidden')
        evt.target.innerText = 'Crear Participante';
    }
}

async function eliminarPart(evt){
    let id = evt.currentTarget.parentElement.lastElementChild.value;
    try {
        let response = await fetch('/comprobarCuentas?id='+id);
        if(!response.ok){
            throw new Error('Error al comprobar participante');
        }
        let data = await response.json();

        if(data){
            location.href = '/eliminarPart?id='+id
        }else{
            errorElim.classList.remove('hidden');
            errorElim.classList.add('flex')
        }


    } catch (error) {
        console.log('Error:', error);
    }

}
function desvincularPart(evt){
    location.href = '/desvincularPart?id='+evt.currentTarget.parentElement.lastElementChild.value
    
}
function ascenderPart(evt){
    location.href = '/ascenderPart?id='+evt.currentTarget.parentElement.lastElementChild.value
    
}