<?php

namespace App\Http\Controllers;

use App\Models\Ajustes;
use App\Models\MisEventos;
use DateTime;
use Illuminate\Support\Facades\Session;

class Calendario extends Controller
{

    protected $meses = [
        'Enero',
        'Febrero',
        'Marzo',
        'Abril',
        'Mayo',
        'Junio',
        'Julio',
        'Agosto',
        'Septiembre',
        'Octubre',
        'Noviembre',
        'Diciembre'
    ];

    public function mostrar(){
        //obtenemos las fechas necesarias para el formulario teniendo en cuenta que se empieza por el mes en el que estamos

        //Obtenemos el primer dia del mes y le restamos tantos días como sea necesario para empezar en lunes
        $fechaInicio = new DateTime();
        $fechaInicio->modify('first day of this month');
        while($fechaInicio->format('w')!= 1){
            $fechaInicio->modify('-1 day');
        };
        $fechaFinal = new DateTime();
        $mes = $this->meses[$fechaFinal->format('m')-1];
        
        //Obtenemos los eventos añadidos al calendario y los ordenamos por hora
        //Los que no tienen hora se ordenan primero
        $eventos = MisEventos::where('aniadido', true)->where('id_portal',Session::get('portal')->id)->get()->toArray();
        usort($eventos, [$this, 'ordenarEventos']);
        $yo=Session::get('participanteUser');
        $ajustes = Ajustes::where('id_portal',Session::get('portal')->id)->first();
        Session::put('ajustes', $ajustes);

        //Pasamos a la vista datos necesarios como los ajustes o el participante usuario
        return view('vistas2/calendario')->with(['eventos'=>$eventos,'fechaInicio'=> $fechaInicio,'fechaFinal'=>$fechaFinal,'mes'=>$mes,'numMes'=>$fechaFinal->format('m')-1,'yo'=>$yo,'ajustes'=>$ajustes]);
    }

    public function cambiarMes(){

        //Mismas funcionalidades que en el index pero con la fecha que se le pase
        $fechaString = request('fecha');
        $fechaInicio = new DateTime($fechaString);
        while($fechaInicio->format('w')!= 1){
            $fechaInicio->modify('-1 day');
        }
        $fechaFinal = new DateTime($fechaString);

        $eventos = MisEventos::where('aniadido', true)->where('id_portal',Session::get('portal')->id)->orderBy('hora_inicio')->get()->toArray();
        usort($eventos, [$this, 'ordenarEventos']);

        $yo=Session::get('participanteUser');
        $ajustes = Ajustes::where('id_portal',Session::get('portal')->id)->first();

        return response()->json(view('partials.diasCalendario', ['eventos'=>$eventos,'fechaInicio'=> $fechaInicio,'fechaFinal'=>$fechaFinal,'yo'=>$yo,'ajustes'=>$ajustes])->render());


    }
    private function ordenarEventos($a,$b){
         // Si uno de los valores es NULL, moverlo al principio
         if (is_null($a['hora_inicio'])) return -1;
         if (is_null($b['hora_inicio'])) return 1;
 
         // Comparar las horas de inicio
         return strcmp($a['hora_inicio'], $b['hora_inicio']);

    }

    public function cambiarFechaEvt(){
        //cambiar fecha de un evento específico
        $fechaString = request('fecha');
        $idEvt = request('evt');
        $evt = MisEventos::where('id',$idEvt)->where('id_portal',Session::get('portal')->id)->first();
        $evt->fecha_cal = $fechaString;
        $evt->save();
        return true;

    }

    public function pedirEvt(){
        //devuelve el html mostrando el evento especificado en la url
        $id = request('id');
        $evento = MisEventos::where('id',$id)->where('id_portal',Session::get('portal')->id)->first();

        $yo=Session::get('participanteUser');
        $ajustes = Session::get('ajustes');
        return response()->json(view('partials.eventoCal', ['evento'=>$evento,'yo'=>$yo,'ajustes'=>$ajustes])->render());

    }
    public function retirarCal(){
        //retirar el evento indicado del calendario
        $id = request('id');
        $evento = MisEventos::where('id',$id)->where('id_portal',Session::get('portal')->id)->first();
        $evento->aniadido = false;
        $evento->hora_inicio = null;
        $evento->hora_fin = null;
        $evento->fecha_cal = null;
        $evento->save();
        return true;
    }

    public function aniadirEvento(){
        //añadir evento al calendario
        $titulo = request('titulo',false);
        $horaInicio = request('hora_inicio',false);
        $horaFin = request('hora_fin',false);
        $fechaCal = request('fecha');
        $evento = request('evento');



        $miEvento = MisEventos::where('id',$evento)->where('id_portal',Session::get('portal')->id)->first();

        if($titulo)
            $miEvento->titulo = $titulo;
        if($horaInicio)
            $miEvento->hora_inicio = $horaInicio;
        if($horaFin)
            $miEvento->hora_fin = $horaFin;
        $miEvento->fecha_cal = $fechaCal;
        $miEvento->aniadido = true;

        $miEvento->save();
        return redirect()->to('/eventos');



    }
}
