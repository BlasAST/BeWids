<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class sesion extends Controller
{
    public function comprobar($dir){
        var_dump('Hola desde cont sesion');
        if(auth()->check()){
            return redirect()->to('vistas2\inicioBeWids');
        }else{
            return redirect()->to('vistas2\sesion',['dir' => $dir]);
        }
    }
    public function formulario(){
        if(request('tipo') == 'inicio'){
            $this->iniciar();
        }
    }
    public function iniciar(){
        if(!auth()->attempt(request(['email','password']))){
            return back()->withErrors([
                'message' => 'pepe pepon'
            ]);
        }
        return redirect()->to('/');
    }
    public function registrar(){
        $user = User::create(request(['name','email','password']));
        auth()->login($user);
        return redirect()->to('/');
    }
    public function cerrar(){
        Auth::logout();
        return back();
    }
}
