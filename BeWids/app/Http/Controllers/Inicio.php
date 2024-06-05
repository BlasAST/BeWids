<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Inicio extends Controller
{
    public function index(){

        if(Auth::check()){
            $user=Auth::user();
            return redirect()->route('perfil');
        }

        return view('home');
    }
    public function home(){

        return view('home');
    }
}
