<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expedientedeposito extends Model
{
    protected $table = 'expedientedeposito';

    protected $fillable = [
      'depositos_id', //  VARCHAR (125)
      'expedientes_id', // INT
    ];

    public function expediente()
    {
      return $this->belongsTo(Expediente::class, 'expedientes_id');
    }

    public function deposito()
    {
      return $this->belongsTo(Deposito::class,'depositos_id', 'id');
    }
}
