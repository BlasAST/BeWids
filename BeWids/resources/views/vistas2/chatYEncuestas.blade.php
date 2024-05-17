@extends('partials.base')
@section('rutaEstilos','../css/estilosChatYEncuestas.css')
@section('rutaEstilos2','../css/estilosBaseServicios.css')

@section('rutaJs','../js/chatYEncuestas.js')
@section('rutaJs2','../js/basicServicios.js')
@section('contenido')
@extends('partials.header')
<main>
    <div class="categorias">
        <span id="chat">
            <p class="selected">Chat</p>
        </span>
        <span id="encuestas">
            <p>Encuestas</p>
        </span>
    </div>
    <livewire:chat-y-encuestas/>
    
</main>

@endsection