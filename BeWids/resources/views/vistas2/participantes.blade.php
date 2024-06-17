@extends('partials/plantillaServicios')
@section('rutaJs','js/participantes.js')
@section('categorias')
@php

    use App\Models\User;
    $portal = Session::get('portal');
    $participanteUser = Session::get('participanteUser');
 
@endphp

<div id="participantesCat" class="flex-grow flex justify-center hover:text-colorDetalles">
    <span class="h-full flex flex-col justify-center border-b-4 border-white  selected">Participantes</span>
</div>

@endsection
@section('contenidoServicio')

@include('componentes.componenteChatEncuestas')
<section id="participantes" class="mostrar flex h-full flex-col gap-6 py-5 items-center">
    @foreach ($participantes as $participante)
        @if ($participante->nombre_en_portal != $participanteUser->nombre_en_portal)
            @php
                $user = User::find($participante->id_usuario);
            @endphp
            <div class="bg-colorCaberaTras border-2 border-colorCabera rounded-md flex @if($participante->admin) text-colorDetalles @else text-colorComplem @endif w-[min(80%,800px)] justify-around py-2">
                <div class="flex flex-col items-center justify-center gap-1">
                    <h1 class="text-colorLetra">Nombre en portal</h1>
                    <h2>{{$participante->nombre_en_portal}}</h2>
                </div>
                <div class="flex flex-col items-center justify-center gap-1">
                    <h1 class="text-colorLetra">Nobre de Usuario</h1>
                    <h2>@if ($user) {{$user->name}} @else Sin usuario asignado @endif</h2>
                </div>
                <div class="flex flex-col items-center justify-center gap-1">
                    <h1 class="text-colorLetra">Admin</h1>
                    <h2>@if ($participante->admin) Sí @else No @endif</h2>
                </div class="flex flex-col items-center justify-center gap-1">
                @if ($participanteUser->admin || !$ajustes->crear_participante)
                    <div class="flex flex-col text-colorLetra gap-1">
                        <button class="bg-colorBarra2 px-2 rounded-md btnDel">Eliminar Participante</button>
                        @if ($user)
                            <button class="bg-colorBarra2 px-2 rounded-md btnDes">Desvincular usuario</button>
                        @endif
                        @if (!$participante->admin)
                            <button class="bg-colorBarra2 px-2 rounded-md btnAsc">Ascender a Admin</button>
                        @endif
                        <input type="hidden" value="{{$participante->id}}">
                    </div>
                @endif

            </div>
        @endif
    @endforeach
    @if ($participanteUser->admin || !$ajustes->crear_participante)
        <button class="bg-colorDetalles rounded-2xl px-4 py-1 text-colorLetra btnNuevoPart">Crear Participante</button>
        <form action="/crearParticipante" method="POST" class="flex-col hidden w-[min(80%, 700px)] text-colorLetra items-center gap-2 formNuevo">
            @csrf
            <label for="nombre">Nombre Participante</label>
            <input type="text" name='nombre' class="rounded-xl focus:outline-none bg-colorCaberaTras2 indent-2">
            <button type="submit" class="my-2 bg-colorDetalles rounded-2xl px-4 py-1">Crear</button>
        </form>
    @endif
</section>
<div class="errorElim z-30 absolute top-0 h-full w-full hidden justify-center items-center bg-colorFondo bg-opacity-60">
    <div class="bg-colorSecundario text-white w-[60%] py-10 text-center flex flex-col justify-start rounded-xl gap-5 items-center">
        <h1>No puedes eliminar participantes con cuentas pendientes.</h1>
        <h1>Asegurate de que el participante no deba ni le deban dinero.</h1>
        <button class="bg-colorDetalles w-1/4 rounded-xl btnCerrar">OK</button>
    </div>
</div>

@endsection