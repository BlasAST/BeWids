@extends('partials/basePortales')
@section('rutaJs',"@yield('rutaJs')") <!-- '../js/chatYEncuestas'-->
@section('rutaJs2','js/basicServicios2.js') <!-- PUEDES LLAMAR A LAS RUTAS DESDE EL SERVICIO QUE CREES-->
@section('contenido')



<div class="contenedor w-[95%] h-[92dvh] flex-col mx-auto mt-4 border-black rounded-2xl border-2">
   <header class="h-[25%] bg-colorCabera w-full rounded-t-2xl flex justify-end logoServicio flex-col text-colorLetra">
   
      <div class="basis-1/2 flex items-center">
         <img class="w-10 ml-[5%] btnVolver" src="{{asset('imagenes/imagenesTailwind/back.svg')}}">
      </div>
      <div class="categoria flex w-full basis-1/2">
         @yield('categorias')
      </div>
   </header>
{{-- //overflow-y-scroll scroll- --}}
   <main class="h-[75%] bg-colorMain rounded-b-2xl overflow-y-auto">
      @yield('contenidoServicio')
   </main>
</div> 
@endsection