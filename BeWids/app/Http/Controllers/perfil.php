<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class perfil extends Controller
{
    public function index(){
        var_dump('Hola desde cont perfil');
        return view('vistas2/perfil');
    }
    public function cerrarSesion(){
        Auth::logout();
        return redirect() ->to('/');
    }
}
