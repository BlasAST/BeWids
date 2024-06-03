<?php

namespace App\Http\Controllers;

use App\Models\Eventos;
use App\Models\MisEventos;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Ramsey\Uuid\Type\Integer;

class EventosC extends Controller
{
    protected $urls = [
        'https://datos.madrid.es/egob/catalogo/206974-0-agenda-eventos-culturales-100.json?all',
        'https://datos.madrid.es/egob/catalogo/300107-0-agenda-actividades-eventos.json?all'
        // Puedes agregar más URLs aquí
    ];
    protected $evtPag = 20;
    //
    
    public function index(){


        return view('vistas2/eventos');

    }

    public function aniadirEvento(){
        $evt = Eventos::find(request('evt'));
        $evento = new MisEventos();
        $evento ->id_portal = Session::get('portal')->id;
        $evento->titulo = $evt->titulo;
        $evento->descripcion = $evt->descripcion;
        $evento->inicio = $evt->inicio;
        $evento->fin = $evt->fin;
        $evento->horario = $evt->horario;
        $evento->horas = $evt->horas;
        $evento->dias = $evt->dias;
        $evento->precio = $evt->precio;
        $evento->calle = $evt->calle;
        $evento->cp = $evt->cp;
        $evento->localidad = $evt->localidad;
        $evento->lugar = $evt->lugar;
        $evento->conex = $evt->conex;
        $evento->latitud = $evt->latitud;
        $evento->longitud = $evt->longitud;
        $evento->edad = $evt->edad;
        $evento->categoria = $evt->categoria;
        $evento->url = $evt->url;
        $evento->api = $evt->api;
        $evento->save();
        return response()->json(view('partials.divMiEvento', ['evento' => $evento])->render());


    }
    public function mostrarEventos(Request $request) {
        $evtPag = $this->evtPag; // Número de eventos por página
    
        // Determinar la página actual
        $pagina = $request->input('pag', 1);

        $query = Eventos::query();


        $categoriasMostrar = [];
        if ($request->has('cat')) {
            $categoriasMostrar = explode('%', str_replace("-", " ", $request->input('cat')));
            $query->whereIn('categoria', $categoriasMostrar);
        }

        $edades = [];
        $filtrosMostrar = [];
        if ($request->has('filt')) {
            $filtrosMostrar = explode('%',$request->input('filt'));

            $edades = Eventos::select('edad')->distinct()->pluck('edad')->toArray();

            $edades = array_filter($edades, function($edad) use ($filtrosMostrar) {
                foreach($filtrosMostrar as $e){
                    if(in_array($e,explode(",",$edad)))
                        return true;
                }
                return false;
            });
            $query->whereIn('edad',$edades);
        }
        
        if($request->has('valor')){
            $valor = $request->input('valor');
            $query->where(function ($subquery) use ($valor) {
                $subquery->where('titulo', 'LIKE', '%' . $valor . '%')
                         ->orWhere('lugar', 'LIKE', '%' . $valor . '%');
            });
        }
        
        
        if ($request->has('gratis')) {
            $gratis = $request->input('gratis');
            $query->where('precio', $gratis);
        }
    
        $totalEventos = $query->count();
        $eventos = $query->skip(($pagina - 1) * $evtPag)->take($evtPag)->get();
    
        $totalPaginas = ceil($totalEventos / $evtPag);
    
        // Generar los divs de eventos
        $divs = [];
        foreach ($eventos as $evento) {
            $divs[] = view('partials.divEvento', ['evento' => $evento])->render();
        }
    
        // Estructura de la respuesta JSON
        return response()->json([
            'eventos' => $divs,
            'currentPage' => $pagina,
            'totalPages' => $totalPaginas,
        ]);
    }

    public function buscador(){
        $valor = request()->valor;
        $titulos = Eventos::where('titulo','LIKE','%'.$valor.'%')->orWhere('lugar','LIKE','%'.$valor.'%')->pluck('titulo')->toArray();
        return $titulos;
    }
    

}
