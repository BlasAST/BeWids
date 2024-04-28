@extends('partials.base')
@section('rutaEstilos','../css/estilosCuentas.css')
@section('rutaJs','../js/cuentas.js')

@section('contenido')
<header>
    <span>Gastos</span>
    <span>Gráficos</span>
    <span>Cuentas</span>
    <span>Notificaciones</span>
</header>

{{-- GASTOS --}}
<main>
    <div>
        @foreach ($gastos as $gasto)
            <div>
                <p>{{$gastos->Titulo}}</p>
                <p>{{$gastos->PagadoPor}}</p>
                <p>{{$gastos->Cantidad}}</p>
                <p>{{$gastos->Fecha}}</p>
            </div>
        @endforeach
    </div>
    <div>
        <h1>Añadir gasto</h1>
        <form action="" method="POST">
            <div>
                <input type="text" name="titulo">
                <label for="titulo">Título</label>
            </div>
            <div>
                <select name="tipo">
                    @foreach($tipos as $tipo)
                        <option value="">{{$tipo}}</option>
                    @endforeach
                    <option value="">Otro</option>
                </select>
                <label for=""></label>
            </div>
            <div>
                <input type="number" name="cantidad">
                <label for="cantidad">Cantidad</label>
            </div>
            <div>
                <select name="divisa">
                    @foreach($divisas as $divisa)
                        <option value="">{{$divisa}}</option>
                    @endforeach
                </select>
                <label for="divisa">Divisa</label>
            </div>
            <div>
                <input type="text" name="fecha">
                <label for="fecha">Fecha</label>
            </div>
            <div>
                <select name="pagador">
                    @foreach($participantes as $user)
                        <option value="">{{$user->NombrePortal}}</option>
                    @endforeach
                </select>
                <label for="pagadore">Pagado por:</label>
            </div>
            <div>
                <select name="deudores" multiple>
                    @foreach($participantes as $user)
                        <option value="">{{$user->NombrePortal}}</option>
                    @endforeach
                </select>
                <label for="deudores">A pagar por:</label>
            </div>

        </form>
    </div>
</main>