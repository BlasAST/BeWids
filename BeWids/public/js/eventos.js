document.addEventListener('DOMContentLoaded', iniciar);
let categorias;
let listaTerm = [
    '206974-0-agenda-eventos-culturales-100.json'
]

async function iniciar(){
    document.querySelector('.btnBurger').addEventListener('click', desplegCat);
    categorias = document.querySelector('.categorias');
    document.querySelector('.buscador').addEventListener('input',buscador);
     await adquirirEventos();

    
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