@extends('partials.base')
@section('titulo','BeWids')
@section('rutaEstilos','css/estilosPerfil.css')
@section('rutaJs','js/perfil.js')
@section('contenidoCabecera')
@endsection
@section('contenido')
@include('partials.header')

<main>
    <div class="contenedor">
        <button class="bperfil" style="cursor: pointer">
            <h3>Perfil</h3>
        </button>
        <form class="formPerfil" action="{{route('guardar')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="perfil">
                <h1>!Bienvenido {{$user->name}}!</h1>
                <div class="parte1">
                    <div>
                        <img src="{{ route('profile.photo', ['nombreFoto' => $infoUsuario->foto_perfil]) }}" alt="foto">
                        <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*">
                    </div>
                    <div>
                        <h3>Nombre: {{$infoUsuario->nombre ??''}} </h3>
                        <input type="text" name="nombre" placeholder="Nombre identificativo">
                        <h3>Descripcion Breve: {{$infoUsuario->descripcion ??''}}</p>
                        <input type="text" name="descripcion" placeholder="Añadir una descripción" value="{{$infoUsuario->descripcion ??''}}">
                    </div>
                </div>

                <h3 class="no">La siguiente información no estará disponibles para el resto de usuarios</h3>
                <div class="noVisible">
                    <h3>Fecha de nacimiento: {{$infoUsuario->fecha_nacimiento ??''}} </h3>
                    <input type="date" name="fecha_nacimiento">
                    <h3>Numero de contacto: {{$infoUsuario->numero_contacto ??''}} </h3>
                    <input type="number" name="numero_contacto">
                    <h3>Provincia:{{$infoUsuario->provincia ??''}}</h3>
                    <input type="text" name="provincia">
                </div>
            </div>
        </form>
        <button class="bsesiones" style="cursor: pointer">
            <h3>Sesiones</h3>
        </button>
        <div class="sesiones">
            <h2>Sesiones activas</h2>
            @if (count($portales)==0)
                <h1>No estas en ningún portal</h1>
            @endif
            @foreach ($portales as $portal)
                <div class="portal" style="cursor: pointer">
                    <form method="POST" action="{{route('abrirPortal')}}">
                        @csrf
                        <input type="hidden" name="portal" value="{{json_encode($portal)}}">
                    </form>
                    <div @if($portal->fondo) style="background-image: url('{{ route('foto.fondo',['foto'=>$portal->fondo]) }}')" @endif></div>
                    {{$portal->nombre}} 
                </div>  
            @endforeach
            <button class="crearPortal" style="cursor: pointer">Crear Portal</button>
            <form class="formPortal" action="{{route('crearP')}}" method="POST">
                @csrf
                <label for="portal">Nombre del portal:</label>
                <input type="text" required name="portal">
                <label for="nombre" >Tu nombre dentro del portal:</label>
                <input type="text" required name="nombre" class="nombreP">
                <p>Puedes añadir participantes para que cuando la gente se una indiquen quienes son, aunque también puedes dejarlo para mas tarde o que cada uno añada su nombre en el portal</p>
                <button type="button" style="cursor: pointer">Agregar participante</button>
                <p style="color:#BF1B4B;display:none">Los participantes no pueden tener el mismo nombre</p>
                <input type="submit" name="enviar" style="cursor: pointer" value="Crear">
            </form>
        </div>
    </div>
</main>
@endsection