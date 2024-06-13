<h3 class="text-center text-xl w-full font-extrabold">{{$evento->titulo}}</h3>
<div class="w-full flex">
    <div class="basis-5/12 flex flex-col items-start pl-6">
        <p>Hora de inicio:  {{$evento->hora_inicio}}</p>
        <p>Hora de fin:   {{$evento->hora_fin}}</p>
        <p>Precio:  {{$evento->precio}}</p>
        @if ($yo->admin||!$ajustes->modif_cal)
            <button class=" bg-colorComplem rounded-2xl py-1 px-3 mt-2">Quitar del calendario</button>
        @endif

    </div>
    <p class="text-xs basis-7/12 font-normal text-left ">{{$evento->descripcion}}</p>
</div>
<div class="w-1/2 min-h-64" id={{$evento->latitud.'|'.$evento->longitud}}></div>