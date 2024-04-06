<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Organismossector extends Model
{
    use Notifiable;

    protected $table = 'organismossectors';

    public function organismos()
    {
        return $this->belongsTo('App\Organismo');
    }

    public function users()
    {
        return $this->belongsTo('App\User');
    }

    //Recursividad Sectores
    public function parentSector()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function childrenSectors()
    {
        // return $this->hasMany(self::class);
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parentsSectorsRec()
    {
        return $this->parentSector()->with('parentsSectorsRec');
    }

    public function childrenSectorsRec()
    {
        return $this->childrenSectors()->with('childrenSectorsRec');
    }


   

}
