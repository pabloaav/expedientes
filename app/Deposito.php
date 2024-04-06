<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deposito extends Model
{
    protected $table = 'depositos';

    public function expediente_deposito()
    {
      return $this->hasMany(Expedientedeposito::class, 'depositos_id');
    }

}
