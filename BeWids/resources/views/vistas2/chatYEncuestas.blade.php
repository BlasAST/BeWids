@extends('partials.base')

@section('rutaEstilos','../css/estilosChatYEncuestas.css')
@section('rutaEstilos2','../css/estilosBaseServicios.css')

@section('rutaJs','../js/chatYEncuestas.js')
@section('rutaJs2','../js/basicServicios.js')
@section('contenido')
@extends('partials.header')
<main>
<div class="categorias">
        <span id="gastos"><p class="selected">Gastos</p></span>
        <span id="graficos"><p>Gráficos</p></span>
        <span id="cuentas"><p>Cuentas</p></span>
        <span id="notificaciones"><p>Notificaciones</p></span>
    </div>
</main>
@endsection