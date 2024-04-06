<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expedientesruta extends Model
{

    protected $table = 'expedientesrutas';

    public function expedientestipos()
    {
        return $this->belongsTo('App\Expedientestipo');
    }

    public function organismossectors()
    {
        return $this->belongsTo('App\Organismossector');
    }
    
    public function sector()
    {
        return $this->belongsTo(Organismossector::class,'organismossectors_id','id');
    }

    public function requisitos()
    {
        return $this->hasMany(Expedientesrutasrequisitos::class,'expedientesrutas_id','id');
    }

}
