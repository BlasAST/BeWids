<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\infousuario;

class perfil extends Controller
{
    public function index(){
        $user = Auth::user();
        $infoUsuario = Infousuario::where('id_user', $user->id)->first();
        return view('vistas2/perfil',['user'=>$user,'infoUsuario'=>$infoUsuario]);
    }
    public function cerrarSesion(){
        Auth::logout();
        return redirect() ->to('/');
    }

    public function guardarDatos(Request $request){
        // Asegurar que el usuario esté autenticado
    $user = Auth::user();

    // Si el usuario no está autenticado, redirigir a una página de error o de inicio de sesión
    if (!$user) {
        return redirect()->route('login'); // Asegúrate de tener una ruta de login definida
    }
        $data = [
            'id_user' =>Auth::user()->id,
            'nombre' => $request ->nombre ?? null,
            'fecha_nacimiento' => $request->fecha_nacimiento ?? null,
            'descripcion' => $request->descripcion ?? null,
            'numero_contacto' => $request->numero_contacto ?? null,
            'provincia' => $request->provincia ?? null
        ];

        Infousuario::updateOrCreate(
            ['id_user' => $request->id_user],
            $data
        );

        return redirect()->to('/');
    }
}
