<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use App\Organismo;
use App\Organismossectorsuser;
use App\Organismossector;
use App\Logg;

class sectoruserOrganismo
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
            $sectorUser = Organismossectorsuser::findOrFail($request->id);
            $sectorIngresa = Organismossector::findOrFail($sectorUser->organismossectors_id);

            if ($Organismo->id != $sectorIngresa->organismos_id) {
                return redirect('/')->with('error', 'Usuario ' . Auth::user()->name . ' no es del mismo organismo. ');
            }

          } catch (\Exception $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
            return redirect('/')->with('error', 'No se ha encontrado dicha operacion o no es del mismo organismo al cual pertenece');
            
          }
      
      
      
          

        return $next($request);
    }
}
