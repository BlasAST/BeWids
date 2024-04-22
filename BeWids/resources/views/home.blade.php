@extends('partials.base')

@section('titulo','BeWids')
@section('rutaEstilos','css/estilosInicio.css')
@section('contenido')

<a href="{{route('inicioSesion.index')}}">Iniciar Sesión</a>
<a href="{{route('registro.index')}}">Registrarse</a>
<br>
<a href="{{route('perfil', 'botones')}}">Perfil</a>
<a href="{{route('perfil', 'inicio')}}">Iniciar</a>
<a href="{{route('perfil', 'crear')}}">Crear Cuentar</a>


@endsection