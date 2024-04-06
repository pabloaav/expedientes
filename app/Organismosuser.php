<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organismosuser extends Model
{

    protected $table = 'organismosusers';

    protected $fillable = [
        'activo',
        // Claves Foraneas
        'users_id', // INT
        'organismos_id', // INT
      ];

    public function organismos()
    {
        return $this->belongsTo(Organismo::class,'organismos_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo('App\User');
    }


}
