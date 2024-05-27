<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class Eventos extends Controller
{
    protected $urls = [
        'https://datos.madrid.es/egob/catalogo/206974-0-agenda-eventos-culturales-100.json?all',
        'https://datos.madrid.es/egob/catalogo/300107-0-agenda-actividades-eventos.json?all'
        // Puedes agregar más URLs aquí
    ];
    //
    public function index(){
        return view('vistas2/eventos');
    }
    public function pedirEventos(){
        $eventos = [];
        foreach ($this->urls as $url) {
            try {
                $response = Http::get($url);
                if ($response->successful()) {
                    $eventos = array_merge($eventos, $response->json());
                } else {
                    return response()->json(['error' => 'Error al obtener los datos de la URL: ' . $url], $response->status());
                }
            } catch (Exception $e) {
                return response()->json(['error' => 'Error al obtener los datos de la URL: ' . $url . ' - ' . $e->getMessage()], 500);
            }
        }
        return response()->json($eventos);
    }

}
