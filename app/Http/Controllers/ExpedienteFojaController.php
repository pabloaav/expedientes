<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\ExpedienteFojaRequest;
use Illuminate\Support\Facades\File;

use App\Organismo;
use App\Expedientesruta;
use App\Expedientestipo;
use App\Organismostiposvinculo;

use App\Http\Controllers\ExpedientesController;
use App\Http\Controllers\FojaController;

class ExpedienteFojaController extends Controller
{
    public function create()
    {
      // INICIAR UN NUEVO DOCUMENTO - VALIDACIONES

      if (!session('permission')->contains('expediente.crear.fojas')) {
        session(['status' => 'No tiene acceso para ingresar a este modulo']);
        return redirect()->route('index.home');
      }

      // 1- EL USUARIO DEBE TENER UN ORGANISMO
      if (DB::table('organismosusers')->where('users_id', Auth::user()->id)->exists()) {
        $organismouser = Auth::user()->userorganismo->first()->organismos_id;
      } else {
        return  redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' no tiene una organización asignada no puede iniciar documento');
      }
  
      // 2- EL USUARIO DEBE TENER UN SECTOR ASIGNADO
      if (DB::table('organismossectorsusers')->where('users_id', Auth::user()->id)->exists()) {
        $sectorusers = Auth::user()->usersector->first()->organismossectors_id;
        $sectororganismo = DB::table('organismossectors')->where('id', $sectorusers)->get();
      } else {
        return redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' no tiene un sector asignado no puede iniciar documento');
      }
  
      // 3 - SI ES USUARIO ADMIN PUEDE INICIAR EL DOCUMENTO AUNQUE SU SECTOR NO FIGURE EN NINGUNA RUTA
      //  SI EL SECTOR DONDE ESTA VINCULADO EL USUARIO NO ESTA EN NINGUNA RUTA NO PODRA INICIAR EL DOCUMENTO
      $user_ruta = Expedientesruta::where('organismossectors_id', '=', $sectorusers)->count();
      $libres_tipo = ExpedientesTipo::where('organismos_id',  $organismouser)->where('sin_ruta', 1)->count();
      if ((!session('permission')->contains('organismos.index.admin')) &&  $user_ruta === 0 && $libres_tipo === 0) {
        return redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' su sector no puede generar un documento porque no figura en ninguna ruta. Por favor comuniquese con el Administrador');
      }
  
      // if ((Expedientesruta::where('organismossectors_id', '=', $sectorusers))->count() === 0) {
      //   return redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' su sector no puede generar un documento porque no figura en ninguna ruta. Por favor comuniquese con el Administrador');
      // }
  
      // 4 TANTO SI ES ADMINISTRADOR COMO UN USUARIO PUEDE TENER VARIOS SECTORES PARA INICIAR EL DOCUMENTO  
      $usuario = Auth::user();
      if (session('permission')->contains('organismos.index.admin') || $usuario->usersector->count() > 1) {
        $idUser = $usuario->id;
        $sectororganismo = DB::table('organismossectorsusers')->join("organismossectors", "organismossectorsusers.organismossectors_id", "=", "organismossectors.id")->where('organismossectorsusers.users_id', "=", $idUser)->get();
      }
  
      // 5 CARGA LOS TIPOS DE DOCUMENTOS SEGUN EL SECTOR DEL USUARIO
      $tiposexpedientes = DB::table('expedientestipos')
        ->join("expedientesrutas", "expedientesrutas.expedientestipos_id", "=", "expedientestipos.id")
        ->where('expedientestipos.activo', "1")
        ->select("expedientestipos.id", "expedientestipos.expedientestipo")
        ->where('expedientestipos.sin_ruta', "=", 0)
        ->where('expedientesrutas.activo', "1")
        ->where('organismossectors_id', '=', $sectorusers)
        ->get();
  
      $tiposexpedientes = $tiposexpedientes->unique("expedientestipo");
  
      // CARGAR LOS TIPOS DE DOCUMENTOS SIN RUTA
      $tipoexpedientesinruta =  DB::table('expedientestipos')
        ->where('expedientestipos.activo', "1")
        //  ->join("expedientesrutas", "expedientesrutas.expedientestipos_id", "=", "expedientestipos.id")
        ->select("expedientestipos.id", "expedientestipos.expedientestipo")
        ->where('expedientestipos.organismos_id', $organismouser)
        ->where('expedientestipos.sin_ruta', 1)
        ->get();
  
      $personas = DB::table('personas')
        ->where('organismos_id', $organismouser)
        ->orderBy('apellido')
        ->get();
  
      //  AGREGAR LOS TIPOS DE DOCUMENTOS LIBRES A LA COLECION 
      foreach ($tipoexpedientesinruta as $c) {
        $tiposexpedientes->push($c);
      }
  
      $organismo = Organismo::find($organismouser);
      $configOrganismo = $organismo->configuraciones;
      $tiposvinculo = Organismostiposvinculo::where('organismos_id', $organismo->id)
                      ->where('activo', 1)
                      ->get();
  
      // 6 El proximo numero de expediente lo extraemos de la funcion del ExpedienteHelper
      $proximoNumeroExpediente = getNextExpedienteNumber($organismouser);
      $title = "Nuevo Documento";
      return view('expedientes.crearexpconfojas', ['title' => $title, 'tiposexpedientes' => $tiposexpedientes, 'sectorusers' => $sectorusers, 'organismouser' => $organismouser, 'sectororganismo' => $sectororganismo, 'proximoNumeroExpediente' => $proximoNumeroExpediente, 'personas' => $personas, 'configOrganismo' => $configOrganismo, 'tiposvinculo' => $tiposvinculo]);
    }

    public function store(ExpedienteFojaRequest $request)
    {
        if (!session('permission')->contains('expediente.crear.fojas')) {
          session(['status' => 'No tiene acceso para ingresar a este modulo']);
          return redirect()->route('index.home');
        }

        $expedienteController = new ExpedientesController();
        $expediente = $expedienteController->store($request);
        $respuestaExp = json_decode($expediente->content(), true);

        if ($respuestaExp['success'] === 1)
        {
          $datosFojas = new Request;
          $requestFojas = $request->replace([
            'pdfs' => $request->file('pdfs'),
            'expediente_id' => $respuestaExp['ultimoExp']
          ]);

          $fojaController = new FojaController();
          $fojas = $fojaController->storefile($requestFojas);
          $respuestaFojas = json_decode($fojas->content(), true);

          // Control si el PDF esta encriptado o hubo un problema al convertirlo a imagen
          if (array_key_exists('status', $respuestaFojas))
          {
            return response()->json([
              'success' => 3,
              'message' => $respuestaFojas['message'],
              'ultimoExp' => $respuestaExp['ultimoExp']
            ]);
          }
          else if ($respuestaFojas[0][0] === 1)
          {
            return response()->json([
              'success' => 1,
              'ultimoExp' => $respuestaExp['ultimoExp']
            ]);
          }
          else
          {
            return response()->json([
              'success' => 2,
              'ultimoExp' => $respuestaExp['ultimoExp']
            ]);
          }
        }
        else
        {
          return response()->json([
              'success' => false,
              'message' => $respuestaExp['errors'][0]
          ]);
        }
    }
}
