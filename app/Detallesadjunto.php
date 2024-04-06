<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detallesadjunto extends Model
{
    protected $table = 'detallesadjuntos';

    public $timestamps = false;

    public function adjunto()
    {
        return $this->belongsTo(Expedientesadjunto::class, 'expedientesadjuntos_id');
    }
}
