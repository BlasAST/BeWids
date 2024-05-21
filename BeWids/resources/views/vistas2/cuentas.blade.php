@extends('partials.base')
@section('rutaEstilos','../css/estilosCuentas.css')
@section('rutaEstilos2','../css/estilosBaseServicios.css')
@section('rutaJs','../js/basicServicios.js')
@section('rutaJs','../js/cuentas.js')



@section('contenido')
@include('partials.header')

@php
    $gastos = Session::get('gastos');
    $reembolsosPorPagar = Session::get('reembolsosSin');
    $reembolsosPagados = Session::get('reembolsosPag');
    $participantes = Session::get('participantes');
    $tipos = ['Supermercado','Alcohol','Cine','Conciento','ropa','pepe'];
    $deudaMayor = 0;
    foreach ($participantes as $participante) {
        if(abs($participante->deuda)>$deudaMayor)
            $deudaMayor = abs($participante->deuda);
    }
@endphp
{{-- <header>
    <span>Gastos</span>
    <span>Gráficos</span>
    <span>Cuentas</span>
    <span>Notificaciones</span>
</header> --}}

{{-- GASTOS --}}
<main>
    <div class="categorias">
        <span id="gastos"><p class="selected">Gastos</p></span>
        <span id="graficos"><p>Gráficos</p></span>
        <span id="cuentas"><p>Cuentas</p></span>
        <span id="notificaciones"><p>Notificaciones</p></span>
    </div>
    <div class="gastos mostrar">
        <div>
            @if (count($gastos) == 0)
                <h1>No hay gastos todavía</h1>                
            @endif
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
                    <input type="number" step="0.01" min="0" name="cantidad">
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
                            <option value="{{$user->nombre_en_portal}}" @if(Auth::id() == $user->id_usuario)
                                selected
                            @endif>{{$user->nombre_en_portal}}</option>
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
    <div class="graficos">
        @foreach ($participantes as $participante)
            @php
                if($deudaMayor != 0) $porcentaje = (abs($participante->deuda)/$deudaMayor)*100;
            @endphp
            <div>
                @if ($participante->deuda > 0)
                    <div class="barra" style="background-image: linear-gradient(to top, #4465B8,var(--color-secundario) {{$porcentaje.'%'}}, var(--color-secundario) 100%)">
                        <figure style="color:#4465B8; bottom: {{$porcentaje - 5 . "%" }}" ><p>{{"+".$participante->deuda}}</p></figure>
                    </div>
                @endif
                @if($participante->deuda < 0)
                    <div class="barra" style="background-image: linear-gradient(to top, #D63865,var(--color-secundario) {{$porcentaje."%"}}, var(--color-secundario) 100%)">
                        <figure style="color:#D63865; bottom: {{$porcentaje - 5 . "%"}}"><p>{{$participante->deuda}}</p></figure>
                    </div>
                @endif
                <p>{{$participante->nombre_en_portal}}</p>
            </div>
        @endforeach
    </div>
    <div class="cuentas">
        <div>
            @if (count($reembolsosPorPagar)==0)
                <h1>No se hay nada que reembolsar actualmente</h1>
            @else
                @foreach ($reembolsosPorPagar as $reembolso)
                    <div class="reembolso">
                        <p>{{$reembolso->pagador}} tiene que rembolsar a {{$reembolso->receptor}}</p>
                        <p>Cantidad: {{abs($reembolso->cantidad)}}</p>
                        <button id={{$reembolso -> id}}>Saldar deuda</button>
                    </div>
                @endforeach
            @endif
            
        </div>
        <div>
            @if (count($reembolsosPagados)==0)
                <h1>No se ha realizado ningún reembolso aún</h1>
            @else
                @foreach ($reembolsosPagados as $reembolso)
                    <div class="reembolso">
                        <p>Reembolso de {{$reembolso->pagador}} a {{$reembolso->receptor}}</p>
                        <p>{{abs($reembolso->cantidad)}}</p>
                    </div>
                @endforeach
            @endif
            
        </div>
    </div>
    <div class="notificaciones">Notificaciones</div>
</main>
@endsection