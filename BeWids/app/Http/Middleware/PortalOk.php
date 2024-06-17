<?php

namespace App\Http\Middleware;

use App\Models\Participantes;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class PortalOk
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if(Session::get('portal') && Participantes::where('id_portal',Session::get('portal')->id)->where('id_usuario', Auth::user()->id)->exists()){
            return $next($request);
        }else{
            return redirect()->to('/');
        }
    }
}
