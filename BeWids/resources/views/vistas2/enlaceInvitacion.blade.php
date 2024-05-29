@include('vistas2.portal')
@php
$portal=Session::get('portal');
    use App\Models\Participantes;
    $participantes=Participantes::where('id_portal',$portal->id)->where('id_usuario',NULL)->get();
@endphp
@if (Session::get('invitacion')!='newPar')
   
<div class="absolute top-0 w-full h-full flex justify-center items-center">

    <div class="bg-colorDetalles w-[50%] h-[50%] text-center flex flex-col overflow-y-scroll"> 
    <h2 class="sticky top-0 bg-colorSecundario text-colorLetra p-5">¿Eres alguno de estos participantes?</h2>
        @foreach($participantes as $participante)
                <button class="bg-red-600">{{$participante->nombre_en_portal}}</button>
        @endforeach
    </div>

</div>

@else
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
            <h1>http://127.0.0.1:8000/portal/{{$portal->token_portal}}</h1>
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
@endif