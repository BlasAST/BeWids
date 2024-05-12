@extends('partials.base')
@section('rutaEstilos','../css/estilosCuentas.css')
@section('rutaJs','../js/cuentas.js')

@section('contenido')
@php
    $gastos = Session::get('gastos');
    $participantes = Session::get('participantes');
    $tipos = ['Supermercado','Alcohol','Cine','Conciento','ropa','pepe'];
@endphp
<header>
    <span>Gastos</span>
    <span>Gráficos</span>
    <span>Cuentas</span>
    <span>Notificaciones</span>
</header>

{{-- GASTOS --}}
<main>
    <div class="gastos">
        <div>
            @foreach ($gastos as $gasto)
                <div class="gasto">
                    <p>{{$gasto->titulo}}</p>
                    <p>{{$gasto->cantidad}}</p>
                    <p>Pagado por: {{$gasto->pagado_por}}</p>
                    <p>{{$gasto->fecha}}</p>
                </div>
            @endforeach
        </div>
        <div>
            <h1>Añadir gasto</h1>
            <form action="{{route('aniadirGasto')}}" method="POST">
                @csrf
                <div>
                    <label for="titulo">Título</label>
                    <input type="text" name="titulo">
                </div>
                <div>
                    <label for="tipo">Tipo:</label>
                    <select name="tipo">
                        @foreach($tipos as $tipo)
                            <option value="{{$tipo}}">{{$tipo}}</option>
                        @endforeach
                        <option value="">Otro</option>
                    </select>
                </div>
                <div>
                    <label for="cantidad">Cantidad</label>
                    <input type="number" step="0.01" name="cantidad">
                </div>
                {{-- <div>
                    <select name="divisa">
                        @foreach($divisas as $divisa)
                            <option value="">{{$divisa}}</option>
                        @endforeach
                    </select>
                    <label for="divisa">Divisa</label>
                </div> --}}
                <div>
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha">
                </div>
                <div>
                    <label for="pagador">Pagado por:</label>
                    <select name="pagador">
                        @foreach($participantes as $user)
                            <option value="{{$user->nombre_en_portal}}">{{$user->nombre_en_portal}}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="">A pagar por:</label>
                    <div class="participantes">
                        @foreach ($participantes as $user)
                        <div>
                            <input type="checkbox" value="{{$user->nombre_en_portal}}" name="participantes[]">
                            <label for="participantes[]">{{$user->nombre_en_portal}}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <button>enviar</button>
    
            </form>
        </div>
    </div>
</main>