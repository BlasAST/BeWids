<?php

namespace App\Livewire\Chat;

use App\Models\Conversacion;
use App\Models\Participantes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class ListaChats extends Component
{
    public $participantes;
    public $usuario;
    public $nombreUserPortal;
    public $portal;
    public $conexion;
    public $participant;
    public $mensaje;

    public function participanteSelecionado($valor)
    {
        $this->participant=$valor;
        $this->conexion=NULL;
    }


    public function cerrar()
    {
        $this->participant=False;
    }

    public function comprobarChat($valor)
    {
        $comprobarConversacion=Conversacion::where('receptor',auth()->user()->id)->where('emisor',$valor)
                                ->orwhere('receptor',$valor)->where('emisor',auth()->user()->id)->get();
        
        if ($comprobarConversacion->isEmpty()){
            // $crearConversacion;
            $this->conexion=True;
            $this->mensaje = 'Nueva conversación creada.';
        }else{
            $this->conexion=False;
            $this->mensaje='Ya existe esta conversación'; 
        }   
    }

    public function crearNuevoChats($valores){

    }

    

    public function render()
    {
        // $this->nombreUserPortal=Participantes::where('idUsuario',auth()->user()->id);
        $this->portal=Session::get('portal');
        $this->usuario=Auth::user();
        // >whereNotIn('id_usuario',[$this->usuario->id])
        $this->participantes=Participantes::where('id_portal',$this->portal->id)->get();
        return view('livewire.chat.lista-chats');
    }
}
