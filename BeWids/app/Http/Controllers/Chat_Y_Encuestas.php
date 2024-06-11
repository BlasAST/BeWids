<?php

namespace App\Http\Controllers;

use App\Models\encuesta;
use App\Models\Participantes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Chat_Y_Encuestas extends Controller
{
    public function index(Request $request)
    {

        $ruta = $request->path();

        $encuestas = encuesta::where('id_portal', Session::get('portal')->id)->get();
        $participantes = Participantes::where('id_portal', Session::get('portal')->id)
            //      ->where('id_usuario','!=',auth()->user()->id)
            ->get();
        $participanteActual = Participantes::where('id_portal', Session::get('portal')->id)
            ->where('id_usuario', auth()->user()->id)->first();


        return view('vistas2/chatYEncuestas', ['ruta' => $ruta,'encuestas'=>$encuestas,'participates'=>$participantes,'participanteActual'=>$participanteActual]);
    }
}
