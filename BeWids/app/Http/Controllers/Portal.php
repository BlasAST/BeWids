<?php

namespace App\Http\Controllers;

use App\Models\MisEventos;
use App\Models\Notificaciones;
use App\Models\Participantes;
use App\Models\Reembolsos;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Portal extends Controller
{
    public function index(){
        Session::put('participanteUser',Participantes::where('id_usuario',Auth::user()->id)->where('id_portal',Session::get('portal')->id)->first());  
        $deudaMax = $this->calcularDeuda();
        $solicitudes = Notificaciones::where('id_portal',Session::get('portal')->id)->where('receptor',Session::get('participanteUser')->nombre_en_portal)->first();
        if($solicitudes)
            $notificaciones = true;
        else
            $notificaciones = false;
        $reembolsos = Reembolsos::where('id_portal',Session::get('portal')->id)->where('saldado',false)->where('pagador',Session::get('participanteUser')->nombre_en_portal)->get();
        $deudas = Reembolsos::where('id_portal',Session::get('portal')->id)->where('saldado',false)->where('receptor',Session::get('participanteUser')->nombre_en_portal)->get();
        $eventos = MisEventos::where('id_portal',Session::get('portal')->id)->where('aniadido',false)->get();
        $eventosCal = MisEventos::where('id_portal',Session::get('portal')->id)->where('aniadido',true)->get();
        $fechaInicio = new DateTime();
        $fechaInicio->modify('first day of this month');
        while($fechaInicio->format('w')!= 1){
            $fechaInicio->modify('-1 day');
        };
        $fechaFinal = new DateTime();


        return view('/vistas2/portal',['notificaciones'=>$notificaciones,'reembolsos'=>$reembolsos,'deudas'=>$deudas,'deudaMax'=>$deudaMax, 'eventos'=>$eventos,'eventosCal'=>$eventosCal,'fechaInicio'=>$fechaInicio, 'fechaFinal'=>$fechaFinal]);
    }
    private function calcularDeuda(){
        $cantMax = Participantes::where('id_portal',Session::get('portal')->id)->orderBy('deuda','desc')->pluck('deuda')->first();
        $cantMin = Participantes::where('id_portal',Session::get('portal')->id)->orderBy('deuda','asc')->pluck('deuda')->first();
        return abs($cantMax) >= abs($cantMin) ? abs($cantMax) : abs($cantMin);

    }
    public function irPortal(){
        Session::put('portal',json_decode(request('portal')));
        $portal=Session::get('portal');
        return redirect()->to('/portal');
    }
}
