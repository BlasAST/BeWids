@extends('partials/basePortales')
@section('rutaJs',"@yield('rutaJs')") <!-- '../js/chatYEncuestas'-->
@section('rutaJs2','js/basicServicios2.js') <!-- PUEDES LLAMAR A LAS RUTAS DESDE EL SERVICIO QUE CREES-->
@section('contenido')



<div class="contenedor w-[95%] h-[92dvh] flex-col mx-auto mt-4 border-black rounded-lg border-2">
   <header class="h-[25%] bg-colorCabera w-full rounded-t-lg flex justify-end logoServicio flex-col text-colorLetra">
   
      <div class="basis-3/6 flex items-center">
         <img src="{{asset('imagenes/imagenesTailwind/flecha.png')}}" class="w-10 rotate-180 ml-[5%] btnVolver">
      </div>
      <div class="categoria flex w-full basis-3/6">
         @yield('categorias')
      </div>
   </header>
   <main class="h-[75%] bg-colorMain rounded-b-lg overflow-y-scroll scroll-">
      @yield('contenidoServicio')
   </main>
</div> 
@endsection