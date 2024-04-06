<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expedientesadjunto extends Model
{
    protected $table = 'expedientesadjuntos';

    protected $fillable = [
        'expedientes_id',
        'fojas_id',
        'path'
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'expedientes_id');
    }

    public function foja()
    {
        // el 2do argumento es el id de la tabla fojas, y el 3er argumento es el id de la foja que se almacena en la tabla expedientesadjuntos
        return $this->hasOne(Foja::class, 'id', 'fojas_id');
    }

    public function detalles()
    {
        return $this->hasMany(Detallesadjunto::class, 'expedientesadjuntos_id');
    }
}
