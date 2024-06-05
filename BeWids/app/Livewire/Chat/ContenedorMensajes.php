<?php

namespace App\Livewire\Chat;

use App\Models\Conversacion;
use App\Models\Participantes;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Session as FacadesSession;
use Livewire\Component;
use Livewire\Attributes\On;

class ContenedorMensajes extends Component
{
    public $conversacionSeleccionada;
    public $participanteSeleccionado;
    public $participantesSeleccionados;
    public $arrayParticipantes=[];

    #[On('newChatSimple')]
    public function cargarConversacionIndivual(Conversacion $conversacion,Participantes $participante)
    {
        $this->participantesSeleccionados=False;
        $this->arrayParticipantes=[];
        $this->conversacionSeleccionada = $conversacion;
        $this->participanteSeleccionado = $participante;
    }
    #[On('newChatGroup')]
    public function cargarConversacionGrupal(Conversacion $conversacion, $participantes)
    {   
        $this->participanteSeleccionado=False;
        $this->participantesSeleccionados=$participantes;
        $this->conversacionSeleccionada = $conversacion;


        foreach ($participantes as $participe){
            $this->arrayParticipantes[]=Participantes::where('id_portal',FacadesSession::get('portal')->id)
            ->where('nombre_en_portal',$participe)->get();
        };
        
    }

    public function render()
    {
        return view('livewire.chat.contenedor-mensajes');
    }
}
