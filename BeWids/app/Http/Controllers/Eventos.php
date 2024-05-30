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
        if(request('pagina'))
            $pagina = request('pagina');
        else
            $pagina = 1;
        
        $limite = 20;
        // var_dump('<pre>');
        // var_dump(Session::get('listaEventos'));
        // var_dump('</pre>');
        // return;

        $paginaEventos = array_slice(Session::get('listaEventos'),($pagina - 1) * $limite,$limite);
        Session::put('paginaEventos',$paginaEventos);


        return view('vistas2/eventos');

    }

}
