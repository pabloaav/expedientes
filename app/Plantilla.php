<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plantilla extends Model
{
    protected $table = 'plantillas';

    protected $fillable = [
        'plantilla', //  VARCHAR (125)
        'activo', // INT
        'contenido', // INT    
      ];

      public function organismosector()
      {
        return $this->belongsTo(Organismossector::class, 'organismossectors_id');
      }
}
