<?php

namespace App\Livewire\Chat;

use App\Models\Conversacion;
use App\Models\Infousuario;
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

    //Los metodos con On son eventos recibidos mediante livewire desde el listadodeChats

    // Carga los datos de la conversacion individual y borra los de grupal
    #[On('newChatSimple')]
    public function cargarConversacionIndivual(Conversacion $conversacion, Participantes $participante)
    {
        $this->participantesSeleccionados = False;
        $this->conversacionSeleccionada = $conversacion;
        $this->participanteSeleccionado = $participante;
        $this->buscarMensajes();
    }
// Carga los datos de la conversacion grupal y borra los de individual
    #[On('newChatGroup')]
    public function cargarConversacionGrupal(Conversacion $conversacion, $participantes, $arrayPar=NULL)
    {

        $this->participanteSeleccionado = False;
        $this->participantesSeleccionados = $participantes;
        $this->arrayParticipantes = $arrayPar;
        $this->conversacionSeleccionada = $conversacion;

        $this->buscarMensajes();
    }
    // Busca en la base de datos la existencia de mensajes existentes o realiza su creación
    public function buscarMensajes()
    {
        $this->participanteActual = Participantes::where('id_usuario', auth()->user()->id)->where('id_portal', FacadesSession::get('portal')->id)->first();
        $this->mensajes = NULL;
        $this->inforParticipante = NULL;

        $this->mensajes = Mensaje::where('id_portal', FacadesSession::get('portal')->id)
            ->where('conversacion_id', $this->conversacionSeleccionada->id)->first();
        // Crea una conversación individual
        if ($this->mensajes == NULL && $this->participantesSeleccionados == NULL) {
            $mensajesConversacion = new Mensaje();
            $mensajesConversacion->id_portal = FacadesSession::get('portal')->id;
            $mensajesConversacion->emisor = $this->participanteActual->nombre_en_portal;
            $mensajesConversacion->receptor = $this->participanteSeleccionado->nombre_en_portal;
            $mensajesConversacion->conversacion_id = $this->conversacionSeleccionada->id;
            $mensajesConversacion->save();
            $this->mensajes = $mensajesConversacion;
        }
        // Crea una conversación grupal
        if ($this->mensajes == NULL && $this->participanteSeleccionado == NULL) {
            $mensajesConversacion = new Mensaje();
            $mensajesConversacion->id_portal = FacadesSession::get('portal')->id;
            $mensajesConversacion->conversacion_id = $this->conversacionSeleccionada->id;
            $mensajesConversacion->participantes_group = json_encode($this->participantesSeleccionados);
            $mensajesConversacion->save();
            $this->mensajes = $mensajesConversacion;
        }
        // Restablece el scroll hasta abajo
        $this->dispatch('scrollFixed');
    }

    // Busca información del participante seleccionado en su perfil relacionada con un participante
    public $inforParticipante;
    public $participanteBuscado;
    public function buscarInfoParticipantes($participe)
    {
        $this->participanteBuscado = Participantes::where('id_portal', FacadesSession::get('portal')->id)->where('nombre_en_portal', $participe)->first();
        $this->inforParticipante = Infousuario::where('id_user', $this->participanteBuscado->id_usuario)->first();
    }
    //Cierra la conversación en caso de no querer tener seleccionada ninguna
    public function cerrarConversacion()
    {
        $this->participanteSeleccionado = NULL;
        $this->participantesSeleccionados = NULL;
        $this->inforParticipante = NULL;
        return redirect()->route('chat');
    }
    // Oculta la información en caso de que se le de al boton
    public function cerrarInfo()
    {
        $this->inforParticipante = NULL;
    }
    // Recopila la información necesaria del participante para emitir el mensaje mediante un evento
    // que sera controlado por pusher y actualizará la base de datos

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
            // Evento que carga el mensaje con pusher
            event(new \App\Events\envioMensaje($this->participanteActual->nombre_en_portal, $this->mensajeEnviado, $this->mensajes->conversacion_id,$enviando['timestamp'],$enviando['id']));
            // Eliminación del mensaje escrito en el input
            if ($this->participanteActual->nombre_en_portal == $enviando['emisor']) {
                $this->mensajeEnviado = NULL;
            }
        }
    }
    // Guarda la información del mensaje enviado y realiza una comprobación de existencia mediante
    // un identificador unico para que no se generen duplicados mediante pusher
    #[On('actualizandoChat')]
    public function actualizacion($datos)
    {

        if ($this->conversacionSeleccionada != NULL) {

            if ($datos['conversacion'] == $this->conversacionSeleccionada->id) {
                
                $updateBody = $this->mensajes->body ? json_decode($this->mensajes->body, true) : [];
                $mensajeExistente = collect($updateBody)->where('id', $datos['id'])->first();
                if (!$mensajeExistente) {
                    $updateBody[] = $datos;
                }
    
                $updateBody = json_encode($updateBody);
    
                $this->mensajes->body = $updateBody;
                $this->mensajes->update(['body' => $updateBody]);
                $this->mensajes->save();
                $this->lectores();
                // Evento manejado desde js para que cada vez que se manda un mensaje
                // se reinicie el scroll empezando desde abajo
                $this->dispatch('scrollFixed');
            }
        }
    }
    public $guardado=True;
    public function lectores(){
        $this->conversacionSeleccionada->leido_por=NULL;
        if($this->conversacionSeleccionada!=NULL){
        if($this->conversacionSeleccionada->name_group!=NULL&&$this->conversacionSeleccionada->chat_global!=True){
            if($this->guardado==True){
                foreach ($this->participantesSeleccionados as $nombreParticipante){
                    $par=Participantes::where('id_portal',FacadesSession::get('portal')->id)->where('nombre_en_portal',$nombreParticipante)->first();
                    if($par->leyendo==$this->conversacionSeleccionada->name_group){
                        $array=[];
                        $array[]=['lector'=>$par->nombre_en_portal];
                        $array=json_encode($array);
                        $this->conversacionSeleccionada->leido_por=$array;
                        $this->conversacionSeleccionada->save();
                    }
                }
                $this->guardado=False;
            }
            
        }elseif($this->conversacionSeleccionada->name_group==NULL&&$this->conversacionSeleccionada->chat_global!=True){
            
        }else{
            
        }
    }
        
        
        
    
        
        
    }


    public function render()
    {
        return view('livewire.chat.contenedor-mensajes');
    }
}
