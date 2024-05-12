<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Portal extends Controller
{
    public function index(){
        return view('/vistas2/portal');
    }
    public function irPortal(){
        Session::put('portal',json_decode(request('portal')));
        return view('vistas2/portal');
    }
}
