<?php

namespace App\Http\Controllers;

use App\Models\Reembolsos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Portal extends Controller
{
    public function index(){
        return view('/vistas2/portal');
    }
    public function irPortal(){
        Session::put('portal',json_decode(request('portal')));
        Session::put("reembolsosSin",Reembolsos::where('id_portal', Session::get('portal')->id)->where("saldado",false)->get());
        Session::put("reembolsosPag",Reembolsos::where('id_portal', Session::get('portal')->id)->where("saldado",true)->get());
        $portal=Session::get('portal');
        return view('vistas2/portal');
    }
}
