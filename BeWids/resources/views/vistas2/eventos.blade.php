@extends('partials/plantillaServicios')
@section('rutaJs','js/eventos.js')
@section('categorias')

<div id="buscadorCat" class="flex-grow flex justify-center cursor-pointer hover:text-colorDetalles">
    <span class="h-full flex flex-col justify-center border-b-4 border-white  selected">BUSCADOR</span>
</div>
<div id= "listaCat"class="flex-grow flex justify-center cursor-pointer  hover:text-colorDetalles ">
    <span class="h-full flex flex-col justify-center">NUESTRA LISTA</span>
</div>

@endsection
@section('contenidoServicio')


<section id="buscador" class="mostrar">Buscador</section>
<section id="lista" class="hidden">Lista</section>





@endsection


