<?php

namespace App\Http\Controllers;

use App\Models\Gastos;
use App\Models\Deudas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Contabilidad extends Controller
{
    public function index(){
        
        return view('/vistas2/cuentas');
    }
    public function aniadirGasto(){
        $gasto = new Gastos();
        $gasto->id_portal = Session::get('portal')->id;
        $gasto->titulo = request('titulo');
        $gasto->tipo = request('tipo');
        $gasto->cantidad = request('cantidad');
        $gasto->fecha = request('fecha');
        $gasto->pagado_por = request('pagador');
        $participantes = "";
        if(request('participantes')){
            $parte = request('cantidad') / count(request('participantes'));
            foreach(request('participantes') as $participante){
                $participantes .= $participante.";";
                $deuda = new Deudas();
                $deuda->id_portal = Session::get('portal')->id;
                $deuda->participante = $participante;
                $deuda->cantidad = $parte;
                $deuda->save();
            }
        }
        $gasto->participantes = trim($participantes,';');
        $gasto->save();

        $deuda = new Deudas();
        $deuda->id_portal = Session::get('portal')->id;
        $deuda->participante = request('pagador');
        $deuda->cantidad = request('cantidad')*-1;
        $deuda->save();
        return redirect()->to('/contabilidad');
    }
}
