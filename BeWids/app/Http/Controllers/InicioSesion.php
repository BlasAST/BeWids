<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InicioSesion extends Controller
{
    public function crear(){
        return view('vistas2.auth.inicioSesion');
    }
}
