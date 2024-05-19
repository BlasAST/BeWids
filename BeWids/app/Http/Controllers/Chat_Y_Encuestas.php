<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Chat_Y_Encuestas extends Controller
{
    public function index(){
        return view('vistas2/chatYEncuestas');
    }
}
