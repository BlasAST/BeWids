@php
    $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado','Domingo'];
    $diasSemanaCorto = ['L', 'M', 'X', 'J', 'V', 'S','D'];
@endphp
@extends('partials/plantillaServicios')
@section('rutaJs','js/calendario.js')

@section('categorias')

<div id= "mes"class="flex-grow flex justify-center items-stretch flex-wrap">
    <div class="basis-full flex justify-end mr-16">
        <select name="anio" id="" class="shrink bg-transparent anios  overflow-auto">
            <option value="{{$fechaFinal->format('Y')}}" class="text-sm bg-colorCabera">{{$fechaFinal->format('Y')}}</option>
        </select>
    </div>
    <button class="basis-1/6" value="-1"><</button>
    <span class="grow flex justify-center border-b-4 border-colorDetalles text-center text-2xl">
        <select name="mes" id="" class="shrink bg-transparent meses">
            <option value="{{$numMes}}" class="text-lg bg-colorCabera" selected>{{$mes}}</option>
        </select>
    </span>
    <button class="basis-1/6" value="1">></button>
</div>

@endsection

@section('contenidoServicio')

<section class="flex flex-col w-full h-full">
    <div class="basis-1/12 grid grid-cols-7">
        @foreach ($diasSemana as $dia)
            <div class="font-bold bg-gray-200 hidden md:flex items-center justify-center border-2 border-black ">{{ $dia }}</div>
        @endforeach
        @foreach ($diasSemanaCorto as $dia)
            <div class="font-bold bg-gray-200 flex md:hidden items-center justify-center border-2 border-black ">{{ $dia }}</div>
        @endforeach
    </div>
    <div class="grid grid-cols-7 grid-rows-6 basis-11/12 contDias">
        @include('partials.diasCalendario')
    </div>
</section>
<div class="w-[100dvw] h-[100dvh] fixed hidden justify-center items-center top-0 left-0 confirmMov">
    <div class="basis-1/3 bg-colorDetalles flex flex-wrap text-center justify-center space-x-5 rounded-xl border-4 border-colorCabera text-colorLetra space-y-6 py-6">
        
        <h1 class="basis-full">Seguro que quieres cambiar la fecha del <span class="origen"></span> al <span class="destino"></span> </h1>
        <button class="bg-colorComplem border-2 border-colorCabera rounded-xl px-5 btnConf">Confirmar</button>
        <button class="bg-colorCabera border-2 border-colorCabera rounded-xl px-5 btnCanc">Cancelar</button>

        <input type="hidden" name='evento'>
        
        


    </div>


</div>
<div class="w-[100dvw] h-[100dvh] fixed hidden justify-center items-center top-0 left-0  mostrarEvt">
    <div class="hidden mapa" id="40.44820807603311|-3.6754035358523303"></div>
    <div class="basis-1/2 bg-colorCabera flex flex-col text-center items-end rounded-xl border-4 border-colorLetra text-colorLetra p-3">
        <figure class="w-7 h-7 logoCancel btnCerrar"></figure>
        <div class="w-full flex flex-col text-center items-center max-h-96 overflow-y-auto gap-6">  
            
        </div>
         
        


    </div>


</div>

@endsection