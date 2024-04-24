@extends('partials.base')

@section('titulo','BeWids')
@section('rutaEstilos','css/estilosHome.css')
@section('contenido')


<header>
    <div class="cabecera">
        
        <div></div>
        <div>
              <a href="{{route('inicioSesion.index')}}">Iniciar Sesión</a>
              <a href="{{route('registro.index')}}">Registrarse</a>
              <a href="{{route('cerrarSesion')}}">Cerrar Sesión</a>
              <br>
              <a href="{{route('sesion', 'botones')}}">Perfil</a>
              <a href="{{route('sesion', 'inicio')}}">Iniciar</a>
              <a href="{{route('sesion', 'crear')}}">Crear Cuentar</a>
        </div>
    </div>
</header>
<main>
    <div class="margen">
        <div class="contenedor">
            <div class="hero">
                <h2>Te ayudamos a organizarte mejor con otras personas</h2>
            </div>
            <div><h2>¿Para que sirve esta aplicación?</h2></div>

            <div><h2>Planificate mejor con tus personas cercanas</h2></div>
            <div><h2>Aportar vuestras ideas y opiniones</h2></div>
            <div><h2>Crear encuestas para saber lo que la mayoría quiere</h2></div>
            <div><h2>Busca eventos y organiza planes cerca de ti</h2></div>
        </div>
    </div>
</main>


@include('partials.footer')
@endsection


