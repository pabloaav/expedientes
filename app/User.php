<?php

namespace App;

use Auth;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Caffeinated\Shinobi\Traits\ShinobiTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
  use Notifiable, ShinobiTrait;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'email', 'password',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
  ];

  public function getJWTIdentifier()
  {
      return $this->getKey();
  }
 
  public function getJWTCustomClaims()
  {
      return [
          'organismo'       => $this->userorganismo()->first()->organismos_id,
          'usuario'      => $this->id,
      ];
  }



  public function agencias()
  {
    return $this->belongsTo('App\Agencia');
  }

  // datos del organismo segun usuario
  public function userorganismo()
  {
    return $this->hasMany(Organismosuser::class, 'users_id');
  }

  // datos del sector de la organizacion segun usuario
  public function usersector()
  {
    return $this->hasMany(Organismossectorsuser::class, 'users_id');
  }

  public function notificaciones()
  {
    return $this->hasMany(Expedienteestado::class, 'users_id');
  }

  /**
   * Retorna el organismo al que pertenece el ususario
   * SI el usuario tiene mas de un organismo considera el primero
   *
   * @return void
   */
  public function organismo()
  {
    return  $this->userorganismo()->first()->organismos();
  }

}
