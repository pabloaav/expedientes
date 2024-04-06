<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Organismossector;
use App\Expediente;
use App\Foja;
use App\Logg;

class SameFojaOrganismo
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
            
            $Organismossector = Organismossector::findOrFail(Auth::user()->usersector->first()->organismossectors_id);
            $id = base64_decode($request->id);
            $fojaIngresa = Foja::findOrFail($id);
           $expedienteIngresa = Expediente::findOrFail($fojaIngresa->expedientes_id);

            if ($Organismossector->organismos_id != $expedienteIngresa->organismos_id) {
                return redirect('/')->with('error', 'Usuario ' . Auth::user()->name . ' no es del mismo organismo que dicha hoja');
            }

          } catch (\Exception $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
            return redirect('/')->with('error', 'No se ha encontrado dicha hoja o no es del mismo organismo al cual pertenece');
            
          }
      
      
      
          

        return $next($request);
    }
}
