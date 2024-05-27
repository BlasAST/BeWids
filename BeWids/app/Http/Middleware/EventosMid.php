<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EventosMid
{
    protected $urls = [
        'https://datos.madrid.es/egob/catalogo/206974-0-agenda-eventos-culturales-100.json?all',
        'https://datos.madrid.es/egob/catalogo/300107-0-agenda-actividades-eventos.json?all',
        //'https://datos.madrid.es/egob/catalogo/202105-0-mercadillos.json?all'
        // Puedes agregar más URLs aquí
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
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
        Session::put('listaEventos',$eventos['@graph']);
        //return response()->json($eventos);
            //}
        return $next($request);
    }
}
