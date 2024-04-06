<?php

namespace App\Http\Middleware;

use Closure;
use App\Organismo;
use App\Organismossector;
use Illuminate\Support\Facades\Auth;
use App\Logg;

class SameSectorOrganism
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
            
            $Organismossector = Organismo::findOrFail(Auth::user()->userorganismo->first()->organismos_id);      
            $sectorIngresa = Organismossector::findOrFail($request->sector_id);

            if ($Organismossector->id != $sectorIngresa->organismos_id) {
                return redirect('/')->with('error', 'Usuario ' . Auth::user()->name . ' no es del mismo organismo que el sector');
            }

          } catch (\Exception $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
            return redirect('/')->with('error', 'No se ha encontrado dicho sector o no es del mismo organismo al cual pertenece');
            
          }
      
      
      
          

        return $next($request);
    }
}
