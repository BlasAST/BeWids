@extends('partials.base')
@section('rutaEstilos','css/estilosSesion.css')
@section('rutaJs','js/sesion.js')

@section('contenido')
    <div class="inicio">
        <h1>INICIAR SESIÓN</h1>
        <p>Si ya tienes una cuenta con nosotros, indica tus credenciales y accede a tus sesiones</p>
        <form action="{{route('inicioSesion.index')}}" method="POST">
            @csrf
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Indica tu correo electrónico">
            </div>
            <div>
                <label for="password">Contraseña</label>
                <input type="password" name="password" placeholder="Indica tu contraseña">
                <a href="">He olvidado mi contraseña</a>
                <p>¿No tienes cuenta? <a href="{{route('registro.index')}}">Registrate</a></p>
            </div>
            <input type="submit" name="inicio" value="INICIAR SESIÓN">
            @error('message')
                <p>*error</p>
                @endError
        </form>
    </div>
@endsection