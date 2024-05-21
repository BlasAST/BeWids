<?php

namespace App\Http\Controllers;

use App\Models\Gastos;
use App\Models\Deudas;
use App\Models\Participantes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\Transacciones;
use function PHPUnit\Framework\isEmpty;

class Contabilidad extends Controller
{
    public function index(){
        
        return view('/vistas2/cuentas');
    }
    public function aniadirGasto(){
        $gasto = new Gastos();
        $gasto->id_portal = Session::get('portal')->id;
        $gasto->titulo = request('titulo');
        $gasto->tipo = request('tipo');
        $gasto->cantidad = request('cantidad');
        $gasto->fecha = request('fecha');
        $gasto->pagado_por = request('pagador');
        $participantes = "";
        if(request('participantes')){
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
        $gasto->save();

        $pagador= Participantes::where('id_portal',Session::get('portal')->id)->where('nombre_en_portal',request('pagador'))->first();
        $pagador->deuda += request('cantidad') - $dif;
        $pagador->save();
        $participantes = Participantes::where('id_portal',Session::get('portal')->id)->get();
        Session::put('participantes',$participantes);
        $deudas = $this->hacerCuentas();
        //return redirect()->to('/contabilidad');
    }

    private function hacerCuentas(){
        $participantes = Participantes::where('id_portal', Session::get('portal')->id)->get();
        foreach($participantes as $participante){
            if($participante->deuda < 0)
                $pagadores[$participante->nombre_en_portal] = round(abs($participante->deuda),2);
            if($participante->deuda > 0)
                $receptores[$participante->nombre_en_portal] = round($participante->deuda,2);
        }
        

        $min = PHP_INT_MAX;
        $minTransacciones = [];
        $combP = $this->crearCombinaciones($pagadores);
        $combR = $this->crearCombinaciones($receptores);
        $totalTransacciones = [];
        $transacciones = [];
        $transaccionesFinales = [];
        foreach($combP as $pagar){
            foreach($combR as $recibir){
                $transaccion = new Transacciones($pagar,$recibir,$transacciones);
                $vuelta = $transaccion->devolverTransaccion();
                $totalTransacciones[] = $vuelta;
            }
        }
        // echo "<pre>";
        // var_dump($totalTransacciones);
        // echo "</pre>";
        do{
            $nuevasTransacciones = [];
            foreach($totalTransacciones as $unaTrans){
                foreach($unaTrans["pagadores"] as $pagar){
                    foreach($unaTrans["deudores"] as $recibir){
                        $transaccion = new Transacciones($pagar,$recibir,$unaTrans["transacciones"]);
                        $vuelta = $transaccion->devolverTransaccion();

                        if(count($vuelta["pagadores"]) == 0||count($vuelta["deudores"]) == 0){
                            $transaccionesFinales[] = $vuelta["transacciones"];
                            echo "<pre>";
                            var_dump($transaccionesFinales);
                            echo "</pre>";
                            return true;
                        }else{
                            $nuevasTransacciones[] = $vuelta ;
                        }

                    }
                }
            }
            $totalTransacciones = $nuevasTransacciones;

        }while(count($totalTransacciones));

        foreach($transaccionesFinales as $caso){
            switch(true){
                case count($caso) < $min:
                    $minTransacciones = [];
                    $minTransacciones[] = $caso;
                    $min = count($caso);
                    break;
                case count($caso) == $min:
                    // if(round(rand(0,1))){
                    //     $minTransacciones = [];
                    // }
                    $minTransacciones[] = $caso;
                    
                    break;
                default:
                    break;
            }
        }

        // do{
        //     foreach($minTransacciones as $key => $opcion){
        //         if(round(rand(0,1)) && count($minTransacciones) > 1){
        //            unset($minTransacciones[$key]);
        //         } 
        //     }
        // }while(count($minTransacciones) == 1);
        echo "<pre>";
        var_dump($minTransacciones);
        echo "</pre>";


    }

    private function generarTransacciones2($pagar, $recibir, $transacciones){



        do{
            

        }while(true);





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
