@extends ('partials.basePortales')
@section('rutaJs','js/portal.js')
@section('contenido')
@php
    use App\Models\Participantes;  
    $portal = Session::get('portal');
    $yo = Session::get('participanteUser');
    $participantes=Participantes::where('id_portal',$portal->id)->where('id_usuario',NULL)->get();
@endphp
<div class="contenedor h-screen w-screen grid grid-cols-12 grid-rows-12 relative">
    <!-- <h1>Benefits With friends</h1>
    <h1>{{$portal->nombre}}</h1>
    <h1>{{$portal->id}}</h1> -->
    @if($usuario)<h1 class="col-[1/3] row-[1] text-colorLetra flex justify-center items-center">{{$yo->nombre_en_portal}} en</h1>@endif
    <h1 class="row-[1/3] col-[2/4] flex justify-center items-center text-4xl text-colorLetra">{{$portal->nombre}}</h1>
    <div  id="calendario" class=" cursor-pointer btn bg-blue-800 row-[1/5] col-[5/10] w-[100%] h-[80%] self-center justify-self-center">
        @include('partials.calendarioMini')
    </div>
    <div class=" row-[1/2] col-[10/13] h-[70%] flex items-center justify-around">
        <button class="closeSession text-base font-bold border-b-2 hover:border-b-black">Salir del portal</button>
        <figure class="w-10 relative z-20 btnAjustes"><img src="{{asset('imagenes/imagenesBasic/ajustes2.png')}}" alt=""></figure>
    </div>
    <div class="bg-colorCaberaTras border-colorCabera border-2 row-[2/8] col-[10/13] w-[70%] justify-self-center flex flex-col items-center text-colorLetra">
        <div class="grow w-full flex flex-col-reverse justify-end items-center gap-2 py-2 overflow-y-auto">
            @foreach ($eventos as $evt)
            @php
                $cat = explode(" ",$evt->categoria)[0] . ".jpg";   
            @endphp
                <div class="flex w-10/12 bg-colorComplem rounded-2xl">
                    <figure class="basis-3/12 m-0 imagenEvento rounded-l-2xl" style="background-image: url('{{ asset('imagenes/imagenesEventos/' . $cat) }}');"></figure>
                    <h1 class="py-4  px-1 text-center basis-9/12  whitespace-nowrap overflow-hidden text-ellipsis">{{$evt->titulo}}</h1>
                </div>
            @endforeach

        </div>
        <button class="btnEv bg-colorDetallesTras rounded-2xl w-3/4">Ir a Eventos</button>
    </div>
    <div class="bg-gray-800 row-[8/13] col-[7/13] w-[80%] h-[70%] self-center justify-self-center">
        Mapa
    </div>
    <div class= " absolute bottom-0 right-0 mr-5 text-colorComplem underline cursor-pointer decoration-colorCabera btnEnlace">Enlace de invitación</div>
    <div class="bg-pink-800 row-[5/12] col-[4/7] w-[100%] h-[40%] self-end">Planificador</div>
    <figure class=" btnCE row-[10/13] col-[1/2] w-[80%] h-[40%] self-end mb-5 justify-self-center">
        <img src="{{asset('imagenes/imagenesBasic/chat2.png')}}" alt="">
    </figure>
    <figure class=" btnCE2 row-[10/13] col-[2/3] w-[80%] h-[40%] self-end mb-5 justify-self-end">
        <img src="{{asset('imagenes/imagenesBasic/encuestas.png')}}" alt="">
    </figure>
    <div class="bg-colorCaberaTras row-[3/9] col-[1/5] w-3/4 justify-self-center flex items-stretch border-colorCabera border-2">
        @include('partials.contabilidadMini')
    </div>
    <div class="row-[9] col-[2/4] flex flex-col items-center justify-center">
        <button class="btnGastos px-6 py-1 rounded-2xl bg-colorCabera text-colorLetra">Ir a Gastos</button>
    </div>
    <div class="w-[min(50%,300px)] hidden min-h-full absolute right-0 bg-colorBarra2 ajustes flex-col items-start pt-3 text-colorLetra">
        <h1 class="text-3xl self-center mb-2">AJUSTES</h1>
        <h1 class="self-start ml-2 text-xl text-colorComplem">Personalizar Portal</h1>
        <div class="flex flex-col gap-2 w-full text-sm px-1 py-2">
            <label for="nombre">Cambiar nombre:</label>
            <input type="text" name="nombre" class=" indent-2 bg-colorCabera w-3/4 rounded-xl self-center">
            <label for="img">Cambiar fondo:</label>
            <input type="file">
        </div>
        <h1 class="self-start ml-2 text-xl text-colorComplem">Permisos</h1>
        <div class="flex flex-col text-sm items-center gap-3 py-2">

            <div class="flex flex-wrap gap-2 justify-center">
                <p class="basis-full text-center">Crear Participante</p>
                <p class="text-black">Todos</p>
                <label for="crearPart" class="relative inline-block w-12 h-6">
                    <input type="checkbox" id="crearPart" class="sr-only peer">
                    <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                    <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                </label>
                <p class="text-colorComplem">Admins</p>
            </div>

            <div class="flex flex-wrap gap-2 justify-center">
                <p class="basis-full text-center">Crear Enlace de Invitación</p>
                <p class="text-black">Todos</p>
                <label for="crearLink" class="relative inline-block w-12 h-6">
                    <input type="checkbox" id="crearLink" class="sr-only peer">
                    <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                    <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                </label>
                <p class="text-colorComplem">Admins</p>
            </div>

            <div class="flex flex-wrap gap-2 justify-center">
                <p class="basis-full text-center">Añadir Gasto</p>
                <p class="text-black">Todos</p>
                <label for="crearGasto" class="relative inline-block w-12 h-6">
                    <input type="checkbox" id="crearGasto" class="sr-only peer">
                    <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                    <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                </label>
                <p class="text-colorComplem">Admins</p>
            </div>

            <div class="flex flex-wrap gap-2 justify-center">
                <p class="basis-full text-center">Añadir Evento a "Nuestra Lista"</p>
                <p class="text-black">Todos</p>
                <label for="evtList" class="relative inline-block w-12 h-6">
                    <input type="checkbox" id="evtList" class="sr-only peer">
                    <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                    <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                </label>
                <p class="text-colorComplem">Admins</p>
            </div>

            <div class="flex flex-wrap gap-2 justify-center">
                <p class="basis-full text-center">Añadir Evento a Calendario</p>
                <p class="text-black">Todos</p>
                <label for="evtCal" class="relative inline-block w-12 h-6">
                    <input type="checkbox" id="evtCal" class="sr-only peer">
                    <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                    <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                </label>
                <p class="text-colorComplem">Admins</p>
            </div>

            <div class="flex flex-wrap gap-2 justify-center">
                <p class="basis-full text-center">Crear Encuesta</p>
                <p class="text-black">Todos</p>
                <label for="crearEncuesta" class="relative inline-block w-12 h-6">
                    <input type="checkbox" id="crearEncuesta" class="sr-only peer">
                    <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                    <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                </label>
                <p class="text-colorComplem">Admins</p>
            </div>

            <div class="flex flex-wrap gap-2 justify-center">
                <p class="basis-full text-center">Personalizar Portal</p>
                <p class="text-black">Todos</p>
                <label for="PersPort" class="relative inline-block w-12 h-6">
                    <input type="checkbox" id="PersPort" class="sr-only peer">
                    <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                    <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                </label>
                <p class="text-colorComplem">Admins</p>
            </div>
        </div>

        <div class="grow flex flex-col justify-end pb-5">
            <h1 class="ml-3 text-lg text-white font-extrabold border-b-4 border-black cursor-pointer">Abandonar Portal</h1>
        </div>
    </div>
    <div class="enlace z-10 absolute top-0 h-full w-full hidden justify-center items-center bg-colorFondo bg-opacity-60">
        <div class="bg-colorSecundario text-white w-[60%] h-[50%] text-center flex flex-col justify-start rounded-xl">
            <figure class="w-10 volverPortal">
                <img class=" inline-block" src="{{asset('imagenes/imagenesBasic/cerrar.png')}}" alt="">
            </figure>
            <h1  class="inline-block ">Atención:</h1>
    
            <p>El siguiente enlace permitirá unirse a cualquier persona que contenga este link.</p>
            <p>Compartalo solo con las personas que considere necesario</p>
            <hr class="mt-6">
            <div class="direccionInvitacion hover:text-blue-500 flex justify-center items-center grow">
                <h1 class=""></h1>
            </div>
        </div>
    </div>

    @if (!$usuario)
        
        <div class="absolute top-0 w-full h-full flex justify-center items-center bg-colorFondo bg-opacity-60">

            <div class="bg-colorDetalles w-[50%] max-h-[50%] text-center overflow-y-scroll border-2 border-black"> 
            <h2 class="sticky top-0 bg-colorSecundario text-colorLetra p-5 ">¿Eres alguno de estos participantes?</h2>
                @foreach($participantes as $participante)
                        <button value={{$participante->nombre_en_portal}} class="bg-colorMain text-colorLetra w-full h-16 border-t-2 border-b-1 border-black hover:bg-colorCaberaTras btnPart">{{$participante->nombre_en_portal}}</button>
                @endforeach
                <div class="sticky bottom-0 w-full flex items-center text-colorLetra space-x-4 bg-colorDetalles pr-2">
                    <button class=" bg-colorComplem  btnNuevo inline-block p-5 hover:bg-blue-600">Crear nuevo participante</button>
                    <label for="">Nombre:</label>
                    <input type="text" class="mx-auto bg-colorCabera rounded-xl indent-3 nombreNuevo">
        
                </div>
        
            </div>
        
        </div>       
    @endif

</div>
@endsection