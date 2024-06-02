@extends('partials/plantillaServicios')
@section('rutaJs','../js/chatYEncuestas.js')
@section('categorias')
@php 
    $portal=Session::get('portal');
@endphp

<div id="chatCat" class="flex-grow flex justify-center cursor-pointer hover:text-colorDetalles">
    <span class="h-full flex flex-col justify-center @if ($ruta=='chat') border-b-4 border-white  selected @endif">Chat</span>
</div>
<div id= "encuestasCat"class="flex-grow flex justify-center cursor-pointer  hover:text-colorDetalles ">
    <span class="h-full flex flex-col justify-center @if ($ruta=='encuestas') border-b-4 border-white  selected @endif">Encuestas</span>
</div>

@endsection



@section('contenidoServicio')



<section id="chat" class=" @if ($ruta=='chat') mostrar flex @else hidden @endif  h-full w-full">
    @livewire('chat.lista-chats')
    @livewire('chat.contenedor-mensajes')
</section>

<section id="encuestas" class=" @if ($ruta=='encuestas') mostrar flex @else hidden @endif h-full w-full">
    @livewire('encuestas.encuestas')
</section>





@endsection