<div class="lista bg-colorBarra2 basis-1/4 flex flex-col rounded-bl-2xl">
    <header class="flex border-b-2 border-b-blue-600 justify-between items-center h-1/6 mx-4">

        <select type="text" value="" wire:change='participanteSelecionado($event.target.value)' class="rounded-3xl ">
            <option value="">Buscar participante</option>
            @foreach($participantes as $participante)
            <option value="{{$participante->nombre_en_portal}}">{{$participante->nombre_en_portal}}</option>
            @endforeach
        </select>

    </header>
    
    <div class="bg-blue-500 text-center nuevo">
    @if ($participant != NULL)
        <p>Crear Chat con:</p>
        <p>{{$participant}}</p>
        <button class="bg-green-500 aceptar " wire:click='comprobarChat("{{$participant}}")'>Aceptar</button>
        <button class="bg-red-700 cancelar" wire:click="cerrar()">Cancelar</button>
        @if ($conexion != False)
        <p class="text-red-600">{{$mensaje}}</p>
    @endif
    @endif
    </div>
    
    
    
    <main class="seleccionesChat flex flex-col h-full justify-around items-center">
        <button class="flex items-center">@include('componentes.notificacion')Chat global</button>

        <button id="destacados">Destacados</button>
        <ul class="hidden destacados">

        </ul>
        <button id="entrada">Bandeja de entrada</button>
        <ul class="hidden entrada">

        </ul>
        <button id="grupos">Grupos internos</button>
        <ul class="hidden grupos">

        </ul>
    </main>
</div>