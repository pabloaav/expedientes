<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Expediente;


trait VerificarNumeroDocumento
{
  public function verificarNumeroDocumento($doc)
  {
      // Los expedientes del organismo actual
      $expedientes = Expediente::where('organismos_id', '=', $doc['organismo'])
                                ->whereNull('deleted_at');
      $expteNumeroExiste = $expedientes->pluck('expediente_num')->contains($doc['num_documento']);
      // $anioActual = Carbon::now()->format('Y');

      // Controlar que no exista cargado un numero de expediente igual para esta organizacion
      // Si el numero de documento existe, se compara el año del existente con el año que ingresa el usuario al momento de crear
      if ($expteNumeroExiste) {
        $comprobacion =  Expediente::where('organismos_id', '=', $doc['organismo'])
          ->where('expediente_num', '=', $doc['num_documento'])
          ->whereNull('deleted_at')
          ->get();
        $anioIngresado = date("Y", strtotime($doc['fecha_inicio']));
        foreach ($comprobacion as $expediente => $value) {
          $anioDoc = Carbon::parse($value->fecha_inicio );
          // if ($anioActual ==  $anioDoc->format('Y')) {
          if ($anioIngresado ==  $anioDoc->format('Y')) {
            $errors[0] = 'El numero de documento ' . $doc['num_documento'] . ' ya existe y está en uso en dicho año.';
            return ['error' =>$errors,'success' =>false];
            die;
          }
        }
      }
      return['success' =>true];
  }
}
