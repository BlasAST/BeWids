<?php

namespace App\Http\Controllers;

use App\Models\Ajustes;
use App\Models\Participantes as ModelsParticipantes;
use App\Models\Portales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Participantes extends Controller
{
    public function index(){
        //mostramos la vista participantes con los participantes del portal
        $ajustes = Ajustes::where('id_portal',Session::get('portal')->id)->first();
        $participantes = ModelsParticipantes::where('id_portal',Session::get('portal')->id)->get();
        return view('vistas2/participantes',['participantes'=>$participantes,'ajustes'=>$ajustes]);
    }

    public function crearParticipante(){
        //creamos un nuevo participante en el portal con los datos del formulario
        $participante = new ModelsParticipantes();
        $participante->nombre_en_portal = request('nombre');
        $participante->id_portal = Session::get('portal')->id;
        $participante->save();
        return redirect()->to('/participantes');
    }
    public function desvincular(){
        //le quitamos la relación un participante con el id_usuario
        $id = request('id');
        $part = ModelsParticipantes::where('id_portal',Session::get('portal')->id)->where('id',$id)->first();
        $part->id_usuario = null;
        $part->save();
        return redirect()->to('/participantes');
    }
    public function ascender(){
        //cambiamos la propiedad de un participante a admin
        $id = request('id');
        $part = ModelsParticipantes::where('id_portal',Session::get('portal')->id)->where('id',$id)->first();
        $part->admin = true;
        $part->save();
        return redirect()->to('/participantes');
    }
    public function eliminar(){
        //Eliminamos un participante siempre y cuanto no tenga deudas ni le deban dinero
        //En caso que tenga deudas se muestra un popUp informativo
        $id = request('id',Session::get('participanteUser')->id);
        ModelsParticipantes::where('id_portal',Session::get('portal')->id)->where('id',$id)->delete();
        if($id == Session::get('participanteUser')->id){
            //En caso de eliminarse a uno mismo (abandonar portal)
            if(!ModelsParticipantes::where('id_portal',Session::get('portal')->id)->exists()){
                //Si no quedan mas participantes se elimina el portal
                Portales::find(Session::get('portal')->id)->delete();
            }else{
                if(!ModelsParticipantes::where('id_portal',Session::get('portal')->id)->where('admin',true)->exists()){
                    //si no quedan mas admins se asciende al primero de la lista a admin
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
        //comprobar si tiene deuda
        $id = request('id',Session::get('participanteUser')->id);
        $part = ModelsParticipantes::where('id_portal',Session::get('portal')->id)->where('id',$id)->first();
        return response()->json($part->deuda == 0);
    }
}
