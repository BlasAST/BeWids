<?php

namespace App\Http\Controllers;

use App\Models\Participantes as ModelsParticipantes;
use App\Models\Portales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Participantes extends Controller
{
    public function index(){
        $participantes = ModelsParticipantes::where('id_portal',Session::get('portal')->id)->get();
        return view('vistas2/participantes',['participantes'=>$participantes]);
    }

    public function crearParticipante(){
        $participante = new ModelsParticipantes();
        $participante->nombre_en_portal = request('nombre');
        $participante->id_portal = Session::get('portal')->id;
        $participante->save();
        return redirect()->to('/participantes');
    }
    public function desvincular(){
        $id = request('id');
        $part = ModelsParticipantes::where('id_portal',Session::get('portal')->id)->where('id',$id)->first();
        $part->id_usuario = null;
        $part->save();
        return redirect()->to('/participantes');
    }
    public function ascender(){
        $id = request('id');
        $part = ModelsParticipantes::where('id_portal',Session::get('portal')->id)->where('id',$id)->first();
        $part->admin = true;
        $part->save();
        return redirect()->to('/participantes');
    }
    public function eliminar(){
        $id = request('id',Session::get('participanteUser')->id);
        ModelsParticipantes::where('id_portal',Session::get('portal')->id)->where('id',$id)->delete();
        if($id == Session::get('participanteUser')->id){
            if(!ModelsParticipantes::where('id_portal',Session::get('portal')->id)->exist()){
                Portales::find(Session::get('portal')->id)->delete();
            }else{
                if(!ModelsParticipantes::where('id_portal',Session::get('portal')->id)->where('admin',true)->exist()){
                    $nuevoAdmin = ModelsParticipantes::where('id_portal',Session::get('portal')->id)->first();
                    $nuevoAdmin->admin = true;
                    $nuevoAdmin->save();
                }
            }
            
            return redirect()->to('/');

        } 
        
        return redirect()->to('/participantes');
    }
    public function comprobar(){
        $id = request('id',Session::get('participanteUser')->id);
        $part = ModelsParticipantes::where('id_portal',Session::get('portal')->id)->where('id',$id)->first();
        return response()->json($part->deuda == 0);
    }
}
