@include('vistas2.portal')
@php
$portal=Session::get('portal');
    use App\Models\Participantes;
    $participantes=Participantes::where('id_portal',$portal->id)->where('id_usuario',NULL)->get();
@endphp
@if (Session::get('invitacion')=='newPar')
   
<div class="absolute top-0 w-full h-full flex justify-center items-center bg-colorFondo bg-opacity-60">

    <div class="bg-colorDetalles w-[50%] max-h-[50%] text-center overflow-y-scroll border-2 border-black"> 
    <h2 class="sticky top-0 bg-colorSecundario text-colorLetra p-5 ">¿Eres alguno de estos participantes?</h2>
        @foreach($participantes as $participante)
                <button value={{$participante->nombre_en_portal}} class="bg-colorMain text-colorLetra w-full h-16 border-t-2 border-b-1 border-black hover:bg-colorCaberaTras btnPart">{{$participante->nombre_en_portal}}</button>
        @endforeach
        <div class="sticky bottom-0 w-full flex items-center text-colorLetra space-x-4">
            <button class=" bg-colorComplem  btnNuevo inline-block p-5 hover:bg-blue-600">Crear nuevo participante</button>
            <label for="">Nombre:</label>
            <input type="text" class="mx-auto bg-colorCabera rounded-xl indent-3 nombreNuevo">

        </div>

    </div>

</div>

@else
<div class="enlace z-10 absolute top-0 h-full w-full flex justify-center items-center bg-colorFondo bg-opacity-60">
    <div class="bg-colorSecundario text-white w-[60%] h-[30%] text-center flex flex-col justify-start rounded-xl">
        <figure class="w-10 volverPortal">
            <img class=" inline-block" src="{{asset('imagenes/imagenesBasic/cerrar.png')}}" alt="">
        </figure>
        <h1  class="inline-block ">Atención:</h1>

        <p>El siguiente enlace permitirá unirse a cualquier persona que contenga este link.</p>
        <p>Compartalo solo con las personas que considere necesario</p>
        <hr class="mt-6">
        <div class="direccionInvitacion hover:text-blue-500 flex justify-center items-center grow">
            <h1 class="copiarEnlace">http://127.0.0.1:8000/invitacion/{{$portal->token_portal}}</h1>
            <div>
                
            </div>
        </div>
    </div>
</div>

<script>
    setTimeout(() => {
        window.location.href = '/cerrarEnlace';
    }, 10000)
    document.querySelector('.volverPortal').addEventListener('click',()=>{
        window.location.href='/cerrarEnlace';
    })
</script>
@endif