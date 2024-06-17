@extends('partials.base')
@section('rutaEstilos','../css/estilosSesion.css')

@section('contenido')
    <div class="reset mostrar ">
        <h1>Recuperar contraseña</h1>
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div>
                <label for="email">Indica tu dirección de correo</label>
                <div class="contInput">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    <div class="borde"></div>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <button type="submit">Solicitar Link</button>
            <p><a href="/">Volver</a> a home</p>
        </form>
    </div>
@endsection
