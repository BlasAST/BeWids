@extends('partials/plantillaServicios')
@section('rutaJs','../js/chatYEncuestas.js')
@section('categorias')
@section('pusher')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
@endsection
@php 
    $portal=Session::get('portal')
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
    <div class="encuestas w-full h-full">
        <div class="botonesEncuestas flex justify-center">
            <button class="bg-colorBarra2 text-white p-4 rounded-xl mx-4 creadorEncuestas">Crear encuesta</button>
            <button class="bg-colorBarra2 text-white p-4 rounded-xl mx-4">Encuestas finalizadas</button>
        </div>
    
        <div class="listadoEncuestas w-full h-full border-4 overflow-y-auto relative">
            <table class=" w-full">
                <thead class=" border-4 sticky top-0 h-[20%]">
                    <tr>
                        <th>Encuesta</th>
                        <th>Descripción</th>
                        <th>Creador</th>
                        <th>Participantes</th>
                        <th>Porcentajes</th>
                        <th>Votos</th>
                        <th>Fecha fin</th>
                        <th><button>Votar</button></th>
                    </tr>
                </thead>
                <tbody class="border-4 border-solid border-blue-700 h-screen">
                     @foreach ($encuestas as $encuesta) 
                        <tr class="hover:bg-colorComplem bg-colorCaberaTras all-td:border-2 all-td:border-colorCaberaTras2 ">
                            <td>{{$encuesta->title}}</td>
                            <td><button value="descripcion" class="btn-info leerDescripcion truncate max-w-20">{{$encuesta->descripcion}}</button></td>
                            <td class="w-4">{{$encuesta->creador}}</td>
                            <td class="text-center"><button value="participantes" class="btn-info p-2 bg-white rounded-full hover:bg-colorDetalles">Ver participantes</button></td>
                            <td class="text-center"><button value="votos" class=" p-2 bg-white rounded-full hover:bg-colorDetalles">Ver porcentajes</button></td>   {{-- btn-info --}}
                            <td>{{$encuesta->num_votos_hechos}}/{{$encuesta->num_votos_totales}}</td>
                            <td>{{$encuesta->fecha_final?$encuesta->fecha_final:'Sin fecha'}}</td>
                            <td class="text-center"><button class="p-2 bg-white rounded-full hover:bg-colorDetalles">Votar</button></td>
                            <input type="hidden" value="{{$encuesta->id}}">
                        </tr>
                    @endforeach 
                </tbody>
            </table>
            <div class="formEncuesta absolute top-0 hidden justify-center items-center w-full h-full ">
                <form action="{{route('newEncuesta')}}" method="POST" class="formularioEncuestas bg-colorBarra2 flex flex-col w-[50%] h-[90%] items-center justify-around all-all-label:text-white">
                    @csrf
                    <div class="flex all-div:w-[40%] justify-around items-center">
                        <div>
                            <label for="titulo">Titulo </label>
                            <input type="text" id="titulo" name="tittle" class="w-[100%]">
                        </div>
                        <div>
                            <label for="fecha_final">Fecha final(opcional)</label>
                            <input type="date" id="fecha_final" name="fecha_final">
                        </div>
                    </div>
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" type="text" id="descripcion" class="w-[80%] h-[10rem]"></textarea>
                    <div>
                        <label for="allParticipantes">Votan todos</label>
                        <input type="checkbox" id="allParticipantes" name="allParticipantes" required>
                    </div>
                    <div>
                        <label for="one2Many">Pueden votar:</label>
                        <input type="checkbox" id="one2Many" name="one2Many">
                    </div>
                <div class="seleccionados hidden flex flex-col justify-center px-2 py-5" >
                    @foreach ($participantes as $participante) 
                    <div class="basis-[40%]">
                            @if ($participante->nombre_en_portal==$participanteActual->nombre_en_portal)
                            <label for="{{$participante->nombre_en_portal}}" >{{$participante->nombre_en_portal}}(Tú)</label>
                            <input type="checkbox" id="{{$participante->nombre_en_portal}}" class="individual" name="individual[]" value="{{$participante->nombre_en_portal}}">    
                            @else
                            <label for="{{$participante->nombre_en_portal}}" >{{$participante->nombre_en_portal}}</label>
                            <input type="checkbox" id="{{$participante->nombre_en_portal}}" class="individual" name="individual[]" value="{{$participante->nombre_en_portal}}">
                            @endif
                            
                    </div>
                     @endforeach 
                </div>
                    <div class="opciones flex flex-col h-[20%] overflow-y-auto justify-around opcionesContainer">
                            <input type="text" placeholder="Opcion de encuesta" class="opciones_votos" name="opciones_votos[]">
                            <input type="text" placeholder="Opcion de encuesta" class="opciones_votos" name="opciones_votos[]">
                    </div>
                    
                    <button type="button" class="crearInputs bg-colorComplem rounded-3xl p-1 mb-4">Crear más</button>
                    <button type="submit" class=" enviarFormulario p-2 mb-2 bg-colorComplem rounded-3xl hover:bg-colorDetalles hover:text-white">Guardar Encuesta</button>
                </form>
            </div>
            <div class="contenedorMuestraInfo hidden absolute bottom-0 flex justify-center items-center w-full h-full">
                <div class="fixed bg-colorBarra2 w-[30%] h-[30%] ">
                    <figure class="w-10 absolute z-20 top-[5%] left-[10%] btn-cerrar "><img src="{{asset('imagenes/imagenesBasic/cerrar.png')}}" alt=""></figure>
                    <div class="muestraInfo flex flex-col  w-full h-full overflow-x-auto items-center justify-around "></div>
                </div>
            </div>
        </div>
</section>

@endsection