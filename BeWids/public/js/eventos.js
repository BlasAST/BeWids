document.addEventListener('DOMContentLoaded', iniciar);
let categorias;
let listaTerm = [
    '206974-0-agenda-eventos-culturales-100.json'
]
let coordsArr = [];
let mapa;

async function iniciar(){
    document.querySelector('.btnBurger').addEventListener('click', desplegCat);
    categorias = document.querySelector('.categorias');
    document.querySelector('.buscador').addEventListener('input',buscador);
    //await adquirirEventos();
    let eventos = document.querySelectorAll('.evento');
    if(eventos[0]){
      eventos.forEach(e=>e.addEventListener('click',abrirEvento));
      mapa = eventos[0].lastElementChild.previousElementSibling.firstElementChild;

      let script = document.createElement('script');
      script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyAOsoMk-1yucFTUwhzq4oummSkyyjReN58&loading=async&libraries=places&callback=initMap`;
      script.async = true;
      script.defer = true;
      document.body.appendChild(script);
    }

    
}

function abrirEvento(evt){
    if(mapa.contains(evt.target) || mapa == evt.target )return
    if(evt.currentTarget.nodeName == 'H3'){
      mapa.parentElement.classList.add('hidden');
      mapa.parentElement.classList.remove('flex');
      evt.currentTarget.classList.remove('bg-colorCabera','text-colorLetra');
      evt.currentTarget.parentElement.parentElement.classList.remove('bg-colorComplem','bg-opacity-50');

      evt.currentTarget.parentElement.parentElement.addEventListener('click',abrirEvento);
      evt.currentTarget.removeEventListener('click',abrirEvento)
      evt.currentTarget.nextElementSibling.classList.add('max-h-[3.15rem]');
      evt.stopPropagation();
    }else{
      let cabecera = evt.currentTarget.firstElementChild.nextElementSibling.firstElementChild;
      mapa.parentElement.classList.add('hidden');
      mapa.parentElement.classList.remove('flex');
      mapa.parentElement.parentElement.classList.remove('bg-colorComplem','bg-opacity-50');
      mapa.parentElement.previousElementSibling.firstElementChild.classList.remove('bg-colorCabera','text-colorLetra');

      mapa.parentElement.parentElement.addEventListener('click',abrirEvento);
      mapa = evt.currentTarget.lastElementChild.previousElementSibling.firstElementChild;
      mapa.parentElement.classList.remove('hidden');
      mapa.parentElement.classList.add('flex');
      cabecera.classList.add('bg-colorCabera','text-colorLetra')
      evt.currentTarget.classList.add('bg-colorComplem','bg-opacity-50')
      evt.currentTarget.firstElementChild.nextElementSibling.firstElementChild.addEventListener('click',abrirEvento);
      evt.currentTarget.firstElementChild.nextElementSibling.firstElementChild.nextElementSibling.classList.remove('max-h-[3.15rem]');

      evt.currentTarget.removeEventListener('click',abrirEvento)
      initMap();


    }
    // let coordsArr = mapa.id.split('-');
    // let coords = {lat:coordsArr[0],lon:coordsArr[1]}
    // let script = document.createElement('script');
    // script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyAOsoMk-1yucFTUwhzq4oummSkyyjReN58&libraries=places&callback=initMap`;
    // script.async = true;
    // script.defer = true;
    //document.body.appendChild(script);
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

function desplegCat(evt){
    if(categorias.style.display == 'none'){
        categorias.style.display = 'flex';
        categorias.nextElementSibling.classList.remove('basis-4/4')
        categorias.nextElementSibling.classList.add('basis-3/4')
        evt.target.classList.remove('logoBurger')
        evt.target.classList.add('logoCancel')
    }else{
        categorias.style.display = 'none';
        categorias.nextElementSibling.classList.remove('basis-3/4')
        categorias.nextElementSibling.classList.add('basis-4/4')
        evt.target.classList.remove('logoCancel')
        evt.target.classList.add('logoBurger')
    }
}
function buscador(evt){

}

async function adquirirEventos(){
    // let urlIni = "https://datos.madrid.es/egob/";
    // listaTerm.forEach(e=>recogerEventos(urlIni+e+'?all',guardarDatos,error))
        const url = '/buscarEventos';
        try {
          const response = await fetch(url);
          if (!response.ok) {
            console.log('pepe');
            console.log(response);
            throw new Error(response.status +  ' -> ' + response.statusText);
          }
          const data = await response.json();
          console.log(data);
        } catch (error) {
          console.error('Error:', error);
        }
}
async function recogerEventos(url,ok,noOk){
    try{
      const resp = await fetch(url,{
        mode: 'no-cors'
      });
      const json = await resp.json();
      ok(json);
    }catch(e){
      noOk(`Error: ${e.status} -> ${e.statusText}`)
    }
  }
  function guardarDatos(e){
    console.log(e)

  }
  function error(e){
    console.log(e)

  }