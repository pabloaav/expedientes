<?php

namespace App\Policies;

use App\User;
use App\Expediente;
use Caffeinated\Shinobi\Facades\Shinobi;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Collection;

class ExpedientePolicy
{
  use HandlesAuthorization;

  /**
   * Un usuario administrador  puede visualizar el show de los expedientes de su organismo
   * El usuario solo puede visualizar el show de los expedientes de su organismo y sector
   * @param  \App\User  $user
   * @param  \App\Expediente  $expediente
   * @return mixed
   */
  public function show(User $user, Expediente $expediente, Collection $session)
  {
    // La consulta a que sector pertenece el usuario autenticado puede arrojar mas de uno (en general un sector)
    // la consulta es una coleccion que se transforma a array
    $arraySectores = $user->usersector->pluck('organismossectors_id')->toArray();

    //Obtenemos el id del sector en donde esta actualmente el expediente
    $idSectorExpediente = $expediente->expedientesestados->last()->rutasector->organismossectors->id;

    if ($expediente->expedientesestados->last()->users_id == null && $expediente->expedientetipo->publico !== 1 && $expediente->expedientetipo->historial_publico !== 1
        && !$session->contains('organismos.index.admin') && !$session->contains('expediente.index.all') && in_array($idSectorExpediente, $arraySectores) == false) {
    // if ($expediente->expedientesestados->last()->users_id == null) { // IF ORIGINAL
      return false;
    }

    if( ($expediente->expedientetipo->publico != 1 && $expediente->expedientetipo->historial_publico != 1)) {
      // 
      if ($session->contains('expediente.admin') || $session->contains('expediente.index.all')) {
        // Si es un administrador que quiere hacer show de expediente, solo se le restringe que sea de su organismo
        return $user->usersector->last()->organismosector->organismos_id == $expediente->organismos_id;
      } else {
        // CONDICION ELSE ORIGINAL
        // La consulta a que sector pertenece el usuario autenticado puede arrojar mas de uno (en general un sector)
        // la consulta es una coleccion que se transforma a array
        // $arraySectores = $user->usersector->pluck('organismossectors_id')->toArray();

        //Obtenemos el id del sector en donde esta actualmente el expediente
        // $idSectorExpediente = $expediente->expedientesestados->last()->rutasector->organismossectors->id;
        // CONDICION ELSE ORIGINAL

        // Si el id del sector del usuario autenticado coincide con el sector actual del expediente consultado, devolvemos true
        return in_array($idSectorExpediente, $arraySectores);
      }
    }

    return true;
  }

  /**
   * Solo el usuario que tiene asignado el expediente podrá agregar fojas.
   * 
   * @param  \App\User  $user
   * @return mixed
   */
  public function agregar_foja(User $user, Expediente $expediente)
  {
    if ($expediente->expedientesestados->last()->users_id === $user->id) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Solo el usuario que tiene asignado el expediente podrá generar el pase.
   */
  public function generar_pase(User $user, Expediente $expediente, Collection $session)
  {

    // Si es un un admin o superuser puede generar pase aunque no sea un expediente de su sector
    if ($session->contains(function ($permiso) {
      return $permiso == 'expediente.admin' || $permiso == 'expediente.superuser';
    })) {
      return true;
    } elseif ($expediente->expedientesestados->last()->users_id === $user->id) {
      return true;
    } else {
      return false;
    }
  }
}
