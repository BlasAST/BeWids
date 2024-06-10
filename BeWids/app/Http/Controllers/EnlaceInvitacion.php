<?php

namespace App\Http\Controllers;

use App\Models\Conversacion;
use App\Models\Invitacion;
use App\Models\Participantes;
use App\Models\Portales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class EnlaceInvitacion extends Controller
{
    public $token;

    public function index(){
        if (!Session::get('invitacion')){
            return view('error404');
        }
        return view('vistas2/enlaceInvitacion');
     }

     public function crearEnlace(){
        $portal=Portales::find(Session::get('portal')->id);
        $portal->token_portal=Str::random(20);
        $portal->save();
        Session::put('portal',$portal);
        Session::put('invitacion','enlaceInvitacion');
        return redirect()->to('/invitacion');
     }

     public function redirigir($dir){

        $token=Portales::where('token_portal',$dir)->first();
        if($token){
            Session::put('portal',$token);
            if(Participantes::where('id_portal',$token->id)->where('id_usuario',Auth::id())->first())
                return redirect()->to('/portal');
            Session::put('invitacion','newPar');
            return redirect()->to('/invitacion');
        }
        return redirect()-route('error404');
     }

     public function cerrarEnlace(){
        Session::forget('invitacion');
        return redirect()->to('/portal');
     }
     public function aniadirParticipante(){
        $nombrePart = request("par");
        $participante = Participantes::where('id_portal',Session::get('portal')->id)->where('nombre_en_portal',$nombrePart)->first();
        if($participante){
            $participante->id_usuario = Auth::id();
        }else{
            $participante = new Participantes();
            $participante->id_usuario = Auth::id();
            $participante->id_portal = Session::get('portal')->id;
            $participante->nombre_en_portal = $nombrePart;
        }
        $participante->save();
        $conversacion=Conversacion::where('id_portal',Session::get('portal')->id)->where('chat_global',true)->first();
        
        $array=json_decode($conversacion->participantes_group);
        $array[]=$participante->nombre_en_portal;
        $conversacion->participantes_group=json_encode($array);
        $conversacion->save();
        Session::forget('invitacion');
        Session::put('participanteUser',Participantes::where('id_usuario',Auth::user()->id)->where('id_portal',Session::get('portal')->id)->first());
        return redirect()->to('/portal');
        
     }
}
