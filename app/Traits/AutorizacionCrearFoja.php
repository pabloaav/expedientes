<?php

namespace App\Traits;

use App\Expediente;
use App\Expedienteestado;

trait AutorizacionCrearFoja
{
  public function autorizacionCrearFoja($documento, $usuario)
  {
      // Los expedientes del organismo actual
      $id = Expediente::where('expediente_num',$documento)->firstOrFail();
      $expedientes = Expedienteestado::where('expedientes_id', $id->id)->get()->last()->users_id;
  
      if ($expedientes == $usuario) {
        return true;
      }else{
        return false;
      }
  }
}
