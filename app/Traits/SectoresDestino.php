<?php

namespace App\Traits;

use App\Expediente;
use App\User;
use Illuminate\Support\Facades\DB;

trait SectoresDestino
{
  /**
   * Esta funcion retorna una coleccion con los sectores que coincidan con los ids cargados en la funcion arraySectoresActivos
   *
   */
  public function sectoresDestino($data)
  {
    $sectores = DB::table('organismossectors')
                    ->whereIn('id', $data)
                    ->get();
    
    return $sectores;
  }


  /**
   * Esta funcion retorna un array que contiene las coincidencias entre los sectores a los que pertenece el usuario y que al mismo tiempo estén activos y
   * formen parte de la ruta para ese tipo de documento
   *
   */
  public function arraySectoresActivos($expediente, $user)
  {
    // se obtienen las rutas activas para ese tipo de documento
    $rutas_activas = $expediente->expedientetipo->rutas->where('activo', 1);

    $exptipo_rutas = $rutas_activas->pluck('organismossectors_id')->toArray();
    $sectores_user = $user->usersector->pluck('organismossectors_id')->toArray();

    // permite obtener los id de sector que estan en presentes en los 2 arrays
    $array_sectores = array_intersect($exptipo_rutas, $sectores_user);

    return $array_sectores;
  }
}