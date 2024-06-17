<?php

namespace App\Http\Controllers;

use App\Models\Ajustes;
use App\Models\MisEventos;
use App\Models\Notificaciones;
use App\Models\Participantes;
use App\Models\Portales;
use App\Models\Reembolsos;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


class Portal extends Controller
{
    public function index(){

        //Como en el portal hay que mostrar bastante información y esa información puede cambiar en cualquier momento
        // Recolectamos todos los datos necesarios y se los pasamos a la vista
        
        //en caso de que exista Session::get(invitación) significa que han llegado através del enlace de invitación
        //por lo que el usuario aun no tiene participante asignado y la información individual del portal no hay que mostrarlo
        if(Session::get('invitacion')){
            $usuario = false;
            $deudaMax = false;
            $solicitudes = false;
            $notificaciones = false;
            $reembolsos = false;
            $deudas = false;
        }else{
            $usuario = true;
            Session::put('participanteUser',Participantes::where('id_usuario',Auth::user()->id)->where('id_portal',Session::get('portal')->id)->first());  
            $deudaMax = $this->calcularDeuda();
            $solicitudes = Notificaciones::where('id_portal',Session::get('portal')->id)->where('receptor',Session::get('participanteUser')->nombre_en_portal)->first();
            if($solicitudes)
                $notificaciones = true;
            else
                $notificaciones = false;
                $reembolsos = Reembolsos::where('id_portal',Session::get('portal')->id)->where('saldado',false)->where('pagador',Session::get('participanteUser')->nombre_en_portal)->get();
                $deudas = Reembolsos::where('id_portal',Session::get('portal')->id)->where('saldado',false)->where('receptor',Session::get('participanteUser')->nombre_en_portal)->get();

        }
        $eventos = MisEventos::where('id_portal',Session::get('portal')->id)->where('aniadido',false)->get()->toArray();
        rsort($eventos);
        $eventosCal = MisEventos::where('id_portal',Session::get('portal')->id)->where('aniadido',true)->get();
        $fechaInicio = new DateTime();
        $fechaInicio->modify('first day of this month');
        while($fechaInicio->format('w')!= 1){
            $fechaInicio->modify('-1 day');
        };
        $fechaFinal = new DateTime();

        $ajustes = Ajustes::where('id_portal',Session::get('portal')->id)->first();
        Session::put('ajustes',$ajustes);


        return view('/vistas2/portal',['notificaciones'=>$notificaciones,'reembolsos'=>$reembolsos,'deudas'=>$deudas,'deudaMax'=>$deudaMax, 'eventos'=>$eventos,'eventosCal'=>$eventosCal,
                    'fechaInicio'=>$fechaInicio, 'fechaFinal'=>$fechaFinal, 'usuario'=>$usuario, 'ajustes'=>$ajustes]);
    }
    private function calcularDeuda(){
        //calculamos cual es la cantidad mayor de deuda de los participantes, ya sea positiva o negativa
        $cantMax = Participantes::where('id_portal',Session::get('portal')->id)->orderBy('deuda','desc')->pluck('deuda')->first();
        $cantMin = Participantes::where('id_portal',Session::get('portal')->id)->orderBy('deuda','asc')->pluck('deuda')->first();
        return abs($cantMax) >= abs($cantMin) ? abs($cantMax) : abs($cantMin);

    }

    public function cambiarConf(){
        //se modifica la el ajuste del portal indicado
        $ajustes = Ajustes::where('id_portal',Session::get('portal')->id)->first();
        $tipo = request('tipo');
        $conf = (request('conf') == 'true' && true) || false;
        $ajustes -> $tipo = $conf;
        $ajustes -> save();
        Session::put('ajustes',$ajustes);
        return true;
    }
    public function irPortal(){
        //se guarda la info del portal en sesión y se redirige a portal
        $portal = json_decode(request('portal'));
        Session::put('portal',$portal);
        if($portal->fondo)
            Session::put('fondo',base64_encode($this->pedirFoto($portal->fondo)));
        else
            Session::forget('fondo');
        $portal=Session::get('portal');
        return redirect()->to('/portal');
    }
    public function pedirFoto($foto){
       
        $file = Storage::disk('fondos_portal')->get($foto);
        
        
        return $file;
        
    }
    public function cambiarFondo(Request $request){
        $portal = Portales::find(Session::get('portal')->id);
        $portal->nombre = $request->nombre ?? $portal->nombre;
        $portal->color_titulo = $request->color;
        if ($request->hasFile('foto')) {

            if ($portal->fondo) {
                Storage::disk('fondos_portal')->delete($portal->fondo);
            }
            // Sube la nueva foto de perfil
            $fileName = time() . '.' . $request->foto->extension();
            $request->foto->storeAs('', $fileName, 'fondos_portal');

            // Actualiza la columna de profile_photo en la tabla de usuarios
            $portal->fondo = $fileName;
            Session::put('fondo',base64_encode($this->pedirFoto($portal->fondo)));

        }
        $portal->save();
        Session::put('portal',$portal);

        return redirect()->to('/portal');

    }
}
