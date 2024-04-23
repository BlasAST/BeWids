<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InicioSesion extends Controller
{
    public function mostrar(){
        return view('vistas2.auth.inicioSesion');
    }
    public function iniciar(){
        if(!auth()->attempt(request(['email','password']))){
            return back()->withErrors([
                'message' => 'pepe pepon'
            ]);
        }
        return redirect()->to('/');
    }
}
