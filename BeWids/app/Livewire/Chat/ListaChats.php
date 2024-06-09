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
    }


    public function cerrar()
    {
        $this->participant=False;
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
            $this->mensaje = 'Nueva conversación creada.';
            $this->cerrar();
        }else if(count($comprobarConversacion)>=1){
            $this->mensaje='Ya existe esta conversación';
            $this->cerrar();
        }      
    }

    public $nombreG;
    public $descripcionG;
    public $seleccionAll;
    public $selecionadosG=[];
    public $prueba;
    

    public function newGroup(){
        
        $newG=new Conversacion();
        
        $newG->id_portal=$this->portal->id;
        $newG->name_group=$this->nombreG;
        $newG->descripcion=$this->descripcionG;
        $newG->emisor=$this->participanteActual->nombre_en_portal;
        if($this->seleccionAll){
            $seleccionadosGAll=$this->participantes->pluck('nombre_en_portal')->toArray();
            $newG->participantesGroup=json_encode($seleccionadosGAll);
        }else{
            
            $newG->participantesGroup = json_encode($this->selecionadosG);
        }
        $newG->save();
        $this->nombreG='';
        $this->descripcionG='';
        $this->seleccionAll=false;
        $this->selecionadosG=[];
        
    }
        
    
    public function chatIndividualSeleccionado( Conversacion $conversacion){
            $participantePA=NULL;
        if($conversacion->emisor==$this->participanteActual->nombre_en_portal){
            $participantePA=$conversacion->receptor;
        }else{
            $participantePA=$conversacion->emisor;
        }
            $participanteSeleccionado=Participantes::where('id_portal',$this->portal->id)->where('nombre_en_portal',$participantePA)->get();
            $this->dispatch('newChatSimple',$conversacion,$participanteSeleccionado); 
    }

    public function chatGrupalSeleccionado(Conversacion $conversacion){
            $conversacionSeleccionada=$conversacion;
            $participantesGrupoSeleccionados=json_decode($conversacionSeleccionada->participantesGroup);
            // $arrayParticipantes=[];
            // foreach ($participantesGrupoSeleccionados as $participe){
            //     $arrayParticipantes[]=Participantes::where('id_portal',$this->portal->id)
            //     ->where('nombre_en_portal',$participe)->first();
            // }
            // $actual=$this->participanteActual;
            // $arrayParticipantes[]=$actual;
            // $this->prueba=$arrayParticipantes;
            // $resultado=json_encode($arrayParticipantes);
            $participantesGrupoSeleccionados[]=$this->participanteActual->nombre_en_portal;
            
            
            
            $this->dispatch('newChatGroup',$conversacionSeleccionada,$participantesGrupoSeleccionados);
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

        $this->conversacionesGrupales=Conversacion::where('id_portal',$this->portal->id)->where(function($query){
            $query->whereNotNull('name_group')
            ->where('emisor',$this->participanteActual->nombre_en_portal)
            ->orwhere('participantesGroup','LIKE','%"'.$this->participanteActual->nombre_en_portal.'"%');
        })->get();


        return view('livewire.chat.lista-chats');
    }
}
