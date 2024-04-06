<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expedienteestado extends Model
{
  protected $table = 'expendientesestados';

  protected $fillable = [
    'expendientesestado', 'observacion', 'expedientes_id', 'users_id', 'expedientesrutas_id'
  ];

  public function expediente()
  {
    return $this->belongsTo(Expediente::class, 'expedientes_id');
  }

  public function users()
  {
    return $this->belongsTo(User::class, 'users_id');
  }

  public function rutasector()
  {
      return $this->belongsTo(Expedientesruta::class, 'expedientesrutas_id');
  }


}
