@extends('partials.base')
@section('rutaEstilos','../css/estilosSesion.css')


@section('contenido')
        <section class="verificar">
            <div>
                <h1>Verifica Tu Dirección De Correo</h1>
                
                <p>Revisa tu correo y verificaté mediante el link que te hemos enviado.</p>
                <p>Si no has recivido ningún correo solicitalo de nuevo</p>             
                <form class="" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="">Reenviar link</button>
                </form>
                @error('message')
                    <p class="reenvio">{{$message}}</p>
                @endError
                <p><a href="{{route('casa')}}">Volver</a> a home</p>
            </div>
        </section>
@endsection
