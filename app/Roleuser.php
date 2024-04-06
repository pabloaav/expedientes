<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roleuser extends Model
{

    protected $table = 'role_user';

    public function users()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function roles()
    {
        return $this->belongsTo('Caffeinated\Shinobi\Models\Role','role_id');
    }


}
