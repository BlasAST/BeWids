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
    public $conversacionGlobal;

    // Actualiza el participante cuando se hace un cambio en el select
    public function participanteSelecionado($valor)
    {
        $this->participant = $valor;
    }

    // Cierra la seleccion de participante individual
    public function cerrar()
    {
        $this->participant = False;
    }
    // Comprueba la existencia de una conversación en su momento de crearción
    public function comprobarChat($valor)
    {
        // $this->render(); NO borrar por ahora nose si da fallo
        $comprobarConversacion = Conversacion::where('receptor', $this->participanteActual->nombre_en_portal)->where('emisor', $valor)
            ->orwhere('receptor', $valor)->where('emisor', $this->participanteActual->nombre_en_portal)->get();

        $this->valoracion = $comprobarConversacion;
        // Si no encuentra ninguna conversación la crea
        if (count($comprobarConversacion) == 0) {
            $nuevaConversacion = new Conversacion();
            $nuevaConversacion->id_portal = $this->portal->id;
            $nuevaConversacion->emisor = $this->participanteActual->nombre_en_portal;
            $nuevaConversacion->receptor = $valor;
            $nuevaConversacion->save();
            $this->mensaje = 'Nueva conversación creada.';
            $this->cerrar();
        } else if (count($comprobarConversacion) >= 1) {
            $this->mensaje = 'Ya existe esta conversación';
            $this->cerrar();
        }
    }

    public $nombreG;
    public $descripcionG;
    public $seleccionAll;
    public $selecionadosG = [];


    // Creacion de un nuevo Grupo
    public function newGroup()
    {
        $newG = new Conversacion();

        $newG->id_portal = $this->portal->id;
        $newG->name_group = $this->nombreG;
        $newG->descripcion = $this->descripcionG;
        $newG->emisor = $this->participanteActual->nombre_en_portal;
        if ($this->seleccionAll) {
            $seleccionadosGAll = $this->participantes->pluck('nombre_en_portal')->toArray();
            $seleccionadosGAll[] = $this->participanteActual->nombre_en_portal;
            $newG->participantes_group = json_encode($seleccionadosGAll);
        } else {
            $this->selecionadosG[] = $this->participanteActual->nombre_en_portal;
            $newG->participantes_group = json_encode($this->selecionadosG);
        }
        $newG->save();
        // Limpieza de campos
        $this->nombreG = '';
        $this->descripcionG = '';
        $this->seleccionAll = false;
        $this->selecionadosG = [];
    }
    // Creación de una nueva conversación individual
    public $conversacionSeleccionada;
    public function chatIndividualSeleccionado(Conversacion $conversacion)
    {
        $this->conversacionSeleccionada = NULL;
        $participantePA = NULL;
        $this->conversacionSeleccionada = $conversacion;
        if ($this->conversacionSeleccionada->emisor == $this->participanteActual->nombre_en_portal) {
            $participantePA = $conversacion->receptor;
        } else {
            $participantePA = $conversacion->emisor;
        }
        $participanteSeleccionado = Participantes::where('id_portal', $this->portal->id)->where('nombre_en_portal', $participantePA)->get();
        // Evento a contenedorMensajes para observar la conversación
        $this->dispatch('newChatSimple', $this->conversacionSeleccionada, $participanteSeleccionado);
    }
    // Envia un evento con dispatch con los datos necesarios para mostrar la conversación seleccionada
    public function chatGrupalSeleccionado(Conversacion $conversacion)
    {
        $this->conversacionSeleccionada = NULL;
        $this->conversacionSeleccionada = $conversacion;
        $participantesGrupoSeleccionados = json_decode($this->conversacionSeleccionada->participantes_group);
        $arrayParticipantes = [];
        foreach ($participantesGrupoSeleccionados as $participe) {
            $arrayParticipantes[] = Participantes::where('id_portal', $this->portal->id)
                ->where('nombre_en_portal', $participe)->first();
        }
        $actual = $this->participanteActual;
        $arrayParticipantes[] = $actual;

        $resultado = json_encode($arrayParticipantes);


        // Evento a contenedorMensajes para observar la conversación
        $this->dispatch('newChatGroup', $this->conversacionSeleccionada, $participantesGrupoSeleccionados, $arrayParticipantes);
    }
    // Envia los datos necesarios para mostrar la conversación grupal
    public function chatGlobalSeleccionado()
    {
        $this->conversacionGlobal = Conversacion::where('id_portal', Session::get('portal')->id)->where('chat_global', true)->first();
        $this->dispatch('newChatGroup', $this->conversacionGlobal, json_decode($this->conversacionGlobal->participantes_group));
    }


    // Inicializador de el apartado de listados
    public function render()
    {
        $this->portal = Session::get('portal');
        $this->usuario = Auth::user();
        $this->participantes = Participantes::where('id_portal', $this->portal->id)
            ->where('id_usuario', '!=', $this->usuario->id)->get();
        $this->participanteActual = Participantes::where('id_usuario', $this->usuario->id)->where('id_portal', $this->portal->id)->first();
        $this->conversacionesIndividuales = Conversacion::where('id_portal', $this->portal->id)->whereNull('name_group')->where(function ($query) {
            $query->where('emisor', $this->participanteActual->nombre_en_portal)
                ->orWhere('receptor', $this->participanteActual->nombre_en_portal);
        })->get();

        $this->conversacionesGrupales = Conversacion::where('id_portal', $this->portal->id)->where(function ($query) {
            $query->whereNotNull('name_group')
                ->where('emisor', $this->participanteActual->nombre_en_portal)
                ->orwhere('participantes_group', 'LIKE', '%"' . $this->participanteActual->nombre_en_portal . '"%');
        })->get();


        return view('livewire.chat.lista-chats');
    }
}
