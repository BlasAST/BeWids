@extends('partials.base')
@section('rutaEstilos','css/estilosPortal.css')
@extends('partials.header')

@section('contenido')
        <h1>Benefits With friends</h1>
        <h1>{{session('portal')->nombre ?? $portal->nombre}}</h1>
        <h1>{{session('portal')->id ?? $portal->id}}</h1>
    </div>
    <div class="contenedor">
        <div id="calendario">calendario</div>
        <div id="ajusteyCierre">Ajustes y cierre de Sesion</div>
        <div id="contabilidad">contabilidad</div>
        <div id="buscador">buscador de eventos</div>
        <div id="chatyEncuestas">chat y encuestas</div>
        <div id="planifi">planificacion</div>
        <div id="mapa">mapa</div>
        <div id="enlace">Enlace invitacion</div>
    </div>
    <!-- <script src="transicionComienzo/transicion.js"></script> -->
@endsection