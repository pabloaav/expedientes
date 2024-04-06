<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organismossectorsuser extends Model
{

  protected $table = 'organismossectorsusers';

  public function organismos()
  {
    return $this->belongsTo('App\Organismossector');
  }

  public function users()
  {
    return $this->belongsTo('App\User');
  }

  public function organismosector()
  {
    return $this->belongsTo(Organismossector::class, 'organismossectors_id');
  }
}
