@extends ('partials.basePortales')
@section('rutaJs','js/portal.js')
@section('contenido')
@php
    use App\Models\Participantes;  
    $portal = Session::get('portal');
    $yo = Session::get('participanteUser');
    $participantesLibres=Participantes::where('id_portal',$portal->id)->where('id_usuario',NULL)->get();
    $participantes=Participantes::where('id_portal',$portal->id)->get();

@endphp
<div class="contenedor md:h-screen w-screen grid grid-cols-1 auto-rows-auto md:grid-cols-12 md:grid-rows-12 gap-4 relative px-[2.5%] py-[4dvh]">

    {{-- CUENTAS --}}
    <div class="row-start-5 min-h-[500px] md:col-start-1 md:col-span-3 md:row-start-2 md:row-span-9 h-full flex flex-col self-end gap-6 items-center w-[75%] justify-self-center">
        <div class="bg-colorCaberaTras border-colorCabera border-2 justify-self-center flex items-stretch grow w-full">
            @include('partials.contabilidadMini')
        </div>
        <button id="contabilidad" class="btn text-center py-1 w-1/2 rounded-2xl bg-colorCabera text-colorLetra">Ir a Gastos</button>
    </div>


    {{-- TITULO --}}
    <div class="text-white flex flex-col row-start-2 md:col-start-5 md:col-span-4 md:row-start-1 md:row-span-2 gap-2 h-[75%]" style="color:{{$portal->color_titulo}}" >
        @if($usuario)<h1 class=" text-shadow">{{$yo->nombre_en_portal}} en</h1>@endif
        <h1 class="text-center text-4xl">{{$portal->nombre}}</h1>
    </div>


    {{-- SALIR Y AJUSTES--}}
    <div class="flex justify-end items-center md:justify-around row-start-1 md:col-start-10 md:col-span-3 md:row-start-1 gap-2">
        <button class="closeSession text-base font-bold border-b-2 hover:border-b-black">Salir del portal</button>
        <figure class="w-10 relative btnAjustes cursor-pointer"><img src="{{asset('imagenes/imagenesBasic/ajustes2.png')}}" alt=""></figure>
    </div>



    {{-- EVENTOS --}}
    <div class=" md:col-start-10 row-start-4 min-h-[400px] md:min-h-0  md:col-span-3 md:row-start-2 md:row-span-6 w-[75%] justify-self-center h-full">
        <div class="bg-colorCaberaTras border-colorCabera border-2  flex flex-col items-center text-colorLetra h-full">
            @if (count($eventos) == 0)
                <h1 class="text-center text-colorLetra mt-6">No se han añadido eventos a la lista</h1>
            @endif
            <div class="grow w-full flex flex-col items-center gap-2 pt-2 overflow-y-auto">
                @foreach ($eventos as $evt)
                @php
                    $cat = explode(" ",$evt['categoria'])[0] . ".jpg"   
                @endphp
                    <div class="flex w-10/12 bg-colorComplem rounded-2xl">
                        <figure class="basis-3/12 m-0 imagenEvento rounded-l-2xl" style="background-image: url('{{ asset('imagenes/imagenesEventos/' . $cat) }}');"></figure>
                        <h1 class="py-4  px-1 text-center basis-9/12  whitespace-nowrap overflow-hidden text-ellipsis">{{$evt['titulo']}}</h1>
                    </div>
                @endforeach

            </div>
            <button id='eventos' class="btn bg-colorDetallesTras rounded-2xl w-3/4 my-4">Ir a Eventos</button>
        </div>
    </div>



    {{-- CALENDARIO --}}
    <div  id="calendario" class=" btn cursor-pointer row-start-3 md:col-start-4 md:col-span-6 md:row-start-7 md:row-span-5 md:h-full md:mt-5">
        <div class="h-full w-full">
            @include('partials.calendarioMini')
        </div>
    </div>



    {{-- PARTICIPANTES --}}
    <div class="bg-colorCaberaTras border-colorCabera border-2 text-colorLetra flex flex-col text-center px-2 py-1 row-start-6 min-h-[300px] md:min-h-0 md:col-start-10 md:col-span-3 md:row-start-8 md:row-span-4 w-[75%] justify-self-center">
        <h1>Participantes</h1>
        <div class="grow overflow-y-auto py-2 gap-2 ">
            @foreach ($participantes as $part)
                <h2 class="text-colorComplem text-left whitespace-nowrap overflow-hidden text-ellipsis">{{$part->nombre_en_portal}}</h2>
            @endforeach
        </div>
        <button id="participantes" class="rounded-xl bg-colorDetalles btn">Ver Participantes</button>
    </div>


    {{-- ENLACE INVITACION --}}
    @if ($usuario && ($yo->admin || !$ajustes->crear_invitacion))
        <div class= " absolute top-5 left-5 bg-colorCabera rounded-xl px-3  md:top-auto md:left-auto md:bottom-5 md:right-5 mr-5 text-colorComplem underline cursor-pointer decoration-colorCabera btnEnlace">Enlace de invitación</div>
    @endif



    <figure class=" btnCE hidden row-[10] col-[1] w-[80%] h-[40%] self-end mb-5 justify-self-center">
        <img src="{{asset('imagenes/imagenesBasic/chat2.png')}}" alt="">
    </figure>
    <figure class=" btnCE2 hidden row-[10] col-[2] w-[80%] h-[40%] self-end mb-5 justify-self-end">
        <img src="{{asset('imagenes/imagenesBasic/encuestas.png')}}" alt="">
    </figure>
   


    <div class="w-[min(50%,300px)] hidden h-full absolute right-0 bg-colorBarra2 ajustes flex-col items-start pt-3 text-colorLetra">
        <figure class="w-7 h-7 ml-2 logoCancel btnAjustes cursor-pointer"></figure>
        <h1 class="text-3xl self-center mb-2">AJUSTES</h1>
        <div class="grow w-full flex-col flex overflow-y-auto gap-3">
            @if ($usuario && ($yo->admin || !$ajustes->pers_portal))
                <h1 class="self-start ml-2 text-xl text-colorComplem">Personalizar Portal</h1>
                <form class="flex flex-col gap-2 w-full text-sm px-1 py-2" action="/personalizar" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="nombre">Cambiar nombre:</label>
                    <input type="text" name="nombre" class=" indent-2 bg-colorCabera w-3/4 rounded-xl self-center">
                    <label for="color">Color título:</label>
                    <input type="color" name="color" value="{{$portal->color_titulo}}">
                    <label for="foto">Cambiar fondo:</label>
                    <input type="file" name="foto" accept="image/*">
                    <button type="submit" class="bg-colorCabera w-3/4 self-center mt-4">Modificar</button>
                </form>
            @endif
            @if ($usuario && $yo->admin)
                <h1 class="self-start ml-2 text-xl text-colorComplem">Permisos</h1>
                <div class="flex flex-col text-sm items-center gap-6 py-3">

                    <div class="flex flex-wrap gap-2 justify-center">
                        <p class="basis-full text-center">Crear Participante</p>
                        <p class="text-black">Todos</p>
                        <label for="crear_participante" class="relative inline-block w-12 h-6">
                            <input type="checkbox" id="crear_participante" @if($ajustes->crear_participante)checked @endif class="sr-only peer">
                            <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                            <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                        </label>
                        <p class="text-colorComplem">Admins</p>
                    </div>

                    <div class="flex flex-wrap gap-2 justify-center">
                        <p class="basis-full text-center">Crear Enlace de Invitación</p>
                        <p class="text-black">Todos</p>
                        <label for="crear_invitacion" class="relative inline-block w-12 h-6">
                            <input type="checkbox" id="crear_invitacion" @if($ajustes->crear_invitacion)checked @endif class="sr-only peer">
                            <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                            <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                        </label>
                        <p class="text-colorComplem">Admins</p>
                    </div>

                    <div class="flex flex-wrap gap-2 justify-center">
                        <p class="basis-full text-center">Añadir Gasto</p>
                        <p class="text-black">Todos</p>
                        <label for="aniadir_gasto" class="relative inline-block w-12 h-6">
                            <input type="checkbox" id="aniadir_gasto" @if($ajustes->aniadir_gasto)checked @endif class="sr-only peer">
                            <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                            <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                        </label>
                        <p class="text-colorComplem">Admins</p>
                    </div>

                    <div class="flex flex-wrap gap-2 justify-center">
                        <p class="basis-full text-center">Añadir Evento a "Nuestra Lista"</p>
                        <p class="text-black">Todos</p>
                        <label for="aniadir_lista" class="relative inline-block w-12 h-6">
                            <input type="checkbox" id="aniadir_lista" @if($ajustes->aniadir_lista)checked @endif class="sr-only peer">
                            <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                            <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                        </label>
                        <p class="text-colorComplem">Admins</p>
                    </div>

                    <div class="flex flex-wrap gap-2 justify-center">
                        <p class="basis-full text-center">Añadir Evento a Calendario</p>
                        <p class="text-black">Todos</p>
                        <label for="aniadir_cal" class="relative inline-block w-12 h-6">
                            <input type="checkbox" id="aniadir_cal" @if($ajustes->aniadir_cal)checked @endif class="sr-only peer">
                            <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                            <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                        </label>
                        <p class="text-colorComplem">Admins</p>
                    </div>

                    <div class="flex flex-wrap gap-2 justify-center">
                        <p class="basis-full text-center">Crear Encuesta</p>
                        <p class="text-black">Todos</p>
                        <label for="crear_encuesta" class="relative inline-block w-12 h-6">
                            <input type="checkbox" id="crear_encuesta" @if($ajustes->crear_encuesta)checked @endif class="sr-only peer">
                            <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                            <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                        </label>
                        <p class="text-colorComplem">Admins</p>
                    </div>

                    <div class="flex flex-wrap gap-2 justify-center">
                        <p class="basis-full text-center">Modificar Calendario</p>
                        <p class="text-black">Todos</p>
                        <label for="modif_cal" class="relative inline-block w-12 h-6">
                            <input type="checkbox" id="modif_cal" @if($ajustes->modif_cal)checked @endif class="sr-only peer">
                            <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                            <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                        </label>
                        <p class="text-colorComplem">Admins</p>
                    </div>

                    <div class="flex flex-wrap gap-2 justify-center">
                        <p class="basis-full text-center">Personalizar Portal</p>
                        <p class="text-black">Todos</p>
                        <label for="pers_portal" class="relative inline-block w-12 h-6">
                            <input type="checkbox" id="pers_portal" @if($ajustes->pers_portal)checked @endif class="sr-only peer">
                            <span class="block w-full h-full bg-colorCabera rounded-full peer-checked:bg-colorComplem"></span>
                            <span class="absolute w-4 h-4 bg-white rounded-full top-1 left-1 peer-checked:left-7 transition-all duration-200"></span>
                        </label>
                        <p class="text-colorComplem">Admins</p>
                    </div>
                </div>    
            @endif
        </div>

        <div class="grow flex flex-col justify-end pb-5">
            
            <h1 class="ml-3 text-lg text-white font-extrabold border-b-4 border-black cursor-pointer btnAband">Abandonar Portal</h1>
            @if ($usuario && $yo->admin)
                <button class="ml-3 mt-4 text-lg bg-colorCabera text-colorDetalles font-extrabold border-2 border-colorDetalles rounded-2xl cursor-pointer">Eliminar Portal</button>   
            @endif
        </div>
    </div>
    <div class="enlace z-10 absolute top-0 h-full w-full hidden justify-center md:items-center pt-40 md:pt-0 items-start bg-colorFondo bg-opacity-60">
        <div class="bg-colorSecundario text-white w-[90%] h-[20%] md:w-[60%] md:h-[50%] text-center flex flex-col justify-start rounded-xl">
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
    <div class="errorElim z-10 absolute top-0 h-full w-full hidden justify-center items-center bg-colorFondo bg-opacity-60">
        <div class="bg-colorSecundario text-white w-[60%] py-10 text-center flex flex-col justify-start rounded-xl gap-5 items-center">
            <h1>No puedes eliminar participantes con cuentas pendientes.</h1>
            <h1>Asegurate de que el participante no deba ni le deban dinero.</h1>
            <button class="bg-colorDetalles w-1/4 rounded-xl btnCerrar">OK</button>
        </div>
    </div>
    <div class="confirmElim z-10 absolute top-0 h-full w-full hidden justify-center items-center bg-colorFondo bg-opacity-60">
        <div class="bg-colorSecundario text-white w-[60%] py-10 text-center flex flex-col justify-start rounded-xl gap-5 items-center">
            <h1>¿Seguro que quieres abandonar el portal?</h1>
            <div class="space-x-4">
                <button class="bg-colorComplem rounded-xl btnAceptElim px-4 py-1 btnConfirm">Sí, abandonar</button>
                <button class="bg-colorCabera rounded-xl btnCerrarElim px-4 py-1 btnCancConfirm">Cancelar</button>
            </div>
        </div>
    </div>

    @if (!$usuario)
        
        <div class="absolute top-0 w-full h-full flex justify-center items-center bg-colorFondo bg-opacity-60">

            <div class="bg-colorDetalles w-[50%] max-h-[50%] text-center overflow-y-scroll border-2 border-black"> 
            <h2 class="sticky top-0 bg-colorSecundario text-colorLetra p-5 ">¿Eres alguno de estos participantes?</h2>
                @foreach($participantesLibres as $participante)
                        <button value={{str_replace(' ','-',$participante->nombre_en_portal)}} class="bg-colorMain text-colorLetra w-full h-16 border-t-2 border-b-1 border-black hover:bg-colorCaberaTras btnPart">{{$participante->nombre_en_portal}}</button>
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