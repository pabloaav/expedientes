<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Soporte extends Model
{

    protected $table = 'soportes';

    public function users()
        {
            return $this->belongsTo('App\User');
        }



}
