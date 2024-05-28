@extends('partials/plantillaServicios')
@section('rutaJs','../js/chatYEncuestas.js')
@section('categorias')
@php 
    $portal=Session::get('portal')
@endphp

<div id="chatCat" class="flex-grow flex justify-center cursor-pointer hover:text-colorDetalles">
    <span class="h-full flex flex-col justify-center border-b-4 border-white  selected">Chat</span>
</div>
<div id= "encuestasCat"class="flex-grow flex justify-center cursor-pointer  hover:text-colorDetalles ">
    <span class="h-full flex flex-col justify-center">Encuestas</span>
</div>

@endsection



@section('contenidoServicio')

<section id="chat" class="mostrar flex h-full w-full">
    @livewire('chat.lista-chats')
    @livewire('chat.contenedor-mensajes')
</section>

<section id="encuestas" class="hidden h-full w-full">
    @livewire('encuestas.encuestas')
</section>





@endsection