<?php

namespace App\Livewire\Chat;

use App\Models\Conversacion;
use App\Models\infousuario;
use App\Models\Mensaje;
use App\Models\Participantes;
use Illuminate\Support\Str;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Session as FacadesSession;
use Livewire\Component;
use Livewire\Attributes\On;

class ContenedorMensajes extends Component
{
    public $conversacionSeleccionada;
    public $participanteActual;
    public $participanteSeleccionado;
    public $arrayParticipantes;
    public $participantesSeleccionados;
    public $mensajes;

    #[On('newChatSimple')]
    public function cargarConversacionIndivual(Conversacion $conversacion, Participantes $participante)
    {
        $this->participantesSeleccionados = False;
        $this->conversacionSeleccionada = $conversacion;
        $this->participanteSeleccionado = $participante;
        $this->buscarMensajes();
    }

    #[On('newChatGroup')]
    public function cargarConversacionGrupal(Conversacion $conversacion, $participantes, $arrayPar=NULL)
    {

        $this->participanteSeleccionado = False;
        $this->participantesSeleccionados = $participantes;
        // $this->arrayParticipantes = $arrayPar;
        $this->conversacionSeleccionada = $conversacion;

        $this->buscarMensajes();
    }
    public function buscarMensajes()
    {
        $this->participanteActual = Participantes::where('id_usuario', auth()->user()->id)->where('id_portal', FacadesSession::get('portal')->id)->first();
        $this->mensajes = NULL;
        $this->inforParticipante = NULL;

        $this->mensajes = Mensaje::where('id_portal', FacadesSession::get('portal')->id)
            ->where('conversacion_id', $this->conversacionSeleccionada->id)->first();

        if ($this->mensajes == NULL && $this->participantesSeleccionados == NULL) {
            $mensajesConversacion = new Mensaje();
            $mensajesConversacion->id_portal = FacadesSession::get('portal')->id;
            $mensajesConversacion->emisor = $this->participanteActual->nombre_en_portal;
            $mensajesConversacion->receptor = $this->participanteSeleccionado->nombre_en_portal;
            $mensajesConversacion->conversacion_id = $this->conversacionSeleccionada->id;
            $mensajesConversacion->save();
            $this->mensajes = $mensajesConversacion;
        }
        if ($this->mensajes == NULL && $this->participanteSeleccionado == NULL) {
            $mensajesConversacion = new Mensaje();
            $mensajesConversacion->id_portal = FacadesSession::get('portal')->id;
            $mensajesConversacion->conversacion_id = $this->conversacionSeleccionada->id;
            $mensajesConversacion->participantes_group = json_encode($this->participantesSeleccionados);
            $mensajesConversacion->save();
            $this->mensajes = $mensajesConversacion;
        }
        $this->dispatch('scrollFixed');
    }

    public $inforParticipante;
    public $participanteBuscado;
    public function buscarInfoParticipantes($participe)
    {
        $this->participanteBuscado = Participantes::where('id_portal', FacadesSession::get('portal')->id)->where('nombre_en_portal', $participe)->first();
        $this->inforParticipante = infousuario::where('id_user', $this->participanteBuscado->id_usuario)->first();
    }

    public function cerrarConversacion()
    {
        $this->participanteSeleccionado = NULL;
        $this->participantesSeleccionados = NULL;
        $this->inforParticipante = NULL;
    }
    public function cerrarInfo()
    {
        $this->inforParticipante = NULL;
    }

    public $mensajeEnviado;
    public function enviarMensaje()
    {
        if ($this->mensajeEnviado != null || trim($this->mensajeEnviado != '')) {
            $enviando = [
                'emisor' => $this->participanteActual->nombre_en_portal,
                'mensaje' => $this->mensajeEnviado,
                'conversacion' => $this->mensajes->conversacion_id,
                'timestamp' => now()->format('H:i'),
                'id' => Str::uuid()->toString(),
            ];
            event(new \App\Events\envioMensaje($this->participanteActual->nombre_en_portal, $this->mensajeEnviado, $this->mensajes->conversacion_id,$enviando['timestamp'],$enviando['id']));
            if ($this->participanteActual->nombre_en_portal == $enviando['emisor']) {
                $this->mensajeEnviado = NULL;
            }
        }
    }

    #[On('actualizandoChat')]
    public function actualizacion($datos)
    {
        if ($this->conversacionSeleccionada != NULL) {

            if ($datos['conversacion'] == $this->conversacionSeleccionada->id) {
                
                $actual = Participantes::where('id_usuario', auth()->user()->id)->where('id_portal', FacadesSession::get('portal')->id)->first();
                $updateBody = $this->mensajes->body ? json_decode($this->mensajes->body, true) : [];
                $mensajeExistente = collect($updateBody)->where('id', $datos['id'])->first();
                if (!$mensajeExistente) {
                    $updateBody[] = $datos;
                }
    
                $updateBody = json_encode($updateBody);
    
                $this->mensajes->body = $updateBody;
                $this->mensajes->update(['body' => $updateBody]);
    
                $this->mensajes->save();
                $this->dispatch('scrollFixed');
            }
        }
    }


    public function render()
    {
        return view('livewire.chat.contenedor-mensajes');
    }
}
