<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Organismossector;
use App\Organismo;
use App\Expedientestipo;
use App\Logg;

class SameTiposDocumentos
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
            
            // se reemplaza la linea de arriba porque da un fallo al editar un tipo de documento y el usuario no tiene ningun sector
            $Organismo = Organismo::findOrFail(Auth::user()->organismo->id);
            $expedienteTipoIngresa = Expedientestipo::findOrFail($request->id);

            if ($Organismo->id != $expedienteTipoIngresa->organismos_id) {
              return redirect('/')->with('error', 'Usuario ' . Auth::user()->name . ' no es del mismo organismo que el tipo de documento');
            }

          } catch (\Exception $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
            return redirect('/')->with('error', 'No se ha encontrado dicho tipo de documento o no es del mismo organismo al cual pertenece');
            
          }
          

        return $next($request);
    }
}
