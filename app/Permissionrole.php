<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permissionrole extends Model
{

    protected $table = 'permission_role';

    public function roles()
    {
        return $this->belongsTo('Caffeinated\Shinobi\Models\Role','role_id');
    }

}
