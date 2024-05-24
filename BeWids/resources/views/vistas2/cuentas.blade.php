
@extends('partials.base')
@section('rutaEstilos','../css/estilosCuentas.css')
@section('rutaEstilos2','../css/estilosBaseServicios.css')

@section('rutaJs','../js/basicServicios.js')
@section('rutaJs2','../js/cuentas.js')



@section('contenido')
@include('partials.header')
@vite('resources/css/app.css')

@php
    use App\Models\Participantes;

    $gastos = Session::get('gastos');
    $portal = Session::get('portal');
    $reembolsosPorPagar = Session::get('reembolsosSin');
    $reembolsosPagados = Session::get('reembolsosPagados');
    $participantes = Session::get('participantes');
    $participanteUser = Session::get('participanteUser');
    $notificaciones = Session::get('notificaciones');
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
                        <button class="bg-blue-950 w-1/2 mx-auto" >Saldar deuda
                            <form method="POST" action="{{route('reembolso')}}">
                                @csrf
                                <input type="hidden" name="reembolso" value="{{json_encode($reembolso)}}">
                            </form>
                        </button>
                        @if ($reembolso -> solicitado)
                            <span class="bg-green-200 w-1/3 mx-auto border-2 text-center border-green-600">Solicitado</span>
                            
                        @endif
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
    <div class="notificaciones flex-col">
        @foreach ($notificaciones as $notificacion)      
            @if(($notificacion && $notificacion->receptor == $participanteUser->nombre_en_portal) || ($participanteUser->admin && !(Participantes::where('id_portal',$portal->id)->where('nombre_en_portal',$notificacion->receptor)->first()->id_usuario)))
                <div class=" bg-blue-950 w-9/12 mx-auto mt-10 space-y-5">
                    <p class="text-center">{{$notificacion->mensaje}}</p>
                    <div class="flex justify-center w-full space-x-10">
                        <button class="bg-green-300 px-8" data-action="confirmar">Confirmar</button>
                        <button class="bg-red-500 px-8" data-action="denegar">Denegar</button>
                        <form method="POST" action="{{route('responderNot')}}">
                            @csrf
                            <input type="hidden" name="notificacion" value="{{json_encode($notificacion)}}">
                            <input type="hidden" name="respuesta" value="">
                        </form>
                    </div>
                </div>
            @endif
            
        @endforeach
    </div>
</main>
@endsection