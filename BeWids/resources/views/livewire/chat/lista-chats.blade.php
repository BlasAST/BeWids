@php
$uri = request()->path();
@endphp
<div class="lista bg-colorBarra2 basis-1/4 flex flex-col rounded-bl-2xl">
    <header class="flex border-b-2 border-b-blue-600 justify-center items-center h-1/6 mx-4"  >

        <select type="text" value="" wire:change='participanteSelecionado($event.target.value)' class="rounded-3xl">
            <option value="">Buscar participante</option>
            @foreach($participantes as $participante)
            <option value="{{$participante->nombre_en_portal}}">{{$participante->nombre_en_portal}}</option>
            @endforeach
        </select>
    <!-- $nombreUserPortal->nombre_en_portal-->
    </header>
    <div class="bg-blue-500 text-center newChat @if(!$participant) hidden @endif">
        <p>Crear Chat con:</p>
        <p>{{$participant}}</p>
        <button class="bg-green-500 aceptar " wire:click='comprobarChat("{{$participant}}")'>Aceptar</button>
        <button class="bg-red-700 cancelar" wire:click="cerrar({{$uri}})">Cerrar</button>
        @if ($conexion)
        <p class="text-red-600">{{$mensaje}}</p>
        @endif
    </div>
    
    
    <main class="seleccionesChat flex flex-col h-full justify-start items-center all-button:border-b-2 
    all-button:text-xl all-button:my-2 all-all-li:bg-emerald-400 all-all-li:p-2 all-all-li:bg-opacity-15
    all-all-button:text-white all-all-label:text-white">
        
        <button class="flex items-center hover:bg-colorComplem"> @include('componentes.notificacion')Chat global</button>
        <button id="destacados" class="hover:bg-colorComplem">Destacados</button>
        <ul class="hidden destacados">

        </ul>
        <button id="entrada" class="hover:bg-colorComplem">Bandeja <br> de entrada</button>
        <ul class="hidden entrada">
                @foreach ($conversacionesIndividuales as $chatSimple)
                    <li class="my-4 hover:bg-opacity-100"><a href="">{{$chatSimple->receptor!=$participanteActual->nombre_en_portal?$chatSimple->receptor:$chatSimple->emisor}}</a></li>
                @endforeach
        </ul>
        <div class=" botonDiv border-b-2 text-xl hover:bg-colorComplem  my-2 w-[90%] flex items-center justify-between">
        <button id="grupos">Grupos internos</button>
        <figure class="w-4 newGroup"><img src="{{asset('imagenes/imagenesBasic/iconSuma.png')}}" alt=""></figure>
        </div>
        <ul class="hidden grupos">
                @foreach ($conversacionesIndividuales as $chatSimple)
                    <li class="my-4 hover:bg-opacity-100"><a href="">{{$chatSimple->receptor!=$participanteActual->nombre_en_portal?$chatSimple->receptor:$chatSimple->emisor}}</a></li>
                @endforeach
                @foreach ($conversacionesGrupales as $chatGrupal)
                    <li>{{$chatGrupal->receptor!=$participanteActual->nombre_en_portal?$chatGrupal->receptor:$chatGrupal->emisor}}</li> 
                @endforeach
        </ul>

        <form action="/newGroup" class="newGroupForm hidden flex-col w-[80%] text-center all-all-button:bg-colorComplem all-all-button:m-2 all-all-button:p-2 ">
            <label for="nombreG">Nombre del grupo</label>
            <input type="text" id="nombreG" required>
            <label for="desG">Descripción breve</label>
            <input type="text" id="desG">
            <label for="participantes">Participantes</label>
            <div class="creacionGrupo overflow-y-scroll h-[40px]">
            @foreach ($participantes as $participante)
                <label for="all">Todos</label>
                <input type="checkbox" id="all">
                <label for="{{$participante->nombre_en_portal}}">{{$participante->nombre_en_portal}}</label>
                <input type="checkbox" id="{{$participante->nombre_en_portal}}">
            @endforeach
            </div>
            <div>
            <button class="hover:bg-colorDetalles crearFormGroup" type="submit">Crear</button>
            <button class="hover:bg-colorDetalles cierreFormGroup">Cancelar</button>
            </div>
        </form>
    </main>
</div>