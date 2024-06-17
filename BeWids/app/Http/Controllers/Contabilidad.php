<?php

namespace App\Http\Controllers;

use App\Models\Ajustes;
use App\Models\Gastos;
use App\Models\Notificaciones;
use App\Models\Participantes;
use App\Models\Reembolsos;
use Illuminate\Support\Facades\Session;
use function PHPUnit\Framework\isEmpty;

class Contabilidad extends Controller
{
    public function index(){
        //obtenemos la info de ajustes
        $ajustes = Ajustes::where('id_portal',Session::get('portal')->id)->first();
        Session::put('ajustes', $ajustes);

        //actualizamos los participantes en sesión por si ha cambiado
        $participantes = Participantes::where('id_portal',Session::get('portal')->id)->get();
        Session::put('participantes',$participantes);
        
        return view('/vistas2/contabilidad');
    }
    public function aniadirGasto(){
        //creamos el gasto con la info del formulario
        $gasto = new Gastos();
        $gasto->id_portal = Session::get('portal')->id;
        $gasto->titulo = request('titulo');
        $gasto->tipo = request('tipo');
        $gasto->cantidad = request('cantidad');
        $gasto->fecha = request('fecha');
        $gasto->pagado_por = request('pagador');
        $participantes = "";
        if(request('participantes')){
            //calculamos la parte de gasto que corresponde a cada uno y se la añadimos a su deuda
            $parte = round(request('cantidad') / count(request('participantes')),2);
            $dif = request('cantidad') - $parte * count(request('participantes'));
            foreach(request('participantes') as $participante){
                $participantes .= $participante.";";
                $persona = Participantes::where('id_portal',Session::get('portal')->id)->where('nombre_en_portal',$participante)->first();
                $persona->deuda -= $parte;
                $persona->save();
            }
        }
        $gasto->participantes = trim($participantes,';');
        $gasto->creado_por = Session::get('participanteUser')->id_usuario;
        $gasto->save();

        //le sumamos en la deuda el dinero que le deben al pagador
        $pagador= Participantes::where('id_portal',Session::get('portal')->id)->where('nombre_en_portal',request('pagador'))->first();
        $pagador->deuda += request('cantidad') - $dif;
        $pagador->save();
        
        //obtenemos un array con las deudas repartidas en reembolsos
        $deudas = $this->hacerCuentas();
        //creamos los reembolsos
        Reembolsos::where('id_portal', Session::get('portal')->id)->where('saldado', false)->where('solicitado',false)->delete();
        foreach($deudas as $deuda){
            $reembolso = new Reembolsos();
            $reembolso -> id_portal = Session::get('portal')->id;
            $reembolso -> pagador = $deuda["deudor"];
            $reembolso -> receptor = $deuda["receptor"];
            $reembolso -> cantidad = $deuda["cantidad"];
            $reembolso -> save();
            $reembolsos[] = $reembolso;
        }
        
        return redirect()->to('/contabilidad');
    }

    public function solicitarReembolso(){
        //modificamos el reembolso a solicitado
        $reembolso = json_decode(request('reembolso'));
        $reembolso = Reembolsos::find($reembolso->id);
        $notificacion = new Notificaciones();
        $notificacion -> id_portal = Session::get("portal")->id;
        $notificacion -> id_reembolso = $reembolso -> id;
        $notificacion -> mensaje = "$reembolso->pagador solicita saldar una deuda con $reembolso->receptor de unos $reembolso->cantidad €";
        $notificacion -> receptor = $reembolso->receptor;
        $reembolso -> solicitado = true;
        $notificacion -> save();
        $reembolso ->save();

        //saldamos temporalmente las deudas para que no se tengan en cuenta las deudas de reembolsos solicitados a la hora de añadir un gasto y generar los reembolsos
        $pagador = Participantes::where('id_portal', Session::get('portal')->id)->where('nombre_en_portal',$reembolso->pagador)->first();
        $receptor = Participantes::where('id_portal', Session::get('portal')->id)->where('nombre_en_portal',$reembolso->receptor)->first();
        $pagador->deuda += $reembolso->cantidad;
        $receptor->deuda -= $reembolso->cantidad;
        $pagador->save();
        $receptor->save();
        return redirect()->to('/contabilidad');
    }

    public function responderNotificacion(){
        //en caso de que se haya aceptado el reembolso se indica este como saldado y se elimina la notificación
        $noti = json_decode(request('notificacion'));
        if(request('respuesta') == 'confirmar'){
            $reembolso = Reembolsos::find($noti->id_reembolso);
            $reembolso -> saldado = true;

            $reembolso->save();

            Notificaciones::find($noti->id)->delete();
        }else{
        //en caso contrario se vuelve a sumar la deuda que habiamos quitado temporalmente y se elimina la notificación
            if(request('respuesta') == 'denegar'){
                $reembolso = Reembolsos::find($noti->id_reembolso);
                $reembolso -> solicitado = false;
                $reembolso ->save();
                $pagador = Participantes::where('id_portal', Session::get('portal')->id)->where('nombre_en_portal',$reembolso->pagador)->first();
                $receptor = Participantes::where('id_portal', Session::get('portal')->id)->where('nombre_en_portal',$reembolso->receptor)->first();
                $pagador->deuda -= $reembolso->cantidad;
                $receptor->deuda += $reembolso->cantidad;
                $pagador->save();
                $receptor->save();
                Notificaciones::find($noti->id)->delete();
                $notificacion = new Notificaciones();
                $notificacion -> id_portal = Session::get('portal')->id;
                $notificacion -> receptor = $reembolso->pagador;
                $notificacion -> mensaje = "$reembolso->receptor ha denegado la solicitud de reembolso de $reembolso->cantidad €";
                $notificacion -> save();
            }else{
                Notificaciones::find($noti->id)->delete();
            }
        }
        return redirect()->to('/contabilidad');
    }

    private function hacerCuentas(){
        $participantes = Participantes::where('id_portal', Session::get('portal')->id)->get();
        $pagadores = [];
        $receptores = [];
        foreach($participantes as $participante){
            if($participante->deuda < 0)
                $pagadores[$participante->nombre_en_portal] = round(abs($participante->deuda),2);
            if($participante->deuda > 0)
                $receptores[$participante->nombre_en_portal] = round($participante->deuda,2);
        }
        //creamos 2 arrays, 1 de personas que deben y otro de los que les deben
        //si no hay deudas se devuelve un array vacio.
        if(count($pagadores) == 0 && count($receptores) == 0)
            return [];
        
        //se crean todas las combinaciones de los 2 arrays
        $min = PHP_INT_MAX;
        $minTransacciones = [];
        $combP = $this->crearCombinaciones($pagadores);
        $combR = $this->crearCombinaciones($receptores);

        //creamos las transacciones necesarias para saldar deudas con todas las combinaciones de arrays
        foreach($combP as $pagar){
            foreach($combR as $recibir){
                $transaccion = $this->generarTransacciones2($pagar,$recibir);//new Transacciones($pagar,$recibir,$transacciones);
                // $vuelta = $transaccion->devolverTransaccion();
                // $totalTransacciones[] = $vuelta;
                switch(true){
                    //guardamos solo las que tengan el minimo numero de transacciones
                    case count($transaccion) < $min:
                        $minTransacciones = [];
                        $minTransacciones[] = $transaccion;
                        $min = count($transaccion);
                        break;
                    case count($transaccion) == $min:
                        // if(round(rand(0,1))){
                        //     $minTransacciones = [];
                        // }
                        $minTransacciones[] = $transaccion;
                        
                        break;
                    default:
                        break;
                }
            }
        }
        //de todas las posibilidades de con transacciones minimas, cogemos una aleatorea
        do{
            foreach($minTransacciones as $key => $opcion){
                if(round(rand(0,1)) && count($minTransacciones) > 1){
                   unset($minTransacciones[$key]);
                } 
            }
        }while(count($minTransacciones) != 1);
        return $minTransacciones[array_key_first($minTransacciones)];



        // echo "<pre>";
        // var_dump($totalTransacciones);
        // echo "</pre>";
        // do{
        //     $nuevasTransacciones = [];
        //     foreach($totalTransacciones as $unaTrans){
        //         foreach($unaTrans["pagadores"] as $pagar){
        //             foreach($unaTrans["deudores"] as $recibir){
        //                 $transaccion = new Transacciones($pagar,$recibir,$unaTrans["transacciones"]);
        //                 $vuelta = $transaccion->devolverTransaccion();

        //                 if(count($vuelta["pagadores"]) == 0||count($vuelta["deudores"]) == 0){
        //                     $transaccionesFinales[] = $vuelta["transacciones"];
        //                     echo "<pre>";
        //                     var_dump($transaccionesFinales);
        //                     echo "</pre>";
        //                     return true;
        //                 }else{
        //                     $nuevasTransacciones[] = $vuelta ;
        //                 }

        //             }
        //         }
        //     }
        //     $totalTransacciones = $nuevasTransacciones;

        // }while(count($totalTransacciones));

        // foreach($transaccionesFinales as $caso){
        //     switch(true){
        //         case count($caso) < $min:
        //             $minTransacciones = [];
        //             $minTransacciones[] = $caso;
        //             $min = count($caso);
        //             break;
        //         case count($caso) == $min:
        //             // if(round(rand(0,1))){
        //             //     $minTransacciones = [];
        //             // }
        //             $minTransacciones[] = $caso;
                    
        //             break;
        //         default:
        //             break;
        //     }
        // }

        


    }

    private function generarTransacciones2($pagar, $recibir){
        //vamos saldando deudas y guardando las transacciones en un array
        do{
            foreach ($recibir as $receptor => $cantidad){
                $pagador = array_search($cantidad,$pagar);
                if($pagador){
                    $transacciones[] = ["deudor" => $pagador,"receptor"=>$receptor,'cantidad'=>$cantidad];
                    unset($recibir[$receptor]);
                    unset($pagar[$pagador]);
                }
            };
            $pagador = array_key_first($pagar);
            $receptor = array_key_first($recibir);
            if(!$pagador||!$receptor){
                break;
            }
            if($pagar[$pagador] > $recibir[$receptor]){
                $transacciones[] = ["deudor" => $pagador,"receptor"=>$receptor,'cantidad'=>$recibir[$receptor]];
                $pagar[$pagador] = round($pagar[$pagador] - $recibir[$receptor],2);
                unset($recibir[$receptor]);
            }else{
                $transacciones[] = ["deudor" => $pagador,"receptor"=>$receptor,'cantidad'=>$pagar[$pagador]];
                $recibir[$receptor] = round($recibir[$receptor] - $pagar[$pagador],2);
                unset($pagar[$pagador]);
            }
            if(!$pagador&&!$receptor){
                break;
            }

        }while(true);
        return $transacciones;





        // $pagadores = $this->crearCombinaciones($pagar);
        // $receptores = $this->crearCombinaciones($recibir);
        // $transaccionesTotales = [];
        // foreach($pagadores as $deudores){
        //     foreach($receptores as $acreditadores){
        //         $transaccionesTotales[] = $this->generarTransacciones2($deudores, $acreditadores, $transacciones);
        //     }
        // }

        
        
    }
    private function unaTrans($pagar,$recibir,$transacciones){
        foreach ($recibir as $receptor => $cantidad){
            $pagador = array_search($cantidad,$pagar);
            if($pagador){
                $transacciones[] = ["deudor" => $pagador,"receptor"=>$receptor,'cantidad'=>$cantidad];
                $pagar[$pagador] -= $cantidad;
                unset($recibir[$receptor]);
                unset($pagar[$pagador]);
            }
        };
        foreach($recibir as $receptor => $cantidad){
            $pagador = array_key_first($pagar);
            if ($pagador === null) break;
            if($pagar[$pagador] > $cantidad){
                $transacciones[] = ["deudor" => $pagador,"receptor"=>$receptor,'cantidad'=>$cantidad];
                $pagar[$pagador] -= $cantidad;
            }else{
                $transacciones[] = ["deudor" => $pagador,"receptor"=>$receptor,'cantidad'=>$pagar[$pagador]];
                $recibir[$receptor] -= $pagar[$pagador];
                unset($pagar[$pagador]);
            }
        }
        if(isEmpty($pagar)||isEmpty($recibir)){

        }
    }
    private function generarTransacciones($pagar,$recibir){
        foreach ($pagar as $pagador => $deuda) {
            foreach ($recibir as $receptor => $cantidad) {
                $graph[$pagador][$receptor] = 0;
            }
        }
        
        // Llenar la matriz de deudas
        foreach ($pagar as $pagador => $deuda) {
            foreach ($recibir as $receptor => $cantidad) {
                    if ($pagar[$pagador] > $recibir[$receptor]) {
                        $graph[$pagador][$receptor] = $recibir[$receptor];
                        $pagar[$pagador] = round($pagar[$pagador] - $recibir[$receptor],2);
                        $recibir[$receptor] = 0;
                    } else {
                        $graph[$pagador][$receptor] = $pagar[$pagador];
                        $recibir[$receptor] = round($recibir[$receptor] - $pagar[$pagador],2);
                        $pagar[$pagador] = 0;
                        break;
                    }   
            }
        }
        // Generar las transacciones
        foreach ($graph as $pagador => $recibir) {
            foreach ($recibir as $receptor => $deuda) {
                if ($deuda > 0) {
                    $result[] = ["deuda"=>$deuda,"pagador"=>$pagador,"receptor"=>$receptor];
                }
            }
        }
        return $result;
    }
    private function crearCombinaciones($array) {
        
        // Si el array tiene un solo elemento, devolverlo como única permutación
        if (count($array) === 1) {
            return [$array];
        }
        
        // Iterar sobre cada elemento del array
        foreach ($array as $key => $value) {
            // Eliminar el elemento actual y obtener el resto del array
            $subArray = array_diff_key($array, [$key => $value]);
            
            // Obtener las permutaciones del resto del array
            $combinaciones = $this->crearCombinaciones($subArray);
            
            // Añadir el elemento actual a cada una de las permutaciones obtenidas
            foreach ($combinaciones as $combinacion) {
                $arrayCombinado[] = [$key => $value] + $combinacion;
            }
        }
        
        return $arrayCombinado;
    }
}
