<?php

namespace App\Policies;

use App\Expediente;
use App\User;
use App\Foja;
use Illuminate\Auth\Access\HandlesAuthorization;

class FojaPolicy
{
  use HandlesAuthorization;

  /**
   * Determine whether the user can view the foja.
   *
   * @param  \App\User  $user
   * @param  \App\Foja  $foja
   * @return mixed
   */
  public function view(User $user, Foja $foja)
  {
    //
  }

  /**
   * Determine whether the user can create fojas.
   *
   * @param  \App\User  $user
   * @return mixed
   */
  public function create(User $user)
  {
    //
  }
  /**
   * Determine whether the user can update the foja.
   *
   * @param  \App\User  $user
   * @param  \App\Foja  $foja
   * @return mixed
   */
  public function update(User $user, Foja $foja)
  {
    //
  }

  /**
   * Determine whether the user can delete the foja.
   *
   * @param  \App\User  $user
   * @param  \App\Foja  $foja
   * @return mixed
   */
  public function delete(User $user, Foja $foja)
  {
    //
  }
}
