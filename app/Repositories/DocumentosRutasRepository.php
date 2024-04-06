<?php

namespace App\Repositories;

use App\Interfaces\DocumentosRutasInterfaces;
use App\Expedientesruta;

class DocumentosRutasRepository implements DocumentosRutasInterfaces 
{
    public function verificarRutasDocumentos($tipo_documento,$sector) 
    {
            // verificar si la ruta existe  
          $ruta_sector = Expedientesruta::where("organismossectors_id", $sector)->where("expedientestipos_id", $tipo_documento)->first();
          if ($ruta_sector == null) {  /*, si no existe crea la nueva ruta*/
            $ruta_dinamica = new Expedientesruta;
            $ruta_dinamica->expedientestipos_id =  $tipo_documento;
            $ruta_dinamica->organismossectors_id  = $sector;
            $ruta_dinamica->save();
            //  vuelve a buscar coincidencias dentro de las rutas
            $ruta_sector = Expedientesruta::where("organismossectors_id", $sector)->where("expedientestipos_id", $tipo_documento)->first();
          }
          //retorna el id de la ruta que se inserta en expedienteestado
          return $ruta_sector->id;
    }


    public function verificarRutas($sector,$tipo_documento) 
    {
            // verificar si la ruta existe  
          $ruta_sector = Expedientesruta::where("organismossectors_id", $sector)->where("expedientestipos_id", $tipo_documento)->first();
          if ($ruta_sector == null){
            return 0;
          }else{
            return 1;
          }
         
    }


}