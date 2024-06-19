<?php

namespace App\Http\Controllers;

use App\Models\Ajustes;
use App\Models\Eventos;
use App\Models\MisEventos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EventosC extends Controller
{

    protected $evtPag = 20;
    //
    
    public function index(){

        //actualizamos los ajustes y mostramos la vista
        $ajustes = Ajustes::where('id_portal',Session::get('portal')->id)->first();
        Session::put('ajustes', $ajustes);

        return view('vistas2/eventos');

    }

    public function eliminar(){
        MisEventos::where('id_portal',Session::get('portal')->id)->where('id',request('evt'))->delete();
        return true;
    }

    public function aniadirEvento(){
        //creamos un MisEventos y le introducimos los datos del evento seleccionado
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
        $evento->fecha_cal = null;
        $evento->hora_inicio = null;
        $evento->hora_fin = null;
        $evento->save();
        $ajustes = Ajustes::where('id_portal',Session::get('portal')->id)->first();
        $yo = Session::get('participanteUser');
        return response()->json(view('partials.divMiEvento', ['evento' => $evento,'yo'=>$yo,'ajustes'=>$ajustes])->render());


    }
    public function mostrarEventos(Request $request) {
        $evtPag = $this->evtPag; // Número de eventos por página
    
        // Determinar la página actual
        $pagina = $request->input('pag', 1);
        //Creamos una query de Eventos y vamos añadiendo condiciones
        //según los filtros que se han indicado
        $query = Eventos::query();
        $ajustes = Session::get('ajustes');
        $yo = Session::get('participanteUser');

        //si han filtrado por categoria
        $categoriasMostrar = [];
        if ($request->has('cat')) {
            $categoriasMostrar = explode('%', str_replace("-", " ", $request->input('cat')));
            $query->whereIn('categoria', $categoriasMostrar);
        }

        //Si han filtrado por edad de audiencia
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
        //filtro del buscador
        if($request->has('valor')){
            $valor = $request->input('valor');
            $query->where(function ($subquery) use ($valor) {
                $subquery->where('titulo', 'LIKE', '%' . $valor . '%')
                         ->orWhere('lugar', 'LIKE', '%' . $valor . '%');
            });
        }
        
        //filtro por Gratis
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
            $divs[] = view('partials.divEvento', ['evento' => $evento,'yo'=>$yo,'ajustes'=>$ajustes])->render();
        }
    
        // Estructura de la respuesta JSON
        return response()->json([
            'eventos' => $divs,
            'currentPage' => $pagina,
            'totalPages' => $totalPaginas,
        ]);
    }

    public function buscador(){
        //devuelve el titulo de los eventos para mostrar según van buscando en el buscador
        $valor = request()->valor;
        $titulos = Eventos::where('titulo','LIKE','%'.$valor.'%')->orWhere('lugar','LIKE','%'.$valor.'%')->pluck('titulo')->toArray();
        return $titulos;
    }

    public function crearEvento(){
        $evt = new MisEventos();
        $evt-> id_portal = Session::get('portal')->id;
        $evt -> titulo = request('titulo') ?? '';
        $evt -> descripcion = request('descripcion') ?? '';
        $evt -> inicio = request('fecha') ?? '';
        $evt -> horas = request('hora') ?? '';
        $evt->categoria = 'personalizado';
        $evt->save();
        return redirect()->to('/eventos');

    }
    

}
