<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class Inicio extends Controller
{
    public function index(){

        //Comprueba si el usuario ya ha iniciado sesión y redirige a /perfil
        //así si estás loggeado te envia al entrar en la app a tu perfil

        if(Auth::check()){
            $user=Auth::user();
            return redirect()->route('perfil');
        }

        return view('home');
    }
    public function home(){

        //Muestra la vista HOME
        return view('home');
    }
}
