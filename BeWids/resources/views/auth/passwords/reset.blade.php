@extends('partials.base')
@section('rutaEstilos','../../css/estilosSesion.css')

@section('contenido')
    <div class="reset mostrar ">
        <h1>Recuperar contraseña</h1>
        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label for="email">Indica tu dirección de correo</label>
                <div class="contInput">
                    <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                    <div class="borde"></div>
                </div>
            </div>
            @error('email')
                <span role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror


            <div>
                <label for="password">Nueva Contraseña</label>

                <div class="contInput">
                    <input id="password" type="password" name="password" required autocomplete="new-password">
                    <div class="borde"></div>
                </div>
            </div>
            @error('password')
                <span role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror


            <div>
                <label for="password-confirm">Confirmar Contraseña</label>
                <div class="contInput">
                    <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
                    <div class="borde"></div>
                </div>
            </div>
            <button type="submit">Reestablecer Contraseña</button>
            <p><a href="/">Volver</a> a home</p>
        </form>
    </div>
@endsection
