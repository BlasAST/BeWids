<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class inicio extends Controller
{
    public function index(){
         var_dump(Auth::check());
         if(Auth::check()){
            return view('vistas2/inicioBeWids');
         }
        return view('home');
    }
    public function home(){

        return view('home');
    }
    public function portal(){
        return view('portal');
    }
}
