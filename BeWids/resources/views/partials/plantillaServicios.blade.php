@extends('partials/basePortales')
<!-- @section('rutaJs','../js/chatYEncuestas')
@section('rutaJs2','js/basicServicios.js') PUEDES LLAMAR A LAS RUTAS DESDE EL SERVICIO QUE CREES-->
@section('contenido')



<div class="contenedor w-[95%] h-[92dvh] flex-col mx-auto mt-4 border-black rounded-lg border-2">
   <header class="h-[25%] bg-colorCabera w-full rounded-t-lg flex justify-end logoServicio flex-col">
   
      <div class="basis-3/6">
         <img src="{{asset('imagenes/imagenesTailwind/flecha.png')}}" class="w-10 rotate-180 ml-[5%]">
      </div>
      <div class="categorias bg-slate-500 w-full basis-2/6">
         @yield('categorias')
      </div>
   </header>
   <main class="h-[75%] bg-colorMain rounded-b-lg ">
      @yield('contenidoServicio')
   </main>
</div> 
@endsection