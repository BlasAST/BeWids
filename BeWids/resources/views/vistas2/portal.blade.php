@extends ('partials.basePortales')
@section('rutaJs','js/portal.js')
@section('contenido')
@php
    $portal = Session::get('portal');
    $yo = Session::get('participanteUser');
@endphp
<div class="contenedor h-screen w-screen grid grid-cols-12 grid-rows-12">
    <!-- <h1>Benefits With friends</h1>
    <h1>{{$portal->nombre}}</h1>
    <h1>{{$portal->id}}</h1> -->
    <h1 class="col-[1/3] row-[1] text-colorLetra flex justify-center items-center">{{$yo->nombre_en_portal}} en</h1>
    <h1 class="row-[1/3] col-[2/4] flex justify-center items-center text-4xl text-colorLetra">{{$portal->nombre}}</h1>
    <div class="bg-blue-800 row-[1/5] col-[5/10] w-[100%] h-[80%] self-center justify-self-center">
        @include('partials.calendarioMini')
    </div>
    <div class="row-[5] col-[7] flex flex-col items-center justify-center">
        <button value="calendario" class="btn px-6 py-1 rounded-2xl bg-colorCaberaTras2 text-colorLetra">Calendario</button>
    </div>
    <div class=" row-[1/2] col-[10/13] h-[70%] flex items-center justify-around">
        <button class="closeSession text-base font-bold border-b-2 hover:border-b-black">Salir del portal</button>
        <figure class="w-10"><img src="{{asset('imagenes/imagenesBasic/ajustes2.png')}}" alt=""></figure>
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
    <div class="bg-yellow-800 row-[12/13] col-[9/13] w-[100%] h-[50%] self-end btnInvitacion">Enlace de invitación</div>
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

</div>
@endsection