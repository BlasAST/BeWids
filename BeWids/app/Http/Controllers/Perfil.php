<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\infousuario;
use App\Models\Participantes;
use App\Models\Portales;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

// SELECT * FROM personas where id in (Select id_empleado from empleados where dept = mantemientos)

class perfil extends Controller
{
    public function index(){
        $user = Auth::user();
        if ($user) {
            $pUsuario = Participantes::where('id_usuario',Auth::id())->pluck('id_portal')->toArray();
            $portales = Portales::whereIn('id',$pUsuario)->get();
            $infoUsuario = Infousuario::where('id_user', $user->id)->first();
            return view('vistas2/perfil', ['user' => $user, 'infoUsuario' => $infoUsuario, 'portales'=>$portales]);
        }
        return redirect()->route('/');
    }

    public function guardarDatos(Request $request){
        $user = Auth::user();

        $data = [
            'id_user' =>Auth::user()->id,
            'nombre' => $request ->nombre ?? '',
            'fecha_nacimiento' => $request->fecha_nacimiento ?? null,
            'descripcion' => $request->descripcion ?? null,
            'numero_contacto' => $request->numero_contacto ?? null,
            'provincia' => $request->provincia ?? null
        ];

        Infousuario::updateOrCreate(
            ['id_user' => Auth::user()->id],
            $data
        );
        return redirect()->route('perfil');
    }

    public function crearPortal(){
        $portal = new Portales();
        $portal->nombre = request('portal');
        $portal->save();
        Session::put('portal',$portal);
        
        $participante = new Participantes();
        $participante->id_portal = $portal->id;
        $participante->id_usuario = Auth::user()->id;
        $participante->admin = true;
        $participante->nombre_en_portal = request('nombre');
        $participante->save();

        if(request('participantes')){
            foreach(request('participantes') as $nombre){
                if($nombre){
                    $participante = new Participantes();
                    $participante->id_portal = $portal->id;
                    $participante->nombre_en_portal = $nombre;
                    $participante->save();
                }
            }
        }
        return redirect()->route('portal');
    }
}
