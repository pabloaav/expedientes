<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpedientetipoPersona extends Model
{

  protected $table = 'expedientestipos_personas';


  public function persona()
  {
    return $this->belongsTo(Persona::class, 'personas_id');
  }

  public function expedientetipo()
  {
    return $this->belongsTo(Expedientestipo::class, 'expedientestipos_id');
  }

}
