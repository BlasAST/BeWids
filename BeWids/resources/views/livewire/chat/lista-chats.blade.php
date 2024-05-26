<div class="lista bg-colorBarra2 basis-1/4 flex flex-col rounded-bl-2xl">
    <header class="flex border-b-2 border-b-blue-600 justify-between items-center h-1/6 mx-4">
        <select type="text" value="Buscar Participante" class="rounded-3xl">
            <option value="">Buscar participante</option>
            @foreach($participantes as $participante)
            <option value="{{$participante}}">{{$participante->nombre_en_portal}}</option>
            @endforeach
        </select>
    </header>
    <main class="seleccionesChat flex flex-col h-full justify-around items-center">
        <button class="flex items-center">@include('componentes.notificacion')Chat global</button>
        <button>Destacados</button>
        <ul class="hidden">
            
        </ul>
        <button>Bandeja de entrada</button>
        <ul class="hidden">

        </ul>
        <button>Grupos internos</button>
        <ul class="hidden">
            
        </ul>
    </main>
</div>