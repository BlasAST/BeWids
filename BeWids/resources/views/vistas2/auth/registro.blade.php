@extends('partials.base')
@section('rutaEstilos','css/estilosSesion.css')
@section('rutaJs','js/sesion.js')

@section('contenido')
    <div class="registro">
        <h1>CREAR CUENTA</h1>
        <p>Crea una cuenta con nosotros para descubrir los servicios que ofrecemos</p>
        <form action="{{route('registro.index')}}" method="POST">
             @csrf
            <div>
                <label for="name">Usuario</label>
                <div class="contInput">
                    <input type="text" name="name" placeholder="Indica tu usuario">
                    <div class="borde"></div>
                </div>
            </div>
            <div>
                <label for="email">Email</label>
                <div class="contInput">
                    <input type="email" name="email" placeholder="Indica tu correo electrónico">
                    <div class="borde"></div>
                </div>
            </div>
            <div>
                <label for="email2">Repetir Email</label>
                <div class="contInput">
                    <input type="email" name="email2" placeholder="Repite tu correo electrónico">
                    <div class="borde"></div>
                </div>
            </div>
            <div>
                <label for="password">Contraseña</label>
                <div class="contInput">
                    <input type="password" name="password" placeholder="Indica tu contraseña">
                    <div class="borde"></div>
                </div>
            </div>
            <div>
                <label for="pass2">Repetir Contraseña</label>
                <div class="contInput">
                    <input type="password" name="pass2" placeholder="Tepite tu contraseña">
                    <div class="borde"></div>
                </div>
                <a href="{{route('inicioSesion.index')}}">¿Ya tienes cuenta?</a>
            </div>
            <input type="submit" name="registro" value="CREAR CUENTA">
        </form>
    </div>
@endsection