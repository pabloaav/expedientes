<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Preferencies extends Model
{
    protected $table = 'preferencies';

    public function users()
        {
            return $this->belongsTo('App\User');
        }
}


