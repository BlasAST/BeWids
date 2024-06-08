<?php

namespace App\Livewire\Chat;

use App\Models\Conversacion;
use App\Models\infousuario;
use App\Models\Mensaje;
use App\Models\Participantes;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Session as FacadesSession;
use Livewire\Component;
use Livewire\Attributes\On;

class ContenedorMensajes extends Component
{
    public $conversacionSeleccionada;
    public $participanteBuscado;
    public $participanteActual;
    public $participanteSeleccionado;
    public $participantesSeleccionados;
    public $prueba='Hola buenas tardes';
    // public $arrayParticipantes = [];
    public $mensajes;

    #[On('newChatSimple')]
    public function cargarConversacionIndivual(Conversacion $conversacion, Participantes $participante)
    {
        $this->inforParticipante=NULL;
        $this->participantesSeleccionados = False;
        // $this->arrayParticipantes = [];
        $this->participanteActual = Participantes::where('id_usuario', auth()->user()->id)->where('id_portal', FacadesSession::get('portal')->id)->first();
        $this->conversacionSeleccionada = $conversacion;
        $this->participanteSeleccionado = $participante;
        $this->buscarMensajes();
    }

    #[On('newChatGroup')]
    public function cargarConversacionGrupal(Conversacion $conversacion, $participantes)
    {
        $this->inforParticipante=NULL;
        $this->participanteSeleccionado = False;
        $this->participanteActual = Participantes::where('id_usuario', auth()->user()->id)->where('id_portal', FacadesSession::get('portal')->id)->first();
        $this->participantesSeleccionados = $participantes;
        $this->conversacionSeleccionada = $conversacion;
        // foreach ($participantes as $participe) {
        //     $this->arrayParticipantes[] = Participantes::where('id_portal', FacadesSession::get('portal')->id)
        //         ->where('nombre_en_portal', $participe)->get();
        // };

        $this->buscarMensajes();
    }

    public function buscarMensajes()
    {
        if ($this->participanteSeleccionado) {
            $this->mensajes = Mensaje::where('id_portal', FacadesSession::get('portal')->id)->where('conversacion_id',$this->conversacionSeleccionada->id)->where('emisor', $this->participanteActual->nombre_en_portal)->where('receptor', $this->participanteSeleccionado->nombre_en_portal)
                ->orWhere('id_portal', FacadesSession::get('portal')->id)->where('conversacion_id',$this->conversacionSeleccionada->id)->where('emisor', $this->participanteSeleccionado->nombre_en_portal)->where('receptor', $this->participanteActual->nombre_en_portal)->get();
            if ($this->mensajes->isEmpty()) {
                $mensajesConversacion= new Mensaje();
                $mensajesConversacion->id_portal=FacadesSession::get('portal')->id;
                $mensajesConversacion->emisor=$this->participanteActual->nombre_en_portal;
                $mensajesConversacion->receptor=$this->participanteSeleccionado->nombre_en_portal;
                $mensajesConversacion->conversacion_id=$this->conversacionSeleccionada->id;
                $mensajesConversacion->save();
            }
        } else {
            $this->mensajes=Mensaje::where('id_portal',FacadesSession::get('portal')->id)->where('conversacion_id',$this->conversacionSeleccionada->id)->where('participantesGroup',json_encode($this->participantesSeleccionados))->get();
            if($this->mensajes->isEmpty()){
                $mensajesConversacion=new Mensaje();
                $mensajesConversacion->id_portal=FacadesSession::get('portal')->id;    
                $mensajesConversacion->conversacion_id=$this->conversacionSeleccionada->id;
                $mensajesConversacion->participantesGroup=json_encode($this->participantesSeleccionados);
                $mensajesConversacion->save();           
            }
        }
    }

    public $inforParticipante;

    public function buscarInfoParticipantes($participe){
        $this->participanteBuscado=Participantes::where('id_portal',FacadesSession::get('portal')->id)->where('nombre_en_portal',$participe)->first();
        $this->inforParticipante=infousuario::where('id',$this->participanteBuscado->id_usuario)->first();
    }

    public function cerrarConversacion(){
        $this->participanteSeleccionado=NULL;
        $this->participantesSeleccionados=NULL;
        $this->inforParticipante=NULL;
    }
    public function cerrarInfo(){
        $this->inforParticipante=NULL;
    }

    public function render()
    {
        return view('livewire.chat.contenedor-mensajes');
    }
}
