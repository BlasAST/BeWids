@extends('partials.base')
@section('rutaEstilos','css/estilosSesion.css')
@section('rutaJs','js/sesion.js')

@section('contenido')
    <div class="inicio">
        @if(!Auth::check())
            <h1>INICIAR SESIÓN</h1>
            <p>Si ya tienes una cuenta con nosotros, indica tus credenciales y accede a tus sesiones</p>
            <form action="{{route('inicioSesion.index')}}" method="POST">
                @csrf
                <div>
                    <label for="email">Email</label>
                    <div class="contInput">
                        <input type="email" name="email" placeholder="Indica tu correo electrónico">
                        <div class="borde"></div>
                    </div>
                </div>
                <div>
                    <label for="password">Contraseña</label>
                    <div class="contInput">
                        <input type="password" name="password" placeholder="Indica tu contraseña">
                        <div class="borde">
                            <figure class="ojo"></figure>
                        </div>
                    </div>
                </div>
                <div>
                    <div>
                        <input type="checkbox" name="recordar">
                        <label for="recordar">Recordar sesión</label>
                    </div>
                    <a href="">He olvidado mi contraseña</a>
                    <p>¿No tienes cuenta? <a href="{{route('registro.index')}}">Registrate</a></p>
                </div>
                @error('message')
                    <p>{{$message}}</p>
                @endError
                <input type="submit" name="inicio" value="INICIAR SESIÓN">
                <p><a href="{{url()->previous()}}">volver</a> a la pagina anterior</p>
            </form>
        @else
            <h1>Ya estás loggeado</h1>
            <h3>Para iniciar sesión cierra la sesión actual antes</h2>
            <button class="cerrarSesion">CERRAR SESIÓN</button>
            <p><a href="{{url()->previous()}}">volver</a> a la pagina anterior</p>
        @endif
    </div>
@endsection