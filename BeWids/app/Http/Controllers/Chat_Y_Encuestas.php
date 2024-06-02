<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class Chat_Y_Encuestas extends Controller
{
    public function index(Request $request){
        
        $ruta=$request->path();
            
        return view('vistas2/chatYEncuestas',['ruta'=>$ruta]);
        
    }
        
}
