@extends('partials.base')
@section('rutaEstilos','css/estilosPortal.css')
@section('rutaJs','js/portal.js')
@include('partials.header')

@section('contenido')
    @php
        $portal = Session::get('portal');
    @endphp
    <h1>Benefits With friends</h1>
    <h1>{{$portal->nombre}}</h1>
    <h1>{{$portal->id}}</h1>
    <div class="contenedor">
        <div id="calendario">calendario</div>
        <div id="ajusteyCierre">Ajustes y cierre de Sesion</div>
        <div id="contabilidad">contabilidad
            <button class="btnGastos">GASTOS</button>
        </div>
        <div id="buscador">buscador de eventos</div>
        <div id="chatyEncuestas">
            <button class="btnCE">Chat y Encuestas</button> 
            <!-- <a href="{{route(portal),['id'=>$portal->id]}"></a> -->
            chat y encuestas</div>
        <div id="planifi">planificacion</div>
        <div id="mapa">mapa</div>
        <div id="enlace">Enlace invitacion</div>
    </div>
    <!-- <script src="transicionComienzo/transicion.js"></script> -->
@endsection
