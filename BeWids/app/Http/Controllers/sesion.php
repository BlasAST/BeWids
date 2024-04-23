<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class sesion extends Controller
{
    public function comprobar($dir){
        if(auth()->check()){
            return redirect()->to('inicioBeWids');
        }else{
            return redirect()->to('sesion',['dir' => $dir]);
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
}
