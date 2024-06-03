document.addEventListener('DOMContentLoaded', iniciar);
let categorias;
let listaTerm = [
    '206974-0-agenda-eventos-culturales-100.json'
]
let coordsArr = [];
let mapa;
let contEventos;
let categoriasSel = [];
let btnCat;
let contsPag;
let contBuscador;
let contMisEvt;
let filtros;
let checks = [];
let gratis = false;
let contFilt;
let valor = '';

async function iniciar(){
    document.querySelector('.btnBurger').addEventListener('click', desplegCat);
    categorias = document.querySelector('.categorias');
    document.querySelectorAll('.categorias > button').forEach(e=>e.addEventListener('click', aniadirCat));
    btnCat = document.querySelector('.btnCat');
    document.querySelector('.buscador').addEventListener('input',buscador);
    document.querySelector('.buscador').addEventListener('blur',cerrarBuscador);
    contEventos = document.querySelector('.contEventos')
    contsPag = document.querySelectorAll('.contPag');
    contBuscador = document.querySelector('.contBusc');
    contBuscador.addEventListener('submit', buscarEventos);
    contFilt = document.querySelector('.contFiltros');
    contMisEvt = document.querySelector('.nuestrosEventos')
    document.querySelector('.filtrar').addEventListener('click',cambiarFiltrar);
    document.querySelector('.btnFiltrar').addEventListener('click',filtrar);
    filtros = document.querySelectorAll('input[type="checkbox"]');
    await pagYCat();
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

async function aniadirEvento(evt){
  try {
    let response = await fetch('/aniadir?evt='+evt.target.parentElement.parentElement.nextElementSibling.firstElementChild.value);

    if (!response.ok) {
        throw new Error('Error al añadir el evento');
    }
    let data = await response.json();

    console.log(data)

    contMisEvt.firstElementChild.insertAdjacentHTML('afterend', data);
    contMisEvt.firstElementChild.addEventListener('click', abrirEvento)


} catch (error) {
    console.error('Error:', error);
}
}
function filtrar(){
  filtros.forEach(e=>{
    if(e.name != 'gratis'){
      if(e.checked)
        checks.push(e.name);
      else
        checks = checks.filter(check=>e.name != check)
    }else{
        if(e.checked)
          gratis = "Gratis";
        else
          gratis = false;
    }
  });
  pagYCat();
  cambiarFiltrar();


}

function cambiarFiltrar(){
    if(contFilt.classList.contains('hidden')){
      contFilt.classList.remove('hidden');
      contFilt.classList.add('flex');
      contBuscador.style.borderBottomRightRadius = '0px';
      contBuscador.style.borderBottomLeftRadius = '0px';
      contBuscador.firstElementChild.nextElementSibling.disabled = true;
    }else{
      contFilt.classList.remove('flex');
      contFilt.classList.add('hidden');
      contBuscador.style.borderBottomRightRadius = '1rem';
      contBuscador.style.borderBottomLeftRadius = '1rem';
      contBuscador.firstElementChild.nextElementSibling.disabled = false;
    }
}

function aniadirCat(evt){
  if(evt.target == btnCat){
    pagYCat();
    return
  }
  if(evt.target.classList.contains('border-2')){
      evt.target.classList.remove('border-2');
      categoriasSel = categoriasSel.filter(e=>e != evt.target.id);
  }else{
    evt.target.classList.add('border-2');
    categoriasSel.push(evt.target.id)
  }
    

}

function cambPag(evt){
  pagYCat(evt.target.value);
}
async function pagYCat(pag = 1){
  let categoriasGet = (categoriasSel[0] && categoriasSel.join('%')) || '';
  let filtrosGet = (checks[0] && checks.join('%')) || '';
  contEventos.innerHTML = '';
  console.log('/buscarEventos?pag='+pag +((valor && '&valor=' + valor ) || '') +((categoriasGet && '&cat=' + categoriasGet ) || '') + ((filtrosGet && '&filt=' + filtrosGet ) || '') + ((gratis && '&gratis='+ gratis)||''))
  await datos('/buscarEventos?pag='+pag +((valor && '&valor=' + valor ) || '') +((categoriasGet && '&cat=' + categoriasGet ) || '') + ((filtrosGet && '&filt=' + filtrosGet ) || '') + ((gratis && '&gratis='+ gratis)||''))
  

}
async function datos(url) {
  try {
      let response = await fetch(url);

      if (!response.ok) {
          throw new Error('Error al obtener los divs');
      }
      let data = await response.json();

      // Limpiar eventos existentes
      contEventos.innerHTML = '';

      // Añadir nuevos eventos
      data.eventos.forEach(divHtml => {
          contEventos.insertAdjacentHTML('beforeend', divHtml);
          contEventos.lastElementChild.addEventListener('click',abrirEvento);
          console.log(contEventos.lastElementChild)
          contEventos.lastElementChild.lastElementChild.previousElementSibling.lastElementChild.firstElementChild.addEventListener('click',aniadirEvento)
          contEventos.insertAdjacentHTML('beforeend', '<hr class="my-6">');

      });

      // Actualizar paginación
      contsPag.forEach(e=>actualizarPaginacion(data.currentPage, data.totalPages,e));

  } catch (error) {
      console.error('Error:', error);
  }
}
function actualizarPaginacion(currentPage, totalPages, contenedor) {
  contenedor.innerHTML = ''; // Limpiar botones de paginación anteriores
  currentPage = Number(currentPage);


  const firstButton = document.createElement('button');
  firstButton.textContent = '<<';
  firstButton.classList.add('px-1');
  firstButton.value=1;
  contenedor.appendChild(firstButton);

  // Botón para ir a la página anterior
  const prevButton = document.createElement('button');
  prevButton.textContent = '<';
  prevButton.classList.add('px-1');
  prevButton.value=(currentPage > 1 && currentPage-1) || 1;
  prevButton.addEventListener('click', () => pagYCat(currentPage - 1));
  contenedor.appendChild(prevButton);
  let startPage;
  let endPage

  // Determinar el rango de botones a mostrar
  if(Math.max(1, currentPage - 4) == 1){
    startPage = 1;
    endPage = Math.min(9,totalPages);
  }else if(Math.min(totalPages, currentPage + 4) == totalPages){
    startPage = totalPages - 9;
    endPage = totalPages;
  }else{
    startPage = currentPage - 4;
    endPage = currentPage + 4;
  }
  

  for (let i = startPage; i <= endPage; i++) {
      const button = document.createElement('button');
      button.textContent = i;
      button.value = i;
      button.classList.add('grow');
      if (i == currentPage) {
          button.style.borderBottom='1px solid white'; // Resaltar el botón de la página actual
      }
      contenedor.appendChild(button);
  }
  const nextButton = document.createElement('button');
  nextButton.textContent = '>';
  nextButton.classList.add('px-1');
  nextButton.value = currentPage + 1;
  nextButton.value=(currentPage == totalPages && totalPages) || currentPage+1;
  contenedor.appendChild(nextButton);

  // Botón para ir a la página anterior
  const lastButton = document.createElement('button');
  lastButton.textContent = '>>';
  lastButton.classList.add('px-1');
  lastButton.value = totalPages;
  contenedor.appendChild(lastButton);

  contenedor.childNodes.forEach(e=>e.addEventListener('click', cambPag))
}

// async function datos(url){

//   try {
//     let response = await fetch(url);

//     if (!response.ok) {
//         throw new Error('Error al obtener los divs');
//     }
//     let data = await response.json();

//     data.forEach(divHtml => {
//         contEventos.insertAdjacentHTML('beforeend', divHtml);
//         contEventos.insertAdjacentHTML('beforeend', '<hr class="my-6">');
//     });

// } catch (error) {
//     console.error('Error:', error);
// }
  // fetch(url)
  //       .then(response => response.json())
  //       .then(data => {
  //           // Insertar cada div recibido en el DOM
  //           data.forEach(divHtml => {
  //             contEventos.appendChild(divHtml);
  //             console.log(contEventos)
  //           });
  //       })
  //       .catch(error => console.error('Error:', error));
// }

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
      if(mapa.id)initMap();


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
        evt.target.classList.remove('logoDesp')
        evt.target.classList.add('logoCancel')
    }else{
        categorias.style.display = 'none';
        categorias.nextElementSibling.classList.remove('basis-3/4')
        categorias.nextElementSibling.classList.add('basis-4/4')
        evt.target.classList.remove('logoCancel')
        evt.target.classList.add('logoDesp')
    }
}
function buscador(evt){
  contBuscador.style.borderBottomRightRadius = '0px';
  contBuscador.style.borderBottomLeftRadius = '0px';
  contBuscador.nextElementSibling.classList.remove('hidden');
  contBuscador.nextElementSibling.classList.add('flex');
  contBuscador.nextElementSibling.innerHTML = '';
  buscar(evt.target.value);

}

function cerrarBuscador(evt){
  if(evt.relatedTarget && evt.relatedTarget.id == 'btnBuscar')return
  contBuscador.style.borderBottomRightRadius = '1rem';
  contBuscador.style.borderBottomLeftRadius = '1rem';
  contBuscador.nextElementSibling.classList.remove('flex');
  contBuscador.nextElementSibling.classList.add('hidden');
  contBuscador.nextElementSibling.innerHTML = '';
}
async function buscar(valor){
  try {
    let response = await fetch('/buscador?valor='+valor);

    if (!response.ok) {
        throw new Error('Error al obtener los divs');
    }
    let data = await response.json();


    data.forEach(titulo => {
        contBuscador.nextElementSibling.insertAdjacentHTML('beforeend', '<p class="w-full p-5 hover:bg-colorCabera hover:text-colorComplem cursor-pointer">'+titulo+'</p>');
    });

  } catch (error) {
    console.error('Error:', error);
  }

}
function buscarEventos(evt){
  evt.preventDefault();
  if(!evt.target.nextElementSibling.hasChildNodes() && evt.target.firstElementChild.nextElementSibling.value) return
  valor = evt.target.firstElementChild.nextElementSibling.value
  if(valor)
    datos('/buscarEventos?valor='+ valor)
  else
    datos('/buscarEventos');
  contBuscador.style.borderBottomRightRadius = '1rem';
  contBuscador.style.borderBottomLeftRadius = '1rem';
  contBuscador.nextElementSibling.classList.remove('flex');
  contBuscador.nextElementSibling.classList.add('hidden');
  contBuscador.nextElementSibling.innerHTML = '';
  filtros.forEach(e=>e.checked=false);
  checks = [];
  [...categorias.children].forEach(e=>e.classList.remove('border-2'));
  categoriasSel = [];

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