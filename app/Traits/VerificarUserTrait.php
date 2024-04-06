<?php

namespace App\Traits;

use App\User;

trait VerificarUserTrait
{
  public function verificarUser($usuario)
  {
    $user = User::where('email', $usuario)->first();

      if ($user !== NULL) {
        return true;
      }else{
        return false;
      }
  }
}
