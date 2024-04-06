<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Foja extends Model
{
  use SoftDeletes;

  protected $table = 'fojas';
  protected $dates = ['deleted_at'];

  protected $fillable = [
    'expedientes_id', // INT
    'foja', // INT El numero unico de la foja compuesto por el expediente_id mas el numero simple de la foja
    'tipofoja', // ENUM("texto","imagen")
    'texto', // TEXT
    'file', // VARCHAR
    'numero', // INT El numero simple de la foja
    'nombre', // VARCHAR 
    'path',
    'descripcion', // TEXT
    'hashPrevio',
    'hash',
    'users_id',
    'organismossectors_id',
    'created_at',
    'updated_at',
    'deleted_at'
  ];


  public function expediente()
  {
    return $this->belongsTo(Expediente::class, 'expedientes_id');
  }

  public function organismosetiquetas()
  {
    return $this->belongsToMany(Organismosetiqueta::class);
  }

  public function firmada()
  {
    return $this->hasOne(Firmada::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'users_id');
  }

  public function organismossectors() {
    return $this->belongsTo(Organismossector::class, 'organismossectors_id');
  }

  public function signedByUser($user_id)
  {

    if ($this->firmada) {
      return $this->firmada->where('user_id', $user_id)->exists();
    }
    return false;
  }

  /**
   * Devuelve una coleccion con objetos de tipo Model User que son los firmantes de una foja
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function signers()
  {
    $data = User::select('users.name', 'users.email', 'firmadas.cuil', 'firmadas.fecha_firma')
      ->join('firmadas', 'users.id', '=', 'firmadas.user_id')
      ->where('foja_id', $this->id)
      ->get();

    return $data;
  }

  /**
   * Retorna true si la foja firmada tiene todos los campos completos
   *
   * @return bool
   */
  public function isFirmada()
  {
    $firmada = false;
    // es necesario porque puede o no tener foja firmada. Si no tiene es null
    $unafojaFirmada = $this->firmada;
    if ($unafojaFirmada != null && $unafojaFirmada->estado == 'FIRMADA') {
      $firmada = true;
    }
    return $firmada;
  }

  /**
   * Retorna true si la foja firmada tiene un estado pendiente
   * El estado pendiente es aquel en que la foja se pidio firmar y por algun motivo no se firmó
   * @return bool
   */
  public function isPendiente()
  {
    $pendiente = false;
    // es necesario porque puede o no tener foja firmada. Si no tiene es null
    $unafojaPendiente = $this->firmada;

    if ($unafojaPendiente != null && $unafojaPendiente->estado == 'pendiente') {
      $pendiente = true;
    }
    return $pendiente;
  }
}
