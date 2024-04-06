<?php

namespace App\Repositories;

use App\Expedientesruta;
use App\Expedientestipo;
use App\Http\Resources\TipoDocumento;
use App\Http\Resources\TiposDocumentos;
use App\Organismossectorsuser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Interfaces\TiposDocumentosInterfaces;
use App\Http\Resources\TiposDocumentosCollection;

class TiposDocumentosRepository implements TiposDocumentosInterfaces 
{
    public function getTiposDocumentos($usuario) 
    {
             $user = Organismossectorsuser::where('users_id',$usuario)->get();
             /*OBTENER LOS SECTORES DEL USUARIO*/
             foreach ($user as $query) {
                $data[] = [
                  'id' => $query->organismossectors_id,
                ];
              }

             /*CONSULTAR EN QUE RUTAS ESTAN ESOS SECTORES */
              $sectores = [];
              foreach ($data as $query) {
                $rutas = Expedientesruta::where('organismossectors_id',$query)->get();

                foreach ($rutas as $res){
                  array_push($sectores,$res);
                }
              }
              /* ELIMINAR ELEMENTOS DUPLICADOS DEL ARRAY */
              $resultado = array_unique($sectores);
              $tipos_documentos = [];
              foreach ($resultado as $tipos){
                array_push($tipos_documentos, $tipos);
              }

              /*BUSCAR TIPOS DE DOCUMENTOS */
              $response = [];
              foreach ($tipos_documentos as $i) {
               $tipos_doc = Expedientestipo::where('id',$i->expedientestipos_id)->where('activo', 1)->first();
               array_push($response,$tipos_doc);
              }
             
               /* ELIMINAR ELEMENTOS DUPLICADOS DEL ARRAY */
              $resultado1 = array_unique($response);
              $result = [];
              foreach ($resultado1 as $i) {
               array_push($result,$i);
              }

              /* FILTRAR ELEMENTOS VACIOS DEL ARRAY */
              $var =  array_filter($result);
              
              
              /*RESPONSE TIPOS DE DOCUMENTOS QUE PUEDE CREAR EL USUARIO SEGUN SU SECTOR */
              $dataresul= [];
              foreach ($var as $i) {
                $dataresul[] = [
                  'num_tipo_doc' => $i->id,
                  'codigo'  =>   $i->codigo,
                  'nombre_tipo_documento' => $i->expedientestipo,
                ];
               }

              return $dataresul;
   }

   public function getTiposDocumentosSector($usuario,$sector) 
    {
             $sectoresuser = Organismossectorsuser::where('users_id',$usuario)->get();
             /*OBTENER LOS SECTORES DEL USUARIO*/

             foreach ($sectoresuser as $query) {
                $data[] = [
                  'id' => $query->organismossectors_id,
                ];
              }
             /*CONSULTAR EN QUE RUTAS ESTAN ESOS SECTORES */
              $sectores = [];
              foreach ($data as $query) {
                $rutas = Expedientesruta::where('organismossectors_id',$query)->get();

                foreach ($rutas as $res){
                  if ($res->organismossectors_id == $sector) {
                    array_push($sectores,$res);
                  }
                  
                }
              }
              /* ELIMINAR ELEMENTOS DUPLICADOS DEL ARRAY */
              $resultado = array_unique($sectores);
              $sector_ruta = [];
              foreach ($resultado as $sector){
                array_push($sector_ruta, $sector);
              }              
              /*BUSCAR TIPOS DE DOCUMENTOS */
              $response = [];
              foreach ($sector_ruta as $i) {
               $tipos_doc = Expedientestipo::where('id',$i->expedientestipos_id)->where('activo', 1)->first();
               array_push($response,$tipos_doc);
              }
             
               /* ELIMINAR ELEMENTOS DUPLICADOS DEL ARRAY */
              $resultado1 = array_unique($response);
              $result = [];
              foreach ($resultado1 as $i) {
               array_push($result,$i);
              }

              /* FILTRAR ELEMENTOS VACIOS DEL ARRAY */
              $var =  array_filter($result);
              
              /*RESPONSE TIPOS DE DOCUMENTOS QUE PUEDE CREAR EL USUARIO SEGUN SU SECTOR */
              $dataresul = [];
              foreach ($var as $i) {
                $dataresul[] = [
                  'num_tipo_doc' => $i->id,
                  'codigo'  =>   $i->codigo,
                  'nombre_tipo_documento' => $i->expedientestipo,
                ];
               }

              return $dataresul;
   }

   public function getTipoDocumento($usuario,$codigo_tipo_documento, $organismo) 
   {
    $user = Organismossectorsuser::where('users_id',$usuario)->get();
    /*OBTENER LOS SECTORES DEL USUARIO*/
    $data = [];
    foreach ($user as $query) {
       array_push($data,$query->organismossectors_id);
     }
    // $a = [12,13];
    $tipo_documento = Expedientestipo::where('organismos_id',$organismo)->where('codigo',$codigo_tipo_documento)->firstOrFail();
    $rutas = $tipo_documento->load('rutas');

    $response = [];
    foreach ($rutas->rutas as $i) {
     array_push($response,$i->organismossectors_id);
    }

    $coincidencias = array_intersect($data, $response);

    if ($coincidencias == []){
      return null;
    }else{
     return new TipoDocumento($rutas);
    }
   }

}