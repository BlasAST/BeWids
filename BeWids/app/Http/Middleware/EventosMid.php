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
        'https://datos.madrid.es/egob/catalogo/202105-0-mercadillos.json?all',
        'https://datos.madrid.es/egob/catalogo/201132-0-museos.json?all',
        'https://datos.madrid.es/egob/catalogo/200761-0-parques-jardines.json?all',
        'https://datos.madrid.es/egob/catalogo/300261-0-agenda-proximas-carreras.json?all',
        'https://datos.madrid.es/egob/catalogo/208862-7650046-ocio_salas.json?all',
        'https://datos.madrid.es/egob/catalogo/208862-7650164-ocio_salas.json?all',
        'https://datos.madrid.es/egob/catalogo/208862-7650180-ocio_salas.json?all',
        'https://datos.madrid.es/egob/catalogo/208862-7650180-ocio_salas.json?all',




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
                    // echo '<pre>';
                    // var_dump($response->json());
                    // echo'<pre>';
                    $respuesta = $response->json();
                    $eventos[] = array_merge($eventos, $respuesta[array_key_last($respuesta)]);
                } else {
                    return response()->json(['error' => 'Error al obtener los datos de la URL: ' . $url], $response->status());
                }
            } catch (Exception $e) {
                return response()->json(['error' => 'Error al obtener los datos de la URL: ' . $url . ' - ' . $e->getMessage()], 500);
            }
        }

        
        Session::put('listaEventos',$this->limpiarDatos($eventos));
        
        //$this->limpiarDatos($eventos)
        //return response()->json($eventos);
            //}
        return $next($request);
    }

    private function limpiarDatos($evts){


        return array_map(function($lista){
            return array_map(function($evt){
                $evento = [];

                //TITULO
                if(array_key_exists('title',$evt)){
                    $evento['titulo'] = $evt['title'];
                }

                //DESCRIPCIÓN
                if(array_key_exists('description',$evt)){
                    if($evt['description'])
                        $evento['descripcion'] = $evt['description'];
                    else
                        $evento['descripcion'] = "Sin descripción actualmente.....";
                }
                if(array_key_exists('organization',$evt)&&array_key_exists('organization-desc',$evt['organization'])){
                    $evento['descripcion'] = $evt['organization']['organization-desc'];

                }



                //INICIO
                if(array_key_exists('dtstart',$evt)){
                    $evento['inicio'] = $evt['dtstart'];
                }



                //FIN
                if(array_key_exists('dtend',$evt)){
                    $evento['fin'] = $evt['dtend'];
                }

                if(array_key_exists('organization',$evt)&&array_key_exists('schedule',$evt)){
                    $evento['horario'] = $evt['organization']['schedule'];
                }



                //HORARIO
                if(array_key_exists('time',$evt)){
                    $evento['horas'] = $evt['time'];
                }



                //CALENDARIO
                if(array_key_exists('recurrence',$evt)&&array_key_exists('days',$evt['recurrence'])){
                    $evento['dias'] = $evt['recurrence']['days'];
                }



                //PRECIO
                if(array_key_exists('price',$evt)){
                    if($evt['free'])
                        $evento['precio'] = 'GRATIS';
                    else
                        $evento['precio'] = $evt['price'];

                }



                //CALENDARIO
                if(array_key_exists('recurrence',$evt)&&array_key_exists('days',$evt['recurrence'])){
                    $evento['dias'] = $evt['recurrence']['days'];
                }

                // @if(array_key_exists('address',$evento)&& array_key_exists('area',$evento['address'])&& array_key_exists('street-address',$evento['address']['area']))<p>Lugar: <span class="font-normal text-xs">{{$evento['address']['area']['street-address'].", ".$evento['address']['area']['postal-code'].", ".$evento['address']['area']['locality']." -> ".$evento['event-location']}}</span></p>@endif                        
                // @if (array_key_exists('link',$evento))
                // <a href="{{$evento['link']}}" class="font-normal ">Link al evento</a>
                // @endif







                //DIRECCION
                if(array_key_exists('address',$evt)){
                    if(array_key_exists('area',$evt['address'])){
                        if(array_key_exists('street-address',$evt['address']['area'])){
                            $evento['calle'] = $evt['address']['area']['street-address'];
                            $evento['cp'] = $evt['address']['area']['postal-code'];
                            $evento['localidad'] = $evt['address']['area']['locality'];

                        }
                    }
                    if(array_key_exists('locality',$evt['address'])){
                        $evento['calle'] = $evt['address']['street-address'];
                        $evento['cp'] = $evt['address']['postal-code'];
                        $evento['localidad'] = $evt['address']['locality'];
                    }
                }

                if(array_key_exists('event-location',$evt)){
                    $evento['lugar'] = $evt['event-location'];
                }




                //LINK
                if (array_key_exists('link',$evt)){
                    $evento['conex'] = $evt['link'];

                }


                //LAT y LONG
                if(array_key_exists('location',$evt) && array_key_exists('latitude',$evt['location'])){
                    $evento['latitud'] = $evt['location']['latitude'];
                    $evento['longitud'] = $evt['location']['longitude'];
                }






                //AUDIENCIA
                if(array_key_exists('audience',$evt)){
                    $evento['edad'] = $evt['audience'];
                }



                if(array_key_exists('@id',$evt)){
                    $evento['url'] = $evt['@id'];
                }else{
                    $evento['url'] = "pepe";

                }





                return $evento;

            },$lista);
    },$evts);        
    }
}
