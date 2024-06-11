<?php

namespace App\Livewire\Encuestas;

use App\Models\encuesta;
use App\Models\Participantes;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Encuestas extends Component
{
    public $encuestas;
    public $titulo;
    public $descripcion;
    public $creador;
    public $participanteActual;
    public $participantes;
    public $participantesSeleccionados=[];
    public $allParticipantes;
    public $votador_por;
    public $num_votos_totales;
    public $num_votos_hechos;
    public $opciones_votos=[];
    public $fecha_final;
    public $finalizada=True;

    public function crearEncuesta(){
        $enviando=[
            'titulo'=>$this->titulo,
            'descripcion'=>$this->descripcion,
            'creador'=>$this->participanteActual->nombre_en_portal,
        ];
    }

    public function render()
    {
        $this->encuestas=encuesta::where('id_portal',Session::get('portal')->id)->get();
        $this->participantes=Participantes::where('id_portal',Session::get('portal')->id)
        // ->where('id_usuario','!=',auth()->user()->id)
        ->get();
        $this->participanteActual=Participantes::where('id_portal',Session::get('portal')->id)
        ->where('id_usuario',auth()->user()->id)->first();
        return view('livewire.encuestas.encuestas');
    }
}
