<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organismosetiqueta extends Model
{
  use SoftDeletes;

  protected $table = 'organismosetiquetas';
  protected $dates = ['deleted_at'];

  public function organismo()
  {
    return $this->belongsTo('App\Organismo');
  }

  public function expedientes()
  {
    return $this->belongsToMany(Expediente::class);
  }

  public function fojas()
  {
    return $this->belongsToMany(Foja::class);
  }

  public function organismossector()
  {
    return $this->belongsTo(Organismossector::class, 'organismossectors_id');
  }
}
