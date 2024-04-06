<?php

namespace App\Policies;

use App\User;
use App\Expediente;
use Illuminate\Auth\Access\HandlesAuthorization;

class CreateCaratulaPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create_caratula($params,$token)
    {
      if ($params === $token) {
        return true;
      } else {
        return false;
      }
    }
  
}
