@extends ('partials.basePortales')
@section('rutaJs','js/portal.js')
@section('contenido')
@php
$portal = Session::get('portal');
@endphp
<div class="contenedor h-screen w-screen grid grid-cols-12 grid-rows-12">
    <!-- <h1>Benefits With friends</h1>
    <h1>{{$portal->nombre}}</h1>
    <h1>{{$portal->id}}</h1> -->
    <div class="bg-blue-800 row-[1/5] col-[5/8] w-[100%] h-[80%] self-center justify-self-center">
        <button>Calendario</button>
    </div>
    <div class=" row-[1/2] col-[10/13] h-[70%] flex items-center justify-around">
        <button class="closeSession text-base font-bold border-b-2 hover:border-b-black">Salir del portal</button>
        <figure class="w-10"><img src="{{asset('imagenes/imagenesBasic/ajustes2.png')}}" alt=""></figure>
    </div>
    <div class="bg-purple-800 row-[2/8] col-[10/13] w-[70%] justify-self-center">
        <button class="btnEv">Eventos</button>
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
    <div class="bg-red-800 row-[3/7] col-[1/3] w-3/4 justify-self-center">
    <button class="btnGastos">GASTOS</button>
    </div>
</div>
@endsection