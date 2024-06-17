@php
    $cat = explode(" ",$evento->categoria)[0] . ".jpg";
@endphp
<div class="min-h-32 md:min-h-20 w-[80%] sm:w-full  flex items-stretch justify-center flex-wrap evento my-6 sh" >
    <figure class="basis-3/12  m-0 imagenEvento" style="background-image: url('{{ asset('imagenes/imagenesEventos/' . $cat) }}');"></figure>
    <div class="flex flex-col md:flex-row md:flex-wrap basis-9/12 justify-evenly space-y-2 font-bold">

        {{-- TITULO --}}
        @if ($evento->titulo)
            <h3 class="basis-full text-center text-lg md:text-xl py-5">{{$evento->titulo}}</h3>
        @endif




        @if ($evento->descripcion)
            <p class="max-h-[3.15rem] text-[9px] sm:text-xs overflow-hidden text-ellipsis basis-full font-normal pl-3 pr-2">{{$evento->descripcion}}</p>
        @endif




        <div class="basis-3/6 px-2">
            @if ($evento->inicio)
                <p class="text-sm md:text-base flex lg:items-center flex-col lg:flex-row">Inicio: <span class="font-normal text-[9px] sm:text-xs">{{ date('d M Y',strtotime(explode(" ",$evento['inicio'])[0]))}} - {{date('H:i',strtotime(explode(" ",$evento['inicio'])[1]))}}</span></p>
            @endif
            @if ($evento->fin)
                <p class="text-sm md:text-base flex lg:items-center flex-col lg:flex-row">Fin: <span class="font-normal text-[9px] sm:text-xs">{{ date('d M Y',strtotime(explode(" ",$evento['fin'])[0]))}} - {{date('H:i',strtotime(explode(" ",$evento['fin'])[1]))}}</span></p>                
            @endif
            
        </div>




       <div class="basis-3/6 px-2 ">
        @if ($evento->horas)
            <p class=" text-sm md:text-base flex lg:items-center flex-col lg:flex-row">Horario: <span class="font-normal text-[9px] sm:text-xs">{{$evento->horas}} @if($evento->dias) {{$evento->dias}} @endif </span></p>
        @endif






        @if ($evento->precio)
            <p class="text-sm md:text-base flex flex-col lg:flex-row">Precio:<span class="font-normal text-[9px] sm:text-xs bg-opacity-50"> {{$evento->precio}}</span></p>
        @endif

        
           
       </div>
       @if ($evento->calle)
            <p class="  px-2 basis-full text-sm md:text-base flex flex-col lg:flex-row">Lugar: <span class="font-normal text-[9px] sm:text-xs">{{$evento->calle.", ".$evento->cp.", ".$evento->localidad}} @if ($evento->lugar) -> {{$evento->lugar}} @endif </span></p>
        @endif











        @if ($evento->conex)
            <p class="font-normal  px-2 basis-full text-colorDetalles"><a href="{{$evento->conex}}">Link al evento</a> </p>
        @endif
       {{-- <div class="basis-full">
            @if(array_key_exists('address',$evento)&& array_key_exists('area',$evento['address'])&& array_key_exists('street-address',$evento['address']['area']))<p>Lugar: <span class="font-normal text-[9px] sm:text-xs">{{$evento['address']['area']['street-address'].", ".$evento['address']['area']['postal-code'].", ".$evento['address']['area']['locality']." -> ".$evento['event-location']}}</span></p>@endif                        
            @if (array_key_exists('link',$evento))
            <a href="{{$evento['link']}}" class="font-normal ">Link al evento</a>
            @endif

       </div> --}}
    </div>
    <div class="hidden basis-full min-h-96 items-stretch m-4">
        @if ($evento->latitud)
            <div class="basis-1/2" id={{$evento->latitud.'|'.$evento->longitud}}></div>
        @else
            <span id="mapa"></span>
        @endif
        @if ($yo->admin || !$ajustes->aniadir_lista)
            <div class="grow flex flex-col justify-around">
                <button class="mx-auto rounded-md w-4/6 justify-evenly text-colorLetra bg-colorDetalles b-2 border-colorComplem px-4 py-6 flex"> <figure class=" animate-spin hidden logoCheck logoCarga  w-1/6 h-6"></figure>Añadir a "Nuestra Lista"</button>
            </div>
        @endif
        
    </div>
    <form action="">
        <input type="hidden" name="evento" value={{$evento->id}}>
    </form>

</div>