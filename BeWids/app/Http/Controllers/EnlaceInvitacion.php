<?php

namespace App\Http\Controllers;

use App\Models\Invitacion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class EnlaceInvitacion extends Controller
{
    public $token;

    public function index(){
        $this->token=Session::get('portal')->token_portal;
         return view('vistas2/enlaceInvitacion',['invitacion'=>$this->token]);
     }
}
