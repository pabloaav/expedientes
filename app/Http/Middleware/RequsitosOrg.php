<?php

namespace App\Http\Middleware;

use App\Expedientesruta;
use App\Expedientesrutasrequisitos;
use Illuminate\Support\Facades\Auth;
use App\Organismo;
use App\Expedientestipo;
use App\Logg;
use Closure;

class RequsitosOrg
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
            // Obtenemos el organismo de la consulta
            $expRutaReq =Expedientesrutasrequisitos::findOrFail($request->id);
            $expRuta =Expedientesruta::find($request->id);
            $organismo = (Expedientestipo::find($expRuta->expedientestipos_id))->organismo;
           
          } catch (\Exception $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
            return redirect('/')->with('error', 'No se encontro dicha ruta.');
          }
      
          // Obtenemos el organismo del usuario actual
          $userOrganismo = $request->user()->userorganismo->first()->organismos;
      
          if ($userOrganismo->id != $organismo->id) {
            return redirect('/')->with('error', 'No es del mismo organismo al cual pertenece.');
          }
      
          return $next($request);
    }
}
