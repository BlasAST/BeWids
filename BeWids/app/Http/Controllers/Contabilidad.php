<?php

namespace App\Http\Controllers;

use App\Models\Gastos;
use App\Models\Deudas;
use App\Models\Participantes;
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
                $persona = Participantes::where('id_portal',Session::get('portal')->id)->where('nombre_en_portal',$participante)->first();
                $persona->deuda -= $parte;
                $persona->save();
            }
        }
        $gasto->participantes = trim($participantes,';');
        $gasto->save();

        $pagador= Participantes::where('id_portal',Session::get('portal')->id)->where('nombre_en_portal',request('pagador'))->first();
        $pagador->deuda += request('cantidad');
        $pagador->save();
        $participantes = Participantes::where('id_portal',Session::get('portal')->id)->get();
        Session::put('participantes',$participantes);
        //$deudas = $this->hacerCuentas();
        return redirect()->to('/contabilidad');
    }

    public function hacerCuentas(){
        $$deudas = Participantes::selectRaw('nombre_en_portal, SUM(cantidad) as total')
                ->where('id_portal', Session::get('portal')->id)
                ->groupBy('nombre_en_portal')
                ->get();
        foreach($deudas as $deuda){
            if($deuda->total > 0)
                $pagar[$deuda->participante] = $deuda->total;
            if($deuda->total <0)
                $recibir[$deuda->participante] = $deuda->total;
        }

        return ($deudas);
    }
}
