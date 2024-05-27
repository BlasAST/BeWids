@extends('partials/plantillaServicios')
@section('rutaJs','js/eventos.js')
@section('categorias')
@php
    $eventos = Session::get('listaEventos');
@endphp

<div id="buscadorCat" class="flex-grow flex justify-center cursor-pointer hover:text-colorDetalles">
    <span class="h-full flex flex-col justify-center border-b-4 border-white  selected">BUSCADOR</span>
</div>
<div id= "listaCat"class="flex-grow flex justify-center cursor-pointer  hover:text-colorDetalles ">
    <span class="h-full flex flex-col justify-center">NUESTRA LISTA</span>
</div>

@endsection
@section('contenidoServicio')


<section id="buscador" class="mostrar flex items-stretch min-h-full p-3 relative">
    <figure class="absolute w-7 h-7 m-2 logoCancel btnBurger"></figure>
    <div class="bg-colorCabera text-white text-center basis-1/4 flex flex-col space-y-2 p-2 categorias">
        <h1 class="mt-2">Categorias</h1>
        <button class="bg-colorComplem py-2">Restauración</button>
        <button class="bg-colorComplem py-2">Vida Nocturna</button>
        <button class="bg-colorComplem py-2">Sitios Públicos</button>
        <button class="bg-colorComplem py-2">Conciertos</button>
        <button class="bg-colorComplem py-2">Naturaleza</button>
        <button class="bg-colorComplem py-2">Infantil</button>
        <button class="bg-colorComplem py-2">Extremo</button>
        <button class="bg-colorComplem py-2">Familiar</button>
        <button class="bg-colorComplem py-2">Moderno</button>
    </div>
    <div class="basis-3/4 flex flex-col pl-2 space-y-1">
        <div class="basis-1/12 flex items-center pb-1">
            <form action="" class="w-1/2 bg-colorCabera mx-auto h-5/5 rounded-2xl flex items-stretch p-1">
                <button type="submit" class="basis-1/12 logoBuscador m-[2px]"></button>
                <input type="text" name="buscador" placeholder="Buscar evento....." class="bg-transparent grow placeholder:text-gray-400 text-white indent-1 focus:outline-none buscador" >
                <button type="button" class="basis-2/6 text-white border-l-[1px] border-white">Filtrar</button>
            </form>
        </div>
        <div class="grow space-y-4 pt-2">
            @foreach ($eventos as $evento)
            <div class="min-h-20 border-2 border-black flex items-stretch" >
                <figure class="basis-1/6"></figure>
                <div class="flex flex-wrap basis-5/6 justify-evenly space-y-2 font-bold">
                    <h3 class="basis-full text-center text-xl ">{{$evento['title']}}</h3>
                    @if (array_key_exists('description',$evento))
                        <p class="max-h-[3.15rem] text-xs overflow-hidden text-ellipsis basis-full font-normal">{{$evento['description']}}</p>
                    @endif
                    <div class="basis-3/6">
                        @if (array_key_exists('dtstart',$evento)&&array_key_exists('dtend',$evento))
                            <p>Inicio: <span class="font-normal text-xs">{{ date('d M Y',strtotime(explode(" ",$evento['dtstart'])[0]))}} - {{date('H:i',strtotime(explode(" ",$evento['dtstart'])[1]))}}</span></p>
                            <p>Fin: <span class="font-normal text-xs">{{ date('d M Y',strtotime(explode(" ",$evento['dtend'])[0]))}} - {{date('H:i',strtotime(explode(" ",$evento['dtend'])[1]))}}</span></p>
                        @endif
                        
                   </div>
                   <div class="basis-3/6">
                    @if (array_key_exists('dtstart',$evento))
                        <p>Horario: <span class="font-normal text-xs">{{$evento['time']}} @if(array_key_exists('recurrence',$evento)) {{$evento['recurrence']['days']}} @endif </span></p>
                    @endif
                    @if (array_key_exists('price',$evento))
                        <p>Precio:<span class="font-normal text-xs"> @if ($evento['free'] == 0)
                            {{$evento['price']}}
                        @else
                            GRATIS 
                        @endif </span></p>
                    @endif
                       
                   </div>
                   <div class="basis-full">
                        @if(array_key_exists('address',$evento)&& array_key_exists('area',$evento['address'])&& array_key_exists('street-address',$evento['address']['area']))<p>Lugar: <span class="font-normal text-xs">{{$evento['address']['area']['street-address'].", ".$evento['address']['area']['postal-code'].", ".$evento['address']['area']['locality']." -> ".$evento['event-location']}}</span></p>@endif                        
                        @if (array_key_exists('link',$evento))
                        <a href="{{$evento['link']}}" class="font-normal ">Link al evento</a>
                        @endif

                   </div>
                   

                </div>

            </div>
            @endforeach
            
            <div class="h-20 border-2 border-black "></div>
            <div class="h-20 border-2 border-black "></div>
            <div class="h-20 border-2 border-black "></div>
            <div class="h-20 border-2 border-black "></div>
            <div class="h-20 border-2 border-black "></div>
            <div class="h-20 border-2 border-black "></div>
            <div class="h-20 border-2 border-black "></div>
            <div class="h-20 border-2 border-black "></div>
            <div class="h-20 border-2 border-black "></div>
            <div class="h-20 border-2 border-black "></div>
            <div class="h-20 border-2 border-black "></div>
            <div class="h-20 border-2 border-black "></div>
            <div class="h-20 border-2 border-black "></div>
            <div class="h-20 border-2 border-black "></div>
            <div class="h-20 border-2 border-black "></div>
        </div>
    </div>

</section>
<section id="lista" class="hidden">Lista</section>





@endsection


