addEventListener('DOMContentLoaded',()=>{
    move();
})

function move(){
    let botonP= document.querySelector('.bperfil')
    let botonS= document.querySelector('.bsesiones');
    let sesiones=document.querySelector('.sesiones');
    let perfil=document.querySelector('.perfil')
    botonS.addEventListener('click',()=>{
        sesiones.style.display='block';
        perfil.style.display='none';
        botonP.style.display='block'
        botonS.style.display='none';
    })
    botonP.addEventListener('click',()=>{
        sesiones.style.display='none';
        perfil.style.display='block';
        botonP.style.display='none'
        botonS.style.display='block';
    })
}