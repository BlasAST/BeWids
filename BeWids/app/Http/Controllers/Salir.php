<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Salir extends Controller
{
    public function guardarPantalla(){
        $referencia = request('pagina');
        $actual = request('actual');
        Session::put($referencia,$actual);
        return Session::get('eventos');
    }
}
