<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Organismo;
use App\Deposito;
use App\Logg;

class SameDepositoOrganismo
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
            
            $Organismo = Organismo::findOrFail(Auth::user()->userorganismo->last()->organismos_id);
            $Deposito = Deposito::findOrFail($request->id);
        
           

            if ($Organismo->id != $Deposito->organismos_id) {
                return redirect('/')->with('error', 'Usuario ' . Auth::user()->name . ' no es del mismo organismo que el deposito');
            }

          } catch (\Exception $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
            return redirect('/')->with('error', 'No se ha encontrado dicho deposito o no es del mismo organismo al cual pertenece');
            
          }
      
      
      
          

        return $next($request);
    }
}
