<?php

namespace App\Http\Controllers;

use App\Models\Participantes;
use App\Models\Reembolsos;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Portal extends Controller
{
    public function index(){
        return view('/vistas2/portal');
    }
    public function irPortal(){
        Session::put('portal',json_decode(request('portal')));
        $portal=Session::get('portal');
        Session::put('participanteUser',Participantes::where('id_usuario',Auth::user()->id)->where('id_portal',$portal->id)->first());
        return view('vistas2/portal');
    }
}
