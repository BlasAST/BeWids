<?php

namespace App\Http\Middleware;

use App\Models\Deudas;
use App\Models\Gastos;
use App\Models\Participantes;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class Contabilidad
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $portal = Session::get('portal');
        $gastos = Gastos::where('id_portal',$portal->id)->where('cantidad','>',0)->get();
        Session::put('gastos',$gastos);
        $reembolsos = Gastos::where('id_portal',$portal->id)->where('cantidad','<',0)->get();
        Session::put('reembolsos',$reembolsos);
        $deudas = Deudas::where('id_portal',$portal->id)->get();
        Session::put('deudas',$deudas);
        $participantes = Participantes::where('id_portal',$portal->id)->get();
        Session::put('participantes',$participantes);

        return $next($request);
    }
}
