<figure class="btnCE absolute bottom-[3%] left-[5%] w-16 z-20">
    <img src="{{asset('imagenes/imagenesBasic/chat2.png')}}" alt="">
</figure>
<figure class="btnCE2 absolute bottom-[3%] left-[14%] w-16 z-20">
    <img src="{{asset('imagenes/imagenesBasic/encuestas.png')}}" alt="">
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