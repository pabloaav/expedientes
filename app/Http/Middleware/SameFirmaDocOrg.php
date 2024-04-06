<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Organismossector;
use App\Expediente;
use App\Foja;
use App\Logg;

class SameFirmaDocOrg
{
  /**
   * Recibe una foja id de la cual compara para obtener el resultado de si pertenece o no al mismo sector y organismo
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    try {
      // Obtenemos el organismo de la consulta
      
      $organismossector = Organismossector::findOrFail(Auth::user()->usersector->first()->organismossectors_id);
      
      $expedienteIngresa = Expediente::findOrFail($request->id);
      if ($organismossector->organismos_id != $expedienteIngresa->organismos_id) {
          return redirect('/')->with('error', 'Usuario ' . Auth::user()->name . ' no es del mismo organismo que el documento');
      }

    } catch (\Exception $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      return redirect('/')->with('error', 'No se ha encontrado dicho documento o no es del mismo organismo al cual pertenece');
      
    }

  return $next($request);
  }
}
