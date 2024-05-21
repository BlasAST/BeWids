<?php

namespace App\Services;

use function PHPUnit\Framework\isEmpty;

Class Transacciones{
    public $arPagadores,$arReceptores,$transacciones,$combPagadores,$combReceptores;

    public function __construct($arPagadores,$arReceptores,$transacciones){
        $this->arPagadores = $arPagadores;
        $this->arReceptores = $arReceptores;
        $this->transacciones = $transacciones;
        $this->hacerTransaccion();
        $this->combPagadores = $this->reOrganizarComb($this->arPagadores);
        $this->combReceptores = $this->reOrganizarComb($this->arReceptores);


    }

    private function hacerTransaccion(){
        foreach ($this->arReceptores as $receptor => $cantidad){
            $pagador = array_search($cantidad,$this->arPagadores);
            if($pagador){
                $this->transacciones[] = ["deudor" => $pagador,"receptor"=>$receptor,'cantidad'=>$cantidad];
                unset($this->arReceptores[$receptor]);
                unset($this->arPagadores[$pagador]);
            }
        };
        $pagador = array_key_first($this->arPagadores);
        $receptor = array_key_first($this->arReceptores);
        if(!$pagador&&!$receptor){
            return true;
        }
        if($this->arPagadores[$pagador] > $this->arReceptores[$receptor]){
            $this->transacciones[] = ["deudor" => $pagador,"receptor"=>$receptor,'cantidad'=>$this->arReceptores[$receptor]];
            $this->arPagadores[$pagador] -= $this->arReceptores[$receptor];
            unset($this->arReceptores[$receptor]);
        }else{
            $this->transacciones[] = ["deudor" => $pagador,"receptor"=>$receptor,'cantidad'=>$this->arPagadores[$pagador]];
            $this->arReceptores[$receptor] -= $this->arPagadores[$pagador];
            unset($this->arPagadores[$pagador]);
        }
    }

    private function reOrganizarComb($array){
         // Si el array tiene un solo elemento, devolverlo como única permutación
        if (count($array) === 1) {
            return [$array];
        }
        if (count($array) === 0) {
            return null;
        }
        
        // Iterar sobre cada elemento del array
        foreach ($array as $key => $value) {
            // Eliminar el elemento actual y obtener el resto del array
            $subArray = array_diff_key($array, [$key => $value]);
            
            // Obtener las permutaciones del resto del array
            $combinaciones = $this->reOrganizarComb($subArray);
            
            // Añadir el elemento actual a cada una de las permutaciones obtenidas
            foreach ($combinaciones as $combinacion) {
                $arrayCombinado[] = [$key => $value] + $combinacion;
            }
        }
        
        return $arrayCombinado;
    }

    public function devolverTransaccion(){
        $vuelta["pagadores"] = $this->combPagadores;
        $vuelta["deudores"] = $this->combReceptores;
        $vuelta["transacciones"] = $this->transacciones;
        return $vuelta;
    }

}