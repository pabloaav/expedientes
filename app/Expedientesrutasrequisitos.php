<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expedientesrutasrequisitos extends Model
{
  protected $table = 'expedientesrequisitos';


  public function requisitoruta()
  {
    return $this->belongsTo(Expedientesruta::class, 'expedientesrutas_id');
  }

  public function expedientes()
  {
    return $this->hasMany(Expediente::class, 'expediente_expedientesrequisito', 'expedientesrequisito_id', 'expedientes_id')->withPivot('estado')->withTimestamps();
  }
}
