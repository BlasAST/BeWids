@php
    $cat = explode(" ",$evento->categoria)[0] . ".jpg";
    $ajustes = Session::get('ajustes');
    $yo = Session::get('participanteUser');
@endphp
<div class="min-h-20 w-[90%] md:w-4/6 mx-auto flex items-stretch justify-center flex-wrap evento my-6 sh bg-colorCabera text-gray-300 rounded-2xl" >
    <figure class="basis-3/12 m-0 imagenEvento rounded-l-2xl" style="background-image: url('{{ asset('imagenes/imagenesEventos/' . $cat) }}');"></figure>
    <div class="flex flex-wrap basis-9/12 justify-evenly space-y-2 font-bold">

        {{-- TITULO --}}
        @if ($evento->titulo)
            <h3 class="basis-full text-center text-xl py-5 rounded-tr-2xl">{{$evento->titulo}}</h3>
        @endif




        @if ($evento->descripcion)
            <p class="max-h-[3.15rem] text-xs overflow-hidden text-ellipsis basis-full font-normal pl-3 pr-2">{{$evento->descripcion}}</p>
        @endif




        <div class="basis-3/6 px-2">
            @if ($evento->inicio)
                <p>Inicio: <span class="font-normal text-xs">{{ date('d M Y',strtotime(explode(" ",$evento['inicio'])[0]))}} @if( count(explode(" ",$evento['inicio'])) > 1) - {{date('H:i',strtotime(explode(" ",$evento['inicio'])[1]))}}@endif</span></p>
            @endif
            @if ($evento->fin)
                <p>Fin: <span class="font-normal text-xs">{{ date('d M Y',strtotime(explode(" ",$evento['fin'])[0]))}} - {{date('H:i',strtotime(explode(" ",$evento['fin'])[1]))}}</span></p>                
            @endif
            
        </div>




       <div class="basis-3/6 px-2">
        @if ($evento->horas)
            <p>Horario: <span class="font-normal text-xs">{{$evento->horas}} @if($evento->dias) {{$evento->dias}} @endif </span></p>
        @endif






        @if ($evento->precio)
            <p>Precio:<span class="font-normal text-xs bg-opacity-50"> {{$evento->precio}}</span></p>
        @endif

        
           
       </div>
       @if ($evento->calle)
            <p class="basis-full px-2">Lugar: <span class="font-normal text-xs">{{$evento->calle.", ".$evento->cp.", ".$evento->localidad}} @if ($evento->lugar) -> {{$evento->lugar}} @endif </span></p>
        @endif











        @if ($evento->conex)
            <p class="font-normal basis-full text-colorDetalles"><a href="{{$evento->conex}}">Link al evento</a> </p>
        @endif
       {{-- <div class="basis-full">
            @if(array_key_exists('address',$evento)&& array_key_exists('area',$evento['address'])&& array_key_exists('street-address',$evento['address']['area']))<p>Lugar: <span class="font-normal text-xs">{{$evento['address']['area']['street-address'].", ".$evento['address']['area']['postal-code'].", ".$evento['address']['area']['locality']." -> ".$evento['event-location']}}</span></p>@endif                        
            @if (array_key_exists('link',$evento))
            <a href="{{$evento['link']}}" class="font-normal ">Link al evento</a>
            @endif

       </div> --}}
    </div>
    <div class="hidden basis-full min-h-96 items-stretch ml-4">
        @if ($evento->latitud)
            <div class="basis-1/2 self-center h-3/4 md:self-stretch" id={{$evento->latitud.'|'.$evento->longitud}}></div>
        @endif
        <div class="grow flex flex-col justify-around text-xs md:text-base">
            @if ($yo->admin || !$ajustes->aniadir_cal)
                <button class="mx-auto rounded-2xl bg-colorDetalles border-4 border-colorCabera px-1 md:px-6 py-3 w-1/2 btnCal">Añadir al calendario</button>
            @endif
            <button class="mx-auto rounded-2xl bg-colorDetalles border-4 border-colorCabera px-1 md:px-6 py-3 w-1/2 btnElim">Retirar</button>

        </div>
        
    </div>
    <form action="">
        <input type="hidden" name="evento" value={{$evento->id}}>
    </form>

</div>