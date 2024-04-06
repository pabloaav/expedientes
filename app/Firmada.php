<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Firmada extends Model
{
  protected $table = 'firmadas';


  public function foja()
  {
    return $this->belongsTo(Foja::class);
  }
}
