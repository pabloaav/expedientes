<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expedientestipo extends Model
{

  protected $table = 'expedientestipos';

  protected $fillable = [
    'financiero', //  VARCHAR (125)
  ];


  public function organismo()
  {
    return $this->belongsTo(Organismo::class, 'organismos_id');
  }

  public function expedientes()
  {
    return $this->hasMany(Expediente::class, 'expedientestipos_id');
  }

   
  public function rutas()
  {
    return $this->hasMany(Expedientesruta::class, 'expedientestipos_id');
  }

}
