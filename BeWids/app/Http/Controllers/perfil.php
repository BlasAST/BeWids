<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\infousuario;
use App\Models\Portales;

class perfil extends Controller
{
    public function index(){
        $user = Auth::user();
        if ($user) {
            $infoUsuario = Infousuario::where('id_user', $user->id)->first();
            return view('vistas2/perfil', ['user' => $user, 'infoUsuario' => $infoUsuario]);
        }
        return redirect()->route('base');
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
        // $portal = new Portales();
        // $portal->nombre = request('portal');

        var_dump(request());
    }
}
