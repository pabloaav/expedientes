<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organismo extends Model
{

  protected $table = 'organismos';



  public function expedientes()
  {
    return $this->hasMany(Expediente::class, 'organismos_id');
  }

  public function sectores()
  {
    return $this->hasMany(Organismossector::class, 'organismos_id');
  }

  public function expedientestipos()
  {
    return $this->hasMany(Expedientestipo::class, 'organismos_id');
  }

  public function users()
  {
    return $this->belongsToMany(User::class, 'organismosusers', 'users_id');
  }

  public function organismolocalidad()
  {
    return $this->belongsTo(Localidad::class, 'localidads_id');
  }

  public function organismosetiquetas()
  {
    return $this->hasMany(Organismosetiqueta::class, 'organismos_id');
  }

  public function configuraciones()
  {
    return $this->hasOne(Configuracion::class, 'organismos_id');
  }
}
