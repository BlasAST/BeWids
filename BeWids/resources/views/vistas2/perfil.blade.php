@extends('partials.base')
@section('titulo','BeWids')
@section('rutaEstilos','css/estilosPerfil.css')
@section('rutaJs','js/perfil.js')

@section('contenidoCabecera')

<div class="ajustes">
    <div>
        <button class="editar">Editar Perfil</button>
        <a href="{{route('cerrarSesion')}}">Cerrar Sesion</a>
    </div>
</div>
<img src="{{asset('imagenes/imagenesBasic/ajustes.png')}}" alt="" class="bajustes">
@endsection

@section('contenido')

@extends('partials.header')


<main>
    <div class="contenedor">
        <button class="bperfil">
            <h1>Perfil</h1>
        </button>
        <form action="{{route('guardar')}}" method="POST">
        @csrf
        <div class="perfil">
            <h1>!Bienvenido {{$user->name}}!</h1>
            <div class="parte1">
                <img src="{{asset('imagenes/imagenesPerfil/alvaro.jpg')}}" alt="">
                <div>
                    <h3>Nombre->{{$infoUsuario->nombre ??''}} <input type="text" name="nombre" placeholder="Nombre identificativo"></h3>
                    <h4>Edad: </h4>
                    <p>Descripcion Breve->{{$infoUsuario->descripcion ??''}}<input type="text" name="descripcion" placeholder="Añadir una descripción"></p>
                </div>
            </div>

            <p>La siguiente información no estará disponibles para los usuarios por defecto</p>
            <div class="noVisible">
                <h3>Numero de grupos a los que perteneces</h3>
                <h3>Fecha de nacimiento->{{$infoUsuario->fecha_nacimiento ??''}} <input type="date" name="fecha_nacimiento"></h3>
                <h3>Numero de contacto->{{$infoUsuario->numero_contacto ??''}} <input type="number" name="numero_contacto"></h3>
                <h3>Provincia->{{$infoUsuario->provincia ??''}}<input type="text" name="provincia"></h3>
            </div>
        </div>
        </form>
        <button class="bsesiones">
            <h1>Sesiones</h1>
        </button>
        <div class="sesiones">
            <h2>Crear sesion</h2>
            <h2>Sesiones activas</h2>
            <ul>
                <li>Asede</li>
                <li>Vacaciones</li>
                <li>Findes Fiesta</li>
                <li>Cansones</li>
            </ul>
        </div>
        <!-- Perfil sin Ajustes -->
        <!-- Sesiones -->
        <!-- ¿Por defecto abierto perfil, al presionar -->
        <!-- sobre sesiones hacer perfil como boton de abrir -->
        <!-- y sesiones se abre?? -->
    </div>
</main>
@include('partials.footer')
@endsection