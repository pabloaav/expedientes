<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Organismossector;
use App\Organismo;
use App\Plantilla;
use App\Logg;

class SamePlantillaOrganismo
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
            $Organismo = Organismo::findOrFail(Auth::user()->userorganismo->first()->organismos_id);            
            $PlantillaIngresa = Plantilla::findOrFail($request->id);
            $Organismossector = Organismossector::findOrFail($PlantillaIngresa->organismossectors_id);

            if ($Organismossector->organismos_id != $Organismo->id) {
                return redirect('/')->with('error', 'Usuario ' . Auth::user()->name . ' no es del mismo organismo que dicha plantilla');
            }

          } catch (\Exception $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
            return redirect('/')->with('error', 'No se ha encontrado dicha plantilla o no es del mismo organismo al cual pertenece');
            
          }
      
      
      
          

        return $next($request);
    }
}
