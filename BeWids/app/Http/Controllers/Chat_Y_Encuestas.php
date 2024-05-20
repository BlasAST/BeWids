<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class Chat_Y_Encuestas extends Controller
{
    public function index(){
        if(Auth::check()){
            
        return view('vistas2/chatYEncuestas');
        }
        return redirect()->route('base');
    }
}
