<?php

namespace App\Http\Controllers;

use App\Livewire\Encuestas\Encuestas;
use App\Models\encuesta;
use App\Models\Infousuario;
use App\Models\Participantes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Chat_Y_Encuestas extends Controller
{
    public function index(Request $request)
    {

        $ruta = $request->path();

        $encuestas = encuesta::where('id_portal', Session::get('portal')->id)->get();
        $participantes = Participantes::where('id_portal', Session::get('portal')->id)->get();
        $participanteActual = Participantes::where('id_portal', Session::get('portal')->id)
            ->where('id_usuario', auth()->user()->id)->first();

        return view('vistas2/chatYEncuestas', ['ruta' => $ruta,'encuestas'=>$encuestas,'participantes'=>$participantes,'participanteActual'=>$participanteActual]);
    }

    public function newEncuesta(Request $request){
        $encuesta=new encuesta();
        $encuesta->id_portal=Session::get('portal')->id;
        $encuesta->title=$request->tittle;
        $encuesta->descripcion=$request->descripcion;
        $participanteActual=Participantes::where('id_portal', Session::get('portal')->id)->where('id_usuario',auth()->user()->id)->first();
        $encuesta->creador=$participanteActual->nombre_en_portal;
        if($request->allParticipantes){
            $todos= Participantes::where('id_portal', Session::get('portal')->id)->get();
            $encuesta->participantes=json_encode($todos->pluck('nombre_en_portal')->toArray());
            $encuesta->num_votos_totales=count($todos);
        }else{
            $encuesta->participantes=json_encode($request->individual);
            $encuesta->num_votos_totales=count($request->individual);
        }
        $opcionesVotos=$request->opciones_votos;
        $opcionesVotos=array_filter($opcionesVotos,function($valor){
            return $valor!=null;
        });
        $resultado=[];
        foreach ($opcionesVotos as $opcion){
            $resultado[]=[
                'opcion'=>$opcion,
                'porcentaje'=>'0%',
                'id'=>Str::uuid()->toString(),
            ];
        };
        $encuesta->opciones_votos=json_encode($resultado);
        $encuesta->fecha_final=$request->fecha_final;
        $encuesta->save();
       return redirect()->route('encuestas');
    }

    public function pedirDatos(Request $request){
        $id=$request->id;
        $tipo=$request->tipo;
        $encuesta=encuesta::where('id',$id)->where('id_portal',Session::get('portal')->id)->first();
        return response()->json($encuesta->$tipo);
    }

    public function pedirFoto($id){
        if(Participantes::where('id_usuario',$id)->where('id_portal',Session::get('portal')->id)->exists()){
            $user = Infousuario::where('id_user',$id)->first();

                // Obtener el contenido del archivo
                $file = Storage::disk('fotos_perfil')->get($user->foto_perfil);
            }
        return $file;
    }
}
