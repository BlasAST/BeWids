document.addEventListener('DOMContentLoaded',iniciar);
function iniciar(){
    abrirApartadosChat();
    eventosListaChat();
    scrollEncuestas();
    coloresTablaEncuestas();
    mostrarFormulario();
}

// ELEMENTOS DE EL CHAT

let botonesL;
let menusL;
let seleccionado;
let nuevoChat;
let botonHijoDiv;
function abrirApartadosChat(){
    botonesL=document.querySelectorAll('.seleccionesChat > button:not(:first-child)');
    botonHijoDiv=document.querySelector('.botonDiv >button');
    menusL=document.querySelectorAll('.seleccionesChat > ul');
    botonesL.forEach(boton=>boton.addEventListener('click',abrirC));
    botonHijoDiv.addEventListener('click',abrirC);
}
function abrirC(evt){
    menusL.forEach(menu=>{
        menu.classList.contains(evt.currentTarget.id)?menu.classList.toggle('hidden'):'';
    })
}
function eventosListaChat(){
    seleccionado=document.querySelector('select');
    
    seleccionado.addEventListener('change',()=>{
        nuevoChat=document.querySelector('.newChat');
        nuevoChat.style.display='block';
        cambioSeleccionado();       
    })
    crearNuevoGrupo();
}

function cambioSeleccionado(){
    let botonRechazo;

    setTimeout(()=>{
        botonRechazo=document.querySelector('.cancelar')
        botonRechazo.addEventListener('click',()=>{
           seleccionado.value=seleccionado.options[0].value;
           nuevoChat.style.display='none';
        })
        botonAceptar=document.querySelector('.aceptar');
        botonAceptar.addEventListener('click',()=>{
            setTimeout(()=>{
                seleccionado.value=seleccionado.options[0].value;
                nuevoChat.style.display='none';
            },2000)
        })
    },100)
}

function crearNuevoGrupo(){
    let formulario=document.querySelector('.newGroupForm');
    let boton=document.querySelector('.newGroup');
    let inputAll=document.querySelector('#all');
    let inputs=document.querySelectorAll('.creacionGrupo >input:not(#all)');
    let cierreFormulario=document.querySelector('.cierreFormGroup');
    let creacionFormulario=document.querySelector('.crearFormGroup');
    boton.addEventListener('click',()=>{
        menusL.forEach(menu=>menu.classList.add('hidden'));
        formulario.classList.toggle('hidden');
        formulario.classList.toggle('flex');
    })
    
    inputAll.addEventListener('change',()=>{
                if(inputAll.checked){
                    inputs.forEach(input=>input.checked=false);
                }
            })
    inputs.forEach(input=>input.addEventListener('change',()=>{
        if(input.checked && inputAll.checked){
            inputAll.checked=false;
        }
    }))
    cierreFormulario.addEventListener('click',()=>{
        formulario.classList.add('hidden');
    })
    // formulario.addEventListener('submit',(evt)=>{
    //     evt.preventDefault();
    //     if(formulario.checkValidity()){

    //     }else{
    //         alert('Completa los campos correctamente');
    //     }
    // })
    // creacionFormulario.addEventListener('click',()=>{
    //     setTimeout(()=>formulario.classList.add('hidden'),20000);
    // })


}


// ELEMENTOS ENCUESTAS
let botonesE;
let partesCabecera;


function scrollEncuestas(){
    botonesE=document.querySelector('.botonesEncuestas');
    let listadoEncuestas=document.querySelector('.listadoEncuestas');
    let ultimoScroll=0;

    listadoEncuestas.addEventListener('scroll',function(){
        let topeScroll=listadoEncuestas.scrollTop;
        if(topeScroll>ultimoScroll){
            botonesE.classList.add('hidden')
        }else{
            botonesE.classList.remove('hidden')
        }
        ultimoScroll=topeScroll;
    });

}
function coloresTablaEncuestas(){

    partesCabecera=document.querySelectorAll('thead>tr>th')
    partesCabecera.forEach(encabezado=>{
        encabezado.style.border='solid #541530 4px';
        encabezado.style.backgroundColor='#4465B8';
    })    
}

function mostrarFormulario(){
    let botonCrearEncuestas=document.querySelector('.creadorEncuestas');
    let elemento=document.querySelector('.formEncuesta');
    botonCrearEncuestas.addEventListener('click',()=>{
        elemento.classList.toggle('hidden');
        elemento.classList.add('flex');
        botonCrearEncuestas.textContent='Cancelar Encuesta';
        botonCrearEncuestas.style.backgroundColor='#4465B8';

        if(elemento.classList.contains('hidden')){
            botonCrearEncuestas.textContent='Crear Encuesta';
            botonCrearEncuestas.style.backgroundColor='#541530';
            elemento.classList.remove('flex');
            botonesE.classList.remove('hidden');
        }
    })
}

