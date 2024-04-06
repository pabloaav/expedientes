<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
  use SoftDeletes;

  protected $table = 'personas';
  protected $dates = ['deleted_at'];

  protected $guarded = [];

  // Una persona puede estar vinculada a muchos expedientes
  public function expedientes()
  {
    return $this->belongsToMany(Expediente::class);
  }
}
