<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Soportesrespuesta extends Model
{

    protected $table = 'soportesrespuestas';

    public function users()
        {
            return $this->belongsTo('App\User');
        }

    public function soportes()
        {
            return $this->belongsTo('App\Soporte');
        }



}
