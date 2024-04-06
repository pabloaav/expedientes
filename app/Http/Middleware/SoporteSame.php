<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use App\Organismo;
use App\Soporte;
use App\User;
use App\Logg;
use Closure;

class SoporteSame
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
          if(session('permission')->contains('soporteadmin.index'))
          {
            return $next($request);
          } 

            // Obtenemos el organismo de la consulta
            $soporte = Soporte::findOrFail($request->id);
            $organismo = (User::find($soporte->users_id))->userorganismo->first()->organismos;
           
          } catch (\Exception $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
            return redirect('/')->with('error', 'No se encontro dicho soporte.');
          }
      
          // Obtenemos el organismo del usuario actual
          $userOrganismo = $request->user()->userorganismo->first()->organismos;
      
          if ($userOrganismo->id != $organismo->id) {
            return redirect('/')->with('error', 'No es del mismo organismo al cual pertenece.');
          }
        
          return $next($request);
        }
}
