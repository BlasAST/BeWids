document.addEventListener('DOMContentLoaded', iniciar);
window.addEventListener("beforeunload",salir);
let categorias;
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
let idTemp;
let formNuevo;
let formCal;
let fechaCal;
async function iniciar(){
  //añadimos los eventoListeners y las variables globales
    document.querySelector('.btnBurger').addEventListener('click', desplegCat);
    categorias = document.querySelector('.categorias');
    contenedores = document.querySelectorAll('section');
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
    fechaCal = document.querySelector('.fechaCal')
    document.querySelector('.filtrar').addEventListener('click',cambiarFiltrar);
    document.querySelector('.btnFiltrar').addEventListener('click',filtrar);
    formCal = document.querySelector('.confirmCal')
    formCal.firstElementChild.addEventListener('submit',confirmarCal);
    let btnCal = document.querySelectorAll('.btnCal');
    btnCal[0] && btnCal.forEach(e=>e.addEventListener('click',aniadirCal))
    let btnElim = document.querySelectorAll('.btnElim');
    btnElim[0] && btnElim.forEach(e=>e.addEventListener('click',eliminarEvt))
    document.querySelector('.confirmCal figure').addEventListener('click',aniadirCal)
    filtros = document.querySelectorAll('input[type="checkbox"]');
    document.querySelector('.btnNuevoEvt').addEventListener('click',abrirForm);
    formNuevo = document.querySelector('.formNuevoEvt');
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
function confirmarCal(evt){
  //añadir evento a calendario
  evt.preventDefault();
  if(fechaCal.value)
    evt.target.submit();

}
function aniadirCal(evt){
  //mostrar form para meter fecha y añadir al calendario
  if(formCal.classList.contains('hidden')){
    formCal.classList.remove('hidden')
    formCal.classList.add('flex')
    formCal.firstElementChild.lastElementChild.value = evt.target.parentElement.parentElement.nextElementSibling.firstElementChild.value;
  }else{
    formCal.classList.remove('flex')
    formCal.classList.add('hidden')
  }
  
}
function abrirForm(evt){
  //abrir form para crear evento personalizado
  if(evt.target.innerText == 'Evento personalizado'){
    formNuevo.style.display = 'flex';
    evt.target.innerText = 'Cancelar';
  }else{
    formNuevo.style.display = 'none';
    evt.target.innerText = 'Evento personalizado';
  }
}
async function eliminarEvt(evt){
  console.log('hola');
  try {
    let response = await fetch('/eliminarEvt?evt='+evt.target.parentElement.parentElement.nextElementSibling.firstElementChild.value);

    if (!response.ok) {
        throw new Error('Error al eliminar el evento');
    }
    let data = await response.json();
    //recibe el evento añadido a la bd y lo añadimos al DOM
    data && evt.target.parentElement.parentElement.parentElement.remove();
  } catch (error) {
      console.error('Error:', error);
  }
}

async function aniadirEvento(evt){
  //añadir evento a nuestra lista
  animacionAniadir(evt.currentTarget.firstElementChild);
  console.log(evt.target.parentElement.parentElement.nextElementSibling.firstElementChild.value)
  try {
    let response = await fetch('/aniadir?evt='+evt.target.parentElement.parentElement.nextElementSibling.firstElementChild.value);

    if (!response.ok) {
        throw new Error('Error al añadir el evento');
    }
    let data = await response.json();

    //recibe el evento añadido a la bd y lo añadimos al DOM
    contMisEvt.lastElementChild.previousElementSibling.insertAdjacentHTML('beforebegin', data);
    contMisEvt.lastElementChild.previousElementSibling.previousElementSibling.addEventListener('click', abrirEvento)
    let cal = contMisEvt.lastElementChild.previousElementSibling.previousElementSibling.lastElementChild.previousElementSibling.lastElementChild.lastElementChild.previousElementSibling;
    cal && cal.addEventListener('click',aniadirCal)
    contMisEvt.lastElementChild.previousElementSibling.previousElementSibling.lastElementChild.previousElementSibling.lastElementChild.lastElementChild.addEventListener('click',eliminarEvt);



  } catch (error) {
      console.error('Error:', error);
  }

}
function filtrar(){
  //guardamos en un array los filtros seleccionados
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
function animacionAniadir(spinner){
  //genera la animación de añadir evento para que quede mas bonito
  if(spinner.classList.contains('hidden')){
    spinner.classList.remove('hidden')
    spinner.classList.remove('logoCarga');
    spinner.classList.remove('logoCheck');
    spinner.classList.add('logoCarga');

    setTimeout(e=>{
      spinner.classList.remove('logoCarga');
      spinner.classList.add('logoCheck');
      spinner.classList.remove('animate-spin');
      setTimeout(()=>{
        spinner.classList.add('hidden');
        spinner.classList.add('animate-spin');
        spinner.classList.remove('logoCheck');
        spinner.classList.add('logoCarga');
      },1000)
    },2000)
  }

}

function cambiarFiltrar(){
  //abrir y cerrar pestaña de filtros
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
  //seleccionar categoria 

  //Si el evento se ha lanzado por el boton de buscar se realiza la busqueda
  //con las categorias seleccionadas

  if(evt.target == btnCat){
    pagYCat();
    return
  }
  //marcamos las categorias seleccionadas
  if(evt.target.classList.contains('border-2')){
      evt.target.classList.remove('border-2');
      categoriasSel = categoriasSel.filter(e=>e != evt.target.id);
  }else{
    evt.target.classList.add('border-2');
    categoriasSel.push(evt.target.id)
  }
    

}

function cambPag(evt){
  //inidicamos que pagina se ha seleccionado
  pagYCat(evt.target.value);
}
async function pagYCat(pag = 1){
  //crea el URL de la peticion de datos teniendo en cuenta los filtros, las categorias y la paginación
  let categoriasGet = (categoriasSel[0] && categoriasSel.join('%')) || '';
  let filtrosGet = (checks[0] && checks.join('%')) || '';
  contEventos.innerHTML = '';
  await datos('/buscarEventos?pag='+pag +((valor && '&valor=' + valor ) || '') +((categoriasGet && '&cat=' + categoriasGet ) || '') + ((filtrosGet && '&filt=' + filtrosGet ) || '') + ((gratis && '&gratis='+ gratis)||''))
  

}
async function datos(url) {
  //Solicitud de los eventos según los filtros indicados en la URL
  try {
      let response = await fetch(url);

      if (!response.ok) {
          throw new Error('Error al obtener los divs');
      }
      let data = await response.json();

      // Limpiar eventos existentes
      contEventos.innerHTML = '';

      let boton
      // Añadir nuevos eventos
      await data.eventos.forEach(divHtml => {
          contEventos.insertAdjacentHTML('beforeend', divHtml);
          contEventos.lastElementChild.addEventListener('click',abrirEvento);
          if(contEventos.lastElementChild.lastElementChild.previousElementSibling.lastElementChild && !contEventos.lastElementChild.lastElementChild.previousElementSibling.lastElementChild.id)
            contEventos.lastElementChild.lastElementChild.previousElementSibling.lastElementChild.firstElementChild.addEventListener('click',aniadirEvento)
          contEventos.insertAdjacentHTML('beforeend', '<hr class="my-6">');

      });
      if(contEventos.innerHTML == '')
        contEventos.innerHTML = '<p class="text-center text-colorLetra">No se ha encontrado ningún evento</p>';

      // Actualizar paginación
      contsPag.forEach(e=>actualizarPaginacion(data.currentPage, data.totalPages,e));

  } catch (error) {
      console.error('Error:', error);
  }
}
function actualizarPaginacion(currentPage, totalPages, contenedor) {
  //Creamos la paginación guardando la información de a que página corresponde
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
function abrirEvento(evt){
  //Mostrar más información del evento
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
}
function initMap(){
  //Creamos el mapa correspondiente al evento
  let coordsArr = [];
  coordsArr = mapa.id.split('|');
  if(!Number(coordsArr[0]))return
  coords = {lat:Number(coordsArr[0]),lng:Number(coordsArr[1])}

  let map = new google.maps.Map(mapa,{
    zoom:18,
    center: coords,
  });
  let marker = new google.maps.Marker({
    map,
    position:coords,
    
  });
}

function desplegCat(evt){
  //mostrar y quitar barra de categorias
    if(categorias.style.display == 'none'){
        categorias.style.display = 'flex';
        categorias.nextElementSibling.classList.add('basis-3/4')
        evt.target.style.backgroundImage = 'url(../../imagenes/imagenesEventos/cancel.svg)'

    }else{
        categorias.style.display = 'none';
        
        categorias.nextElementSibling.classList.remove('basis-3/4')
        
        categorias.nextElementSibling.classList.add('basis-4/4')
        evt.target.style.backgroundImage = 'url(../../imagenes/imagenesEventos/burger.svg)'
    }
}
function buscador(evt){
  //al empezar a buscar en el buscador se despliega una lista donde se van
  //añadiendo los titulos de evento según se va escribiendo en el buscado
  contBuscador.style.borderBottomRightRadius = '0px';
  contBuscador.style.borderBottomLeftRadius = '0px';
  contBuscador.nextElementSibling.classList.remove('hidden');
  contBuscador.nextElementSibling.classList.add('flex');
  contBuscador.nextElementSibling.innerHTML = '';
  buscar(evt.target.value);

}

function cerrarBuscador(evt){
  //cerramos el buscador, quitando las opciones de evento que se están buscando
  if(evt.relatedTarget && evt.relatedTarget.id == 'btnBuscar')return
  contBuscador.style.borderBottomRightRadius = '1rem';
  contBuscador.style.borderBottomLeftRadius = '1rem';
  contBuscador.nextElementSibling.classList.remove('flex');
  contBuscador.nextElementSibling.classList.add('hidden');
  contBuscador.nextElementSibling.innerHTML = '';
}
async function buscar(valor){
  //Petición a la API del servidor con el valor actual del buscador
  //y se muestran en la lista que habiamos desplegado
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
  //Petición a API con la información de del buscador
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

  async function salir(){
    //guardar pestaña en la que se sale para que al entrar se abra en esa
    let actual;
    contenedores.forEach(e=>{
      if(e.classList.contains('mostrar'))
        actual = e.id;
    });
    let referencia = window.location.href;
    referencia = referencia.replaceAll('https://bewids.blasast.me/','');

    if(actual){
      const formData = new FormData();
      formData.append('actual', actual);
      formData.append('pagina', referencia);
      formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));  // Añade el token CSRF

      navigator.sendBeacon('/salir', formData);

    }
    
  }