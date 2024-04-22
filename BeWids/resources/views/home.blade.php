@extends('partials.base')

@section('titulo','BeWids')
@section('rutaEstilos','css/estilosInicio.css')
@section('contenido')

<a href="{{route('inicioSesion.index')}}">Iniciar Sesión</a>
<a href="{{route('registro.index')}}">Registrarse</a>

@endsection