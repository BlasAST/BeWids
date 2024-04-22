@extends('partials.base')

@section('contenido')
    <div class="inicio">
        <h1>INICIAR SESIÓN</h1>
        <p>Si ya tienes una cuenta con nosotros, indica tus credenciales y accede a tus sesiones</p>
        <form action="{{route('inicioSesion.index')}}" method="POST">
            @csrf
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Indica tu correo electrónico">
            <label for="password">Contraseña</label>
            <input type="password" name="password" placeholder="Indica tu contraseña">
            <a href="">He olvidado mi contraseña</a>
            <p>¿No tienes cuenta? <a href="">Registrate</a></p>
            <input type="submit" name="inicio" value="INICIAR SESIÓN">
            @error('message')
                <p>*error</p>
                @endError
        </form>
    </div>
@endsection