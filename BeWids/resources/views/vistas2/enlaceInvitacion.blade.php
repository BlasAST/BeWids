@include('vistas2.portal')
<div class="enlace z-10 absolute top-0 h-full w-full flex justify-center items-center bg-colorFondo bg-opacity-60">
    <div class="bg-colorSecundario text-white w-[60%] h-[40%] text-center flex flex-col justify-start rounded-xl">
        <figure class="w-10 volverPortal">
            <img class=" inline-block" src="{{asset('imagenes/imagenesBasic/cerrar.png')}}" alt="">
        </figure>
        <h1 class="inline-block">Atención:</h1>

        <p>El siguiente enlace permitirá unirse a cualquier persona que contenga este link.</p>
        <p>Compartalo solo con las personas que considere necesario</p>
        <br>
        <hr>
        <br>
        <div class="direccionInvitacion hover:text-blue-500">
            <h1>http://127.0.0.1:8000/portal/{{$invitacion}}</h1>
        </div>
    </div>
</div>

<script>
    setTimeout(() => {
        window.location.href = '/portal';
    }, 10000)
    document.querySelector('.volverPortal').addEventListener('click',()=>{
        window.location.href='/portal';
    })
</script>