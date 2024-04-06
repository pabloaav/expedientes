<?php

namespace App\Policies;

use App\User;
use App\Organismo;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganismoPolicy
{
  use HandlesAuthorization;


  public function show(User $user, Organismo $organismo)
  {
    // Obtenemos el organismo del usuario actual
    $userOrganismo = $user->userorganismo->first()->organismos;
    return $userOrganismo->id == $organismo->id;
  }
}
