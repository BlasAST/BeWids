<?php

namespace App\Console\Commands;

use App\Models\Eventos;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PedirDatos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pedir-datos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script para actualizar los datos de los eventos en nuestra base de datos';

    protected $urls = [
        'https://datos.madrid.es/egob/catalogo/206974-0-agenda-eventos-culturales-100.json?all',
        'https://datos.madrid.es/egob/catalogo/206717-0-agenda-eventos-bibliotecas.json?all',
        'https://datos.madrid.es/egob/catalogo/212504-0-agenda-actividades-deportes.json?all',
        'https://datos.madrid.es/egob/catalogo/202105-0-mercadillos.json?all',
        'https://datos.madrid.es/egob/catalogo/201132-0-museos.json?all',
        'https://datos.madrid.es/egob/catalogo/200761-0-parques-jardines.json?all',
        'https://datos.madrid.es/egob/catalogo/300261-0-agenda-proximas-carreras.json?all',
        'https://datos.madrid.es/egob/catalogo/208862-7650046-ocio_salas.json?all',
        'https://datos.madrid.es/egob/catalogo/208862-7650164-ocio_salas.json?all',
        'https://datos.madrid.es/egob/catalogo/208862-7650180-ocio_salas.json?all',




        // Puedes agregar más URLs aquí
    ];

    /**
     * Execute the console command.
     */

    public function handle()
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
                    $eventos = $this->limpiarDatos($respuesta[array_key_last($respuesta)],explode('?',$url)[0],$eventos);
                } else {
                    return response()->json(['error' => 'Error al obtener los datos de la URL: ' . $url], $response->status());
                }
            } catch (Exception $e) {
                return response()->json(['error' => 'Error al obtener los datos de la URL: ' . $url . ' - ' . $e->getMessage()], 500);
            }
        }

        //$this->info(print_r($eventos));
        shuffle($eventos);
        Eventos::truncate();
        foreach ($eventos as $evento) {
            Eventos::create($evento);
        }


        

        // $apis = $this->limpiarDatos($eventos);
        // Session::put('listaEventos',array_merge(...$apis));
        // // foreach($eventos as $evento){
        // //     DB::table('eventos')->truncate();
        // //     $evt = new Eventos();
            // $evt->titulo = "hgola";
        //}
        
        //$this->limpiarDatos($eventos)
        //return response()->json($eventos);
            //}
    }



    private function limpiarDatos($evts,$nombreApi,$eventos){


        // return array_map(function($lista){
            foreach($evts as $evt){
                $evento = [];
                $evento['api'] = $nombreApi;

                //TITULO
                $evento['titulo']="Evento";
                if(array_key_exists('title',$evt)){
                    $evento['titulo'] = $evt['title'];
                }

                //DESCRIPCIÓN
                $evento['descripcion']="";
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
                $evento['inicio']="";
                if(array_key_exists('dtstart',$evt)){
                    $evento['inicio'] = $evt['dtstart'];
                }



                //FIN
                $evento['fin']="";
                if(array_key_exists('dtend',$evt)){
                    $evento['fin'] = $evt['dtend'];
                }

                



                //HORARIO
                $evento['horas']="";
                $evento['horario']="";
                if(array_key_exists('time',$evt)){
                    $evento['horas'] = $evt['time'];
                }

                if(array_key_exists('organization',$evt)&&array_key_exists('schedule',$evt)){
                    $evento['horario'] = $evt['organization']['schedule'];
                }



                //CALENDARIO
                $evento['dias']="";
                if(array_key_exists('recurrence',$evt)&&array_key_exists('days',$evt['recurrence'])){
                    $evento['dias'] = $evt['recurrence']['days'];
                }



                //PRECIO
                $evento['precio']="";
                if(array_key_exists('price',$evt)){
                    if($evt['free'])
                        $evento['precio'] = 'GRATIS';
                    else
                        $evento['precio'] = $evt['price'];

                }



                // @if(array_key_exists('address',$evento)&& array_key_exists('area',$evento['address'])&& array_key_exists('street-address',$evento['address']['area']))<p>Lugar: <span class="font-normal text-xs">{{$evento['address']['area']['street-address'].", ".$evento['address']['area']['postal-code'].", ".$evento['address']['area']['locality']." -> ".$evento['event-location']}}</span></p>@endif                        
                // @if (array_key_exists('link',$evento))
                // <a href="{{$evento['link']}}" class="font-normal ">Link al evento</a>
                // @endif







                //DIRECCION
                $evento['calle']="";
                $evento['cp']="";
                $evento['localidad']="";
                $evento['lugar']="";
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
                $evento['conex']="";
                if (array_key_exists('link',$evt)){
                    $evento['conex'] = $evt['link'];

                }


                //LAT y LONG
                $evento['latitud']=null;
                $evento['longitud']=null;
                if(array_key_exists('location',$evt) && array_key_exists('latitude',$evt['location'])){
                    $evento['latitud'] = $evt['location']['latitude'];
                    $evento['longitud'] = $evt['location']['longitude'];
                }






                //AUDIENCIA
                $evento['edad']="";
                if(array_key_exists('audience',$evt)){
                    $evento['edad'] = $evt['audience'];
                }


                $evento['url']="";
                if(array_key_exists('@id',$evt)){
                    $evento['url'] = $evt['@id'];
                }else{
                    $evento['url'] = "pepe";

                }

                switch($nombreApi){
                    case 'https://datos.madrid.es/egob/catalogo/206974-0-agenda-eventos-culturales-100.json':
                        $evento['categoria'] = 'Culturales';
                        break;
                    case 'https://datos.madrid.es/egob/catalogo/212504-0-agenda-actividades-deportes.json':
                        $evento['categoria'] = 'Actividades Deportivas';
                        break;
                    case 'https://datos.madrid.es/egob/catalogo/202105-0-mercadillos.json':
                        $evento['categoria'] = 'Mercadillos';
                            break;
                    case 'https://datos.madrid.es/egob/catalogo/201132-0-museos.json':
                        $evento['categoria'] = 'Museos';
                        break;
                    case 'https://datos.madrid.es/egob/catalogo/200761-0-parques-jardines.json':
                        $evento['categoria'] = 'Parques y jardínes';
                        break;
                    case 'https://datos.madrid.es/egob/catalogo/300261-0-agenda-proximas-carreras.json':
                        $evento['categoria'] = 'Carreras';
                        break;
                    case 'https://datos.madrid.es/egob/catalogo/208862-7650046-ocio_salas.json':
                        $evento['categoria'] = 'Teatro y espectáculos';
                        break;
                    case 'https://datos.madrid.es/egob/catalogo/208862-7650164-ocio_salas.json':
                        $evento['categoria'] = 'Cine';
                        break;
                    case 'https://datos.madrid.es/egob/catalogo/208862-7650180-ocio_salas.json':
                        $evento['categoria'] = 'Música';
                        break;
                    case 'https://datos.madrid.es/egob/catalogo/206717-0-agenda-eventos-bibliotecas.json':
                        $evento['categoria'] = 'Bibliotecas';
                        break;
                    default:
                        break;
                }


                array_push($eventos,$evento);

            }

            return $eventos;
    // },$evts);        
    }
}
