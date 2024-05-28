@extends('partials/plantillaServicios')
@section('rutaJs','js/eventos.js')
@section('categorias')
@php
    $apis = Session::get('listaEventos');
@endphp

<div id="buscadorCat" class="flex-grow flex justify-center cursor-pointer hover:text-colorDetalles">
    <span class="h-full flex flex-col justify-center border-b-4 border-white  selected">BUSCADOR</span>
</div>
<div id= "listaCat"class="flex-grow flex justify-center cursor-pointer  hover:text-colorDetalles ">
    <span class="h-full flex flex-col justify-center">NUESTRA LISTA</span>
</div>

@endsection
@section('contenidoServicio')


<section id="buscador" class="mostrar flex items-stretch h-full w-full p-3 relative overflow-y-scroll">
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
            
            @foreach ($apis as $eventos)
            @foreach ($eventos as $evento)
                
            {{-- @php
                echo '<pre>';
                var_dump($evento);
                echo '<pre>';
            @endphp --}}
            <div class="min-h-20  w-full  flex items-stretch justify-center flex-wrap evento" >
                <figure class="basis-1/6 imagenEvento m-0"></figure>
                <div class="flex flex-wrap basis-5/6 justify-evenly space-y-2 font-bold indent-3">

                    {{-- TITULO --}}
                    @if (array_key_exists('titulo',$evento))
                        <h3 class="basis-full text-center text-xl py-5">{{$evento['titulo']}}</h3>
                    @endif




                    @if (array_key_exists('descripcion',$evento))
                        <p class="max-h-[3.15rem] text-xs overflow-hidden text-ellipsis basis-full font-normal pl-3">{{$evento['descripcion']}}</p>
                    @endif




                    <div class="basis-3/6">
                        @if (array_key_exists('inicio',$evento)&&array_key_exists('fin',$evento))
                            <p>Inicio: <span class="font-normal text-xs">{{ date('d M Y',strtotime(explode(" ",$evento['inicio'])[0]))}} - {{date('H:i',strtotime(explode(" ",$evento['inicio'])[1]))}}</span></p>
                            <p>Fin: <span class="font-normal text-xs">{{ date('d M Y',strtotime(explode(" ",$evento['fin'])[0]))}} - {{date('H:i',strtotime(explode(" ",$evento['fin'])[1]))}}</span></p>
                        @endif
                        
                   </div>




                   <div class="basis-3/6">
                    @if (array_key_exists('horas',$evento))
                        <p>Horario: <span class="font-normal text-xs">{{$evento['horas']}} @if(array_key_exists('dias',$evento)) {{$evento['dias']}} @endif </span></p>
                    @endif






                    @if (array_key_exists('precio',$evento))
                        <p>Precio:<span class="font-normal text-xs bg-opacity-50"> {{$evento['precio']}}</span></p>
                    @endif

                    
                       
                   </div>
                   @if (array_key_exists('calle',$evento))
                        <p class="basis-full">Lugar: <span class="font-normal text-xs">{{$evento['calle'].", ".$evento['cp'].", ".$evento['localidad']}} @if (array_key_exists('lugar',$evento)) -> {{$evento['lugar']}} @endif </span></p>
                    @endif











                    @if (array_key_exists('conex',$evento))
                        <p class="font-normal basis-full text-colorDetalles"><a href="{{$evento['conex']}}">Link al evento</a> </p>
                    @endif
                   {{-- <div class="basis-full">
                        @if(array_key_exists('address',$evento)&& array_key_exists('area',$evento['address'])&& array_key_exists('street-address',$evento['address']['area']))<p>Lugar: <span class="font-normal text-xs">{{$evento['address']['area']['street-address'].", ".$evento['address']['area']['postal-code'].", ".$evento['address']['area']['locality']." -> ".$evento['event-location']}}</span></p>@endif                        
                        @if (array_key_exists('link',$evento))
                        <a href="{{$evento['link']}}" class="font-normal ">Link al evento</a>
                        @endif

                   </div> --}}
                </div>
                <div class="hidden basis-full min-h-96 items-stretch m-4">
                    @if (array_key_exists('latitud',$evento))
                        <div class="basis-1/2" id={{$evento['latitud'].'|'.$evento['longitud']}}></div>
                    @endif
                    <div class="grow flex flex-col justify-around">
                        <button class="mx-auto rounded-md bg-colorDetalles b-2 border-colorComplem px-4 py-6">Añadir a "Nuestra Lista"</button>
                        <button class="mx-auto rounded-md bg-colorDetalles b-2 border-colorComplem px-4 py-6">Añadir al "Calendario"</button>

                    </div>
                    
                </div>
                <form action="">
                    <input type="hidden" name="evento" value={{$evento['url']}}>
                </form>

            </div>
            <hr class="my-6"></hr>
            @endforeach

            @endforeach
            
            <div class="h-20 border-2 border-black"></div>
        </div>
    </div>

</section>
<section id="lista" class="hidden">Lista</section>


{{-- <script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAOsoMk-1yucFTUwhzq4oummSkyyjReN58&loading=async&libraries=places&callback=initMap">
</script> --}}


@endsection


