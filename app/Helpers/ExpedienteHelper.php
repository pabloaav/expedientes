<?php

use Illuminate\Support\Facades\DB;
use App\Expediente;
use App\Organismo;
use App\Configuracion;
use App\Organismossector;
use Carbon\Carbon;
use App\Expedienteestado;

if (!function_exists('getExpedienteName')) {

  /**
   * getExpedienteName
   *
   * @param  mixed $expediente
   * @return String El nombre del expediente segun las pautas establecidas
   */
  function getExpedienteName(Expediente $expediente)
  {
    $codigoOrganismo = $expediente->organismos->codigo;
    //$codigoSector = Organismossector::find($expediente->sector_inicio)->codigo;
    $numeroExpediente = str_pad($expediente->expediente_num, 5, "0", STR_PAD_LEFT);
    $anioInicioExpediente = date('Y', strtotime($expediente->fecha_inicio));

    if (is_null($expediente->extension)) {
      return $codigoOrganismo . '-' . $numeroExpediente . '-' . $anioInicioExpediente;  
    }
    else {
      return substr($codigoOrganismo, 0, -2) . $expediente->extension . '-' . $numeroExpediente . '-' . $anioInicioExpediente;
    }

    // return $codigoOrganismo . '-' . $numeroExpediente . '-' . $anioInicioExpediente;
  }
}

// if (!function_exists('getNextExpedienteNumber')) {

//   /**
//    * getNextExpedienteNumber
//    *
//    * @param  mixed $organismo_id El ide del organismo que crea un nuevo expediente
//    * @return Integer El numero del proximo expediente
//    */
//   function getNextExpedienteNumber($organismo_id)
//   {
//     $proximoNumeroExpediente = 1;
//     $organismo = Organismo::find($organismo_id);
//     if (count($organismo->expedientes) > 0) {
//       // $ultimoNumeroExpediente = $organismo->expedientes->last()->expediente_num;
//       // $org_expedientes = $organismo->expedientes;
//       $org_expedientes = Expediente::where('organismos_id', $organismo_id)
//                                     ->whereNull('deleted_at')
//                                     ->get();

//       $ultimoNumeroExpediente = 0;
//       foreach ($org_expedientes as $expediente) {
//         // se recorre la lista de expedientes del organismo y se guarda en la variable $ultimoNumeroExpediente el numero de expediente mayor y que sea menor a 11 digitos (en caso de que el ultimo numero guardado sea un CUIL)
//         if ($expediente->expediente_num > $ultimoNumeroExpediente && strlen($expediente->expediente_num) < 11) {
//           $ultimoNumeroExpediente = $expediente->expediente_num;
//         }
//       }

//       // Comparar si es un nuevo año para iniciar el conteo del numero del expediente
//       $anioActual = today()->year;
//       $anioUltimoExpediente = Carbon::parse($organismo->expedientes->last()->fecha_inicio)->year;
//       // Si estamos dentro del mismo año, se devuelve el numero siguiente al ultimo numero de expediente
//       if ($anioActual == $anioUltimoExpediente) {
//         $proximoNumeroExpediente = $ultimoNumeroExpediente + 1;
//       }
//     }

//     // Sino, es que el año actual es mayor (no puede ser menor), y el expediente numero es 1
//     return $proximoNumeroExpediente;
//   }
// }

if (!function_exists('getNextExpedienteNumber')) {

  /**
   * getNextExpedienteNumber
   *
   * @param  mixed $organismo_id El ide del organismo que crea un nuevo expediente
   * @return Integer El numero del proximo expediente
   */
  function getNextExpedienteNumber($organismo_id)
  {
    $proximoNumeroExpediente = 1;
    $org_expedientes = Expediente::where('organismos_id',$organismo_id)
                                  ->whereYear('fecha_inicio', '=', now()->year)
                                  ->whereNull('deleted_at')
                                  ->get();

    $ultimoNumeroExpediente = 0;
    if ($org_expedientes->count() > 0)
    {
      foreach ($org_expedientes as $expediente) {
        // se recorre la lista de expedientes del organismo y se guarda en la variable $ultimoNumeroExpediente el numero de expediente mayor y que sea menor a 11 digitos (en caso de que el ultimo numero guardado sea un CUIL)
        if ($expediente->expediente_num > $ultimoNumeroExpediente) {
          $ultimoNumeroExpediente = $expediente->expediente_num;
        }
      }

      $proximoNumeroExpediente = $ultimoNumeroExpediente + 1;
    }

    return $proximoNumeroExpediente;
  }
}

if (!function_exists('getNextExpedienteNumberYear')) {

  /**
   * getNextExpedienteNumberYear
   *
   * @param  mixed $organismo_id El id del organismo que crea un nuevo expediente
   * @return Integer El numero del proximo expediente
   */
  function getNextExpedienteNumberYear($organismo_id)
  {
    $proximoNumeroExpediente = 1;
    $organismo = Organismo::find($organismo_id);
    if (count($organismo->expedientes) > 0) {
      $ultimoNumeroExpediente = $organismo->expedientes->last()->expediente_num;
      // Comparar si es un nuevo año para iniciar el conteo del numero del expediente
      $anioActual = today()->year;
      $anioUltimoExpediente = Carbon::parse($organismo->expedientes->last()->fecha_inicio)->year;

      $expedientes = DB::table('expedientes')
              ->where('organismos_id',$organismo->id)
              ->whereYear('fecha_inicio', '=', $anioActual)
              ->get();

      if (count($expedientes) > 0) {
        $max = 0;
        foreach ($expedientes as $indice => $exp) {
          if ($exp->expediente_num > $max && strlen($exp->expediente_num) < 11) {
            $max = $exp->expediente_num;
          }
        }

      }
        
      $proximoNumeroExpediente = $max + 1;
    }

   
    return  $proximoNumeroExpediente;
    
    
  }

  if (!function_exists('control_nombre')) {

    function control_nombre($cadena)
    {
      $cadena_control = preg_replace('/[^A-Za-z0-9.]/', '', $cadena);

      return $cadena_control;
    }
  }

  if (!function_exists('org_nombreDocumento')) {

    function org_nombreDocumento()
    {
      $organismo = Organismo::find(Auth::user()->organismo->id);
      $nombreDocumento = $organismo->configuraciones->nomenclatura ;

      $nombreNoNull = ($organismo->configuraciones->nomenclatura != null ? $organismo->configuraciones->nomenclatura : 'documento');

      return $nombreNoNull;
    }
  }

  if (!function_exists('historial_doc')) {

    function historial_doc($id_expediente,$text_historial,$estado = null,$user_id=null)
    {
      $expediente = Expediente::find($id_expediente);
      
      $estadoExpAnterior = (Expedienteestado::where('expedientes_id',$expediente->id))->get()->last();
      $estadoexpediente = new Expedienteestado;
      $estadoexpediente->expedientes_id = $expediente->id;
     
        if ($estado != null ) {
          $estadoexpediente->users_id= $user_id;
        } else {
          $estadoexpediente->users_id =  $estadoExpAnterior->users_id;
        }

        if ($estado != null) {
          $estadoexpediente->expendientesestado = $estado;
        }else {

          if ($estadoExpAnterior->expendientesestado == 'nuevo' || $estadoExpAnterior->expendientesestado == 'pasado' || $estadoExpAnterior->expendientesestado == 'devuelto') {
            $estadoexpediente->expendientesestado = "procesando";
          } else if ($estadoExpAnterior->expendientesestado == 'anulado' || $estadoExpAnterior->expendientesestado == 'fusionado' || $estadoExpAnterior->expendientesestado == 'archivado'  ){
            $estadoexpediente->expendientesestado = $estadoExpAnterior->expendientesestado;
          } else {
            $estadoexpediente->expendientesestado = "procesando";
          }
        }
      

      $estadoexpediente->expedientesrutas_id = $estadoExpAnterior->expedientesrutas_id;
      $estadoexpediente->observacion = $text_historial;
      $estadoexpediente->ruta_devolver = $estadoExpAnterior->ruta_devolver;
      $estadoexpediente->save();

      return true;
    }
  }
}
