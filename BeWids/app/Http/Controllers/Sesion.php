<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;

class Sesion extends Controller
{
    public function comprobar($dir=null){
        //si no se viene desde el icono de perfil, no se envia información sobre {dir} por lo que por default este sera null

        if(auth()->check() && !$dir){
        //en caso de estar loggeado y haberle dado al icono de perfil (osea dir es null) -> te redirige al '/' que en el otro controlador te envia a tu perfil
            return redirect()->to('/');
        }else{
        //en cualquier otro caso te envia a la vista de iniciar sesion/registrarse donde en caso de estar loggeado mostrará mensaje de cerrar sesión    
            return view('vistas2/sesion',['dir' => $dir]);
        }
    }
    public function formulario(){
        if(request('tipo') == 'iniciar'){
            if(!auth()->attempt(request(['email','password']),request('recordar'))){
                return back()->withErrors([
                    'message' => 'Correo o contraseña incorrectos'
                ]);
            }
            // Regenera el ID de la sesión después de una autenticación exitosa.
            session()->regenerate();
            return redirect()->to('/');
        }else{
            $user = User::create(request(['name','email','password']));
            Auth::login($user);
            $user->sendEmailVerificationNotification();
            return redirect()->to('/');
        }
    }
    public function cerrar(){
        Auth::logout();
        return redirect()->to('/');
    }

    public function enviarCorreo(){
        return view('auth/verify')->with(['reenviado'=> '']);;
    }
    public function codigoRecibido(EmailVerificationRequest $request) {
        $request->fulfill();    
        return redirect('/perfil');
    }
    public  function reenviar(Request $request) {
        $request->user()->sendEmailVerificationNotification();
    
        return back()->withErrors([
            'message' => 'Hemos reenviado el link a tu dirección de correo'
        ]);
    }
}
