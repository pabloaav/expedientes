<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Expediente;
use App\Expedientestipo;
use App\Expedientesruta;
use App\Expedienteestado;
use App\Expedientepersona;
use App\Interfaces\DocumentosInterfaces;
use App\Http\Resources\TipoDocumentoEstado;
use App\Http\Resources\DocumentosCollection;
use App\Http\Resources\DocumentosNovedadesCollection;
use Illuminate\Support\Facades\DB;

class DocumentosRepository implements DocumentosInterfaces
{

  public function createDocumento(array $doc) /*crear nuevo documento*/
  {
    $expediente = new Expediente;
    $expediente->expediente = $doc['extracto'];
    $expediente->organismos_id = $doc['organismo'];
    $expediente->expedientestipos_id = $doc['tipo_documento'];
    $expediente->importancia = $doc['importancia'];
    $expediente->sector_inicio = $doc['sector'];
    $expediente->usuario_inicio = $doc['usuario'];
    //   $sectorUserExpediente = $doc['expediente'];
    //   $codigoDelOrganismo = $doc['expediente'];
    $expediente->expediente_num = $doc['num_documento'];
    $expediente->fecha_inicio = $doc['fecha_inicio'];
    $expediente->ref_siff = $doc['ref_siff'];
    $expediente->save();
    return $expediente;
  }

  public function createDocumentoEstado($documento_nuevo, $verificar_rutas, $estado_documento,$userAsignado = null) /*crear nuevo estado documento*/
  {
    if ($estado_documento !== null){
      $estado = "procesando";
      $textoLog = "Agrego foja al ". org_nombreDocumento() . " " .  $documento_nuevo->expediente_num . " a las " . Carbon::now()->toTimeString();
      
    }else{
      $estado = 'nuevo';
      $textoLog = "Creó el ". org_nombreDocumento() . " ".   $documento_nuevo->expediente_num . " a las " . Carbon::now()->toTimeString();
    }
    // verificar si es primera foja o si esta agregando fojas tipo imagen/pdf 
    $estadoexpediente = new Expedienteestado;
    $estadoexpediente->expedientes_id = $documento_nuevo->id;
    if ($userAsignado != null ) {
      $estadoexpediente->users_id = $userAsignado;
    } else {
      $estadoexpediente->users_id = null;
    }
    $estadoexpediente->expendientesestado = $estado;
    $estadoexpediente->expedientesrutas_id = $verificar_rutas;
    $estadoexpediente->observacion = $textoLog;
    $estadoexpediente->save();
    return $estadoexpediente;
  }

  public function getAllDocumentos($request)
  {
    $startDate = Carbon::createFromFormat('Y-m-d', $request['fecha_desde']);
    $endDate = Carbon::createFromFormat('Y-m-d', $request['fecha_hasta']);
    $documentos = Expediente::whereBetween('fecha_inicio', [$startDate, $endDate])->where('organismos_id',$request['organismo'])->get();
    return new DocumentosCollection($documentos);
  }

  public function getDocumentoById($documentoId)
  {
    return Expediente::findOrFail($documentoId);
  }

  public function vincularDocumentoPersona($persona, $num_doc, $año, $organismo) /*crear nuevo documento*/
  {
    $expediente_vincular = Expediente::where('expediente_num', $num_doc)->where('organismos_id',$organismo)->whereYear('created_at', $año)->first();
    if ($expediente_vincular == null) {
      return null;
    }else if (Expedientepersona::where('expediente_id', $expediente_vincular->id)->where('persona_id',$persona)->first() == null){
      $expediente = new Expedientepersona;
      $expediente->persona_id =$persona;
      $expediente->expediente_id = $expediente_vincular->id;
      $expediente->save();
      return $expediente;
    }else{
      return "persona-vinculada";
    }
   
  }


  public function getDocumento($request)
  {
    $expediente = Expediente::where('expediente_num', $request->num_doc)->where('organismos_id',$request->organismo)->whereYear('created_at', $request->año_doc)->firstOrFail();
    return new TipoDocumentoEstado($expediente);
    // return $estado;
  }

  public function getDocumentosNovedades($request)
  {
    $startDate = Carbon::createFromFormat('Y-m-d', $request['fecha_desde']);
    $endDate = Carbon::createFromFormat('Y-m-d', $request['fecha_hasta']);

    // se realiza una consulta sobre los documentos que pertenecen al organismo y que se encuentren dentro de la fecha seleccionada
    $queryEstado = Expediente::hydrate(DB::select('select * , (select max(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expendientesestado from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as ultimoestado from expedientes as exp where (exp.organismos_id = ?) and (exp.fecha_inicio between ? and ?) and (exp.read_at = 0) order by exp.id desc', [$request['organismo'], $startDate, $endDate]));

    // si se aplicó algun filtro para el estado, se colocan los mismos en una coleccion para ser devuelta al usuario
    if (!is_null($request['filtro'])) {
      $filtro = strtolower($request['filtro']);
      $expedientes = collect();

      foreach ($queryEstado as $expediente) {
        if ($expediente->ultimoestado == $filtro) {
          $expedientes->push($expediente);
        }
      }
    } else {
      $expedientes = $queryEstado;
    }

    return new DocumentosNovedadesCollection($expedientes);
  }

  // Esta funcion permite consultar los expedientes pasados a traves del JSON para registrar la marca read_at en cada uno
  public function marcarDocumentosLeidos($request)
  {
    $expedientes_id = [];
    $organismo = $request['organismo'];
    $datos_exp = $request['content'];

    foreach ($datos_exp as $expediente) {
      // se van agregando en el array los id de los expedientes pasados por el JSON
      array_push($expedientes_id, $expediente['identificador']);
    }

    // se consultan los expedientes que cumplen 2 condiciones: que pertenezcan al organismo ingresó el usuario y que el id del expediente esté contenido en el array de ids de los expedientes cargado anteriormente
    $verificar_exp = Expediente::hydrate(DB::select('select * from expedientes as exp where (exp.organismos_id = ?) and (exp.id in ('. implode(', ', $expedientes_id) .')) order by exp.id desc', [$organismo]));

    // si la cantidad de elementos del array de ids de expediente es igual a la cantidad de expedientes resultantes de la consulta a la base de datos, significa que todos los consultados pertenecen al organismo ingresado y se procede a agregar la marca en el campo read_at
    if ($verificar_exp->count() === count($expedientes_id)) {

      foreach ($verificar_exp as $expediente) {
        $expediente->read_at = 1;

        $expediente->update();
      }
    }
    else {
        return "error";
    }

    return "leidos";
  }

  public function getDocumentoSectorNow($data)
  {
    // se obtienen los documentos que coincidan con los parametros enviados de: organismo_id y n° doc
    $query = Expediente::join('expedientestipos', 'expedientes.expedientestipos_id', '=', 'expedientestipos.id')
                            ->select('expedientes.id', 'expedientes.expediente_num as num_documento', 'expedientes.expediente as extracto', 'expedientestipos.expedientestipo as tipo_documento', 'expedientes.created_at as fecha_creacion')
                            ->where([
                                ['expedientes.organismos_id', '=', $data['organismo']],
                                ['expedientes.expediente_num', '=', $data['num_doc']]
                              ])
                            ->whereNull('expedientes.deleted_at');

    if ($query->count() > 0)
    {
      
      // Filtro de año
      if (!is_null($data['año_doc']))
      {
        $query->whereYear('expedientes.created_at', $data['año_doc']);
      }

      // Filtro de tipo de documento
      if (!is_null($data['tipo_doc']))
      {
        $query->where('expedientestipos.expedientestipo', '=', $data['tipo_doc']);
      }

      $expedientes = $query->get(); // se convierte el resultado de $query en una coleccion

      if (count($expedientes) > 0)
      {
        foreach($expedientes as $expediente)
        {
          // por cada resultado, se obtiene su ultimo estado y el ultimo estado "pasado" del mismo
          $estado_actual = Expedienteestado::where('expedientes_id', $expediente->id)->get()->last();
          $ingreso_date = Expedienteestado::where('expedientes_id', $expediente->id)
                                          ->whereNotNull('pasado_por')
                                          ->get()
                                          ->last();
    
          $expediente->estado = $estado_actual->expendientesestado;
          $expediente->sector_actual = $estado_actual->rutasector->sector->organismossector;
          // $expediente->usuario_actual = (!is_null($estado_actual->users) ? [['usuario' => $estado_actual->users->email, 'nombre' => $estado_actual->users->name]] : []);
          $expediente->usuario_actual = (!is_null($estado_actual->users) ? true : false);
          $expediente->fecha_ingreso_sector = Carbon::parse((!is_null($ingreso_date) ? $ingreso_date->created_at : $expediente->fecha_creacion))->format('Y-m-d H:m:s'); // si el exp no tiene ningun estado "pasado", se toma la fecha de creación del exp
        }
      }
      else
      {
        $expedientes = "No existen coincidencias de expedientes";  
      }
    }
    else
    {
      $expedientes = "No existen coincidencias de expedientes";
    }
    
    return $expedientes;
  }
}
