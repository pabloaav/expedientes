<?php

namespace App\Traits;

trait AutorizacionConsultar
{
  public function autorizacionConsultar($usuario, $usuarioToken)
  {
      if ($usuario == $usuarioToken) {
        return true;
      }else{
        return false;
      }
  }
}
