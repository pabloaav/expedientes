<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organismostiposvinculo extends Model
{
    protected $table = 'organismostiposvinculo';

    protected $fillable = [
        'vinculo', 'activo'
    ];

    public function organismo() {
        return $this->belongsTo(Organismo::class, 'organismos_id');
    }
}
