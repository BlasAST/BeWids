

document.addEventListener('DOMContentLoaded', iniciar);

let meses = [
    'Enero',
    'Febrero',
    'Marzo',
    'Abril',
    'Mayo',
    'Junio',
    'Julio',
    'Agosto',
    'Septiembre',
    'Octubre',
    'Noviembre',
    'Diciembre'
];
let anio = new Date().getFullYear() + 2;
let selects
let contDias;
let evento;
let confirmMov;
let origen;
let destino;
let zonaDestino;
let mostrarEvt;
let mapa;









function iniciar(){

    document.querySelector('header').style.height='20%'
    document.querySelector('main').style.height='80%'
    mapa = document.querySelector('.mapa')
    let script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyAOsoMk-1yucFTUwhzq4oummSkyyjReN58&loading=async&libraries=places&callback=initMap`;
    script.async = true;
    script.defer = true;
    document.body.appendChild(script);

    

    document.querySelector('.btnConf').addEventListener('click',aceptarCambio);
    document.querySelector('.btnCanc').addEventListener('click',negarCambio);
    document.querySelector('.btnCerrar').addEventListener('click',cerrarEvt);

    confirmMov = document.querySelector('.confirmMov')
    mostrarEvt = document.querySelector('.mostrarEvt')
    aniadirListeners();
    selects = document.querySelectorAll('select');
    selects.forEach(e=>e.addEventListener('change',cambiarFecha));
    origen = document.querySelector('.origen');
    destino = document.querySelector('.destino');
    rellenarMesesAnios();
    document.querySelectorAll('header button').forEach(e=>e.addEventListener('click',cambiarFecha));
    
    document.querySelector('header > div > div').removeEventListener('click', categoria);
    contDias = document.querySelector('.contDias');


}

function aniadirListeners(){
    document.querySelectorAll('.dropZone').forEach(e=>e.addEventListener('dragover',evt=>evt.preventDefault()));
    document.querySelectorAll('.dropZone').forEach(e=>e.addEventListener('drop',moverEvento));
    let evt = document.querySelectorAll('.evt')
    evt[0] && evt.forEach(e=>{
        e.addEventListener('dragstart', iniciarDrag)
        e.addEventListener('click', abrirEvento);

    })

}
function cerrarEvt(){
    mostrarEvt.classList.remove('flex');
    mostrarEvt.classList.add('hidden');
}

async function abrirEvento(evt){
    evento = evt.currentTarget;
    await pedirEvt(evento.lastElementChild.value)
    mapa = mostrarEvt.lastElementChild.lastElementChild.lastElementChild;
    if(mapa.id)initMap();
    mostrarEvt.classList.remove('hidden');
    mostrarEvt.classList.add('flex');
}

async function pedirEvt(id){
    try {
        let response = await fetch('/pedirEvt?id='+id);
  
        if (!response.ok) {
            throw new Error('Error al obtener días');
        }
        let data = await response.json();
  
        // Limpiar eventos existentes
        mostrarEvt.lastElementChild.lastElementChild.innerHTML='';
        mostrarEvt.lastElementChild.lastElementChild.insertAdjacentHTML('beforeend',data)
        mostrarEvt.lastElementChild.lastElementChild.lastElementChild.previousElementSibling.firstElementChild.lastElementChild.addEventListener('click',retirarCal);

  
    } catch (error) {
        console.error('Error:', error);
    }
}

function iniciarDrag(evt){
    evento = evt.currentTarget;
    origen.innerText = evt.currentTarget.parentElement.previousElementSibling.innerText

}
async function retirarCal(){
    try {
        let response = await fetch('/retirarCal?id='+evento.lastElementChild.value);
  
        if (!response.ok) {
            throw new Error('Error al obtener días');
        }
        let data = await response.json();
  
        if(data)evento.remove();
        cerrarEvt();

  
    } catch (error) {
        console.error('Error:', error);
    }
}

function moverEvento(evt){
    confirmMov.classList.remove('hidden');
    confirmMov.classList.add('flex');
    destino.innerText = evt.currentTarget.previousElementSibling.innerText;
    zonaDestino = evt.currentTarget;

    

    
}
function aceptarCambio(){
    confirmMov.classList.remove('flex');
    confirmMov.classList.add('hidden');
    zonaDestino.insertBefore(evento,zonaDestino.firstElementChild)
    let fecha
    if(zonaDestino.classList.contains('mesMenor'))
        fecha = new Date(selects[0].value,Number(selects[1].value) - 1,destino.innerText);
    else if(zonaDestino.classList.contains('mesMayor'))
        fecha = new Date(selects[0].value,Number(selects[1].value) + 1,destino.innerText);
    else
        fecha = new Date(selects[0].value,selects[1].value,destino.innerText);

    solicitarCambio(fecha.getFullYear()+'-'+(Number(fecha.getMonth())+1).toString().padStart(2, '0')+'-'+fecha.getDate().toString().padStart(2, '0'),evento.lastElementChild.value);
}

async function solicitarCambio(fecha,id){
    console.log(fecha)
    console.log(id)

    try {
        let response = await fetch('/cambiarFecha?fecha='+fecha+'&evt='+id);
  
        if (!response.ok) {
            throw new Error('Error al obtener días');
        }
        let data = await response.json();
  
        // Limpiar eventos existentes
        console.log(data)
  
    } catch (error) {
        console.error('Error:', error);
    }

}
function negarCambio(){
    confirmMov.classList.remove('flex');
    confirmMov.classList.add('hidden');
}

async function cambiarFecha(evt){
    let fecha = new Date(selects[0].value,selects[1].value);
    if(evt.target.nodeName == 'BUTTON'){
        fecha.setMonth(fecha.getMonth() + Number(evt.target.value));
        seleccionar(selects[0],fecha.getFullYear())
        seleccionar(selects[1],fecha.getMonth())
    }
    await datos(fecha.getFullYear()+'-'+(Number(fecha.getMonth())+1)+'-1');
    aniadirListeners();

}

function seleccionar(cont,opt){
    cont.removeEventListener('change',cambiarFecha);
    [...cont.children].forEach((e,i)=>{
        if (e.value == opt) {
            cont.selectedIndex = i;
        }
    })
    

    cont.addEventListener('change',cambiarFecha);

}


function rellenarMesesAnios(){

    cont = selects[1];
    cont2 = selects[0];

    meses.forEach((e,i)=>{
        if(cont.firstElementChild.innerText !=e){
            let option = document.createElement('option');
            option.value = i;
            option.text = e;
            option.className = 'text-lg bg-colorCabera'
            cont.appendChild(option)
        }
    })
    for(i=anio;i>anio-7;i--){
        if(cont2.firstElementChild.value !=i){
            let option = document.createElement('option');
            option.value = i;
            option.text = i;
            option.className = 'text-sm bg-colorCabera'
            cont2.appendChild(option)
        }
    }

}


async function datos(fecha) {
    try {
        let response = await fetch('/cambiarCal?fecha='+fecha);
  
        if (!response.ok) {
            throw new Error('Error al obtener días');
        }
        let data = await response.json();
  
        // Limpiar eventos existentes
        contDias.innerHTML = '';
        contDias.insertAdjacentHTML('beforeend', data);
  
        // Añadir nuevos eventos
        // data.eventos.forEach(divHtml => {
        //     contEventos.insertAdjacentHTML('beforeend', divHtml);
        //     contEventos.lastElementChild.addEventListener('click',abrirEvento);
        //     console.log(contEventos.lastElementChild)
        //     contEventos.lastElementChild.lastElementChild.previousElementSibling.lastElementChild.firstElementChild.addEventListener('click',aniadirEvento)
        //     contEventos.insertAdjacentHTML('beforeend', '<hr class="my-6">');
  
        // });
  
    } catch (error) {
        console.error('Error:', error);
    }
  }
  function initMap(){
    let coordsArr = [];
    coordsArr = mapa.id.split('|');
    coords = {lat:Number(coordsArr[0]),lng:Number(coordsArr[1])}
  
    let map = new google.maps.Map(mapa,{
      zoom:20,
      center: coords,
    });
    let marker = new google.maps.Marker({
      map,
      position:coords,
      
    });
  }