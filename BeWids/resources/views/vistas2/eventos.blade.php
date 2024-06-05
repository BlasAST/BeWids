@extends('partials/plantillaServicios')
@section('rutaJs','js/eventos.js')
@section('categorias')
@php
    use App\Models\Eventos;
    use App\Models\MisEventos;

    $nuestrosEventos = MisEventos::where('id_portal',Session::get('portal')->id)->get();
    $pantalla = Session::get('eventos');
    var_dump($pantalla);

    $apis = Session::get('paginaEventos');
    $categorias = [
            'Culturales',
            'Actividades Deportivas',
            'Mercadillos',
            'Museos',
            'Parques y jardínes',
            'Carreras',
            'Teatro y espectáculos',
            'Cine',
            'Música',
        ];
    $edades = Eventos::select('edad')->distinct()->pluck('edad')->toArray();
    $edadesDescompuestas = [];
    foreach ($edades as $edad) {
        $partes = explode(',', $edad);
        foreach ($partes as $parte) {
            $edadesDescompuestas[] = trim($parte);
        }
    }
    
    // Obtener los valores únicos
    $edades = array_unique($edadesDescompuestas);
@endphp

<div id="buscadorCat" class="flex-grow flex justify-center cursor-pointer hover:text-colorDetalles">
    <span class="h-full flex flex-col justify-center @if($pantalla == 'buscador'||!$pantalla) border-b-4 border-white  selected @endif">BUSCADOR</span>
</div>
<div id= "listaCat"class="flex-grow flex justify-center cursor-pointer  hover:text-colorDetalles">
    <span class="h-full flex flex-col justify-center @if($pantalla == 'lista') border-b-4 border-white  selected @endif ">NUESTRA LISTA</span>
</div>

@endsection
@section('contenidoServicio')


<section id="buscador" class="@if($pantalla == 'buscador' || !$pantalla) mostrar flex @else hidden @endif items-stretch h-full w-full p-3 relative overflow-y-scroll contenedor">
    <figure class="fixed w-7 h-7 m-2 logoCancel logoDesp btnBurger"></figure>
    <div class="bg-colorCabera text-white text-center basis-1/4 flex flex-col justify-evenly p-2 min-h-[70dvh] categorias">
        <h1 class="mt-2">Categorias</h1>
        @foreach ($categorias as $categoria)
        <button class="bg-colorComplem py-2 border-colorDetalles" id={{str_replace(" ","-",$categoria)}}>{{$categoria}}</button>
            
        @endforeach
        <button class="bg-colorDetalles py-2 btnCat">Buscar</button>

    </div>
    <div class="basis-3/4 flex flex-col pl-2 space-y-1">
        <div class="basis-1/12 flex flex-col items-center pb-1 relative">
            <form action="" class="w-1/2 bg-colorCabera rounded-2xl  items-stretch p-1 flex contBusc">
                <button id="btnBuscar" type="submit" class="basis-1/12 logoBuscador m-[2px]"></button>
                <input type="text" name="buscador" placeholder="Buscar evento....." class="bg-transparent grow placeholder:text-gray-400 text-white indent-1 focus:outline-none buscador" >
                <button type="button" class="basis-2/6 text-white border-l-[1px] border-white filtrar">Filtrar</button>
            </form>
            <div class="hidden w-1/2 flex-col bg-colorCaberaTras2 min-h-5 max-h-[30dvh] rounded-b-2xl absolute top-[90%] border-t border-colorLetra overflow-y-scroll text-gray-300">
                <p class="hidden w-full p-5 hover:bg-colorCabera hover:text-colorComplem"></p>
            </div>
            <div class=" hidden w-1/2 bg-colorCaberaTras2 rounded-b-2xl absolute top-[90%] border-t border-colorLetra text-gray-300 contFiltros">
                    <div class="grow flex flex-col items-stretch mt-4 pl-2">
                        @foreach ($edades as $edad)
                            @if ($edad)
                                <div>
                                    <input type="checkbox" name="{{$edad}}" style="">
                                    <label for="{{$edad}}">{{$edad}}</label>            
                                </div>  
                            @endif
                            
                        @endforeach

                    </div>
                    <div class="grow flex flex-col justify-around">
                        <div>
                            <input type="checkbox" name="gratis" style="">
                            <label for="gratis">Gratis</label>            
                        </div> 
                        <button class="bg-colorComplem rounded-xl btnFiltrar">Filtrar</button>
                        
                    </div>

            </div>
        </div>
        <div class="contPag flex justify-center items-stretch w-1/2 mx-auto gap-3 pt-4 text-colorLetra">
            
        </div>
        <div class="grow space-y-4 pt-2 contEventos">           
        </div>
        <div class="contPag flex justify-center items-stretch w-1/2 mx-auto gap-3 py-4 text-colorLetra">
            
        </div>
    </div>

</section>
<section id="lista" class="@if($pantalla == 'lista') mostrar flex @else hidden @endif w-full flex-col-reverse nuestrosEventos py-5 contenedor">
    @foreach ($nuestrosEventos as $evento)
        {{view('partials.divMiEvento', ['evento' => $evento])}}
    @endforeach
    <form class="formNuevoEvt w-full hidden" action="" method="POST">
        @csrf
        <div class="flex flex-wrap justify-center w-1/2 mx-auto text-center gap-3 py-4">
            <label for="titulo" class="basis-full text-colorLetra">Título del evento:</label>
            <input type="text" name="titulo" class="basis-full rounded-xl border border-colorDetalles mb-4">
            <label for="descripción" class="basis-full text-colorLetra">Descripción:</label>
            <textarea name="descripcion" cols="30" rows="5" class="mb-4 basis-full rounded-xl border border-colorDetalles"></textarea>
            <label for="fecha" class="basis-full text-colorLetra">Fecha y hora:</label>
            <input type="date" name="fecha" class="mb-4 basis-5/12 rounded-xl border border-colorDetalles text-center">
            <input type="time" name="hora" class="mb-4 basis-5/12 rounded-xl border border-colorDetalles text-center">
            <input type="submit" name="enviar" value="Crear" class="basis-3/4 mx-auto rounded-3xl bg-colorDetalles py-4">
        </div>
        
    </form>
    <button class="w-5/12 mx-auto rounded-3xl bg-colorDetalles py-4 btnNuevoEvt">Evento personalizado</button>

</section>


{{-- <script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAOsoMk-1yucFTUwhzq4oummSkyyjReN58&loading=async&libraries=places&callback=initMap">
</script> --}}


@endsection


