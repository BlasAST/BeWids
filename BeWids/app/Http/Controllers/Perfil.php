<?php

namespace App\Http\Controllers;

use App\Models\Ajustes;
use App\Models\Conversacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Infousuario;
use App\Models\Participantes;
use App\Models\Portales;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

// SELECT * FROM personas where id in (Select id_empleado from empleados where dept = mantemientos)

class perfil extends Controller
{
    public function index(){
        Session::forget('fondo');
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
        $infoUser = Infousuario::where('id_user',$user->id)->first();
        $infoUser->nombre = $request ->nombre ?? $infoUser->nombre;
        $infoUser->fecha_nacimiento = $request ->fecha_nacimiento ?? $infoUser->fecha_nacimiento;
        $infoUser->descripcion = $request ->descripcion ?? $infoUser->descripcion;
        $infoUser->numero_contacto = $request ->numero_contacto ?? $infoUser->numero_contacto;
        $infoUser->provincia = $request ->provincia ?? $infoUser->provincia;

        if ($request->hasFile('foto_perfil')) {

            if (!str_contains($infoUser->foto_perfil,'default')) {
                Storage::disk('fotos_perfil')->delete($infoUser->foto_perfil);
            }
            // Sube la nueva foto de perfil
            $fileName = time() . '.' . $request->foto_perfil->extension();
            $request->foto_perfil->storeAs('', $fileName, 'fotos_perfil');

            // Actualiza la columna de profile_photo en la tabla de usuarios
            $infoUser->foto_perfil = $fileName;
        }

        $infoUser->save();



        return redirect()->route('perfil');
    }

    public function pedirFoto($nombreFoto){
        // Obtener el usuario autenticado
        $user = Infousuario::where('id_user',Auth::user()->id)->first();

        // Verificar que el usuario tenga permiso para acceder a esta foto
        if ($user->foto_perfil == $nombreFoto) {
            // Obtener el contenido del archivo
            $file = Storage::disk('fotos_perfil')->get($nombreFoto);
        }
        
        // Retornar la respuesta HTTP con el contenido del archivo y el tipo MIME
        return $file;
    }

    public function crearPortal(){
        //creamos el portal en la tabla portales
        $portal = new Portales();
        $portal->nombre = request('portal');
        $portal->save();
        Session::put('portal',$portal);
        
       
        //creamos el participante del usuario que lo creo en la bd
        $participante = new Participantes();
        $participante->id_portal = $portal->id;
        $participante->id_usuario = Auth::user()->id;
        $participante->admin = true;
        $participante->nombre_en_portal = request('nombre');
        $participante->save();
        Session::put('participanteUser',$participante);

        //creamos el grupo de chat del portal en la bd y añadimos al participante creador
        $conversacion=new Conversacion();
        $conversacion->id_portal=$portal->id;
        $conversacion->chat_global=True;
        $conversacion->emisor=$participante->nombre_en_portal;
        
        $conversacion->participantes_group=json_encode([$participante->nombre_en_portal]);
        $conversacion->save();

        //creamos los ajustes para el portal
        $ajustes = new Ajustes();
        $ajustes->id_portal = $portal->id;
        $ajustes->save();
        


        //Session::put('participanteUser',$participante);

        //creamos un participante en la bd por cada nombre de participante indicado al crear portal.
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
        $portal=Session::get('portal');
        return redirect()->route('portal');
    }
}
