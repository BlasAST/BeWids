@extends('partials.base')
@section('contenido')
    <div class="registro">
        <h1>CREAR CUENTA</h1>
        <p>Crea una cuenta con nosotros para descubrir los servicios que ofrecemos</p>
        <form action="{{route('registro.index')}}" method="POST">
             @csrf
            <label for="name">Usuario</label>
            <input type="text" name="name" placeholder="Indica tu usuario">
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Indica tu correo electrónico">
            <label for="email2">Repetir Email</label>
            <input type="email" name="email2" placeholder="Repite tu correo electrónico">
            <label for="password">Contraseña</label>
            <input type="password" name="password" placeholder="Indica tu contraseña">
            <label for="pass2">Repetir Contraseña</label>
            <input type="password" name="pass2" placeholder="Tepite tu contraseña">
            <a href="">¿Ya tienes cuenta?</a>
            <input type="submit" name="registro" value="CREAR CUENTA">
        </form>
    </div>
@endsection