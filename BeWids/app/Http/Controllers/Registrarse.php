<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Registrarse extends Controller
{
    public function crear(){
        return view('vistas2.auth.registro');
    }
}
