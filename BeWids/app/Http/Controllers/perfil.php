<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class perfil extends Controller
{
    public function index(){
        var_dump('Hola desde cont perfil');
        return view('inicioBeWids');
    }
}
