<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class Registrarse extends Controller
{
    public function mostrar(){
        return view('vistas2.auth.registro');
    }
    public function crear(){
        $user = User::create(request(['name','email','password']));
        auth()->login($user);
        return redirect()->to('/');
    }
}
