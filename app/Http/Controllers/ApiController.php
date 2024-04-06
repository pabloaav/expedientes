<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use App\Organismosuser;
use Illuminate\Support\Facades\DB;
use App\Interfaces\FojasInterfaces;
use App\Interfaces\LoginInterfaces;
use App\Traits\AutorizacionConsultar;
use App\Traits\AutorizacionCrearFoja;
use App\Interfaces\PersonasInterfaces;
use App\Interfaces\SectoresInterfaces;
use App\Interfaces\UsuariosInterfaces;
use App\Interfaces\DocumentosInterfaces;
use App\Traits\VerificarNumeroDocumento;
use App\Interfaces\PersonaLocalInterfaces;
use App\Interfaces\PersonasLocalInterfaces;
use App\Traits\AutorizacionSectorOrganismo;
use App\Http\Requests\SectoresUsuarioRequest;
use App\Http\Requests\TiposDocumentosRequest;
use App\Http\Requests\TiposDocumentosSectorRequest;
use App\Http\Requests\ValidarCrearFojaImagen;
use App\Interfaces\DocumentosRutasInterfaces;
use App\Interfaces\TiposDocumentosInterfaces;
use App\Http\Requests\ValidateCrearFojaRequest;
use App\Http\Requests\DocumentosOrganimosRequest;
use App\Http\Requests\ValidarTipoDocumentoRequest;
use App\Http\Requests\ValidateCrearFojaPdfRequest;
use App\Http\Requests\ValidarEstadoDocumentoRequest;
use App\Http\Requests\ValidateCrearDocumentoRequest;
use App\Http\Requests\ValidarDocumentoPersonasRequest;
use App\Http\Requests\MarcarDocumentosLeidosRequest;
use App\Http\Requests\DocumentoSectorActualRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

class ApiController extends Controller
{
  
    use VerificarNumeroDocumento, AutorizacionCrearFoja, AutorizacionConsultar, AutorizacionSectorOrganismo;

    public function __construct(DocumentosRutasInterfaces $documentosRutasInterfaces,DocumentosInterfaces $documentosInterfaces,FojasInterfaces $fojasInterfaces,SectoresInterfaces $sectoresInterfaces,TiposDocumentosInterfaces $tiposDocumentosInterfaces,LoginInterfaces $loginInterfaces,  UsuariosInterfaces $usuariosInterfaces , PersonasInterfaces $personasInterfaces,  PersonasLocalInterfaces $personasLocalInterfaces) 
    {
        $this->documentosRutasInterfaces = $documentosRutasInterfaces;
        $this->documentosInterfaces = $documentosInterfaces;
        $this->fojasInterfaces = $fojasInterfaces;
        $this->sectoresInterfaces = $sectoresInterfaces;
        $this->tiposDocumentosInterfaces = $tiposDocumentosInterfaces;
        $this->loginInterfaces = $loginInterfaces;
        $this->documentoRepository = $documentosInterfaces;
        $this->usuariosInterfaces = $usuariosInterfaces;
        $this->personasInterfaces = $personasInterfaces;
        $this->personasLocalInterfaces = $personasLocalInterfaces;
    } // El código inyecta una LoginRepositoryInterfaces a través del constructor y usa los métodos del objeto relevante en cada método del controlador.


    public function documentos(DocumentosOrganimosRequest $request) /*listar los tipos de documentos de un organimos*/
    { 
        try {  
          $token = $this->loginInterfaces->decodetoken();
          $organismo = $request->organismo; 
          $autorizacion =  $this->loginInterfaces->autorizacion($organismo, $token['organismo']);
          if ($autorizacion == true){
            $documentos = $this->documentosInterfaces->getAllDocumentos($request);
            return response()->json($documentos, 200);
          }else{
            return response()->json(['error' => 'No tiene permisos para realizar operaciones en el organismo']);
          }
        } catch (Exception $exception) {
          if ($exception instanceof ModelNotFoundException) {
            return response()->json(['error' => 'No se encontraron registros, verifique parametros enviados']);
         }
         else{
          return response()->json(['errors' => $exception->getMessage()], 500);
         }
        }
      }


      public function estadoDocumento(ValidarEstadoDocumentoRequest $request) /*listar los tipos de documentos de un organimos*/
      { 
          try {  
            $token = $this->loginInterfaces->decodetoken();
            $organismo = $request->organismo; 
            $autorizacion =  $this->loginInterfaces->autorizacion($organismo, $token['organismo']);
            if ($autorizacion == true){
              $documentos = $this->documentosInterfaces->getDocumento($request);
              return response()->json($documentos, 200);
            }else{
              return response()->json(['error' => 'No tiene permisos para realizar operaciones en el organismo']);
            }
          } catch (Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
              return response()->json(['error' => 'No se encontraron registros, verifique parametros enviados']);
           }
           else{
            return response()->json(['errors' => $exception->getMessage()], 500);
           }
          }
        }

      


    public function crearDocumento(ValidateCrearDocumentoRequest $request) 
    {
        DB::beginTransaction(); /*iniciar transaccion */
        try {    
          /*autorizacion para realizar operaciones sobre este organismo */
          $token = $this->loginInterfaces->decodetoken();
          $organismo = $request->organismo; 
          $autorizacion =  $this->loginInterfaces->autorizacion($organismo, $token['organismo']);
          $autorizacion_consultar = $this->autorizacionConsultar($request->usuario, $token['usuario']);
          /* validar que el sector ingresado sea del organismo */
          $sectores_usuario = $this->sectoresInterfaces->sectoresOrganismo($request->sector, $token['usuario']);

          $autorizacion_sector_organismo= $this->AutorizacionSectorOrganismo($sectores_usuario, $token['organismo']);

          $autorizacion_sector_tipodocumento = $this->documentosRutasInterfaces->verificarRutas($request->sector,$request->tipo_documento);

          if ($autorizacion_sector_tipodocumento == 0){
            return response()->json(['error' => 'El sector y el tipo de documento no pertenecen al organismo o no coinciden con alguna ruta defenida']);
          }

          if ($autorizacion == true  && $autorizacion_consultar == true && $autorizacion_sector_organismo == true){
          // Guardar los datos de un documento
          $doc = $request->all();          
          
          // Control Opcionales 
          $sigDocNro = getNextExpedienteNumber($organismo);
          $hoy = Carbon::now()->format('Y/m/d H:i:s');
          
          $doc['num_documento'] =  ( $request->num_documento != null ) ?  $request->num_documento : ($sigDocNro);
          $doc['fecha_inicio'] = ( $request->fecha_inicio != null ) ? $request->fecha_inicio : $hoy;

          $datos_entrada = $this->verificarNumeroDocumento($doc);
          if($datos_entrada['success'] == false){
            return response()->json(['errors' => $datos_entrada['error']], 400);
          }
          else{
          $documento_nuevo = $this->documentosInterfaces->createDocumento($doc);
          $verificar_rutas = $this->documentosRutasInterfaces->verificarRutasDocumentos($documento_nuevo->expedientestipos_id,$documento_nuevo->sector_inicio);
          $estado_documento = null;
          $userAsignado = null ;
          if ( array_key_exists("asignarse",$doc)){
            $userAsignado = $doc['usuario'];
          }
          $this->documentosInterfaces->createDocumentoEstado($documento_nuevo, $verificar_rutas, $estado_documento, $userAsignado);
          $this->fojasInterfaces->primeraFoja($documento_nuevo);    
          }
          DB::commit(); /*confirmar transaccion */
          // RETORNAR URL DEL DOCUMENTO CREADO 
         
          $url = url('expediente', [base64_encode($documento_nuevo->id)]);
          return response()->json(['success' => 'Registro creado con éxito', 'documento' => $url,"num_documento" => $documento_nuevo->expediente_num], 201);}
           else{
          return response()->json(['error' => 'No tiene permisos para realizar operaciones en el organismo, verificar parametros enviados']);
         }
        } catch (Exception $exception) {
          DB::rollback(); /*Puede revertir la transacción*/
          if ($exception instanceof ModelNotFoundException) {
            return response()->json(['error' => 'Verificar parametros enviados, el sector/usuario/organismo enviado no corresponde']);
          }
         else{
          return response()->json(['errors' => $exception->getMessage()], 500);
         }
        }
    }

    public function usuarioSector(SectoresUsuarioRequest $request) /*listar los sectores de un usuario especifico*/
    { 
        try {  
          $token = $this->loginInterfaces->decodetoken();
          $organismo = Organismosuser::where('users_id',$request->usuario)->firstOrFail()->organismos_id; 
          $autorizacion =  $this->loginInterfaces->autorizacion($organismo, $token['organismo']);  
          $autorizacion_consultar = $this->autorizacionConsultar($request->usuario, $token['usuario']);
          if ($autorizacion == true && $autorizacion_consultar == true){
            $sectores_usuario = $this->sectoresInterfaces->sectoresUsuario($request->usuario);
            return response()->json($sectores_usuario, 200);
          }else {
            return response()->json(['error' => 'No tiene permisos para realizar operaciones en el organismo, verificar parametro enviado']);
          }
        } catch (Exception $exception) {
          if ($exception instanceof ModelNotFoundException) {
            return response()->json(['error' => 'No se encontraron registros, verificar parametro enviado.']);
         }else{
          return response()->json(['errors' => $exception->getMessage()], 500);
         }
        }
    }

    public function tiposDocumentos(TiposDocumentosRequest $request) /*listar los tipos de documentos de un organimos*/
    { 
        try {  
          $token = $this->loginInterfaces->decodetoken();
          $organismo = $request->organismo; 
          $autorizacion =  $this->loginInterfaces->autorizacion($organismo, $token['organismo']);
          $autorizacion_consultar = $this->autorizacionConsultar($request->usuario, $token['usuario']);
          if ($autorizacion == true && $autorizacion_consultar == true ){ 
            $tipos_documentos = $this->tiposDocumentosInterfaces->getTiposDocumentos($request->usuario);
            return response()->json($tipos_documentos, 200);
          }else{
            return response()->json(['error' => 'No tiene permisos para realizar operaciones en el organismo, verificar parametro enviado']);
          }
        } catch (Exception $exception) {
          if ($exception instanceof ModelNotFoundException) {
            return response()->json(['error' => 'No se encontraron registros.']);
         }
         else{
          return response()->json(['errors' => $exception->getMessage()], 500);
         }
        }
      }

      public function tiposDocumentosSector(TiposDocumentosSectorRequest $request) /*listar los tipos de documentos de sector de un organimos*/
      { 
          try {  
            $token = $this->loginInterfaces->decodetoken();
            $organismo = $request->organismo; 
            $autorizacion =  $this->loginInterfaces->autorizacion($organismo, $token['organismo']);
            $autorizacion_consultar = $this->autorizacionConsultar($request->usuario, $token['usuario']);
            if ($autorizacion == true && $autorizacion_consultar == true ){ 
              $tipos_documentos = $this->tiposDocumentosInterfaces->getTiposDocumentosSector($request->usuario,$request->sector);
              return response()->json($tipos_documentos, 200);
            }else{
              return response()->json(['error' => 'No tiene permisos para realizar operaciones en el organismo, verificar parametro enviado']);
            }
          } catch (Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
              return response()->json(['error' => 'No se encontraron registros.']);
           }
           else{
            return response()->json(['errors' => $exception->getMessage()], 500);
           }
          }
        }

      public function tipoDocumento(ValidarTipoDocumentoRequest $request) /*listar los tipos de documentos de un organimos*/
      { 
          try {  
            $token = $this->loginInterfaces->decodetoken();
            $organismo = $request->organismo; 
            $autorizacion =  $this->loginInterfaces->autorizacion($organismo, $token['organismo']);

            if ($autorizacion == true ){ 
              $tipos_documentos = $this->tiposDocumentosInterfaces->getTipoDocumento($token['usuario'],$request->codigo,$request->organismo);
              if ($tipos_documentos == null){
                return response()->json(['error' => 'El usuario que inicio sesión no puede crear este tipo de documento']);
              }else{
                return response()->json($tipos_documentos, 200);
              }
            }else{
              return response()->json(['error' => 'No tiene permisos para realizar operaciones en el organismo, verificar parametro enviado']);
            }
          } catch (Exception $exception) {
            if ($exception instanceof ModelNotFoundException) {
              return response()->json(['error' => 'No se encontraron registros.']);
           }
           else{
            return response()->json(['errors' => $exception->getMessage()], 500);
           }
          }
        }


  
      
  public function storeFojaTexto(ValidateCrearFojaRequest $request)
  {
    
    DB::beginTransaction(); /*iniciar transaccion */
    try {
      $parametros =  $request->all();

      $token = $this->loginInterfaces->decodetoken();
      $organismo = $request->organismo; 
      $autorizacion =  $this->loginInterfaces->autorizacion(intval($organismo), $token['organismo']);
      $autorizacion_crear_foja = $this->autorizacionCrearFoja($parametros['num_documento'], $token['usuario']);
  
      if ($autorizacion == true && $autorizacion_crear_foja == true){
           $texto = $this->fojasInterfaces->createFojaTexto($parametros);
            if($texto == null){
              return response()->json(['error' => 'El documento ingresado no éxiste en la base de datos, verifique los parametros enviados'], 400);
            }else{
              $expediente = $texto;
              //  return response()->json($texto);
              $verificar_rutas = $expediente->expedientesestados->last()->expedientesrutas_id;
              $estado_documento = $expediente->expedientesestados->last()->expendientesestado;
              $this->documentosInterfaces->createDocumentoEstado($expediente, $verificar_rutas,$estado_documento);
              DB::commit(); /*confirmar transaccion */
              return response()->json(['success' => 'Registro creado con éxito'], 201);
            }
         }else{
          return response()->json(['error' => 'No tiene permisos necesarios para crear fojas']);
      }

    } catch (Exception $exception) {
      DB::rollback(); /*Puede revertir la transacción*/
      return response()->json(['error' => $exception->getMessage()], 500);
    }
    
  }


  public function storeFojaPdf(ValidateCrearFojaPdfRequest $request)
  {
    
    DB::beginTransaction(); /*iniciar transaccion */
    try {
      $parametros =  $request->all();

      $token = $this->loginInterfaces->decodetoken();
      $organismo = $request->organismo; 
      $autorizacion =  $this->loginInterfaces->autorizacion(intval($organismo), $token['organismo']);
      $autorizacion_crear_foja = $this->autorizacionCrearFoja($parametros['num_documento'], $token['usuario']);
  
      if ($autorizacion == true && $autorizacion_crear_foja == true){
        $pdf = $this->fojasInterfaces->createFojaPdf($parametros);
        //  return response()->json($imagen);
        if($pdf == null){
          return response()->json(['error' => 'El documento ingresado no existe en la base de datos'], 400);
        }else{
          $expediente = $pdf;
          $verificar_rutas = $expediente->expedientesestados->last()->expedientesrutas_id;
          $estado_documento = $expediente->expedientesestados->last()->expendientesestado;
          $this->documentosInterfaces->createDocumentoEstado($expediente, $verificar_rutas,$estado_documento);
          DB::commit(); /*confirmar transaccion */
          return response()->json(['success' => 'Registro creado con éxito'], 201);
        }
        }else{
          return response()->json(['error' => 'No tiene permisos necesarios para crear fojas']);
       }

    } catch (Exception $exception) {
      DB::rollback(); /*Puede revertir la transacción*/
      if ($exception instanceof ModelNotFoundException) {
        return response()->json(['error' => 'No se encontraron registros, ']);
     }
     else{
      return response()->json(['errors' => $exception->getMessage()], 500);
     }
    }
  }

  public function storeFojaImagen(ValidarCrearFojaImagen $request)
  {
    
    DB::beginTransaction(); /*iniciar transaccion */
    try {
      $parametros =  $request->all();
      $token = $this->loginInterfaces->decodetoken();
      $organismo = $request->organismo; 
      $autorizacion =  $this->loginInterfaces->autorizacion(intval($organismo), $token['organismo']);
      $autorizacion_crear_foja = $this->autorizacionCrearFoja($parametros['num_documento'], $token['usuario']);
  
      if ($autorizacion == true  && $autorizacion_crear_foja == true){
        $imagen = $this->fojasInterfaces->createFojaImagen($parametros);
        if($imagen == null){
          return response()->json(['error' => 'El documento ingresado no éxiste en la base de datos'], 400);
        }else{
          $expediente = $imagen;
          $verificar_rutas = $expediente->expedientesestados->last()->expedientesrutas_id;
          $estado_documento = $expediente->expedientesestados->last()->expendientesestado;
          $this->documentosInterfaces->createDocumentoEstado($expediente, $verificar_rutas,$estado_documento);
          DB::commit(); /*confirmar transaccion */
          return response()->json(['success' => 'Registro creado con éxito'], 201);
        }
         }else{
          return response()->json(['error' => 'No tiene permisos para realizar operaciones en el organismo']);
       }

    } catch (Exception $exception) {
      DB::rollback(); /*Puede revertir la transacción*/
      return response()->json(['error' => $exception->getMessage()], 500);
    }
  }

  public function documentosPersonas(ValidarDocumentoPersonasRequest $request)
  {
    
    DB::beginTransaction(); /*iniciar transaccion */
    try {

      $token = $this->loginInterfaces->decodetoken();
      $organismo = $request->organismo; 
      $autorizacion =  $this->loginInterfaces->autorizacion(intval($organismo), $token['organismo']);
  

      if ($autorizacion == true){

        $renaper = $this->personasLocalInterfaces->buscarPersonaLocal($request->dni, $request->sexo, $request->organismo);

        if($renaper == null){
            /* OBTENER TOKEN PARA ENVIAR A RENAPER */ 
        // $user['username'] = "lucas.gil@telco.com.ar";
        // $users = $this->usuariosInterfaces->getUsuario($user);
        // $password['password'] = "Lucasg2022@@";
        // $this->loginInterfaces->login($users, $password['password']);

        // //  /* OBTENER PERSONA DE RENAPER  */ 
        // $buscar_renaper = $this->personasInterfaces->buscarPersonaRenaper($request->dni, $request->sexo, session('token_go'));
        // // return response()->json($buscar_renaper);
        // if($buscar_renaper['success'] == false){
        //   return response()->json(['error' => 'No se encontro resultado de persona , volver a intentar'], 400);
        // }else{
        //       /*  UNA VEZ OBTENIDA LA PERSONA VINCULAR ORGANISMO */
        //  $renaper =  $this->personasLocalInterfaces->vincularPersonaOrganismo($buscar_renaper['persona'], $request->organismo);
        // }

          // funcionalidad sin servicio de RENAPER
          $renaper = $this->personasLocalInterfaces->crearPersonaLocal($request->organismo, $request->dni, $request->nombre, $request->apellido, $request->sexo);
        }
          $vinculo = $this->documentosInterfaces->vincularDocumentoPersona($renaper['id'], $request->num_doc, $request->año_doc , $request->organismo);
          if ($vinculo == null) {
             return response()->json(['error' => 'El documento ingresado no éxiste en la base de datos'], 400);
          }
          else if ($vinculo == "persona-vinculada"){
            return response()->json(['error' => 'La persona ingresada ya esta vinculada al documento'], 400);
          }
            else{
            DB::commit(); /*confirmar transaccion */
            return response()->json(['success' => 'El vinculo persona documento se creo con éxito'], 200);
          }
        
         }else{
          return response()->json(['error' => 'No tiene permisos para realizar operaciones en el organismo']);
       }

    } catch (Exception $exception) {
      DB::rollback(); /*Puede revertir la transacción*/
      return response()->json(['error' => $exception->getMessage()], 500);
    }
  }

  // Esta funcion permite consultar los expedientes y su recorrido de pases que no tengan la marca de "read_at"
  public function documentosNovedades(DocumentosOrganimosRequest $request)
    { 
        try {  
          $token = $this->loginInterfaces->decodetoken();
          $organismo = $request->organismo; 
          $autorizacion =  $this->loginInterfaces->autorizacion($organismo, $token['organismo']);
          if ($autorizacion == true){
            $documentos = $this->documentosInterfaces->getDocumentosNovedades($request);
            return response()->json($documentos, 200);
          }else{
            return response()->json(['error' => 'No tiene permisos para realizar operaciones en el organismo']);
          }
        } catch (Exception $exception) {
          if ($exception instanceof ModelNotFoundException) {
            return response()->json(['error' => 'No se encontraron registros, verifique parametros enviados']);
         }
         else{
          return response()->json(['errors' => $exception->getMessage()], 500);
         }
        }
      }

      public function documentosLeidos(MarcarDocumentosLeidosRequest $request) 
      {
          DB::beginTransaction(); /*iniciar transaccion */
          try {
            $token = $this->loginInterfaces->decodetoken();
            $organismo = $request->organismo;
            $autorizacion = $this->loginInterfaces->autorizacion($organismo, $token['organismo']);

            if ($autorizacion == true) {
              $respuesta = $this->documentosInterfaces->marcarDocumentosLeidos($request);
            
              if ($respuesta == "error") {
                return response()->json(['error' => 'Los documentos seleccionados no coinciden con el organismo ingresado']);
              }
              else {
                DB::commit();

                return response()->json(['success' => 'Se registraron con éxito los documentos leídos'], 201);
              }
            }
            else {
              return response()->json(['error' => 'No tiene permisos para realizar operaciones en el organismo']);
            }

          } catch (Exception $exception) {
            DB::rollback(); /*Puede revertir la transacción*/
            if ($exception instanceof ModelNotFoundException) {
              return response()->json(['error' => 'Verificar parametros enviados']);
            }
            else{
              return response()->json(['errors' => $exception->getMessage()], 500);
            }
          }
      }

      public function documentoSectorActual(DocumentoSectorActualRequest $request)
      {
        try
        {  
          $token = $this->loginInterfaces->decodetoken();
          $organismo = $request->organismo; 
          $autorizacion =  $this->loginInterfaces->autorizacion($organismo, $token['organismo']);

          if ($autorizacion == true)
          {
            $documentos = $this->documentosInterfaces->getDocumentoSectorNow($request);

            // Si la variable $documentos es una coleccion, significa que se encontraron coincidencias de expedientes y se devuelve un 200, sino, se devuelve un 204, ya que la solicitud se realizó correctamente pero no existen coincidencias para los valores ingresados
            if ($documentos instanceOf \Illuminate\Database\Eloquent\Collection)
            {
              return response()->json([
                'data' => $documentos,
                'status' => true,
                'code' => 200,
                'message' => 'Consulta exitosa'
              ], 200);
            }
            else
            {
              return response()->json([
                'data' => $documentos,
                'status' => true,
                'code' => 204,
                'message' => 'Consulta exitosa, pero no hay contenido para los datos consultados'
              ], 204);
            }
          }
          else
          {
            return response()->json([
              'status' => false,
              'code' => 401,
              'message' => 'No tiene permisos para realizar operaciones en el organismo'
            ], 401);
          }
        }
        catch (Exception $exception)
        {
          if ($exception instanceof ModelNotFoundException)
          {
            return response()->json([
              'status' => false,
              'code' => 400,
              'message' => 'No se encontraron registros, verifique parámetros enviados'
            ], 400);
          }
          else
          {
            return response()->json([
              'status' => false,
              'code' => 500,
              'message' => 'Ocurrió un error al realizar la consulta. Intente nuevamente más tarde'
            ], 500);
          }
        }
      }

}


