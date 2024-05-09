<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Portal extends Controller
{
    public function index(){
        return view('/vistas2/portal');
    }
    public function irPortal(){
        return view('vistas2/portal',['portal'=>json_decode(request('portal'))]);
    }
}
