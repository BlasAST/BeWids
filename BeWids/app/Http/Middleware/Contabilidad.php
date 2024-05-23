<?php

namespace App\Http\Middleware;

use App\Models\Deudas;
use App\Models\Gastos;
use App\Models\Notificaciones;
use App\Models\Participantes;
use App\Models\Reembolsos;
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
        $gastos = Gastos::where('id_portal',$portal->id)->get();
        Session::put('gastos',$gastos);
        $reembolsosSin = Reembolsos::where('id_portal', Session::get('portal')->id)->where("saldado",false)->get();
        $reembolsosPagados = Reembolsos::where('id_portal', Session::get('portal')->id)->where("saldado",true)->get();
        Session::put("reembolsosSin",$reembolsosSin);
        Session::put("reembolsosPagados",$reembolsosPagados);
        Session::put("reembolsosPag",Reembolsos::where('id_portal', Session::get('portal')->id)->where("saldado",true)->get());
        $participantes = Participantes::where('id_portal',$portal->id)->get();
        Session::put('participantes',$participantes);
        $notificaciones = Notificaciones::where('id_portal',$portal->id)->get();
        Session::put('notificaciones',$notificaciones);


        return $next($request);
    }
}
