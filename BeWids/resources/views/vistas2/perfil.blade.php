@extends('partials.base')
@section('titulo','BeWids')
@section('rutaEstilos','css/estilosPerfil.css')
@section('rutaJs','js/perfil.js')

@section('contenidoCabecera')
<div class="ajustes">
    <h1>Cerrar Sesion</h1>
</div>
<img src="{{asset('imagenes/imagenesBasic/ajustes.png')}}" alt="">
@endsection

@section('contenido')

@extends('partials.header')

   
    <main>
        <div class="contenedor">
            <button class="bperfil">
                <h1>Perfil</h1>
            </button>
            <div class="perfil">
                <h1>!Bienvenido {{Auth::user()->name}}!</h1>
                <div class="parte1">
                    <img src="{{asset('imagenes/imagenesPerfil/alvaro.jpg')}}" alt="">
                    <div>
                        <h3>Nombre: Nombre Fijo</h3>
                        <h3>Edad: </h3>
                        <p>Descripcion Breve: Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam nulla,
                            natus corrupti quod delectus porro nihil eum sed incidunt vero cum culpa quis aliquid,
                            beatae sit obcaecati minus, repellat reiciendis!</p>
                    </div>
                </div>

                <p>La siguiente información no estará disponibles para los usuarios por defecto</p>
                <div class="noVisible">
                    <h3>Numero de grupos a los que perteneces</h3>
                    <h3>Fecha de nacimiento: 20</h3>
                    <p>Numero de contacto</p>
                    <p>Provincia</p>
                </div>
            </div>
            <button class="bsesiones">
                <h1>Sesiones</h1>
            </button>
            <div class="sesiones">
                <h2>Sesiones activas</h2>
                <ul>
                    <li>Asede</li>
                    <li>Vacaciones</li>
                    <li>Findes Fiesta</li>
                    <li>Cansones</li>
                </ul>
                <h2>Sesiones anteriores</h2>
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
   