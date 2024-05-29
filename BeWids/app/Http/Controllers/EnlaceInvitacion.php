<?php

namespace App\Http\Controllers;

use App\Models\Invitacion;
use App\Models\Portales;
use Illuminate\Http\Request;
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
            Session::put('invitacion','newPar');
            return redirect()->to('/invitacion');
        }
        return redirect()-route('error404');
     }

     public function cerrarEnlace(){
        Session::forget('invitacion');
     }
     public function aniadirParticipante(){

     }
}
