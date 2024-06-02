<?php

namespace App\Livewire\Chat;

use App\Models\Conversacion;
use App\Models\Participantes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class ListaChats extends Component
{
    public $participantes;
    public $participanteActual;
    public $usuario;
    public $portal;
    public $conexion;
    public $participant;
    public $mensaje;
    public $valoracion;
    public $conversacionesIndividuales;
    public $conversacionesGrupales;
    public function participanteSelecionado($valor)
    {
        $this->participant=$valor;
        $this->conexion=NULL;
    }


    public function cerrar($uri)
    {
        $this->participant=False;
        return redirect()->to('/'+$uri);
    }

    public function comprobarChat($valor)
    {
        $this->render();
        $comprobarConversacion=Conversacion::where('receptor',$this->participanteActual->nombre_en_portal)->where('emisor',$valor)
                                ->orwhere('receptor',$valor)->where('emisor',$this->participanteActual->nombre_en_portal)->get();
        
            $this->valoracion=$comprobarConversacion;
        if (count($comprobarConversacion)==0){
             $nuevaConversacion=new Conversacion();
             $nuevaConversacion->id_portal=$this->portal->id;
            $nuevaConversacion->emisor=$this->participanteActual->nombre_en_portal;
            $nuevaConversacion->receptor=$valor;
            $nuevaConversacion->save();
            $this->conexion=True;    
            $this->mensaje = 'Nueva conversación creada.';
        }else if(count($comprobarConversacion)>=1){
            $this->conexion=True;
            $this->mensaje='Ya existe esta conversación';
        }      
    }
    public function nuevoGrupo(Request $request){
        return redirect()->to('/chat');
    }

    

    public function render()
    {
        $this->portal=Session::get('portal');
        $this->usuario=Auth::user();
        $this->participantes=Participantes::where('id_portal',$this->portal->id)
        ->where('id_usuario','!=',$this->usuario->id)->get();
        $this->participanteActual=Participantes::where('id_usuario',$this->usuario->id)->where('id_portal',$this->portal->id)->first();
        $this->conversacionesIndividuales=Conversacion::where('id_portal',$this->portal->id)->whereNull('name_group')->where(function($query){
            $query->where('emisor',$this->participanteActual->nombre_en_portal)
            ->orWhere('receptor',$this->participanteActual->nombre_en_portal);
        })->get();
        $this->conversacionesGrupales=Conversacion::where('id_portal',$this->portal->id)->whereNotNull('name_group')->where(function($query){
            $query->where('emisor',$this->participanteActual->nombre_en_portal)
            ->orWhere('receptor',$this->participanteActual->nombre_en_portal);
        })->get();

        return view('livewire.chat.lista-chats');
    }
}
