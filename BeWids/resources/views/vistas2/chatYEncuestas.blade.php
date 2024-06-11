@extends('partials/plantillaServicios')
@section('rutaJs','../js/chatYEncuestas.js')
@section('categorias')
@section('pusher')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
@endsection
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
    {{-- livewire('encuestas.encuestas') --}}
    <div class="encuestas w-full h-full">
        <div class="botonesEncuestas flex justify-center">
            <button class="bg-colorBarra2 text-white p-4 rounded-xl mx-4 creadorEncuestas">Crear encuesta</button>
            <button class="bg-colorBarra2 text-white p-4 rounded-xl mx-4">Encuestas finalizadas</button>
        </div>
    
        <div class="listadoEncuestas w-full h-full border-4 overflow-y-scroll relative">
            <table class=" w-full">
                <thead class=" border-4sticky top-0 h-[20%]">
                    <tr>
                        <th> Nombre encuesta</th>
                        <th>Porcetaje positivo</th>
                        <th>Porcentaje negativo</th>
                        <th>Votadores</th>
                        <th>Descripción</th>
                        <th><button>Detalles</button></th>
                        <th><button>Votar</button></th>
                    </tr>
                </thead>
                <tbody class="border-4 border-solid border-blue-700 ">
                    {{-- foreach ($encuestas as $encuesta) --}}
                        
                    {{-- endforeach --}}
                </tbody>
            </table>
            <div class="formEncuesta absolute top-0 hidden justify-center items-center w-full h-full ">
                <form class="bg-colorBarra2 flex flex-col w-[50%] h-[90%] items-center justify-around">
                    @csrf
                    <label for="titulo">Titulo</label>
                    <input type="text" id="titulo">
                    <label for="descripcion">Descripción</label>
                    <textarea type="text" id="descripcion" class="w-[80%] h-[10rem]"></textarea>
                    <div>
                        <label for="allp">Votan todos</label>
                        <input type="checkbox" id="allp" >
                    </div>
                    <div>
                        <label for="one2Many">Pueden votar:</label>
                        <input type="checkbox" id="one2Many">
                    </div>
                <div class="seleccionados flex flex-wrap justify-center px-2 py-5" >
                    {{-- foreach ($participantes as $participante) --}}
                    <div class="basis-[40%]">
                            <label for="">$participante->nombre_en_portal</label>
                            <input type="checkbox" id="$participante->nombre_en_portal" >
                    </div>
                    {{-- endforeach --}}
                </div>
                    <div class="opciones flex flex-col h-[20%] overflow-y-auto justify-around opcionesContainer">
                            
                            <input type="text" placeholder="Opcion de encuesta" class="opciones_votos">
                            <input type="text" placeholder="Opcion de encuesta" class="opciones_votos">
                            
                    </div>
                    
                    <button type="button" class="crearInputs bg-colorComplem rounded-3xl p-1 mb-4">Crear más</button>
    
                    <button type="submit" class="p-2 mb-2 bg-colorComplem rounded-3xl hover:bg-colorDetalles hover:text-white">Guardar Encuesta</button>
                </form>
            </div>
        </div>
</section>

@endsection