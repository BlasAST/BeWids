@extends('partials/plantillaServicios')
@section('rutaJs','js/cuentas.js')
@section('categorias')
@php

    use App\Models\Participantes;

    $gastos = Session::get('gastos');
    $portal = Session::get('portal');
    $reembolsosPorPagar = Session::get('reembolsosSin');
    $reembolsosPagados = Session::get('reembolsosPagados');
    $participantes = Session::get('participantes');
    $participanteUser = Session::get('participanteUser');
    $notificaciones = Session::get('notificaciones');
    $ajustes = Session::get('ajustes');
    $tipos = ['Supermercado','Alcohol','Cine','Conciento','ropa','pepe'];
    $deudaMayor = 0;
    foreach ($participantes as $participante) {
        if(abs($participante->deuda)>$deudaMayor)
            $deudaMayor = abs($participante->deuda);
    }

    $pantalla = Session::get('contabilidad');
 
@endphp
@include('componentes.componenteChatEncuestas')
<div id="gastosCat" class="flex-grow flex justify-center cursor-pointer hover:text-colorDetalles">
    <span class="h-full flex flex-col justify-center @if($pantalla == 'gastos'||!$pantalla) border-b-4 border-white  selected @endif">Gastos</span>
</div>
<div id= "graficosCat"class="flex-grow flex justify-center cursor-pointer  hover:text-colorDetalles">
    <span class="h-full flex flex-col justify-center @if($pantalla == 'graficos') border-b-4 border-white  selected @endif ">Gráficos</span>
</div>
<div id= "cuentasCat"class="flex-grow flex justify-center cursor-pointer  hover:text-colorDetalles">
    <span class="h-full flex flex-col justify-center @if($pantalla == 'cuentas') border-b-4 border-white  selected @endif ">Cuentas</span>
</div>
<div id= "notificacionesCat"class="flex-grow flex justify-center cursor-pointer  hover:text-colorDetalles">
    <span class="h-full flex flex-col justify-center @if($pantalla == 'notificaciones') border-b-4 border-white  selected @endif ">Notificaciones</span>
</div>

@endsection
@section('contenidoServicio')


<section id="gastos" class="@if($pantalla == 'gastos' || !$pantalla) mostrar flex @else hidden @endif items-stretch h-full flex-col lg:flex-row-reverse text-colorLetra w-full p-3 space-y-5 lg:space-y-0"> 
    @if ($participanteUser->admin || !$ajustes->aniadir_gasto)
        <div class="lg:basis-1/2">

            <div class="w-full sm:w-[90%] mx-auto lg:h-[95%] lg:overflow-auto border-colorLetra border px-7 py-6 rounded-xl">
                <h1 class="text-xl text-center">Añadir gasto</h1>
                <form action="{{route('aniadirGasto')}}" method="POST" autocomplete="off" class="flex flex-col mt-4 gap-2 formGasto">
                    @csrf
                    <label for="titulo">Título:</label>
                    <input type="text" name="titulo" class="bg-transparent border-b border-colorLetra focus:outline-none focus:border-colorComplem inputG">
                
                    <label for="tipo">Tipo:</label>
                    <select name="tipo" class="bg-transparent border-b border-colorLetra focus:outline-none focus:border-colorComplem inputG">
                        @foreach($tipos as $tipo)
                            <option class="text-colorCabera" value="{{$tipo}}">{{$tipo}}</option>
                        @endforeach
                    </select>
                    <label for="cantidad">Cantidad</label>
                    <input type="number" step="0.01" min="0" name="cantidad" class="bg-transparent border-b border-colorLetra focus:outline-none focus:border-colorComplem inputG">
                
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" class="bg-transparent border-b border-colorLetra focus:outline-none focus:border-colorComplem inputG">
                
                
                    <label for="pagador">Pagado por:</label>
                    <select name="pagador" class="bg-transparent border-b border-colorLetra focus:outline-none focus:border-colorComplem inputG">
                        @foreach($participantes as $user)
                            <option class="text-colorCabera" value="{{$user->nombre_en_portal}}" @if(Auth::id() == $user->id_usuario)
                                selected
                            @endif>{{$user->nombre_en_portal}}</option>
                        @endforeach
                    </select>
                
                
                    <label for="">A pagar por:</label>
                    <div class="participantes">
                        @foreach ($participantes as $user)
                        <div>
                            <input type="checkbox" value="{{$user->nombre_en_portal}}" name="participantes[]" class="bg-transparent border-b border-colorLetra focus:outline-none focus:border-colorComplem peer checkBoxes">
                            <label for="participantes[]" class="peer-checked:text-colorComplem">{{$user->nombre_en_portal}}</label>
                        </div>
                        @endforeach
                    </div>
                    
                    <button class="bg-colorCabera w-1/2 rounded-xl self-center hover:text-colorComplem">enviar</button>

                    <p class="text-colorDetalles hidden">Rellena todos los campos antes de crear el gasto</p>

                </form>
            </div>
        </div>
    @endif
    <div class="lg:basis-1/2 grow">
        <div class="w-full sm:w-[min(90%,1200px)] lg:h-[95%] lg:overflow-auto border border-colorComplem  px-6 md:px-12 py-6 rounded-xl space-y-2 mx-auto">
            @if (count($gastos) == 0)
                <h1 class="text-center">No se ha realizado ningún gasto aún</h1>                
            @endif
            @foreach ($gastos as $gasto)
                <div class="gasto bg-colorCabera rounded-2xl flex flex-wrap py-2 space-y-1 ">
                    <p class="basis-1/2 overflow-hidden text-ellipsis text-center">{{$gasto['titulo']}}</p>
                    <p class="basis-1/2 text-center">{{$gasto['cantidad']}}€</p>
                    <p class="basis-1/2  text-center ">Pagado por:</p>
                    <p class="basis-1/2 text-center whitespace-nowrap overflow-hidden text-ellipsis"> {{$gasto['pagado_por']}}</p>
                    <div class="hidden basis-full flex-wrap pt-2 space-y-2 text-xs px-2 sm:px-6">
                        <hr class="basis-full border-colorCabera ">
                        <p class="basis-1/2">Tipo: {{$gasto['tipo']}}</p>
                        <p class="basis-1/2">Fecha: {{$gasto['fecha']}}</p>
                        <p class="basis-1/2">Creado por:</p>
                        <p class="basis-1/2">{{Participantes::where('id_portal',$portal->id)->where('id_usuario', $gasto['creado_por'])->pluck('nombre_en_portal')->first()}}</p>
                        <p class="basis-1/2 mt-2">Participantes</p>
                        <div class="basis-3/4 flex flex-wrap mx-auto">
                        @foreach (explode(';',$gasto['participantes']) as $participante)
                            <p class="basis-1/2 text-center whitespace-nowrap overflow-hidden text-ellipsis"> {{$participante}}</p>
                            
                        @endforeach

                        </div>


                    </div>
                    
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="graficos" class="@if($pantalla == 'graficos') mostrar flex @else hidden @endif flex-col md:flex-row w-full  contenedor h-full items-stretch text-colorLetra overflow-auto px-12 md:px-4 md:py-12 md:justify-evenly">

            @foreach ($participantes as $participante)
                @php
                    if($deudaMayor != 0) $porcentaje = (abs($participante->deuda)/$deudaMayor)*100;
                @endphp
            <div class="flex flex-col-reverse md:flex-col items-stretch min-h md:min-h-16 md:items-center gap-4">
                @if ($participante->deuda > 0)
                    <div class="grow md:w-[20px] lg:w-[40px] h-[30px] md:h-auto relative positivo" data-porcentaje="{{$porcentaje}}">
                        <figure class="absolute bg-colorCaberaTras2 w-[55px] h-[55px] lg:w-[75px] lg:h-[75px] rounded-full translate-y-[50%] md:translate-x-[-50%] bottom-[50%] md:left-[50%] flex flex-col justify-center items-center text-[#4465B8]"><p>{{"+".$participante->deuda}}</p></figure>
                    </div>
                @endif
                @if($participante->deuda < 0)
                    <div class="grow md:w-[20px] lg:w-[40px] h-[30px] md:h-auto relative negativo" data-porcentaje="{{$porcentaje}}">
                        <figure class="absolute bg-colorCaberaTras2 w-[55px] h-[55px] lg:w-[75px] lg:h-[75px] rounded-full translate-y-[50%] md:translate-x-[-50%] bottom-[50%] md:left-[50%] flex flex-col justify-center items-center text-[#D63865]"><p>{{$participante->deuda}}</p></figure>
                    </div>
                @endif
                @if($participante->deuda == 0)
                    <div class="grow md:w-[20px] lg:w-[40px] h-[30px] md:h-auto relative bg-colorCabera">
                        <figure class="absolute bg-colorCaberaTras2 w-[55px] h-[55px] lg:w-[75px] lg:h-[75px] rounded-full translate-y-[50%] translate-x-[-50%] bottom-[50%] md:left-[50%] left-0 md:bottom-0 flex flex-col justify-center items-center  text-colorComplem"><p>+{{$participante->deuda}}</p></figure>
                    </div>
                @endif
                <p class="mt-5">{{$participante->nombre_en_portal}}</p>
            </div>
        @endforeach

</section>

<section id="cuentas" class="@if($pantalla == 'cuentas') mostrar flex @else hidden @endif flex-col lg:flex-row w-full h-full py-5 contenedor text-colorLetra space-y-10 lg:space-y-0">

    <div class="lg:basis-1/2 lg:h-[95%] lg:overflow-auto flex flex-col items-center gap-3">
        <h1 class="text-3xl text-colorDetalles mb-2">Reembolsos Pendientes</h1>
        @if (count($reembolsosPorPagar)==0)
            <h1>No se hay nada que reembolsar actualmente</h1>
        @else
            @foreach ($reembolsosPorPagar as $reembolso)
                <div class="reembolso  w-[80%] bg-colorComplem rounded-xl text-center flex flex-col gap-2 pb-2">
                    @if ($reembolso -> solicitado)
                        <span class="bg-green-200 rounded-t-xl border-2 border-green-600 text-colorCabera">Solicitado</span>
                        
                    @endif
                    <p>{{$reembolso->pagador}} tiene que rembolsar a {{$reembolso->receptor}}</p>
                    <p>Cantidad: {{abs($reembolso->cantidad)}}€</p>
                    @if (!$reembolso -> solicitado)
                        <button class="bg-colorDetalles w-1/2 mx-auto mt-2 rounded-xl">Saldar deuda
                            <form method="POST" action="{{route('reembolso')}}">
                                @csrf
                                <input type="hidden" name="reembolso" value="{{json_encode($reembolso)}}">
                            </form>
                        </button>
                    @endif
                </div>
            @endforeach
        @endif
        
    </div>
    <div class="lg:basis-1/2 lg:border-l-2 lg:h-[95%] lg:overflow-auto border-colorLetra flex flex-col items-center gap-2">
        <h1 class="text-3xl text-colorComplem mb-2">Reembolsos Realizados</h1>
        @if (count($reembolsosPagados)==0)
            <h1>No se ha realizado ningún reembolso aún</h1>
        @else
            @foreach ($reembolsosPagados as $reembolso)
                <div class="reembolso w-[80%] bg-colorCabera rounded-xl text-center">
                    <p>{{$reembolso->pagador}} ha reembosado {{abs($reembolso->cantidad)}}€ a</p>
                    <p>{{$reembolso->receptor}}</p>
                </div>
            @endforeach
        @endif
        
    </div>
</section>

<section id="notificaciones" class="@if($pantalla == 'notificaciones') mostrar flex @else hidden @endif w-full flex-col-reverse py-5 contenedor text-colorLetra text-center">

    @if(count($notificaciones) == 0)
        <h1>No tienes ninguna notificación</h1>
    @endif

    @foreach ($notificaciones as $notificacion)      
        @if ($notificacion -> id_reembolso || $notificacion->receptor == $participanteUser->nombre_en_portal)
            <div class=" @if ($notificacion -> id_reembolso) bg-colorDetalles @else bg-colorComplem @endif w-9/12 mx-auto mt-10 space-y-5 py-4 rounded-2xl">
                <p class="text-center">{{$notificacion->mensaje}}</p>
                <div class="flex justify-center w-full space-x-10">
                    @if ($notificacion -> id_reembolso)
                        <button class="bg-colorComplem px-8 rounded-lg" data-action="confirmar">Confirmar</button>
                        <button class="bg-colorCabera px-8 rounded-lg" data-action="denegar">Denegar</button>
                    @else
                        <button class="bg-colorDetalles px-8 rounded-lg" data-action="aceptar">Aceptar</button>
                    @endif
                    <form method="POST" action="{{route('responderNot')}}">
                        @csrf
                        <input type="hidden" name="notificacion" value="{{json_encode($notificacion)}}">
                        <input type="hidden" name="respuesta" value="">
                    </form>
                </div>
            </div>      
        @endif
    @endforeach

</section>

@endsection