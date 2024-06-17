<figure class="btnCE fixed md:absolute bottom-5 left-5 md:bottom-[3%] md:left-[5%] w-16 z-20">
    <img src="{{asset('imagenes/imagenesBasic/chat.svg')}}" alt="">
</figure>
<figure class="btnCE2 fixed md:absolute bottom-5 left-28 md:bottom-[3%] md:left-[14%] w-16 z-20">
    <img src="{{asset('imagenes/imagenesBasic/encuestas.svg')}}" alt="">
</figure>
<script>
    document.addEventListener('DOMContentLoaded',iniciar);

function iniciar(){
    document.querySelector('.btnCE').addEventListener('click',irChat);
    document.querySelector('.btnCE2').addEventListener('click',irEncuestas);
}
function irChat(){
    window.location.href = '/chat';
 }
 function irEncuestas(){
    window.location.href="/encuestas";
 }
</script>