<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{

    protected $table = 'configuracions';




    public static function ano()
      {

      $c = Configuracion::where('configuracion', 'ano_documentos')->first();

      if (!$c) {
        $c = new Configuracion;
        $c->configuracion = 'ano_documentos';
        $c->numero = '2019';
        $c->save();
      }
      return $c->numero;
    }

    public function organismo()
    {
      return $this->belongsTo(Organismo::class, 'organismos_id');
    }


}
