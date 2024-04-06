<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Organismossector;
use App\Logg;

class HasSector
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            // Falla si no tiene sector
            
            $Organismossector = Organismossector::findOrFail(Auth::user()->usersector->first()->organismossectors_id);

          } catch (\Exception $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
            return redirect('/')->with('error', 'Usuario ' . Auth::user()->name . ' no tiene un sector asignado. Solicitelo con un administrador de su organizacion');
            // return redirect('/')->with('errors', 'No tiene asignado un sector. Solicitelo con un administrador de su organizacion');
            
          }
      
      
      
          return $next($request);
        
    }
}
