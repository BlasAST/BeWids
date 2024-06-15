<?php

namespace App\Http\Controllers;

use App\Events\encuestas as EventsEncuestas;
use App\Livewire\Encuestas\Encuestas;
use App\Models\encuesta;
use App\Models\Participantes;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class Chat_Y_Encuestas extends Controller
{
    public function index(Request $request)
    {

        $ruta = $request->path();
        $participanteActual = Participantes::where('id_portal', Session::get('portal')->id)
            ->where('id_usuario', auth()->user()->id)->first();
        $encuestas = encuesta::where('id_portal', Session::get('portal')->id)->where('participantes', 'LIKE', '%"' . $participanteActual->nombre_en_portal . '"%')->where('finalizada', '!=', true)->get();
        $encuestasFinalizadas = encuesta::where('id_portal', Session::get('portal')->id)->where('participantes', 'LIKE', '%"' . $participanteActual->nombre_en_portal . '"%')->where('finalizada', true)->get();
        $participantes = Participantes::where('id_portal', Session::get('portal')->id)->get();
        return view('vistas2/chatYEncuestas', ['ruta' => $ruta, 'encuestas' => $encuestas, 'participantes' => $participantes,'encuestasF'=>$encuestasFinalizadas ,'participanteActual' => $participanteActual]);
    }

    public function newEncuesta(Request $request)
    {
        $encuesta = new encuesta();
        $encuesta->id_portal = Session::get('portal')->id;
        $encuesta->title = $request->tittle;
        $encuesta->descripcion = $request->descripcion;
        $participanteActual = Participantes::where('id_portal', Session::get('portal')->id)->where('id_usuario', auth()->user()->id)->first();
        $encuesta->creador = $participanteActual->nombre_en_portal;
        if ($request->allParticipantes) {
            $todos = Participantes::where('id_portal', Session::get('portal')->id)->get();
            $encuesta->participantes = json_encode($todos->pluck('nombre_en_portal')->toArray());
            $encuesta->num_votos_totales = count($todos);
        } else {
            $encuesta->participantes = json_encode($request->individual);
            $encuesta->num_votos_totales = count($request->individual);
        }
        $encuesta->save();

        $opcionesVotos = $request->opciones_votos;
        $opcionesVotos = array_filter($opcionesVotos, function ($valor) {
            return $valor != null;
        });
        $resultado = [];
        foreach ($opcionesVotos as $opcion) {
            $resultado[] = [
                'opcion' => $opcion,
                'porcentaje' => '0%',
                'id_encuesta' => $encuesta->id,
                'id' => Str::uuid()->toString(),
            ];
        };
        $encuesta->opciones_votos = json_encode($resultado);
        $encuesta->fecha_final = $request->fecha_final;
        $encuesta->save();
        return redirect()->route('encuestas');
    }

    public function pedirDatos(Request $request)
    {
        $id = $request->id;
        $tipo = $request->tipo;
        $encuesta = encuesta::where('id', $id)->where('id_portal', Session::get('portal')->id)->first();
        return response()->json($encuesta->$tipo);
    }

    public function updateEncuestas(Request $request)
    {
        $id = $request->id;
        $seleccion = $request->seleccion;
        $encuesta = encuesta::where('id', $id)->where('id_portal', Session::get('portal')->id)->first();
        $participanteActual = Participantes::where('id', auth()->user()->id)->where('id_portal', Session::get('portal')->id)->first();

        if (!$encuesta->finalizada) {
            if ($encuesta->fecha_final) {
                // $fechaActual = new DateTime();
                // $fechaActualFormateada = $fechaActual->format('Y-m-d');
                // $resultado = $encuesta->fecha_final->format('Y-m-d') < $fechaActualFormateada ? true : false;//Esto tiene error
                $resultado= $encuesta->fecha_final< now()?true:false;
                if ($resultado) {
                    $encuesta->finalizada=true;
                    $encuesta->save();
                    return response()->json('La encuesta a finalizado');
                }
            }
            if ($encuesta->num_votos_totales != $encuesta->num_votos_hechos) {
                $votosHechos = $encuesta->votado_por ? json_decode($encuesta->votado_por) : [];
                $votoYaExistente = collect($votosHechos)->where('id_usuario', $participanteActual->id)->first();
                if (!$votoYaExistente) {
                    $votosHechos[] = ['id_usuario' => $participanteActual->id];
                    $votosHechos = json_encode($votosHechos);
                    $encuesta->votado_por=$votosHechos;
                    $encuesta->num_votos_hechos+=1;
                    $encuesta->save();
                    $opciones=json_decode($encuesta->opciones_votos);
                    $opcionActualizar=collect($opciones)->where('opcion',$seleccion)->first();
                    if($opcionActualizar){
                        $solucion=(float)$encuesta->num_votos_hechos/$encuesta->num_votos_totales*100;
                        $solucion=round($solucion,2);
                        $opcionActualizar->porcentaje=$solucion.'%';
                        foreach($opciones as $op){
                        if($op->id==$opcionActualizar->id){
                                $op=$opcionActualizar;
                                $encuesta->opciones_votos=json_encode($opciones);
                                $encuesta->save();
                                break;
                            }
                        }
                         return response()->json('Tu voto a sido guardado correctamente');
                    }
                    if($encuesta->num_votos_totales==$encuesta->num_votos_hechos){
                        $encuesta->finalizada=true;
                        $encuesta->save();
                        return response()->json('La encuesta a finalizado');
                    }
                    $encuesta->save();
                    
                } else {
                    return response()->json('Tu voto ya ha sido guardado');
                }
            } else {
                $encuesta->finalizada=true;
                    $encuesta->save();
                return response()->json('Ya han votado todos los participantes');
            }
        } else {
            return response()->json('Esta encuesta finalizo');
        }

    }
}
