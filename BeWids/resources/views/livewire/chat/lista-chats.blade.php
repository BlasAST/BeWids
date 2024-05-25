<div class="lista bg-colorBarra2 basis-1/4 flex flex-col">
    <header class="flex border-b-2 border-b-blue-600 justify-between items-center h-1/6 mx-4">
        <select type="text" value="Buscar Participante" class="rounded-3xl">
            <option value="">Buscar participante</option>
            @foreach($participantes as $participante)
            <option value="{{$participante}}">{{$participante->nombre_en_portal}}</option>
            @endforeach
        </select>
    </header>
    <main class="seleccionesChat flex flex-col h-full justify-around items-center">
        <h2 class="flex items-center">
            <span class="relative flex h-3 w-3 mr-2">
                <span class="animate-ping absolute h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                <span class="rounded-full h-3 w-3 bg-sky-500"></span>
            </span>
            Chat global
        </h2>
        <h2>Destacados</h2>
        <h2>Bandeja de entrada</h2>
        <h2>Grupos internos</h2>
    </main>
</div>