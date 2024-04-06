<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    protected $table = 'localidads';

    public function localidades()
  {
    return $this->belongsTo(Organismo::class, 'localidads_id' );
  }

}
