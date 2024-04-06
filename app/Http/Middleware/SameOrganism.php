<?php

namespace App\Http\Middleware;

use App\Organismo;
use Closure;
use App\Logg;

class SameOrganism
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
      $organismo = Organismo::findOrFail($request->id);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      return redirect('/')->with('error', 'No se encontro dicho organismo');
    }

    // Obtenemos el organismo del usuario actual
    $userOrganismo = $request->user()->userorganismo->first()->organismos;

    if ($userOrganismo->id != $organismo->id) {
      return redirect('/')->with('error', 'No es del mismo organismo al cual pertenece');
    }

    return $next($request);
  }
}
