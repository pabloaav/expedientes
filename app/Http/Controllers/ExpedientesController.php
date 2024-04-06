<?php

namespace App\Http\Controllers;

use PDF;
use App\Foja;
use App\User;
use DateTime;
use Exception;
use App\Localidad;
use App\Organismo;
use App\Plantilla;
use Carbon\Carbon;
use App\Expediente;
use App\Expedientesruta;
use App\Expedientestipo;
use App\Expedienteestado;
use App\Organismossector;
use App\Organismosetiqueta;
use App\Organismostiposvinculo;
use App\Expedienteorganismoetiqueta;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Organismossectorsuser;
use App\Traits\FileHandlingTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Notifications\PaseExpediente;
use App\Notifications\PaseSector;
use App\Traits\ExpedientesPermisosTrait;
use Illuminate\Support\Facades\Storage;
use Caffeinated\Shinobi\Facades\Shinobi;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use SplFileInfo;
use App\Logg;
use App\Persona;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Events\ClickCompartirLink;
use App\Events\ClickGenerarPdf;
use ZipArchive;
use App\Traits\AdjuntarArchivos;
use App\Expedientesadjunto;
use App\ExpedientetipoPersona;
use App\Detallesadjunto;
use DataTables;
use App\Traits\SectoresDestino;
use App\Http\Requests\FojasSelectedRequest;
use App\Traits\EmailService;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExpedientesController extends Controller
{
  use FilehandlingTrait, ExpedientesPermisosTrait, AdjuntarArchivos, SectoresDestino, EmailService;

  // public function index($opcion = "todos", $filtro = null) // parametros originales de la funcion index de documentos
  public function index($opcion = "todos", $bandera = 0, $categoryFilter = 0, $typeFilter = "Vacio", $tagFilter = "Vacio", $sectorFilter = "Vacio", $dateFilter = "Vacio", $inputSearch , $cantidad = 0, $filtro = null)
  {
    $session = session('permission');
    $auth_user = Auth::user();

    // Usando Trait para refactorizar logica de filtro de expedientes segun permisos, sus sectores/subsectores y sector filtro de la pagina inicial
    // if ($opcion == "sub") {
    //   $expedientes = $this->getExpedientesPorPermisos($session, $auth_user, "sub", $filtro);
    // } else if ($filtro != null) {
    //   $expedientes = $this->getExpedientesPorPermisos($session, $auth_user, "todos", $filtro);
    // } else {
    //   $expedientes = $this->getExpedientesPorPermisos($session, $auth_user);
    // }

    $id = $auth_user->id;
    $orgId = Auth::user()->userorganismo->first()->organismos_id;
    $organismo = Organismo::find($orgId);
    $arraySectores = Auth::user()->usersector->pluck('organismossectors_id')->toArray();
    $configOrganismo = $organismo->configuraciones;
    $cantidad = $configOrganismo->cant_registros;
    $sectoresFiltro = Organismossector::where('organismos_id', $orgId)->where('activo',1)->orderBy('organismossector','asc')->get();
    $tiposDocFiltro = ExpedientesTipo::where("organismos_id", $orgId)->where('activo',1)->get();
    $etiquetasFiltro = Organismosetiqueta::withTrashed()->where('organismos_id', $orgId)->get();


    $filterEstado = DB::table('preferencies')->where('users_id', $id)->where('filterNombre', 'Estado')->value('filterPref');
    $filterTipo = DB::table('preferencies')->where('users_id', $id)->where('filterNombre', 'TipoExpediente')->value('filterPref');
    $filterEtiqueta = DB::table('preferencies')->where('users_id', $id)->where('filterNombre', 'Etiqueta')->value('filterPref');
    $filterSector = DB::table('preferencies')->where('users_id', $id)->where('filterNombre', 'Sector')->value('filterPref');
    $filterDate = DB::table('preferencies')->where('users_id', $id)->where('filterNombre', 'Fecha')->value('filterPref');
    $campoBusqueda = DB::table('preferencies')->where('users_id', $id)->where('filterNombre', 'Busqueda')->value('filterPref');
    
    // Si el check de Recordar filtros en Configuraciones de organismo está activo, los filtros perdurarán activos hasta que el usuario los limpie
    if ($configOrganismo->filtros_documentos == 1) {
      $filterEstado = ($filterEstado != "" ? $filterEstado : 0);
      $filterTipo = ($filterTipo != "" ? $filterTipo : "");
      $filterEtiqueta = ($filterEtiqueta != "" ? $filterEtiqueta : "");
      $filterSector = ($filterSector != "" ? $filterSector : "");
      $filterDate = ($filterDate != "" ? $filterDate : "");
      $campoBusqueda = ($campoBusqueda != "" ? $campoBusqueda : "");
    }
    elseif ($configOrganismo->filtros_documentos == 0 && $bandera == 0) {
      $filterEstado = 0;
      $filterTipo = "Vacio";
      $filterEtiqueta = "Vacio";
      $filterSector = "Vacio";
      $filterDate = "Vacio";
      $campoBusqueda = "";
    }
    elseif ($configOrganismo->filtros_documentos == 0 && $bandera !== 0) {
      $filterEstado = $categoryFilter;
      $filterTipo = $typeFilter;
      $filterEtiqueta = $tagFilter;
      $filterSector = $sectorFilter;
      $filterDate = $dateFilter;
      if ($inputSearch !== "default") {
        $campoBusqueda = $inputSearch;
      } else {
        $campoBusqueda = "";
      }
    }

    // Usando Trait para refactorizar logica de filtro de expedientes segun permisos, sus sectores/subsectores y sector filtro de la pagina inicial
    if ($opcion == "sub") {
      $expedientes = $this->getExpedientesPorPermisos($session, $auth_user, "sub", $filterEstado, $filterTipo, $filterEtiqueta, $filterSector, $filterDate, $campoBusqueda, $filtro, $cantidad);
    } else if ($filtro != null) {
      $expedientes = $this->getExpedientesPorPermisos($session, $auth_user, "todos", $filterEstado, $filterTipo, $filterEtiqueta, $filterSector, $filterDate, $campoBusqueda, $filtro, $cantidad);
    }else {
      $expedientes = $this->getExpedientesPorPermisos($session, $auth_user, "todos", $filterEstado, $filterTipo, $filterEtiqueta, $filterSector, $filterDate, $campoBusqueda, $filtro, $cantidad);
    }

    // Al cargar el index por primera vez, el valor de $cantidad es NULL, por lo tanto se debe asignar un valor por defecto (10) que corresponde a la cantidad
    // de registros por pagina en la vista
    // if ($cantidad == NULL) {
    //   $cantidad = 10;
    // }
    
    $totalExpedientes = $expedientes->total();
    // $totalExpedientes = $expedientes->count();
    // $expedientes = $expedientes->paginate($cantidad);

    $preferencia = [$filterEstado, $filterTipo, $filterEtiqueta, $campoBusqueda, $filterSector, $filterDate];
    $title = "Documentos";
    return view('expedientes.index', ['title' => $title, 'expedientes' => $expedientes, 'arraySectores' => $arraySectores,'preferencia' => $preferencia, 'opcion' => $opcion,
     'filtro' => $filtro, 'tiposDoc' => $tiposDocFiltro, 'etiquetas' => $etiquetasFiltro,'sectores' => $sectoresFiltro, 'configOrganismo' => $configOrganismo,
     'totalExpedientes' => $totalExpedientes, 'cantidad' => ($cantidad == 0) ? 50 : $cantidad]);
  }

  // BUSCADOR SIMPLE(VERIFICARLO NO ESTA EN USO)
  // public function finder(Request $request)
  // {
  //   // El usuario que busca
  //   $auth_request_user = $request->user();
  //   // Los permisos del usuario que busca
  //   $session = session('permission');
  //   // El término de busqueda
  //   $term = $request->buscar;
  //   // Puede venir nulo cuando se hace la busqueda vacía
  //   if (is_null($term)) {
  //     $term = "";
  //   }

  //   // El trait filtra segun los permisos
  //   $expedientes = $this->findExpedientesPorPermisos($session, $auth_request_user, $term);

  //   $title = "Documento: buscando " . $term;
  //   return view('expedientes.index', ['expedientes' => $expedientes, 'title' => $title]);
  // }

  public function show($id, $fojas = null)
  {

    $session = session('permission');

    if (!$session->contains('expediente.show')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $id = base64_decode($id);

    if ($fojas !== null) {
      $gestion_fojas = "activo";
    }
    else {
      $gestion_fojas = "inactivo";
    }

    $expediente = Expediente::findOrfail($id);

    $organismo = Organismo::find($expediente->organismos->id);
    $configOrganismo = $organismo->configuraciones;

    // Autorizacion
    try {
      $this->authorize('show', [$expediente, $session]);
    } catch (\Exception $exception) {
      Logg::error($exception->getMessage(), ("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()));
      if ($exception instanceof AuthorizationException) {
        return redirect()->route('expedientes.index')->with('errors', ['No tiene permisos sobre el documento/sector.']);
      }
    }

    // Prueba: que no se genere un estado nuevo cuando el usuario que pasó el documento ingrese a ver el detalle
    $estadoactual = Expedienteestado::where('expedientes_id', $expediente->id)->get()->last();

    if (($estadoactual->pasado_por == NULL && $estadoactual->users_id !== NULL) && $estadoactual->expendientesestado !== "nuevo") {
      $textoLog = Auth::user()->name . " ingreso a ver los detalles del " . org_nombreDocumento() . " a las " . Carbon::now()->toTimeString();
      historial_doc(($expediente->id),$textoLog);
    }
    // Prueba: que no se genere un estado nuevo cuando el usuario que pasó el documento ingrese a ver el detalle

    // $textoLog = Auth::user()->name . " ingreso a ver los detalles del " . org_nombreDocumento() . " a las " . Carbon::now()->toTimeString();
    // historial_doc(($expediente->id),$textoLog);
   

    //CARGAR DATOS EN VISTA GENERAL DEL DOCUMENTO 
    // 1 cargar plantillas de cada sector
    $palntila_sector =  $expediente->expedientesestados->last()->rutasector->organismossectors_id;
    $plantillas = Plantilla::where('activo', 1)->where(function ($query) use ($palntila_sector){
      $query->where('organismossectors_id', $palntila_sector)
      ->orWhere('global', 1);})->get();

      $controlSectoresGlobales= [];
      foreach ($plantillas as $indice => $plantilla) {
        $sectorplantilla = Organismossector::find($plantilla->organismossectors_id);
        if ($sectorplantilla->organismos_id != $expediente->organismos_id){
          array_push($controlSectoresGlobales , ($sectorplantilla->id));
        }
      }
      $plantillas = $plantillas->reject(function ($plantilla)  use ($controlSectoresGlobales) {
        return in_array($plantilla->organismossectors_id, $controlSectoresGlobales);
      });


    // 2 verificacion de rutas segun tipo de expediente
    $estados = $expediente->expedientesestados->unique('expedientesrutas_id');
    $data = [];

    foreach ($estados as $query) {
      $data[] = [
        'id' => $query->expedientesrutas_id,
      ];
    }
    $dato = Arr::flatten($data);

    // 3 cargar en un array los tiempo de de inicio de cada sector
    $tiemposector = $expediente->expedientesestados;

    $datatiempo = [];
    foreach ($tiemposector as $key => $query) {
      if (($query->expendientesestado == 'nuevo') || ($query->expendientesestado == 'pasado')) {
        $datatiempo[] = [
          'organismossectors_id' => $query->rutasector->sector->organismossector,
          'organismossectors_id_sector' => $query->rutasector->id,
          'estado' => $query->expendientesestado,
          'tiempo_iniciado' => $query->created_at,
          'tiempo_pase' => $query->updated_at,
          'dias' => $query->rutasector->dias,
          $dias =  $query->created_at->diffInDays($query->updated_at),
          'tiempo_calculado' =>  $dias,
          'pasado_por' => User::find($query->pasado_por),
          'cantidad_fojas_pase' => $query->cantidad_fojas_pase

        ];
      }
    }
    // 4 actualizar campos updated_at de tabla estadosexpediente para calcular dias de cada sector 
    $ruta = Expedienteestado::where("expedientes_id", $expediente->id)->where("expendientesestado", "nuevo")->get();
    $ruta1 = Expedienteestado::where("expedientes_id", $expediente->id)->where("expendientesestado", "pasado")->get();
    if ($ruta1->count() > 0) {
      $ultimaruta = $ruta1->last();
      $ultimaruta->updated_at = Carbon::now();
      $ultimaruta->update();
    } elseif ($ruta->count() === 1) {
      $ultimaruta = $ruta->last();
      $ultimaruta->updated_at = Carbon::now();
      $ultimaruta->update();
    }

    // 5 datos del documento
    $sectordevolver = Expedientesruta::find($estadoactual->ruta_devolver);
    $useractual = User::find($expediente->expedientesestados->last()->users_id);
    $sectoresUsuario = Auth::user()->usersector->pluck('organismossectors_id')->toArray();
    $sectoractual = Expedientesruta::find($expediente->expedientesestados->last()->expedientesrutas_id);
    $expediente_organismo = $expediente->organismos->localidads_id;
    $organismo_localidad = Localidad::find($expediente_organismo);
    $expediente_rutas = $expediente->expedientetipo->rutas->where('activo', 1)->sortBy('orden');
    $title = "Datos del Documento";
    $fojas = $expediente->fojas()->orderBy('numero', 'ASC')->get();
    $fojas_preview = $expediente->fojas()->orderBy('numero', 'ASC')->paginate(10);
    $fojasEtiquetas = [];
    $control = [];
    foreach ($fojas as $i => $foja) {
      $etiquetas = $foja->organismosetiquetas;
      if (($etiquetas) != null) {
        foreach ($etiquetas as $j => $etiqueta) {
          if (!in_array($etiqueta->organismosetiqueta, $control)) {
            array_push($fojasEtiquetas,  $etiqueta);
            array_push($control,  $etiqueta->organismosetiqueta);
          }
        }
      }
    }
    $etiquetas = $expediente->organismosetiquetas;

    //Relaciones Documentos
    $user = Auth::user();
    $userLogin = User::find($user->id);
    $expedientesEnlazados = $expediente->documentosEnlazados()->get();
    $expedientesFusionados = $expediente->documentosFusionados()->get();
    //Relaciones Personas
    $personas = $expediente->personas()->get()->unique('id'); // si 1 persona está asociada mas de una vez al documento, se lo trae solo 1 vez
    $vinculos = $expediente->personasVinculo; // contiene los tipos de vinculo que poseen las personas asociadas al documento

    // se comenta la variable $etiquetasSinVincular porque se usa $etiquetasPdf, que ya tiene el control de etiquetas por sector del usuario
    // $etiquetasSinVincular = Organismosetiqueta::where('organismos_id', $expediente->organismos->id)->get();

    // Etiquetas para fojas a subir
    $etiquetasPdf = Organismosetiqueta::where('organismos_id', $organismo->id)->where('activo', 1)->get();
    
    if (session('permission')->contains('expediente.etiqueta.sector')) {
      $sectoresUsuario = Auth::user()->usersector->pluck('organismossectors_id')->toArray(); // etiquetas solo de los sectores que pertenece el usuario (PRUEBA)
      $global = NULL;

      $etiquetasPdfPorSector = $etiquetasPdf->filter(function ($etiqueta) use ($sectoresUsuario) {
        return in_array($etiqueta->organismossectors_id, $sectoresUsuario);
      });

      $etiquetasPdfGlobales = $etiquetasPdf->filter(function ($etiqueta) use ($global) {
        return $etiqueta->organismossectors_id == $global;
      });

      $etiquetasPdf = $etiquetasPdfPorSector->concat($etiquetasPdfGlobales);
    }
    // Etiquetas para fojas a subir

    // dd($expediente->expedientetipo->sin_ruta);
    return view('expedientes.show', [
      'expediente' => $expediente, 'dato' => $dato, 'datatiempo' => $datatiempo, 'expediente_rutas' => $expediente_rutas, 'title' => $title,'sectoresUsuario' => $sectoresUsuario,
      'sectoractual' => $sectoractual, 'organismo_localidad' => $organismo_localidad, 'useractual' => $useractual, 'userLogin' => $userLogin, 'fojas' => $fojas, 'fojas_preview' => $fojas_preview, 'estados' => $estados,
      'etiquetas' => $etiquetas, 'plantillas' => $plantillas, 'expedientesEnlazados' => $expedientesEnlazados,  'expedientesFusionados' => $expedientesFusionados, 'personas' => $personas, 'fojasEtiquetas' => $fojasEtiquetas,
      'configOrganismo' => $configOrganismo, 'sectordevolver' => $sectordevolver, 'etiquetasPdf' => $etiquetasPdf, 'gestion_fojas' => $gestion_fojas, 'vinculos' => $vinculos
    ]);
  }

  // Esta funcion trae las 10 fojas de la siguiente pagina como un string a partir de la funcion render, y luego se inserta eso por JS a la vista previa
  public function showPreview($id)
  {
    $id = base64_decode($id);

    $expediente = Expediente::findOrfail($id);
    $fojas_preview = $expediente->fojas()->orderBy('numero', 'ASC')->paginate(10, ['*'], 'page', request('page'));

    return view('expedientes.partial_fojas', compact('fojas_preview'))->render();
  }

  // Esta funcion permite actualizar los valores del select que permite filtrar por una foja en particular
  public function dataFojas($id)
  {
    $id = base64_decode($id);

    $fojas = Foja::where('expedientes_id', $id)
                ->select('id', 'numero')  
                ->paginate(10, ['*'], 'page', request('page'))
                ->items();

    return response()->json([
      'respuesta' => 1,
      'fojas' => $fojas
    ]);
  }

  public function requisitos($expediente_id, $id_ruta)
  {
    $expedientesruta = Expedientesruta::find($id_ruta)->requisitos->where('activo', 1);
    $expedientesector =  Expedientesruta::find($id_ruta)->sector->organismossector;
    return response()->json(['expedientesruta' => $expedientesruta, 'expedientesector' => $expedientesector]);
  }

  public function edit($id)
  {

    if (!session('permission')->contains('expediente.editar')) {
      session(['status' => 'No tiene permiso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $id = base64_decode($id);
    if (DB::table('organismosusers')->where('users_id', Auth::user()->id)->exists()) {
      $organismouser = Auth::user()->userorganismo->first()->organismos_id;
    } else {
      return  redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' no tiene una organización asignada no puede iniciar expediente');
    }


    if (DB::table('organismossectorsusers')->where('users_id', Auth::user()->id)->exists()) {
      $sectorusers = Auth::user()->usersector->first()->organismossectors_id;
      $sectororganismo = DB::table('organismossectors')->where('id', $sectorusers)->get();
    } else {
      return redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' no tiene un sector asignado no puede iniciar expediente');
    }

    // se comenta IF por si el 1er sector al que pertenece el usuario no forma parte de ninguna ruta de los tipos de documentos definidos
    // if ((Expedientesruta::where('organismossectors_id', '=', $sectorusers))->count() === 0) {
    //   return redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' su sector no puede generar un expediente');
    // }

    $expediente = Expediente::find($id);
    // $expediente_tipo = Expedientestipo::all();
    // dd($expediente_tipo);

    $tiposexpedientes = DB::table('expedientestipos')->where('activo', "1")->where('organismos_id', $organismouser)->orderBy('expedientestipo')->get();

    $title = "Editar Carátula del documento " . getExpedienteName($expediente);
    return view('expedientes.edit', ['expediente' => $expediente, 'title' => $title, 'tiposexpedientes' => $tiposexpedientes, 'sectorusers' => $sectorusers, 'organismouser' => $organismouser, 'sectororganismo' => $sectororganismo,]);
  }

  public function update(Request $request)
  {

    DB::beginTransaction();
    try {
      if (!session('permission')->contains('expediente.editar')) {
        session(['status' => 'No tiene acceso para ingresar a este modulo']);
        return redirect()->route('index.home');
      }

      $expediente = Expediente::find($request->exp_id);
      $tipoexp_original = Expedientestipo::find($expediente->expedientestipos_id);
      $expedientetipo = Expedientestipo::find(intval($request->tipo_expediente));
      $validator = Validator::make($request->all(), [
        //'tipo_expediente' => 'required',
        'expediente' => 'required',
        'sectorusers' => 'required',
        'fecha_inicio' => 'required',


      ]);

      if ($validator->fails()) {
        foreach ($validator->messages()->getMessages() as $field_name => $messages) {
          foreach ($messages as $message) {
            $errors[] = $message;
          }
        }

        session()->flash('errors', $errors);

        return response()->json([
          'response' => 2,
        ]);
      }

      // se consulta si se cambió el tipo de documento al editar el expediente
      if ($expediente->expedientestipos_id !== $expedientetipo->id) {
        /* se consulta si tipo seleccionado es de tipo "sin ruta"
            - si es sin ruta, se verifican las rutas que ya tiene cargado ese tipo para ver si coincide alguna con el sector destino seleccionado. Si no hay coincidencia, se genera una nueva ruta para ese tipo "sin ruta" y se asigna al expediente
           si es con ruta
            - se verifican todas las rutas activas para guardar la ruta que coincida con el sector destino seleccionado
        */
        if ($expedientetipo->sin_ruta == 1) {
          if ($expedientetipo->rutas->count() > 0) {
            $ruta_sector = NULL;
            $rutas_tipo = $expedientetipo->rutas->sortBy('orden');
  
            foreach ($rutas_tipo as $ruta) {
              if ($ruta->organismossectors_id == $request->selectRutaTipo) {
                $ruta_sector = $ruta;
                break;
              }
            }

            if (is_null($ruta_sector)) {
              $expedienteruta = new Expedientesruta;
              $expedienteruta->expedientestipos_id = $expedientetipo->id;
              $expedienteruta->organismossectors_id = $request->selectRutaTipo;
              $expedienteruta->orden = NULL;

              $expedienteruta->save();

              $ruta_sector = $expedienteruta;
            }
          }
          else {
            $expedienteruta = new Expedientesruta;
            $expedienteruta->expedientestipos_id = $expedientetipo->id;
            $expedienteruta->organismossectors_id = $request->selectRutaTipo;
            $expedienteruta->orden = NULL;

            $expedienteruta->save();

            $ruta_sector = $expedienteruta;
          }
        }
        else {
          if ($expedientetipo->rutas->count() > 0) {
            $ruta_sector = $expedientetipo->rutas->sortBy('orden');
            $ruta_activa = NULL;
  
            foreach ($ruta_sector as $ruta) {
              if ($ruta->organismossectors_id == $request->selectRutaTipo) {
                $ruta_activa = $ruta;
  
                break;
              }
            }
  
            if (is_null($ruta_activa)) {
              $errors = array('El tipo de documento seleccionado debe tener al menos 1 sector activo en su ruta');
  
              session()->flash('errors', $errors);

              return response()->json([
                'response' => 2,
              ]);
            }
            else {
              $ruta_sector = $ruta_activa;
            }
          }
          else {
            $errors = array('El tipo de documento seleccionado debe tener al menos 1 sector en su ruta');
  
            session()->flash('errors', $errors);

            return response()->json([
              'response' => 2,
            ]);
          }
        }
      }
      else {
        // si no se cambió el tipo, se asigna la misma ruta y usuario que ya tenia
        $ruta_sector = $expediente->expedientesestados->last()->rutasector;
        $user_actual = $expediente->expedientesestados->last()->users;
      }

      $expediente->expedientestipos_id = $expedientetipo->id;

      $expediente->expediente = $request->expediente;

      $expediente->fecha_inicio = $request->fecha_inicio;

      $expediente->ref_siff = $request->ref_siff;

      $expediente->update();

      //estados del expediente
     
      // $textoLog = Auth::user()->name . " actualizó la caratula " . org_nombreDocumento() . " " .  getExpedienteName($expediente)  . " a las " . Carbon::now()->toTimeString();
      // Logg::info($textoLog);
      // historial_doc(($expediente->id), $textoLog );

      $estadoexpediente = new Expedienteestado;
      $estadoexpediente->expedientes_id = $expediente->id;
      $estadoexpediente->expendientesestado = "procesando";
      $estadoexpediente->expedientesrutas_id = $ruta_sector->id;
      if (isset($user_actual)) {
        $estadoexpediente->users_id = $user_actual->id;
      }
      $textoLog = Auth::user()->name . " actualizó la caratula del " . org_nombreDocumento() . " " .  getExpedienteName($expediente)  . " de tipo " . $tipoexp_original->expedientestipo . " a " . $expedientetipo->expedientestipo . " a las " . Carbon::now()->toTimeString();
      $estadoexpediente->observacion = $textoLog;
      Logg::info($textoLog);

      $estadoexpediente->save();
      

      // Modificar la caratula del expediente que es la foja 1
      $carpetaRaizOrganismo = $expediente->organismos->id;
      Storage::disk('local')->makeDirectory($carpetaRaizOrganismo);
      $fojaPathAndName =  $this->crearPrimerFojaCaratula($carpetaRaizOrganismo, $expediente);
      $path = Storage::disk('local')->path($fojaPathAndName);
      $fojaImagen = $this->singlePdfToImage($path, "caratula_" . $expediente->expediente_num . ".pdf");
      $newPath = $carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $expediente->id . DIRECTORY_SEPARATOR . $fojaImagen;
      $imageContent = Storage::disk('local')->get($newPath);
      $minioPathFojaCaratulaImagen = $carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $expediente->id . DIRECTORY_SEPARATOR . $fojaImagen;

      // Modificar la primera foja del Expediente
      $foja = Foja::where("expedientes_id", $expediente->id)->where("numero", 1)->first();
      $foja->expedientes_id = $expediente->id;
      $foja->tipofoja = "texto";
      $foja->texto =  "Caratula del Documento: " . getExpedienteName($expediente) . ", con extracto: " . $expediente->expediente;
      $foja->file = null;
      $foja->numero = 1;
      $foja->nombre = "foja_" . 1;
      $foja->updated_at = new Carbon;
      $foja->path =  $newPath;
      $foja->update();


      try {
        // La respuesta es true o verdadera en el caso de que se pueda guardar la imagen en el servidor
        $storageMinioResult = Storage::cloud()->put($minioPathFojaCaratulaImagen, $imageContent);

        if ($storageMinioResult)
        {
          $path_clean = storage_path() . DIRECTORY_SEPARATOR ."app". DIRECTORY_SEPARATOR .$carpetaRaizOrganismo. DIRECTORY_SEPARATOR .$expediente->id; // directorio local del expediente donde se guarda la caratula temporalmente
          File::cleanDirectory($path_clean); // quita imagen y PDF de la caratula generada para el expediente, ya que se almacena en el minio
        }
      } catch (\Exception $e) {
        Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
        return response()->json([
          'success' => 'false',
          'errors'  => ["El servidor de fojas no esta disponible"],
        ], 400);
      }

      DB::commit();
      
      if (session('permission')->contains('organismos.index.admin')) {
        return response()->json([
          'response' => 1,
          'expediente' => base64_encode($expediente->id)
        ]);
      }
      else {
        session()->flash('message', 'Se ha editado la carátula con éxito');

        return response()->json([
          'response' => 1,
          'expediente' => 0
        ]);
      }
    } catch (Exception $e) {
      DB::rollback();
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return $e->getMessage();
    }
  }

  public function generarPase($id)
  {

    // Si el usuario no tiene el pemiso para generar pase 
    if (!session('permission')->contains('expediente.pase')) {

      return redirect()->route('expedientes.index')->with('errors', ['No tiene acceso para ingresar a este modulo']);
    }

    $id = base64_decode($id);

    // Datos del expediente y tipo 
    $expediente = Expediente::find($id);

    $session = session('permission');

    // Autorizacion 
    // 1 si el usuario es admin o super user podra realizar los pases de cualquier sector 
    // si el usuario no tiene asignado el expediente no puede relizar el pase 
    try {
      $this->authorize('generar_pase', [$expediente, $session]);
    } catch (\Exception $exception) {
      Logg::error($exception->getMessage(), ("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()));
      if ($exception instanceof AuthorizationException) {
        return redirect()->route('expedientes.index')->with('errors', ["Debe tener asignado el documento para generar el pase"]);
      }
    }
    $sectoractualexpediente =  Expedientesruta::find($expediente->expedientesestados->last()->expedientesrutas_id);
    $sectoractual = Organismossector::find($sectoractualexpediente->organismossectors_id);

    // cargar solo los sectores del tipo de documento (verificar si el tipo de documento es libre)
    if ($expediente->expedientetipo->sin_ruta == 1) {
      $organismos_sectores = Organismo::find($expediente->organismos_id)->sectores->where('activo', 1);
    } else {
      $organismos_sectores = $expediente->expedientetipo->rutas->where('activo', 1);
      $organismos_sectores = $organismos_sectores->sortBy('orden');
    }

    // barra de progreso 
    $activos = Expedientesruta::where("expedientestipos_id", $expediente->expedientetipo->id)->where('activo', 1);
    $total = $activos->count();
    $pluckActual = Expedienteestado::where('expedientes_id', $id)->distinct()->pluck('expedientesrutas_id');
    $actual = $activos->whereIn('id', $pluckActual)->count();

    $reporte = round(($actual / $total) * 100);
    // $title = "Pase del Documento " . $expediente->expediente_num;
    $title = "Pase del Documento " . getExpedienteName($expediente);
    $fojas = $expediente->fojas()->orderBy('numero', 'ASC')->get();

    $expediente_rutas = $expediente->expedientetipo->rutas->where('activo', 1)->sortBy('orden');
    // dd( $expediente_rutas);
    // verificacion de rutas segun tipo de expediente
    $estados = $expediente->expedientesestados->unique('expedientesrutas_id');
    $data = [];

    foreach ($estados as $query) {
      $data[] = [
        'id' => $query->expedientesrutas_id,
      ];
    }
    $dato = Arr::flatten($data);

    if ($expediente->expedientetipo->sin_ruta == 1) {
      $idProximoSector = $sectoractual->id;
      $orden = true; // se pone en TRUE para que la variable $orden tenga un valor al pasar a la vista
    } else {
      // Obtenemos el lugar que ocupa en la coleccion ordenada de rutas, el sector actual en el que esta el expediente
      if ($sectoractualexpediente->activo == 0) {
        // si el sector actual esta inactivo, se asigna FALSE al orden para que tome el 1er sector del circuito como el siguiente
        $orden = false;
        session()->flash('message', 'El sector actual está inactivo como ruta para su tipo de documento');
      }
      else {
        $orden = $expediente_rutas->where('id', '=', $sectoractualexpediente->id)->first()->orden;
      }

      // como la busqueda del indice puede resultar en false, hay que preguntar
      if ($orden !== false) {
        // Obtenemos la siguiente ruta que deberia seguir el expediente segun el orden establecido y que esté activo 
        $sigOrden = $orden + 1;
        for ($sig = ($sigOrden); $sig < count($expediente_rutas); $sig++) {
          $proximaRuta =  $expediente_rutas->where('orden', $sig)->where('activo', 1)->first();
          if (!is_null($proximaRuta)) {
            $sigOrden = $sig;
            $sig = count($expediente_rutas);
          }
        }
        $proximaRuta =  $expediente_rutas->where('orden', $sigOrden)->first();
        // A esa proxima ruta le corresponde un determinado sector:
        // $proximoSector = Organismossector::find($proximaRuta->organismossectors_id);
        // La proximaRuta puede no existir, en caso de que la ruta actual sea la ultima
        if (is_null($proximaRuta)) {
          // asignamos el mismo sector que el actual
          $idProximoSector = $sectoractualexpediente->id;
        } else {
          // Solo necesitamos el id del proximo sector, dado por la proxima ruta
          $idProximoSector = $proximaRuta->id;
        }
      } else {
        $proximaRuta =  $expediente_rutas->first();
        $idProximoSector = $proximaRuta->id;
      }
    }


    return view('expedientes.pase', ['expediente_rutas' => $expediente_rutas, 'dato' => $dato, 'reporte' => $reporte, 'expediente' => $expediente, 'title' => $title,  'sectoractual' => $sectoractual, 'fojas' => $fojas, 'organismos_sectores' => $organismos_sectores, 'idProximoSector' => $idProximoSector, 'orden' => $orden]);
  }

  public function cargarUsuarioSector($id)
  {
    $relations = ['users'];
    $Rutaexpediente =  Expedientesruta::find($id);

    return Organismossectorsuser::where('organismossectors_id', $Rutaexpediente->sector->id)->with($relations)->whereHas('users', function($query) { $query->where('activo', 1); })->get();
  }

  public function cargarUsuarioSectorLibre($id)
  {
    $relations = ['users'];
    $sector =  Organismossector::find($id);

    return Organismossectorsuser::where('organismossectors_id', $sector->id)->with($relations)->get();
  }


  public function cargarTiposSector($id)
  {

    $organismo = Auth::user()->userorganismo->first()->organismos_id;

    // 1 CARGA LOS TIPOS DE DOCUMENTOS SEGUN EL SECTOR
    $tiposexpedientes = DB::table('expedientestipos')
      ->join("expedientesrutas", "expedientesrutas.expedientestipos_id", "=", "expedientestipos.id")
      ->where('expedientestipos.activo', "1")
      ->select("expedientestipos.id", "expedientestipos.expedientestipo")
      ->where('expedientestipos.sin_ruta', "=", 0)
      ->where('expedientesrutas.activo', "1")
      ->where('expedientesrutas.organismossectors_id', '=', $id)
      ->get();

    // EVITAR CARGAR MISMO TIPO VARIAS VECES POR DIFERENTES RUTAS X EL MISMO SECTOR 
    $tiposexpedientes = $tiposexpedientes->unique('expedientestipo');

    // AGREGAR A LA COLECCION LOS TIPOS DE DOCUMENTOS SIN RUTA 
    $tipoexpedientesinruta =  DB::table('expedientestipos')
      ->where('expedientestipos.activo', "1")
      ->select("expedientestipos.id", "expedientestipos.expedientestipo")
      ->where('expedientestipos.organismos_id', $organismo)
      ->where('expedientestipos.sin_ruta', 1)
      ->get();

    //  AGREGAR LOS TIPOS DE DOCUMENTOS LIBRES A LA COLECION 
    foreach ($tipoexpedientesinruta as $c) {
      $tiposexpedientes->push($c);
    }

    return $tiposexpedientes;
  }
  
  /**
   * Este metodo se ejecuta cuando se hace click en el boton guardar de la vista generar pase.
   * Se ejecuta por medio de un codigo js que llama a la ruta que ejecuta este metodo
   * @param  mixed $request
   * @return void
   */
  // public function expedientePase(Request $request)
  // {
  //   // GENERAR PASE DEL DOCUMENTO
  //   DB::beginTransaction();
  //   try {
  //     if (!session('permission')->contains('expediente.pase')) {
  //       session(['status' => 'No tiene acceso para ingresar a este modulo']);
  //       return redirect()->route('index.home');
  //     }

  //     $validator = Validator::make($request->all(), [
  //       'expedientes_id' => 'required',
  //       'expedientesrutas_id' => 'required',  // sector que se hace el pase
  //     ]);

  //     if ($validator->fails()) {
  //       foreach ($validator->messages()->getMessages() as $field_name => $messages) {
  //         foreach ($messages as $message) {
  //           $errors[] = $message;
  //         }
  //       }
  //       return redirect()->back()->with('errors', $errors)->withInput();
  //       die;
  //     }

  //     $expediente = Expediente::find($request->expedientes_id);
  //     // Control Requisitos Obligatorios del sector actual del expediente 
  //     $controlReq = $expediente->expedientesestados->last()->rutasector->requisitos->count();
  //     $requisitos = $expediente->expedientesestados->last()->rutasector->requisitos;
  //     if ($controlReq > 0) {
  //       foreach ($requisitos as $indice => $requisito) {
  //         if ($requisito->obligatorio == 1 && $requisito->activo == 1) {
  //          $idDelRequisito = $requisito->id;
  //           $nombreRequisito = $requisito->expedientesrequisito;
  //           $valorDelRequisito = $request->input($idDelRequisito);
  //           if ($valorDelRequisito == "on") {
  //             # code...
  //           } else {
  //             return response()->json(['mesagge' =>  $nombreRequisito, 'response' => 4]);
  //           }
  //         }
  //       }
  //     }


  //     $tipoExp = ExpedientesTipo::where("id", $request->expedientestipos_id)->first();

  //     if ($tipoExp->sin_ruta == 1) {

  //       $ruta_dinamica = new Expedientesruta;
  //       $ruta_dinamica->expedientestipos_id =  $request->expedientestipos_id;
  //       $ruta_dinamica->organismossectors_id  = $request->expedientesrutas_id;
  //       $ruta_dinamica->save();
  //       //  vuelve a buscar coincidencias dentro de las rutas
  //       $ruta_sector = $ruta_dinamica;
  //     } else {
  //       // buscar dentro de la tabla rutas las coincidencias(el sector y el tipo de expediente )
  //       $ruta_sector = Expedientesruta::where("id", $request->expedientesrutas_id)->where("expedientestipos_id", $request->expedientestipos_id)->first();

  //       // // Control Requisitos Obligatorios  Control hacia adelante sector
  //       // $requisitos = $ruta_sector->requisitos;
  //       // foreach ($requisitos as $indice => $requisito) {
  //       //   if ($requisito->obligatorio == 1) {
  //       //     $nombreRequisito= $requisito->expedientesrequisito;
  //       //     $requisitoID= $requisito->id;
  //       //     if ($request->$requisitoID) {
  //       //       # code...
  //       //     } else {
  //       //       return response()->json(['mesagge' =>  $nombreRequisito, 'response' => 4]);
  //       //     }
  //       //   }
  //       // }
  //     }

  //     if ($ruta_sector == null) {
  //       return response()->json([[3]]);
  //     }





  //     //ver registros estado nuevo o pasado para calcular los dias en show expediente
  //     $ruta = Expedienteestado::where("expedientes_id", $request->expedientes_id)->where("expendientesestado", "nuevo")->get();
  //     $ruta1 = Expedienteestado::where("expedientes_id", $request->expedientes_id)->where("expendientesestado", "pasado")->get();

  //     $usersession_name = Auth::user()->name; // usuario que genera el pase

  //     if ($ruta_sector <> null) {
  //       //actualizacion de campo updated_at para calcular total de dias en cada sector 
  //       if ($ruta1->count() > 0) {
  //         $ultimaruta = $ruta1->last();
  //         $ultimaruta->updated_at = Carbon::now();
  //         $ultimaruta->update();
  //       } elseif ($ruta->count() === 1) {
  //         $ultimaruta = $ruta->last();
  //         $ultimaruta->updated_at = Carbon::now();
  //         $ultimaruta->update();
  //       }

  //       // si el usuario agregó un comentario o no, éste será mostrado en el mail que notifica del pase
  //       if ($request->comentarios == null) {
  //         $comentarioPase = "No posee comentarios";
  //       } else {
  //         $comentarioPase = $request->comentarios;
  //       }

  //       $sectorOrigen = $ultimaruta->rutasector->sector->organismossector; // se guarda el nombre del sector desde el cual se realizó el pase para mostrarlo en el mail de notificacion

  //       //nuevo estados del expediente
  //       $estadoexpediente = new Expedienteestado;
  //       $estadoexpediente->expedientes_id = $request->expedientes_id;
  //       if ($request->users_id <> null) {
  //         $estadoexpediente->users_id = $request->users_id;
  //         //enviar email al usuario que se le asigno el  expediente
  //         $toUser = User::find($request->users_id);

  //         // notificar solo al sector dependiendo del campo notificacion_sector
  //         if ($ruta_sector->sector->notificacion_sector == 0) {
  //           Notification::send($toUser, new PaseExpediente($toUser, $estadoexpediente->id,  $usersession_name, $sectorOrigen, $comentarioPase));
  //         } else {
  //           $sector  = Organismossector::find($ruta_sector->sector->id);
  //           $nombreSector = $sector->organismossector;

  //           if ($sector->email !== NULL) {
  //             try {
  //               Notification::send($sector, new PaseSector($estadoexpediente->id, $nombreSector, $sectorOrigen, $comentarioPase));
  //             }
  //             catch (\Exception $e) {
  //               return response()->json([[5]]);
  //             }
  //           }
  //         }
  //         // notificar solo al sector dependiendo del campo notificacion_sector

  //         $estadoexpediente->notificacion_usuario = 'No leido';
  //       } else {
  //         $estadoexpediente->users_id = NULL;
  //         $estadoexpediente->notificacion_usuario = null;

  //         //enviar email al correo del sector que se le asigno el  expediente ORIGINAL
  //         // $sector  = Organismossector::find($request->expedientesrutas_id);
  //         // $nombreSector = $sector->organismossector;
  //         //enviar email al correo del sector que se le asigno el  expediente ORIGINAL

  //         $sector  = Organismossector::find($ruta_sector->sector->id);
  //         $nombreSector = $sector->organismossector;

  //         if ($sector->email !== NULL) {
  //             try {
  //               Notification::send($sector, new PaseSector($estadoexpediente->id, $nombreSector, $sectorOrigen, $comentarioPase));
  //             }
  //             catch (\Exception $e) {
  //               return response()->json([[5]]);
  //             }
  //         }
  //       }
  //       $estadoexpediente->expendientesestado = 'pasado';
  //       // VERIFICAR SI EL TIPO DE DOCUMENTO ES LIBRE 
  //       $estadoexpediente->expedientesrutas_id = $ruta_sector->id;
  //       $textoLog = Auth::user()->name . " pasó el " . org_nombreDocumento() . " al sector " . $ruta_sector->sector->organismossector  . " a las " . Carbon::now()->toTimeString();
  //       Logg::info($textoLog);
  //       $estadoexpediente->observacion = $textoLog;
  //       $estadoexpediente->comentario_pase = $request->comentarios;
  //       $estadoexpediente->pasado_por = Auth::user()->id;
  //       $estadoexpediente->cantidad_fojas_pase = Foja::where('expedientes_id', $expediente->id)->where('deleted_at', NULL)->count();
  //       $estadoexpediente->save();
  //     } else {
  //       // return  redirect()->back()->with('error', 'El sector al que intenta pasar el expediente no tiene una ruta asignada , verifique su configuración ');
  //       return response()->json([[2]]);
  //     }

  //     // Permite editar la importancia a la hora de realizar el pase
  //     if ($request->expediente_importancia <> '') {
  //       // $expediente = Expediente::find($request->expedientes_id);

  //       $expediente->Importancia = $request->expediente_importancia;
  //       // $expediente->update();
  //     }

  //     $expediente->read_at = 0;
  //     $expediente->update();
     
  //     $expedienteAct = Expediente::find($request->expedientes_id);
  //     foreach ($requisitos as $indice => $requisito) {
        
  //       $requisitoID = $requisito->id;
  //       if ($request->$requisitoID) {
  //         $expedienteAct->expedienteRequisitos()->attach($requisito->id, ['expedientes_id' => $expedienteAct->id,'estado' =>  1]);
  //       } else {
  //         $expedienteAct->expedienteRequisitos()->attach($requisito->id, ['expedientes_id' =>  $expedienteAct->id,'estado' =>  0]);
  //       }
  //     }

  //     DB::commit();
  //     return response()->json([[1]]);
  //     // el pase se genero con exito
  //   } catch (Exception $e) {
  //     DB::rollback();
  //     Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
  //     return response()->json([[6]]);
  //   }
  // }

  public function expedientePase(Request $request)
  {
    // GENERAR PASE DEL DOCUMENTO
    DB::beginTransaction();
    try {
      if (!session('permission')->contains('expediente.pase')) {
        session(['status' => 'No tiene acceso para ingresar a este modulo']);
        return redirect()->route('index.home');
      }

      $validator = Validator::make($request->all(), [
        'expedientes_id' => 'required',
        'expedientesrutas_id' => 'required',  // sector que se hace el pase
      ]);

      if ($validator->fails()) {
        foreach ($validator->messages()->getMessages() as $field_name => $messages) {
          foreach ($messages as $message) {
            $errors[] = $message;
          }
        }
        return redirect()->back()->with('errors', $errors)->withInput();
        die;
      }

      $expediente = Expediente::find($request->expedientes_id);
      // Control Requisitos Obligatorios del sector actual del expediente 
      $controlReq = $expediente->expedientesestados->last()->rutasector->requisitos->count();
      $requisitos = $expediente->expedientesestados->last()->rutasector->requisitos;
      if ($controlReq > 0) {
        foreach ($requisitos as $indice => $requisito) {
          if ($requisito->obligatorio == 1 && $requisito->activo == 1) {
           $idDelRequisito = $requisito->id;
            $nombreRequisito = $requisito->expedientesrequisito;
            $valorDelRequisito = $request->input($idDelRequisito);
            if ($valorDelRequisito == "on") {
              # code...
            } else {
              return response()->json(['mesagge' =>  $nombreRequisito, 'response' => 4]);
            }
          }
        }
      }


      $tipoExp = ExpedientesTipo::where("id", $request->expedientestipos_id)->first();

      if ($tipoExp->sin_ruta == 1) {

        $ruta_dinamica = new Expedientesruta;
        $ruta_dinamica->expedientestipos_id =  $request->expedientestipos_id;
        $ruta_dinamica->organismossectors_id  = $request->expedientesrutas_id;
        $ruta_dinamica->save();
        //  vuelve a buscar coincidencias dentro de las rutas
        $ruta_sector = $ruta_dinamica;
      } else {
        // buscar dentro de la tabla rutas las coincidencias(el sector y el tipo de expediente )
        $ruta_sector = Expedientesruta::where("id", $request->expedientesrutas_id)->where("expedientestipos_id", $request->expedientestipos_id)->first();

        // // Control Requisitos Obligatorios  Control hacia adelante sector
        // $requisitos = $ruta_sector->requisitos;
        // foreach ($requisitos as $indice => $requisito) {
        //   if ($requisito->obligatorio == 1) {
        //     $nombreRequisito= $requisito->expedientesrequisito;
        //     $requisitoID= $requisito->id;
        //     if ($request->$requisitoID) {
        //       # code...
        //     } else {
        //       return response()->json(['mesagge' =>  $nombreRequisito, 'response' => 4]);
        //     }
        //   }
        // }
      }

      if ($ruta_sector == null) {
        return response()->json([[3]]);
      }

      //ver registros estado nuevo o pasado para calcular los dias en show expediente
      $ruta = Expedienteestado::where("expedientes_id", $request->expedientes_id)->where("expendientesestado", "nuevo")->get();
      $ruta1 = Expedienteestado::where("expedientes_id", $request->expedientes_id)->where("expendientesestado", "pasado")->get();

      $usersession_name = Auth::user()->name; // usuario que genera el pase

      if ($ruta_sector <> null) {
        //actualizacion de campo updated_at para calcular total de dias en cada sector 
        if ($ruta1->count() > 0) {
          $ultimaruta = $ruta1->last();
          $ultimaruta->updated_at = Carbon::now();
          $ultimaruta->update();
        } elseif ($ruta->count() === 1) {
          $ultimaruta = $ruta->last();
          $ultimaruta->updated_at = Carbon::now();
          $ultimaruta->update();
        }

        // si el usuario agregó un comentario o no, éste será mostrado en el mail que notifica del pase
        if ($request->comentarios == null) {
          $comentarioPase = "No posee comentarios";
        } else {
          $comentarioPase = $request->comentarios;
        }

        $sectorOrigen = $ultimaruta->rutasector->sector->organismossector; // se guarda el nombre del sector desde el cual se realizó el pase para mostrarlo en el mail de notificacion

        //nuevo estados del expediente
        $estadoexpediente = new Expedienteestado;
        $estadoexpediente->expedientes_id = $request->expedientes_id;
        if ($request->users_id <> null) {
          $estadoexpediente->users_id = $request->users_id;
          //enviar email al usuario que se le asigno el  expediente
          $toUser = User::find($request->users_id);

          $estadoexpediente->notificacion_usuario = 'No leido';
        } else {
          $estadoexpediente->users_id = NULL;
          $estadoexpediente->notificacion_usuario = null;

          //enviar email al correo del sector que se le asigno el  expediente ORIGINAL
          // $sector  = Organismossector::find($request->expedientesrutas_id);
          // $nombreSector = $sector->organismossector;
          //enviar email al correo del sector que se le asigno el  expediente ORIGINAL

          $sector  = Organismossector::find($ruta_sector->sector->id);
          $nombreSector = $sector->organismossector;
        }
        $estadoexpediente->expendientesestado = 'pasado';
        // VERIFICAR SI EL TIPO DE DOCUMENTO ES LIBRE 
        $estadoexpediente->expedientesrutas_id = $ruta_sector->id;
        $textoLog = Auth::user()->name . " pasó el " . org_nombreDocumento() . " al sector " . $ruta_sector->sector->organismossector  . " a las " . Carbon::now()->toTimeString();
        Logg::info($textoLog);
        $estadoexpediente->observacion = $textoLog;
        $estadoexpediente->comentario_pase = $request->comentarios;
        $estadoexpediente->pasado_por = Auth::user()->id;
        $estadoexpediente->cantidad_fojas_pase = Foja::where('expedientes_id', $expediente->id)->where('deleted_at', NULL)->count();
        $estadoexpediente->save();
      } else {
        // return  redirect()->back()->with('error', 'El sector al que intenta pasar el expediente no tiene una ruta asignada , verifique su configuración ');
        return response()->json([[2]]);
      }

      // Permite editar la importancia a la hora de realizar el pase
      if ($request->expediente_importancia <> '') {
        // $expediente = Expediente::find($request->expedientes_id);

        $expediente->Importancia = $request->expediente_importancia;
        // $expediente->update();
      }

      $expediente->read_at = 0;
      $expediente->update();
     
      $expedienteAct = Expediente::find($request->expedientes_id);
      foreach ($requisitos as $indice => $requisito) {
        
        $requisitoID = $requisito->id;
        if ($request->$requisitoID) {
          $expedienteAct->expedienteRequisitos()->attach($requisito->id, ['expedientes_id' => $expedienteAct->id,'estado' =>  1]);
        } else {
          $expedienteAct->expedienteRequisitos()->attach($requisito->id, ['expedientes_id' =>  $expedienteAct->id,'estado' =>  0]);
        }
      }

      // notificar solo al sector dependiendo del campo notificacion_sector
      if ($ruta_sector->sector->notificacion_sector == 0) {
        try {
          Notification::send($toUser, new PaseExpediente($toUser, "Pase Expediente",  $usersession_name, $sectorOrigen, $comentarioPase));
        }
        catch (\Exception $e) {
          DB::commit();
          return response()->json([[1]]);
        }
      } else {
        $sector  = Organismossector::find($ruta_sector->sector->id);
        $nombreSector = $sector->organismossector;

        if ($sector->email !== NULL) {
          try {
            Notification::send($sector, new PaseSector("Pase Sector", $nombreSector, $sectorOrigen, $comentarioPase));
          }
          catch (\Exception $e) {
            DB::commit();
            return response()->json([[1]]);
          }
        }
      }
      // notificar solo al sector dependiendo del campo notificacion_sector

      DB::commit();
      return response()->json([[1]]);
      // el pase se genero con exito
    } catch (Exception $e) {
      DB::rollback();
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return response()->json([[6]]);
    }
  }

  public function create()
  {
    // INICIAR UN NUEVO DOCUMENTO - VALIDACIONES

    // 1- EL USUARIO DEBE TENER UN ORGANISMO
    if (DB::table('organismosusers')->where('users_id', Auth::user()->id)->exists()) {
      $organismouser = Auth::user()->userorganismo->first()->organismos_id;
    } else {
      return  redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' no tiene una organización asignada no puede iniciar documento');
    }

    // 2- EL USUARIO DEBE TENER UN SECTOR ASIGNADO
    if (DB::table('organismossectorsusers')->where('users_id', Auth::user()->id)->exists()) {
      $sectorusers = Auth::user()->usersector->pluck('organismossectors_id')->toArray(); // se obtiene un array con los id de los sectores a los que pertenece el usuario
      $sectororganismo = DB::table('organismossectors')->where('id', $sectorusers[0])->orderBy('organismossectors.organismossector')->get();
    } else {
      return redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' no tiene un sector asignado no puede iniciar documento');
    }

    // 3 - SI ES USUARIO ADMIN PUEDE INICIAR EL DOCUMENTO AUNQUE SU SECTOR NO FIGURE EN NINGUNA RUTA
    //  SI EL SECTOR DONDE ESTA VINCULADO EL USUARIO NO ESTA EN NINGUNA RUTA NO PODRA INICIAR EL DOCUMENTO
    $user_ruta = Expedientesruta::whereIn('organismossectors_id', $sectorusers)->where('activo', 1)->count(); // se busca en la tabla expedientesrutas los sectores a los que pertenece el usuario que esten activos
    $libres_tipo = ExpedientesTipo::where('organismos_id',  $organismouser)->where('sin_ruta', 1)->count();
    if ((!session('permission')->contains('organismos.index.admin')) &&  $user_ruta === 0 && $libres_tipo === 0) {
      return redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' su sector/sectores no puede generar un documento porque no figura en ninguna ruta. Por favor comuniquese con el Administrador');
    }

    // if ((Expedientesruta::where('organismossectors_id', '=', $sectorusers))->count() === 0) {
    //   return redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' su sector no puede generar un documento porque no figura en ninguna ruta. Por favor comuniquese con el Administrador');
    // }

    // 4 TANTO SI ES ADMINISTRADOR COMO UN USUARIO PUEDE TENER VARIOS SECTORES PARA INICIAR EL DOCUMENTO  
    $usuario = Auth::user();
    if (session('permission')->contains('organismos.index.admin') || $usuario->usersector->count() > 1) {
      $idUser = $usuario->id;
      $sectororganismo = DB::table('organismossectorsusers')->join("organismossectors", "organismossectorsusers.organismossectors_id", "=", "organismossectors.id")->where('organismossectors.activo', 1)->where('organismossectorsusers.users_id', "=", $idUser)->orderBy('organismossectors.organismossector')->get();
    }

    // 5 CARGA LOS TIPOS DE DOCUMENTOS SEGUN EL SECTOR DEL USUARIO
    $tiposexpedientes = DB::table('expedientestipos')
      ->join("expedientesrutas", "expedientesrutas.expedientestipos_id", "=", "expedientestipos.id")
      ->where('expedientestipos.activo', "1")
      ->select("expedientestipos.id", "expedientestipos.expedientestipo")
      ->where('expedientestipos.sin_ruta', "=", 0)
      ->where('expedientesrutas.activo', "1")
      ->whereIn('organismossectors_id', $sectorusers)
      ->orderBy('expedientestipos.expedientestipo', 'ASC')
      ->get();

    $tiposexpedientes = $tiposexpedientes->unique("expedientestipo");

    // CARGAR LOS TIPOS DE DOCUMENTOS SIN RUTA
    $tipoexpedientesinruta =  DB::table('expedientestipos')
      ->where('expedientestipos.activo', "1")
      //  ->join("expedientesrutas", "expedientesrutas.expedientestipos_id", "=", "expedientestipos.id")
      ->select("expedientestipos.id", "expedientestipos.expedientestipo")
      ->where('expedientestipos.organismos_id', $organismouser)
      ->where('expedientestipos.sin_ruta', 1)
      ->orderBy('expedientestipos.expedientestipo')
      ->get();

    $personas = DB::table('personas')
      ->where('organismos_id', $organismouser)
      ->orderBy('nombre')
      ->get();

    //  AGREGAR LOS TIPOS DE DOCUMENTOS LIBRES A LA COLECION 
    foreach ($tipoexpedientesinruta as $c) {
      $tiposexpedientes->push($c);
    }
    $tiposexpedientes = $tiposexpedientes->sortBy('expedientestipo');

    $organismo = Organismo::find($organismouser);
    $configOrganismo = $organismo->configuraciones;
    $tiposvinculo = Organismostiposvinculo::where('organismos_id', $organismo->id)
                    ->where('activo', 1)
                    ->orderBy('vinculo')
                    ->get();

    // 6 El proximo numero de expediente lo extraemos de la funcion del ExpedienteHelper
    $proximoNumeroExpediente = getNextExpedienteNumber($organismouser);
    $title = "Nuevo Documento";
    return view('expedientes.create', ['title' => $title, 'tiposexpedientes' => $tiposexpedientes, 'organismouser' => $organismouser, 'sectororganismo' => $sectororganismo, 'proximoNumeroExpediente' => $proximoNumeroExpediente, 'personas' => $personas, 'configOrganismo' => $configOrganismo, 'tiposvinculo' => $tiposvinculo]);
  }

  public function createips()
  {
    // INICIAR UN NUEVO DOCUMENTO - VALIDACIONES

    // 1- EL USUARIO DEBE TENER UN ORGANISMO
    if (DB::table('organismosusers')->where('users_id', Auth::user()->id)->exists()) {
      $organismouser = Auth::user()->userorganismo->first()->organismos_id;
    } else {
      return  redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' no tiene una organización asignada no puede iniciar documento');
    }

    // 2- EL USUARIO DEBE TENER UN SECTOR ASIGNADO
    if (DB::table('organismossectorsusers')->where('users_id', Auth::user()->id)->exists()) {
      $sectorusers = Auth::user()->usersector->pluck('organismossectors_id')->toArray(); // se obtiene un array con los id de los sectores a los que pertenece el usuario
      $sectororganismo = DB::table('organismossectors')->where('activo', 1)->where('id', $sectorusers[0])->orderBy('organismossectors.organismossector')->get();
    } else {
      return redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' no tiene un sector asignado no puede iniciar documento');
    }

    // 3 - SI ES USUARIO ADMIN PUEDE INICIAR EL DOCUMENTO AUNQUE SU SECTOR NO FIGURE EN NINGUNA RUTA
    //  SI EL SECTOR DONDE ESTA VINCULADO EL USUARIO NO ESTA EN NINGUNA RUTA NO PODRA INICIAR EL DOCUMENTO
    $user_ruta = Expedientesruta::whereIn('organismossectors_id', $sectorusers)->where('activo', 1)->count(); // se busca en la tabla expedientesrutas los sectores a los que pertenece el usuario que esten activos
    $libres_tipo = ExpedientesTipo::where('organismos_id',  $organismouser)->where('sin_ruta', 1)->count();
    if ((!session('permission')->contains('organismos.index.admin')) &&  $user_ruta === 0 && $libres_tipo === 0) {
      return redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' su sector/sectores no puede generar un documento porque no figura en ninguna ruta. Por favor comuniquese con el Administrador');
    }

    // if ((Expedientesruta::where('organismossectors_id', '=', $sectorusers))->count() === 0) {
    //   return redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' su sector no puede generar un documento porque no figura en ninguna ruta. Por favor comuniquese con el Administrador');
    // }

    // 4 TANTO SI ES ADMINISTRADOR COMO UN USUARIO PUEDE TENER VARIOS SECTORES PARA INICIAR EL DOCUMENTO  
    $usuario = Auth::user();
    if (session('permission')->contains('organismos.index.admin') || $usuario->usersector->count() > 1) {
      $idUser = $usuario->id;
      $sectororganismo = DB::table('organismossectorsusers')->join("organismossectors", "organismossectorsusers.organismossectors_id", "=", "organismossectors.id")->where('organismossectors.activo', 1)->where('organismossectorsusers.users_id', "=", $idUser)->orderBy('organismossectors.organismossector')->get();
    }

    // 5 CARGA LOS TIPOS DE DOCUMENTOS SEGUN EL SECTOR DEL USUARIO
    $tiposexpedientes = DB::table('expedientestipos')
      ->join("expedientesrutas", "expedientesrutas.expedientestipos_id", "=", "expedientestipos.id")
      ->where('expedientestipos.activo', "1")
      ->select("expedientestipos.id", "expedientestipos.expedientestipo")
      ->where('expedientestipos.sin_ruta', "=", 0)
      ->where('expedientesrutas.activo', "1")
      ->whereIn('organismossectors_id', $sectorusers)
      ->orderBy('expedientestipos.expedientestipo')
      ->get();

    $tiposexpedientes = $tiposexpedientes->unique("expedientestipo");

    // CARGAR LOS TIPOS DE DOCUMENTOS SIN RUTA
    $tipoexpedientesinruta =  DB::table('expedientestipos')
      ->where('expedientestipos.activo', "1")
      //  ->join("expedientesrutas", "expedientesrutas.expedientestipos_id", "=", "expedientestipos.id")
      ->select("expedientestipos.id", "expedientestipos.expedientestipo")
      ->where('expedientestipos.organismos_id', $organismouser)
      ->where('expedientestipos.sin_ruta', 1)
      ->orderBy('expedientestipos.expedientestipo')
      ->get();

    $personas = DB::table('personas')
      ->where('organismos_id', $organismouser)
      ->orderBy('apellido')
      ->get();

    //  AGREGAR LOS TIPOS DE DOCUMENTOS LIBRES A LA COLECION 
    foreach ($tipoexpedientesinruta as $c) {
      $tiposexpedientes->push($c);
    }
    $tiposexpedientes = $tiposexpedientes->sortBy('expedientestipo');

    $organismo = Organismo::find($organismouser);
    $configOrganismo = $organismo->configuraciones;
    $tiposvinculo = Organismostiposvinculo::where('organismos_id', $organismo->id)
                    ->where('activo', 1)
                    ->get();

    // 6 El proximo numero de expediente lo extraemos de la funcion del ExpedienteHelper
    $proximoNumeroExpediente = getNextExpedienteNumber($organismouser);
    $title = "Nuevo Documento";
    return view('expedientes.createips', ['title' => $title, 'tiposexpedientes' => $tiposexpedientes, 'organismouser' => $organismouser, 'sectororganismo' => $sectororganismo, 'proximoNumeroExpediente' => $proximoNumeroExpediente, 'personas' => $personas, 'configOrganismo' => $configOrganismo, 'tiposvinculo' => $tiposvinculo]);
  }

  public function store(Request $request)
  {

    DB::beginTransaction();
    try {
      if (!session('permission')->contains('expediente.editar') and (!session('permission')->contains('expediente.crear'))) {
        session(['status' => 'No tiene acceso para ingresar a este modulo']);
        return redirect()->route('index.home');
      }

      $messages = [
        'tipo_expediente.required' => 'Debe seleccionar un tipo de documento para el documento.',
        'expediente.required' => 'Debe ingresar un nombre/extracto para el documento.',
        'expediente_num.min' => 'No puede ingresar un numero negativo como Nro. del documento.',
        // 'expediente_num.digits_between' => 'El Nro. del documento debe tener 5 dígitos numéricos como máximo.',
      ];

      $validator = Validator::make($request->all(), [
        'tipo_expediente' => 'required',
        'expediente' => 'required',
        'sectorusers' => 'required',
        'fecha_inicio' => 'required',
        'expediente_num' => 'required|numeric|min:0',
      ], $messages);

      if ($validator->fails()) {
        foreach ($validator->messages()->getMessages() as $field_name => $messages) {
          foreach ($messages as $message) {
            $errors[] = $message;
          }
        }
        return response()->json([
          'success' => 'false',
          'errors'  => $errors,
        ], 400);
      }
      // Los expedientes del organismo actual
      $expedientes = Expediente::where('organismos_id', '=', $request->organismos_id);
      $expteNumeroExiste = $expedientes->pluck('expediente_num')->contains($request->expediente_num);
      // $anioActual = Carbon::now()->format('Y');

      // Controlar que no exista cargado un numero de expediente igual para esta organizacion
      // Si el numero de documento existe, se compara el año del existente con el año que ingresa el usuario al momento de crear
      if ($expteNumeroExiste) {
        $expedientetipo = Expedientestipo::find($request->tipo_expediente);
        $tiposexpunique = Expedientestipo::where('repite_num', 0)->get();
        $arraytiposunique = $tiposexpunique->pluck('id')->toArray();
        $comprobacion =  Expediente::where('organismos_id', '=', $request->organismos_id)->where('expediente_num', '=', $request->expediente_num)->get();
        $anioIngresado = date("Y", strtotime($request->fecha_inicio));
        $configuracion = Organismo::find($request->organismos_id)->configuraciones;

        foreach ($comprobacion as $expediente => $value) {
          $anioDoc = Carbon::parse($value->fecha_inicio);
          // if ($anioActual ==  $anioDoc->format('Y')) {

          if ($configuracion->control_ext == 1) {
            if ($anioIngresado ==  $anioDoc->format('Y') && $value->deleted_at == NULL && $value->extension == $request->codigo_input) {
              $errors[0] = 'El numero de documento ' . $request->expediente_num . ' ya existe y está en uso en dicho año.';
              return response()->json([
                'success' => 'false',
                'errors'  => $errors,
              ], 400);
              die;
            }
          }
          else {
            if ($anioIngresado ==  $anioDoc->format('Y') && $value->deleted_at == NULL && ($expedientetipo->repite_num == 0 && (in_array($value->expedientestipos_id, $arraytiposunique)))) {
              $errors[0] = 'El numero de documento ' . $request->expediente_num . ' ya existe y está en uso en dicho año.';
              return response()->json([
                'success' => 'false',
                'errors'  => $errors,
              ], 400);
              die;
            }  
          }

          // if ($anioIngresado ==  $anioDoc->format('Y') && $value->deleted_at == NULL && ($expedientetipo->repite_num == 0 && (in_array($value->expedientestipos_id, $arraytiposunique)))) {
          //   $errors[0] = 'El numero de documento ' . $request->expediente_num . ' ya existe y está en uso en dicho año.';
          //   return response()->json([
          //     'success' => 'false',
          //     'errors'  => $errors,
          //   ], 400);
          //   die;
          // }
        }
      }

      // Guardar los datos de un expediente
      $expediente = new Expediente;
      $expediente->expediente = $request->expediente;
      $expediente->organismos_id = $request->organismos_id;
      $expediente->expedientestipos_id = $request->tipo_expediente;
      $expediente->importancia = $request->expediente_importancia;
      $expediente->sector_inicio = $request->sectorusers;
      $expediente->usuario_inicio = Auth::user()->id;
      $sectorUserExpediente = Organismossector::find($request->sectorusers);
      $codigoDelOrganismo = $sectorUserExpediente->organismos->codigo;
      $expediente->expediente_num = $request->expediente_num;
      $expediente->fecha_inicio = $request->fecha_inicio;
      $expediente->ref_siff = $request->ref_siff;
      $expediente->extension = $request->codigo_input;
      $expediente->save();




      // verificar si la ruta existe , si no existe crea la nueva ruta 
      $ruta_sector = Expedientesruta::where("organismossectors_id", $request->sectorusers)->where("expedientestipos_id", $request->tipo_expediente)->first();
      if ($ruta_sector == null) {
        $ruta_dinamica = new Expedientesruta;
        $ruta_dinamica->expedientestipos_id =  $request->tipo_expediente;
        $ruta_dinamica->organismossectors_id  = $request->sectorusers;
        $ruta_dinamica->save();
        //  vuelve a buscar coincidencias dentro de las rutas
        $ruta_sector = Expedientesruta::where("organismossectors_id", $request->sectorusers)->where("expedientestipos_id", $request->tipo_expediente)->first();
      }

      // si el tipo de documentos es libre se inserta en la tabla Expediente rutas
      // $verificar_ruta = Expedientestipo::find($request->tipo_expediente);
      // dd($verificar_ruta);
      // if($verificar_ruta->sin_ruta == 1){
      //    $ruta_dinamica = New Expedientesruta;
      //    $ruta_dinamica->expedientestipos_id = $request->tipo_expediente;
      //    $ruta_dinamica->organismossectors_id  = $request->sectorusers;
      //    $ruta_dinamica->save();
      // }

      // guardar el sector actual - buscar dentro de rutas las coincidencias de ruta 
      // $usuario_sector = $request->sectorusers;
      // $tiposexpediente_sector = $request->tipo_expediente;
      // $ruta_sector = Expedientesruta::where("organismossectors_id", $usuario_sector)->where("expedientestipos_id", $tiposexpediente_sector)->first();

      //estados del expediente
      $estadoexpediente = new Expedienteestado;
      $estadoexpediente->expedientes_id = $expediente->id;
      $estadoexpediente->users_id =  Auth::user()->id;
      $estadoexpediente->expendientesestado = 'nuevo';
      // ruta libre controlar cuando es libre 
      $estadoexpediente->expedientesrutas_id = $ruta_sector->id;
      $textoLog = "Creó el " . org_nombreDocumento() . " " .  getExpedienteName($expediente)  . " a las " . Carbon::now()->toTimeString();
      Logg::info($textoLog);
      $estadoexpediente->observacion = $textoLog;
      $estadoexpediente->save();

      // Foja Primera del Expediente
      $primeraFoja = new Foja([
        'expedientes_id' => $expediente->id,
        // 'foja' => $expediente->id . '1', 
        // concatenamos el expediente_id con la foja y resulta un string
        'tipofoja' => "texto",
        'texto' => "Caratula del Expediente: " . getExpedienteName($expediente) . ", con extracto: " . $expediente->expediente,
        'file' => null,
        'numero' => 1,
        'nombre' => "",
        'hashPrevio' => "genesis",
        'created_at' => new Carbon,
      ]);

      // CARPETA RAIZ DEL ORGANISMO. Es el id de cada Organismo
      $carpetaRaizOrganismo = $expediente->organismos->id;

      // Crear la ruta a la carpeta del Organismo dado para esta operacion
      // makeDirectory(...) crea el directorio si no existe. Si existe, no hace nada
      Storage::disk('local')->makeDirectory($carpetaRaizOrganismo);

      // Se transforma la foja a pdf. Deberia retornar la ubicacion del archivo y su nombre
      $fojaPathAndName =  $this->crearPrimerFojaCaratula($carpetaRaizOrganismo, $expediente);

      // El path del archivo se recupera. Este $path es una ruta completa o absoluta del servidor o contenedor donde esta la app
      $path = Storage::disk('local')->path($fojaPathAndName);

      // Pdf a imagen. $fojaImagen es el nombre de la foja caratula en este caso, pero con la extension de tipo imagen mas un UUID unico
      $fojaImagen = $this->singlePdfToImage($path, "caratula_" . $expediente->expediente_num . ".pdf");

      // La foja imagen debe estar ahora en:
      $newPath = $carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $expediente->id . DIRECTORY_SEPARATOR . $fojaImagen;

      // Con el path y el nuevo nombre de la foja convertida en imagen, se recupera el contenido de la foja convertida a imagen.
      // Poner cuidado que disco se esta usando.
      $imageContent = Storage::disk('local')->get($newPath);

      /* concatenamos el nuevo nombre de la foja imagen para formar la ruta donde se almacena la foja imagen.
     La ruta consta de:
     - El id del organismo;
     - El id del expediente;
     - El nombre de la foja imagen; */
      $minioPathFojaCaratulaImagen = $carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $expediente->id . DIRECTORY_SEPARATOR . $fojaImagen;

      // Se llama al servidor de imagenes para guardar la foja en el almacenamiento externo
      try {
        // La respuesta es true o verdadera en el caso de que se pueda guardar la imagen en el servidor
        $storageMinioResult = Storage::cloud()->put($minioPathFojaCaratulaImagen, $imageContent);

        if ($storageMinioResult)
        {
          $path_clean = storage_path() . DIRECTORY_SEPARATOR ."app". DIRECTORY_SEPARATOR .$carpetaRaizOrganismo. DIRECTORY_SEPARATOR .$expediente->id; // directorio local del expediente donde se guarda la caratula temporalmente
        }
      } catch (\Exception $e) {
        Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
        return response()->json([
          'success' => 'false',
          'errors'  => ["El servidor de fojas no esta disponible"],
        ], 400);
      }


      // se guarda el hash calculado en el campo hash del expediente
      $hashActual = $this->generarHashSHA256($expediente->makeHashFromFile($path), $primeraFoja->hashPrevio, $primeraFoja->created_at);
      $primeraFoja->hash = $hashActual;
      // Guardar la ruta con el slash comun
      $pathCaratula = $carpetaRaizOrganismo . '/' . $expediente->id . '/' . $fojaImagen;
      $primeraFoja->path =  $pathCaratula;
      // En el caso de quere guardar la url full al recurso foja:
      // $primeraFoja->path =  Storage::disk('minio')->url($pathCaratula);
      $primeraFoja->updated_at = $primeraFoja->created_at;
      $primeraFoja->save();

      if ($storageMinioResult)
      {
        File::cleanDirectory($path_clean); // quita imagen y PDF de la caratula generada para el expediente, ya que se almacena en el minio
      }

      // Se decodifica el array de string pasado por JS
      $personas_vinculo = json_decode($request->personas_vinculo);

      if ($personas_vinculo !== NULL) {

        foreach ($personas_vinculo as $persona_vinculo) {
          if ($persona_vinculo !== "eliminado") {
            if ($persona_vinculo->vinculo_id !== "") {
              $expediente->personas()->attach($persona_vinculo->persona_id, ['organismostiposvinculo_id' => $persona_vinculo->vinculo_id]);
            }
            else {
              $expediente->personas()->attach($persona_vinculo->persona_id);
            }
          }
        }
      }

      // Permite vincular una persona al documento en el momento en que se crea este ultimo
      // if ($request->vincularPersona != NULL) {

        // $personas_id = $request->vincularPersona;
        // $personas_id = array_unique($personas_id);

        // // Funcion para asociar un tipo de vinculo a varias personas o una sola
        // if ($request->tipovinculo != NULL) {
        //   // permite vincular el/los ID de persona al documento que se crea, y ademas, se agrega el ID del tipo de vinculo en la tabla expediente_persona
        //   $expediente->personas()->attach($personas_id, ['organismostiposvinculo_id' => $request->tipovinculo]);
        // }
        // else {
        //   $expediente->personas()->attach($personas_id);
        // }

      // }

      // El commit es necesario cuando se hace una transaccion de DB
      DB::commit();

      // obtengo el ID del ultimo documento creado y se lo pasa por JSON para redireccionar al show despues de su creación
      // $expedientesActuales = Expediente::all();
      // $nroUltimoExpediente = $expedientesActuales->last()->id;


      return response()->json([
        'success' => 1,
        'ultimoExp' => $expediente->id
        // 'ultimoExp' => $nroUltimoExpediente
      ], 201);
    } catch (Exception $exception) {
      DB::rollback();
      Logg::error($exception->getMessage(), ("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()));
      if ($exception instanceof AuthorizationException) {
        return response()->json([
          'success' => 'false',
          'errors'  => ["No posee los permisos para realizar esta acción"],
        ], 400);
      } else {
        return response()->json([
          'success' => 'false',
          'errors'  => ["No se puede crear el documento en este momento"],
        ], 400);
      }
    }
  }

  function controltitular($personasvinculo) {
    if ($personasvinculo !== NULL) {
      $titular = false;

      foreach ($personasvinculo as $personavinculo) {
        if ($personavinculo !== "eliminado" && $personavinculo->vinculo_id !== "") {
          $vinculo = Organismostiposvinculo::find($personavinculo->vinculo_id);

          if ($vinculo->titular == 1) {
            if (!$titular) {
              $titular = $personavinculo;
            } else {
              return false;
            }
          }
        }
      }

      if (!$titular) {
        return false;
      }
      
      return $titular;
    } else {
      return false;
    }
  }
  
  function controlvinculo($personasvinculo) {
    if ($personasvinculo != null) {
      foreach ($personasvinculo as $personavinculo) {
        if ($personavinculo !== "eliminado" && $personavinculo->vinculo_id !== "") {
          $repeat = array_count_values(array_column($personasvinculo, 'vinculo_id'))[$personavinculo->vinculo_id];
          if ($repeat > 1) {
            return false;
          }
        }
      }
    }

    return true;
  }

  // function tipotitularpersona() {
  //   $expedientes = DB::table('expedientes')
  //     ->select('expedientes.id as expediente', 'expediente_persona.persona_id as titular', 'expedientes.expedientestipos_id as tipoexpediente')
  //     ->join('expediente_persona', 'expediente_persona.expediente_id', '=', 'expedientes.id')
  //     ->join('expedientestipos', 'expedientes.expedientestipos_id', '=', 'expedientestipos.id')
  //     ->join('organismostiposvinculo', 'expediente_persona.organismostiposvinculo_id', '=', 'organismostiposvinculo.id')
  //     ->where('expedientestipos.control_cuil', 1)
  //     ->where('expedientes.organismos_id', Auth::user()->userorganismo->first()->organismos_id)
  //     ->where('organismostiposvinculo.titular', 1)
  //     ->get();

  //   if ($expedientes != null) {
  //     foreach ($expedientes as $expediente) {
  //         $expedientetipopersona = new ExpedientetipoPersona;
  //         $expedientetipopersona->expedientestipos_id = $expediente->tipoexpediente;
  //         $expedientetipopersona->personas_id = $expediente->titular;
  //         $expedientetipopersona->save();
  //     }
  //   }
  // }

  public function storeips(Request $request)
  {

    DB::beginTransaction();
    try {
      if (!session('permission')->contains('expediente.crearips')) {
        session(['status' => 'No tiene acceso para ingresar a este modulo']);
        return redirect()->route('index.home');
      }

      $messages = [
        'tipo_expediente.required' => 'Debe seleccionar un tipo de documento para el documento.',
        'expediente.required' => 'Debe ingresar un nombre/extracto para el documento.',
        'expediente_num.min' => 'No puede ingresar un numero negativo como Nro. del documento.',
        // 'expediente_num.digits_between' => 'El Nro. del documento debe tener 5 dígitos numéricos como máximo.',
      ];

      $validator = Validator::make($request->all(), [
        'tipo_expediente' => 'required',
        'expediente' => 'required',
        'sectorusers' => 'required',
        'fecha_inicio' => 'required',
        'expediente_num' => 'required|numeric|min:0',
      ], $messages);

      if ($validator->fails()) {
        foreach ($validator->messages()->getMessages() as $field_name => $messages) {
          foreach ($messages as $message) {
            $errors[] = $message;
          }
        }
        return response()->json([
          'success' => 'false',
          'errors'  => $errors,
        ], 400);
      }

      //control tipo expediente con control de CUIL
      $expedientetipo = Expedientestipo::find($request->tipo_expediente);
      $personasvinculo = json_decode($request->personas_vinculo);
      $titular = null;
      if ($expedientetipo->control_cuil == 1) {
        // control por titular obligatorio y único
        $vinculotitular = $this->controltitular($personasvinculo);
        // dd($vinculotitular);
        if (!$vinculotitular) {
          $errors[0] = 'Debe de seleccionar una persona con vínculo Titular para este tipo de documento.';
          return response()->json([
            'success' => 'false',
            'errors'  => $errors,
          ], 400);
          die;
        }

        //control por titular sin otros documentos relacionados
        $documentocuil = ExpedientetipoPersona::where('personas_id', $vinculotitular->persona_id)->count();

        if ($documentocuil > 0) {
          $errors[0] = 'Esta persona ya se encuentra vinculado como Titular en otro documento';
          return response()->json([
            'success' => 'false',
            'errors'  => $errors,
          ], 400);
          die;
        } else {
          $titular = $vinculotitular->persona_id;
        }
      }

      $controlvinculos = $this->controlvinculo($personasvinculo);
      if (!$controlvinculos) {
        $errors[0] = 'Debe de seleccionar sólo una persona por cada tipo de vínculo.';
        return response()->json([
          'success' => 'false',
          'errors'  => $errors,
        ], 400);
        die;
      }

      // Los expedientes del organismo actual
      $expedientes = Expediente::where('organismos_id', '=', $request->organismos_id);
      $expteNumeroExiste = $expedientes->pluck('expediente_num')->contains($request->expediente_num);
      // $anioActual = Carbon::now()->format('Y');

      // Controlar que no exista cargado un numero de expediente igual para esta organizacion
      // Si el numero de documento existe, se compara el año del existente con el año que ingresa el usuario al momento de crear
      if ($expteNumeroExiste) {
        $expedientetipo = Expedientestipo::find($request->tipo_expediente);
        $tiposexpunique = Expedientestipo::where('repite_num', 0)->get();
        $arraytiposunique = $tiposexpunique->pluck('id')->toArray();
        $comprobacion =  Expediente::where('organismos_id', '=', $request->organismos_id)->where('expediente_num', '=', $request->expediente_num)->get();
        $anioIngresado = date("Y", strtotime($request->fecha_inicio));
        $configuracion = Organismo::find($request->organismos_id)->configuraciones;

        foreach ($comprobacion as $expediente => $value) {
          $anioDoc = Carbon::parse($value->fecha_inicio);
          // if ($anioActual ==  $anioDoc->format('Y')) {

          if ($configuracion->control_ext == 1) {
            if ($anioIngresado ==  $anioDoc->format('Y') && $value->deleted_at == NULL && $value->extension == $request->codigo_input) {
              $errors[0] = 'El numero de documento ' . $request->expediente_num . ' ya existe y está en uso en dicho año.';
              return response()->json([
                'success' => 'false',
                'errors'  => $errors,
              ], 400);
              die;
            }
          } else {
            $errors[0] = 'Debe tener activo el control de extensiones para continuar.';
              return response()->json([
                'success' => 'false',
                'errors'  => $errors,
              ], 400);
              die;
          }
        }
      }

      // Guardar los datos de un expediente
      $expediente = new Expediente;
      $expediente->expediente = $request->expediente;
      $expediente->organismos_id = $request->organismos_id;
      $expediente->expedientestipos_id = $request->tipo_expediente;
      $expediente->importancia = $request->expediente_importancia;
      $expediente->sector_inicio = $request->sectorusers;
      $expediente->usuario_inicio = Auth::user()->id;
      $sectorUserExpediente = Organismossector::find($request->sectorusers);
      $codigoDelOrganismo = $sectorUserExpediente->organismos->codigo;
      $expediente->expediente_num = $request->expediente_num;
      $expediente->fecha_inicio = $request->fecha_inicio;
      $expediente->ref_siff = $request->ref_siff;
      $expediente->extension = $request->codigo_input;
      $expediente->save();


      // verificar si la ruta existe , si no existe crea la nueva ruta 
      $ruta_sector = Expedientesruta::where("organismossectors_id", $request->sectorusers)->where("expedientestipos_id", $request->tipo_expediente)->first();
      if ($ruta_sector == null) {
        $ruta_dinamica = new Expedientesruta;
        $ruta_dinamica->expedientestipos_id =  $request->tipo_expediente;
        $ruta_dinamica->organismossectors_id  = $request->sectorusers;
        $ruta_dinamica->save();
        //  vuelve a buscar coincidencias dentro de las rutas
        $ruta_sector = Expedientesruta::where("organismossectors_id", $request->sectorusers)->where("expedientestipos_id", $request->tipo_expediente)->first();
      }

      // si el tipo de documentos es libre se inserta en la tabla Expediente rutas
      // $verificar_ruta = Expedientestipo::find($request->tipo_expediente);
      // dd($verificar_ruta);
      // if($verificar_ruta->sin_ruta == 1){
      //    $ruta_dinamica = New Expedientesruta;
      //    $ruta_dinamica->expedientestipos_id = $request->tipo_expediente;
      //    $ruta_dinamica->organismossectors_id  = $request->sectorusers;
      //    $ruta_dinamica->save();
      // }

      // guardar el sector actual - buscar dentro de rutas las coincidencias de ruta 
      // $usuario_sector = $request->sectorusers;
      // $tiposexpediente_sector = $request->tipo_expediente;
      // $ruta_sector = Expedientesruta::where("organismossectors_id", $usuario_sector)->where("expedientestipos_id", $tiposexpediente_sector)->first();

        if ($expedientetipo->control_cuil == 1) {
          if ($titular != null) {
            $expedientetipopersona = new ExpedientetipoPersona;
            $expedientetipopersona->expedientestipos_id = $request->tipo_expediente;
            $expedientetipopersona->personas_id = $titular;
            $expedientetipopersona->save();
          }
        }

      //estados del expediente
      $estadoexpediente = new Expedienteestado;
      $estadoexpediente->expedientes_id = $expediente->id;
      $estadoexpediente->users_id =  Auth::user()->id;
      $estadoexpediente->expendientesestado = 'nuevo';
      // ruta libre controlar cuando es libre 
      $estadoexpediente->expedientesrutas_id = $ruta_sector->id;
      $textoLog = "Creó el " . org_nombreDocumento() . " " .  getExpedienteName($expediente)  . " a las " . Carbon::now()->toTimeString();
      Logg::info($textoLog);
      $estadoexpediente->observacion = $textoLog;
      $estadoexpediente->save();

      // Foja Primera del Expediente
      $primeraFoja = new Foja([
        'expedientes_id' => $expediente->id,
        // 'foja' => $expediente->id . '1', 
        // concatenamos el expediente_id con la foja y resulta un string
        'tipofoja' => "texto",
        'texto' => "Caratula del Expediente: " . getExpedienteName($expediente) . ", con extracto: " . $expediente->expediente,
        'file' => null,
        'numero' => 1,
        'nombre' => "",
        'hashPrevio' => "genesis",
        'created_at' => new Carbon,
      ]);

      // CARPETA RAIZ DEL ORGANISMO. Es el id de cada Organismo
      $carpetaRaizOrganismo = $expediente->organismos->id;

      // Crear la ruta a la carpeta del Organismo dado para esta operacion
      // makeDirectory(...) crea el directorio si no existe. Si existe, no hace nada
      Storage::disk('local')->makeDirectory($carpetaRaizOrganismo);

      // Se transforma la foja a pdf. Deberia retornar la ubicacion del archivo y su nombre
      $fojaPathAndName =  $this->crearPrimerFojaCaratula($carpetaRaizOrganismo, $expediente);

      // El path del archivo se recupera. Este $path es una ruta completa o absoluta del servidor o contenedor donde esta la app
      $path = Storage::disk('local')->path($fojaPathAndName);

      // Pdf a imagen. $fojaImagen es el nombre de la foja caratula en este caso, pero con la extension de tipo imagen mas un UUID unico
      $fojaImagen = $this->singlePdfToImage($path, "caratula_" . $expediente->expediente_num . ".pdf");

      // La foja imagen debe estar ahora en:
      $newPath = $carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $expediente->id . DIRECTORY_SEPARATOR . $fojaImagen;

      // Con el path y el nuevo nombre de la foja convertida en imagen, se recupera el contenido de la foja convertida a imagen.
      // Poner cuidado que disco se esta usando.
      $imageContent = Storage::disk('local')->get($newPath);

      /* concatenamos el nuevo nombre de la foja imagen para formar la ruta donde se almacena la foja imagen.
     La ruta consta de:
     - El id del organismo;
     - El id del expediente;
     - El nombre de la foja imagen; */
      $minioPathFojaCaratulaImagen = $carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $expediente->id . DIRECTORY_SEPARATOR . $fojaImagen;

      // Se llama al servidor de imagenes para guardar la foja en el almacenamiento externo
      try {
        // La respuesta es true o verdadera en el caso de que se pueda guardar la imagen en el servidor
        $storageMinioResult = Storage::cloud()->put($minioPathFojaCaratulaImagen, $imageContent);

        if ($storageMinioResult)
        {
          $path_clean = storage_path() . DIRECTORY_SEPARATOR ."app". DIRECTORY_SEPARATOR .$carpetaRaizOrganismo. DIRECTORY_SEPARATOR .$expediente->id; // directorio local del expediente donde se guarda la caratula temporalmente
        }
      } catch (\Exception $e) {
        Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
        return response()->json([
          'success' => 'false',
          'errors'  => ["El servidor de fojas no esta disponible"],
        ], 400);
      }


      // se guarda el hash calculado en el campo hash del expediente
      $hashActual = $this->generarHashSHA256($expediente->makeHashFromFile($path), $primeraFoja->hashPrevio, $primeraFoja->created_at);
      $primeraFoja->hash = $hashActual;
      // Guardar la ruta con el slash comun
      $pathCaratula = $carpetaRaizOrganismo . '/' . $expediente->id . '/' . $fojaImagen;
      $primeraFoja->path =  $pathCaratula;
      // En el caso de quere guardar la url full al recurso foja:
      // $primeraFoja->path =  Storage::disk('minio')->url($pathCaratula);
      $primeraFoja->updated_at = $primeraFoja->created_at;
      $primeraFoja->save();

      if ($storageMinioResult)
      {
        File::cleanDirectory($path_clean); // quita imagen y PDF de la caratula generada para el expediente, ya que se almacena en el minio
      }

      // Se decodifica el array de string pasado por JS
      $personas_vinculo = json_decode($request->personas_vinculo);

      if ($personas_vinculo !== NULL) {

        foreach ($personas_vinculo as $persona_vinculo) {
          if ($persona_vinculo !== "eliminado") {
            if ($persona_vinculo->vinculo_id !== "") {
              $expediente->personas()->attach($persona_vinculo->persona_id, ['organismostiposvinculo_id' => $persona_vinculo->vinculo_id]);
            }
            else {
              $expediente->personas()->attach($persona_vinculo->persona_id);
            }
          }
        }
      }

      // Permite vincular una persona al documento en el momento en que se crea este ultimo
      // if ($request->vincularPersona != NULL) {

        // $personas_id = $request->vincularPersona;
        // $personas_id = array_unique($personas_id);

        // // Funcion para asociar un tipo de vinculo a varias personas o una sola
        // if ($request->tipovinculo != NULL) {
        //   // permite vincular el/los ID de persona al documento que se crea, y ademas, se agrega el ID del tipo de vinculo en la tabla expediente_persona
        //   $expediente->personas()->attach($personas_id, ['organismostiposvinculo_id' => $request->tipovinculo]);
        // }
        // else {
        //   $expediente->personas()->attach($personas_id);
        // }

      // }

      // El commit es necesario cuando se hace una transaccion de DB
      DB::commit();

      // obtengo el ID del ultimo documento creado y se lo pasa por JSON para redireccionar al show despues de su creación
      // $expedientesActuales = Expediente::all();
      // $nroUltimoExpediente = $expedientesActuales->last()->id;


      return response()->json([
        'success' => 1,
        'ultimoExp' => $expediente->id
        // 'ultimoExp' => $nroUltimoExpediente
      ], 201);
    } catch (Exception $exception) {
      DB::rollback();
      dd($exception);
      Logg::error($exception->getMessage(), ("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()));
      if ($exception instanceof AuthorizationException) {
        return response()->json([
          'success' => 'false',
          'errors'  => ["No posee los permisos para realizar esta acción"],
        ], 400);
      } else {
        return response()->json([
          'success' => 'false',
          'errors'  => ["No se puede crear el documento en este momento"],
        ], 400);
      }
    }
  }

  public function printpdf($id)
  {
    
    ini_set('max_execution_time', '-1'); // Establece el límite de tiempo de ejecución a ilimitado
    ini_set('memory_limit', '1024M'); // Establece el límite de memoria a 1024 MB
    $id = base64_decode($id);

    $name = getExpedienteName(Expediente::findOrFail($id));
    $datosexpediente = DB::table('expedientes')
      ->select(
        'expedientes.id as id',
        'expedientes.fecha_inicio as fecha_inicio',
        'expedientes.expediente as expediente',
        'expedientes.expediente_num as expediente_num',
        'organismos.localidads_id as localidads_id',
        'organismos.organismo as organismo',
        'organismos.id as organismoid',
        'organismos.logo as logo',
        'organismos.direccion as direccion',
        'localidads.localidad as localidad',
        'expedientestipos.color as color',
        DB::raw('(SELECT MAX(id) FROM expendientesestados WHERE expendientesestados.expedientes_id = expedientes.id) as lastestado'),
        DB::raw('(SELECT expedientesrutas_id FROM expendientesestados WHERE expendientesestados.id = lastestado) as rutaid'),
        DB::raw('(SELECT organismossectors_id FROM expedientesrutas WHERE expedientesrutas.id = rutaid) as rutasectorid'),
        DB::raw('(SELECT organismossector FROM organismossectors WHERE organismossectors.id = rutasectorid) as organismossector'),
        DB::raw('(SELECT telefono FROM organismossectors WHERE organismossectors.id = rutasectorid) as telefonosector'),
        DB::raw('(SELECT email FROM organismossectors WHERE organismossectors.id = rutasectorid) as emailsector'),
      )
      ->join("organismos", "expedientes.organismos_id", "=", "organismos.id")
      ->join("localidads", "organismos.localidads_id", "=", "localidads.id")
      ->join("expedientestipos", "expedientes.expedientestipos_id", "=", "expedientestipos.id")
      ->join("expendientesestados", "expendientesestados.expedientes_id", "=", "expedientes.id")
      ->join("expedientesrutas", "expendientesestados.expedientesrutas_id", "=", "expedientesrutas.id")
      ->join("organismossectors", "expedientesrutas.organismossectors_id", "=", "organismossectors.id")
      ->where('expedientes.id', $id)
      ->first();

    $fojas = DB::table('fojas')
      ->select(
        'fojas.path',
        'fojas.created_at',
        'fojas.users_id',
        'users.name',
        'fojas.deleted_at',
        'fojas.numero',
        'fojas.id'
      )
      ->leftJoin("users", "fojas.users_id", "=", "users.id")
      ->where('expedientes_id', $id)
      ->where('fojas.deleted_at', null)
      ->orderBy('numero')
      ->get();

    $configuraciones = DB::table('configuracions')
      ->select(
        'configuracions.foja_user',
        'configuracions.expediente_num',
        'configuracions.foja_num',
        'configuracions.sector',
        'configuracions.sector_telefono',
        'configuracions.sector_correo',
        'configuracions.foja_fecha',
        'configuracions.foja_hora',
      )
      ->where('organismos_id', $datosexpediente->organismoid)
      ->first();

    $id = base64_decode($id);
    // set style for barcode
    $style = array(
      'border' => 0,
      'vpadding' => 'auto',
      'hpadding' => 'auto',
      'fgcolor' => array(0, 0, 0),
      'bgcolor' => false, //array(255,255,255)
      'module_width' => 1, // width of a single module in points
      'module_height' => 1 // height of a single module in points
    );

    $fecha = Carbon::parse($datosexpediente->fecha_inicio);
    $afecha = $fecha->year;

    PDF::SetTitle('Documento');
    PDF::AddPage();
    $bMargin = PDF::getBreakMargin();
    $auto_page_break = PDF::getAutoPageBreak();
    PDF::SetAutoPageBreak(false, 0);
    PDF::Image('images/caratula.jpg', 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
    PDF::SetAutoPageBreak($auto_page_break, $bMargin);
    PDF::setPageMark();

    if ($datosexpediente->color != null) {
      $hex = $datosexpediente->color;
      list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");

      PDF::SetFillColor($r, $g, $b);
      PDF::Rect(190, 0, 8, 80, 'F');
      //  PDF::Rect(118, 75, 100 , 8, 'F');
    }

    PDF::SetFont('helvetica', '', 7);

    $id_url = base64_decode($id);
    $url = url('expediente/' . $id_url);
    //el codigo Qr debe direccionar al show del expediente 
    // PDF::write2DBarcode($url, 'QRCODE,L', 85, 170, 35, 35, $style, 'N');
    PDF::write2DBarcode($url, 'QRCODE,L', 85, 195, 35, 35, $style, 'N');

    // PDF::SetFont('helvetica', 'B', 8);
    // PDF::MultiCell(120, 5, $fecha->format('d-m-Y'), 0, 'L', 0, 1, 42, 32, true);
    // PDF::MultiCell(120, 5, $fecha->format('H:i:s'), 0, 'L', 0, 1, 42, 38, true);

    PDF::SetFont('helvetica', 'B', 12);
    // PDF::Cell(0, 60, $expediente->organismos->organismo, 0, false, 'C', 0, '', 0, false, 'T', 'M');
    // PDF::MultiCell(120, 5, $expediente->organismos->organismo, 0, 'L', 0, 1, 58, 70, true);

    PDF::SetFont('helvetica', 'B', 12);
    PDF::MultiCell(120, 5, $name, 0, 'L', 0, 1, 48, 109, true);
    PDF::MultiCell(120, 5, $datosexpediente->organismo, 0, 'L', 0, 1, 50, 120, true);

    PDF::MultiCell(120, 5, $datosexpediente->expediente, 0, 'L', 0, 1, 52, 131, true);
    // PDF::MultiCell(120, 5, $organismo_localidad->localidad, 0, 'L', 0, 1, 53, 142, true);
    PDF::MultiCell(120, 5, $datosexpediente->localidad, 0, 'L', 0, 1, 53, 167, true);
    // PDF::MultiCell(120, 5, $fecha->format('d-m-Y'), 0, 'L', 0, 1, 55, 156, true);
    PDF::MultiCell(120, 5, $fecha->format('d-m-Y'), 0, 'L', 0, 1, 55, 179, true);

    // PDF::MultiCell(120, 5, $afecha, 0, 'L', 0, 1, 103, 206, true);
    PDF::MultiCell(120, 5, $afecha, 0, 'L', 0, 1, 103, 229, true);

    // fojas del expediente
    foreach ($fojas as $key => $data) {

      if ($key > 0) {
        PDF::AddPage();
        PDF::SetAutoPageBreak(false, 0);

        // $logo_foja = Expediente::find($expediente->id)->organismos->logo;
        // $html = '
        // <div>
        //    <img src="'.Storage::disk('public')->path($logo_foja).'" style="height:45px;width:50px"/>
        // </div>';
        // PDF::writeHTML($html, true, false, true, false, '');

        // cabecera de fojas 
        // Verificar si existe logo
        if (!is_null($datosexpediente->logo)) {
          PDF::Image(Storage::disk('public')->path($datosexpediente->logo), 20, 10, 15, '', '', '', '', false, 300, '', false, false, 0, false, false, false);
        }
        PDF::SetY(-20);
        PDF::SetFont('helvetica', '', 10);
        PDF::MultiCell(120, 5,  $datosexpediente->organismo, 0, 'L', 0, 1, 38, 15, true);

        PDF::MultiCell(120, 5,  $datosexpediente->direccion, 0, 'R', 0, 1, 80, 11, true);
        PDF::MultiCell(120, 5,  $datosexpediente->localidad, 0, 'R', 0, 1, 80, 15, true);

        //obtener la ruta de fojas(contenido -foja)
        //PDF::Image(Storage::disk('local')->path($data->path), 20, 25, 180, 250, '', '', '', false, 300, '', false, false, 0);

        // La sintaxis @ le dice a TCPDF que el origen es un stream y no un nombre o ruta de imagen.
        // Fuente: https://tcpdf.org/examples/example_009/
        // The '@' character is used to indicate that follows an image data stream and not an image file name
        //$pdf->Image('@'.$imgdata);
        // Image method signature:
        // Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)

        PDF::Image('@' . Storage::cloud()->get($data->path), 20, 25, 180, 0, '', '', '', false, 300, '', false, false, 0);

        // pie de pagina --> A partir de la configuracion que establece el administrador de cada organismo, se muestran los datos en el pie de pagina 
        //                   de las fojas cuando se da a la opcion de generar PDF

        if ($configuraciones->foja_user == true && $data->name !== null) {
          PDF::MultiCell(120, 5, "Cargada por: " .$data->name, 0, 'L', 0, 1, 20, 285, true);
        }

        if ($configuraciones->expediente_num) {
          PDF::SetY(-15);
          PDF::SetFont('helvetica', '', 10);
          PDF::MultiCell(120, 5, 'Doc N°: ' . $name, 0, 'L', 0, 1, 85, 285, true);
        }

        if ($configuraciones->foja_num) {
          PDF::SetY(-17);
          PDF::SetFont('helvetica', '', 10);
          // PDF::Cell(0, 0, $key + 1, 0, false, 'R', 0, '', 0, false, 'T', 'M');
          PDF::MultiCell(120, 5, $key + 1, 0, 'R', 0, 1, 80, 285, true);
        }

        if ($configuraciones->sector) {
          PDF::SetY(-15);
          PDF::SetFont('helvetica', '', 10);
          PDF::MultiCell(120, 5, $datosexpediente->organismossector, 0, 'L', 0, 1, 20, 290, true);
        }

        if ($configuraciones->sector_telefono) {
          PDF::SetY(-15);
          PDF::SetFont('helvetica', '', 10);
          PDF::MultiCell(120, 5, ("Telefono:" . $datosexpediente->telefonosector), 0, 'L', 0, 1, 90, 290, true);
        }

        if ($configuraciones->sector_correo) {
          PDF::SetY(-15);
          PDF::SetFont('helvetica', '', 10);
          PDF::MultiCell(120, 5, ("Correo:" . $datosexpediente->emailsector), 0, 'R', 0, 1, 80, 290, true);
        }

        if ($configuraciones->foja_fecha == 1 && $configuraciones->foja_hora == 0) {
          $fechaFoja = Carbon::parse($data->created_at);
          PDF::SetY(-15);
          PDF::SetFont('helvetica', '', 10);
          PDF::MultiCell(120, 5, $fechaFoja->format('d-m-Y'), 0, 'R', 0, 1, 80, 19, true);
        } else if ($configuraciones->foja_hora == 1 && $configuraciones->foja_fecha == 0) {
          $fechaFoja = Carbon::parse($data->created_at);
          PDF::SetY(-15);
          PDF::SetFont('helvetica', '', 10);
          PDF::MultiCell(120, 5, $fechaFoja->format('d-m-Y - h:i:s'), 0, 'R', 0, 1, 80, 19, true);
        } else if ($configuraciones->foja_fecha == 1 && $configuraciones->foja_hora == 1) {
          $fechaFoja = Carbon::parse($data->created_at);
          PDF::SetY(-15);
          PDF::SetFont('helvetica', '', 10);
          PDF::MultiCell(120, 5, $fechaFoja->format('d-m-Y - h:i:s'), 0, 'R', 0, 1, 80, 19, true);
        }
      }
    } // end del foreach

    PDF::Output('expediente-' . $configuraciones->expediente_num . '.pdf');
    ini_set('max_execution_time', '120'); // Establece el límite de tiempo de ejecución de 120
    ini_set('memory_limit', '512M'); // Establece el límite de memoria a 512 MB
    die;

  }

  public function indexpdfcustom(Request $request, $id)
  {
    if (!session('permission')->contains('expediente.printpdfcustom') && !session('permission')->contains('organismos.index.admin')) {
      return redirect()->route('expedientes.index')->with('errors', ['No tiene permisos para notificar expedientes']);
    }

    try
    {
      $id = base64_decode($id);
      $expediente = Expediente::findOrFail($id);
      $title = "Notificar Via Email";

      $textoLog = Auth::user()->name . " ingreso a la sección de notificar PDF via email del " . org_nombreDocumento() . " a las " . Carbon::now()->toTimeString();
      historial_doc(($expediente->id),$textoLog);

      $fojas = Foja::select('id', 'numero', 'nombre')
                    ->where('expedientes_id', $id)
                    ->orderBy('numero')
                    ->get();
      $personas = $expediente->personas;
    }
    catch (\Exception $e)
    {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      session()->flash('error', 'Ocurrió un problema al ingresar a la sección');
      return redirect('/');
    }

    return view('expedientes.indexpdfcustom', ['expediente' => $expediente, 'personas' => $personas, 'fojas' => $fojas, 'title' => $title]);
  }

  public function printpdfcustom(FojasSelectedRequest $request)
  {
    ini_set('max_execution_time', '-1'); // Establece el límite de tiempo de ejecución a ilimitado
    ini_set('memory_limit', '1024M'); // Establece el límite de memoria a 1024 MB
    $id = $request->expediente_id;
    $expediente = Expediente::findOrFail($id);
    $expedientename = getExpedienteName($expediente);
    $emailPersonas = [];

    if (session('permission')->contains('expediente.printpdfcustom.manual')) {
      $personasnotificar = json_decode($request->correos_notificar);
      $correos = [];
      if ($personasnotificar !== null) {
        foreach ($personasnotificar as $personanotificar) {
          if ($personanotificar !== 'eliminado') {
            if ($personanotificar->persona_id != 0) {
              array_push($correos, Persona::select('correo')->where('id', $personanotificar->persona_id)->pluck('correo')->first());
            } else {
              array_push($correos, $personanotificar->correo);
            }
          }
        }
      }
      $emailPersonas = $correos;
    } else {
      if ($request->notificar_personas != null) {
        $emailPersonas = Persona::select('correo')
        ->whereIn('id', $request->notificar_personas)
        ->pluck('correo')
        ->toArray();
      }
    }

    if ($emailPersonas == []) {
      session()->flash('error', 'Seleccionar una persona a notificar para continuar.');

      return redirect()->back();
    }
    
    $datosexpediente = DB::table('expedientes')
      ->select(
        'expedientes.id as id',
        'expedientes.expediente as expediente',
        'expedientes.fecha_inicio as fecha_inicio',
        'organismos.organismo as organismo',
        'organismos.localidads_id as localidads_id',
        'localidads.localidad as localidad',
        'organismos.logo as logo',
        'organismos.organismo as organismo',
        'organismos.id as organismoid',
        'organismos.direccion as direccion',
      )
      ->join("organismos", "expedientes.organismos_id", "=", "organismos.id")
      ->join("localidads", "organismos.localidads_id", "=", "localidads.id")
      ->where('expedientes.id', $id)
      ->first();

    $fojas = DB::table('fojas')
      ->select('fojas.path')
      ->whereIn('id', $request->mychecks)
      ->orderBy('numero')
      ->get();

    // set style for barcode
    $style = array(
      'border' => 0,
      'vpadding' => 'auto',
      'hpadding' => 'auto',
      'fgcolor' => array(0, 0, 0),
      'bgcolor' => false, //array(255,255,255)
      'module_width' => 1, // width of a single module in points
      'module_height' => 1 // height of a single module in points
    );

    $fecha = Carbon::parse($expediente->fecha_inicio);
    $afecha = $fecha->year;

    PDF::SetTitle('Documento');
    PDF::AddPage();
    $bMargin = PDF::getBreakMargin();
    $auto_page_break = PDF::getAutoPageBreak();
    PDF::SetAutoPageBreak(false, 0);
    PDF::Image('images/caratula.jpg', 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
    PDF::SetAutoPageBreak($auto_page_break, $bMargin);
    PDF::setPageMark();

    PDF::SetFont('helvetica', '', 7);

    $id_url = base64_decode($id);
    $url = url('expediente/' . $id_url);
    //el codigo Qr debe direccionar al show del expediente 
    PDF::write2DBarcode($url, 'QRCODE,L', 85, 195, 35, 35, $style, 'N');

    PDF::SetFont('helvetica', 'B', 12);

    PDF::SetFont('helvetica', 'B', 12);
    PDF::MultiCell(120, 5, $expedientename, 0, 'L', 0, 1, 48, 109, true);
    PDF::MultiCell(120, 5, $datosexpediente->organismo, 0, 'L', 0, 1, 50, 120, true);

    PDF::MultiCell(120, 5, $datosexpediente->expediente, 0, 'L', 0, 1, 52, 131, true);
    PDF::MultiCell(120, 5, $datosexpediente->localidad, 0, 'L', 0, 1, 53, 167, true);
    PDF::MultiCell(120, 5, $fecha->format('d-m-Y'), 0, 'L', 0, 1, 55, 179, true);

    PDF::MultiCell(120, 5, $afecha, 0, 'L', 0, 1, 103, 229, true);


    // fojas del expediente
    foreach ($fojas as $key => $data) {

      PDF::AddPage();
      PDF::SetAutoPageBreak(false, 0);

      // // cabecera de fojas 
      // Verificar si existe logo
      if (!is_null($datosexpediente->logo)) {
        PDF::Image(Storage::disk('public')->path($datosexpediente->logo), 20, 10, 15, '', '', '', '', false, 300, '', false, false, 0, false, false, false);
      }
      PDF::SetY(-20);
      PDF::SetFont('helvetica', '', 10);
      PDF::MultiCell(120, 5,  $datosexpediente->organismo, 0, 'L', 0, 1, 38, 15, true);

      PDF::MultiCell(120, 5,  $datosexpediente->direccion, 0, 'R', 0, 1, 80, 11, true);
      PDF::MultiCell(120, 5,  $datosexpediente->localidad, 0, 'R', 0, 1, 80, 15, true);

      PDF::Image('@' . Storage::cloud()->get($data->path), 20, 25, 180, 0, '', '', '', false, 300, '', false, false, 0);

      PDF::SetY(-15);
      PDF::SetFont('helvetica', '', 10);
      PDF::MultiCell(120, 5, 'Doc N°: ' . $expedientename, 0, 'L', 0, 1, 85, 285, true);
    } // end del foreach

    $carpetaRaiz = $datosexpediente->organismoid . DIRECTORY_SEPARATOR . $datosexpediente->id;
    Storage::disk('local')->makeDirectory($carpetaRaiz);
    $path = storage_path('app') . DIRECTORY_SEPARATOR . $carpetaRaiz . DIRECTORY_SEPARATOR . $expedientename . '.pdf';

    try
    {
      PDF::Output($path, 'F'); // se guarda el PDF generado en el directorio correspondiente
      $attachment = file_get_contents($path); // se obtiene el contenido del PDF

      $this->emailAdjunto($path, $expediente, $emailPersonas, $attachment); // trait que permite enviar el email y su adjunto a las personas vinculadas seleccionadas
      
      $textoLog = Auth::user()->name . " notificó vía email el " . org_nombreDocumento() . " personalizado en formato PDF a las " . Carbon::now()->toTimeString();
      historial_doc(($datosexpediente->id),$textoLog);
    }
    catch (\Exception $e)
    {
      if (file_exists($path))
      {
        unlink($path);
      }

      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      session()->flash('error', 'Ocurrió un problema al intentar enviar el PDF del expediente');

      return redirect()->back();
    }

    session()->flash('success', 'Se compartió el PDF del documento correctamente');
    ini_set('max_execution_time', '60'); // Establece el límite de tiempo de ejecución a 60
    ini_set('memory_limit', '512M'); // Establece el límite de memoria a 512 MB
    return redirect()->back();
  }

  public function historial($id)
  {
    $id = base64_decode($id);

    // if (!session('permission')->contains('expediente.historial')) {
    //   session(['status' => 'No tiene permisos para ver historial del expediente']);
    //   return redirect()->route('index.home');
    // }
    $relations = ['organismos', 'expedientetipo'];
    $expedientes = Expediente::find($id)->with($relations)->first();
    $datosexpediente = Expediente::find($id);
    $localidad = Localidad::find($expedientes->organismos->localidads_id);
    $estados_exp = $datosexpediente->expedientesestados()->orderBy('id', 'desc')->get();

    $ultimoEstado = $estados_exp->first();

    if ( $ultimoEstado->expendientesestado != "pasado") {
      $textoLog = Auth::user()->name . " consulto el historial del " . org_nombreDocumento() . " a las " . Carbon::now()->toTimeString(); 
      historial_doc(($datosexpediente->id),$textoLog,null,null);
    }

    // $title = "Historial Documento Nº " . $expedientes->expediente_num;
    $title = "Historial Documento Nº " . getExpedienteName($datosexpediente);
    return view('expedientes.historial', ['title' => $title, 'expedientes' => $expedientes, 'localidad' => $localidad, 'datosexpediente' => $datosexpediente, 'estados_exp' => $estados_exp]);
  }

  public function historialRequisitos($id)
  {
    $datosexpediente = Expediente::where("id",$id)->first();
    $datosRequisitos = $datosexpediente->expedienteRequisitos()->get();
    $localidad = Localidad::find($datosexpediente->organismos->localidads_id);
    
    

    $title = "Historial Requisitos Documento " . getExpedienteName($datosexpediente);
    return view('expedientes.requisitos', ['title' => $title, 'datosexpediente' => $datosexpediente, 'datosRequisitos' => $datosRequisitos, 'localidad' => $localidad,]);
  }

  public function historialpdf($id)
  {

    $fecha_actual = Carbon::now();
    $datosexpediente = Expediente::find($id);
    $localidad = Localidad::find($datosexpediente->organismos->localidads_id);
    $estados_exp = $datosexpediente->expedientesestados->sortBy('created_at');

    return \DOMPDF::loadView('expedientes.historialpdf', compact('fecha_actual', 'datosexpediente', 'localidad', 'estados_exp'))->stream('historial.pdf');
  }

  public function etiquetas($expediente_id)
  {
    $session = session('permission');

    if (!$session->contains('expediente.etiqueta') && !$session->contains('expediente.etiqueta.sector')) {
      //session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('expedientes.index')->with('errors', ["No tiene acceso para ingresar a este modulo"]);
    }

    $expediente_id = base64_decode($expediente_id);
    $expediente = Expediente::findOrFail($expediente_id);
    $organismo = Organismo::findOrfail($expediente->organismos_id);
    $activas = 1;
    // Las etiquetas del organismo que aun no estan vinculadas a este expediente. Organismosetiquetas es la tabla que podria llamarse "etiquetas". Son las una o muchas etiquetas de un organismo
    $etiquetasSinVincular = Organismosetiqueta::whereDoesntHave('expedientes', function ($q) use ($expediente_id) {
      $q->where('expediente_id', $expediente_id);
    })
      ->where('organismos_id', $organismo->id)
      ->get();
    
    // si el usuario tiene el permiso de etiquetas de su sector, se deja ver etiquetas solo si:
    // 1. el sector del usuario es el mismo que el sector donde se creó la etiqueta
    // 2. la etiqueta es global
    if (session('permission')->contains('expediente.etiqueta.sector')) {
      $sectoractual = $expediente->expedientesestados->last()->rutasector->sector->id;
      $global = NULL;
      $sectoresUsuario = Auth::user()->usersector->pluck('organismossectors_id')->toArray(); // etiquetas solo de los sectores que pertenece el usuario (PRUEBA)

      // etiquetas solo de los sectores que pertenece el usuario (PRUEBA)
      $etiquetasSinVincularPorSector = $etiquetasSinVincular->filter(function ($etiqueta) use ($sectoresUsuario) {
        return in_array($etiqueta->organismossectors_id, $sectoresUsuario);
      });

      // $etiquetasSinVincularPorSector = $etiquetasSinVincular->filter(function ($etiqueta) use ($sectoractual) {
      //   return $etiqueta->organismossectors_id == $sectoractual;
      // });

      $etiquetasSinVincularGlobales = $etiquetasSinVincular->filter(function ($etiqueta) use ($global) {
        return $etiqueta->organismossectors_id == $global;
      });

      $etiquetasSinVincular = $etiquetasSinVincularPorSector->concat($etiquetasSinVincularGlobales);
    }

    // solo se pueden vincular etiquetas que esten activas
    $etiquetasSinVincular = $etiquetasSinVincular->filter(function ($etiqueta) use ($activas) {
      return $etiqueta->activo == $activas;
    })->sortBy('organismosetiqueta');

    // Las etiquetas vinculadas a este expediente
    $etiquetasVinculadas = $expediente->organismosetiquetas->sortBy('organismosetiqueta');
    return view('expedientes.etiquetas',  compact('expediente', 'etiquetasSinVincular', 'etiquetasVinculadas'));
  }

  public function asignarEtiquetas(Request $request)
  {
    $tags = $request->get('tags');

    if (!isset($tags) || is_null($tags)) {
      return redirect()->back()->with('errors', ['Debe asignar una etiqueta antes de guardar']);
    }

    // Ver que las etiquetas no esten repetidas

    $expediente = Expediente::findOrFail($request->get('expedientes_id'));
    try {
      $etiquetasAsignadas = $expediente->organismosetiquetas;

      if (count($etiquetasAsignadas) > 0)
      {
        foreach ($etiquetasAsignadas as $etiqueta)
        {
          for ($i = 0; $i < count($tags); $i++) {
            // se consulta por el id de la etiqueta ingresada y el id de la etiqueta que tiene cargada para no cargar la misma etiqueta mas de una vez
            if ($etiqueta->id !== $tags[$i]) {
              $expediente->organismosetiquetas()->syncWithoutDetaching([$tags[$i]]);
            }
          }
        }
      }
      else
      {
        for ($i = 0; $i < count($tags); $i++) {
          $expediente->organismosetiquetas()->syncWithoutDetaching([$tags[$i]]);
        }
      }
     
    
      $textoLog = Auth::user()->name . " asignó etiqueta/s al ". org_nombreDocumento() ." a las " . Carbon::now()->toTimeString();
      historial_doc(($expediente->id),$textoLog);
     

    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return redirect()->back()->with('errors', ['No se pudo asignar la etiqueta']);
    }
    // En caso de exito redirecciona a 
    return redirect()->route('expediente.show', base64_encode($expediente->id));
  }
  public function desasignarEtiquetas(Request $request)
  {

    try {
      $expediente = Expediente::find($request->expediente_id);
      $etiqueta = Organismosetiqueta::find($request->etiqueta_id);

      if (session('permission')->contains('expediente.etiqueta')) {
        $expediente->organismosetiquetas()->detach($request->etiqueta_id);
      }
      elseif (session('permission')->contains('expediente.etiqueta.sector')) {
        // si el usuario tiene el permiso de etiquetas de su sector, se quitan etiquetas solo si:
        // 1. el sector al que pertenece el usuario es el mismo que el sector donde se creó la etiqueta
        // 2. la etiqueta es global
        // if ($etiqueta->organismossectors_id == NULL || $expediente->expedientesestados->last()->rutasector->sector->id == $etiqueta->organismossectors_id) {
        //   $expediente->organismosetiquetas()->detach($request->etiqueta_id);
        // }
        // else {
        //   return response()->json(['3']);
        // }

        $sectoresUsuario = Auth::user()->usersector->pluck('organismossectors_id')->toArray(); // etiquetas solo de los sectores que pertenece el usuario (PRUEBA)

        // etiquetas solo de los sectores que pertenece el usuario (PRUEBA)
        if ($etiqueta->organismossectors_id == NULL || in_array($etiqueta->organismossectors_id, $sectoresUsuario)) {
          $expediente->organismosetiquetas()->detach($request->etiqueta_id);
        }
        else {
          return response()->json(['3']);
        }
      }
     
      // $expediente->organismosetiquetas()->detach($request->etiqueta_id);
      $textoLog = Auth::user()->name . " quitó etiqueta/s del ". org_nombreDocumento() ." a las " . Carbon::now()->toTimeString();
      historial_doc(($expediente->id),$textoLog);
      
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return response()->json(['2']);
    }
    return response()->json(['1']);
  }

  public function crearPrimerFojaCaratula($carpetaRaiz, $expediente)
  {
    // set style for barcode
    $style = array(
      'border' => 0,
      'vpadding' => 'auto',
      'hpadding' => 'auto',
      'fgcolor' => array(0, 0, 0),
      'bgcolor' => false, //array(255,255,255)
      'module_width' => 1, // width of a single module in points
      'module_height' => 1 // height of a single module in points
    );

    //obtener la localidad 
    $localidads_id = $expediente->organismos->localidads_id;
    $organismo_localidad = Localidad::find($localidads_id);

    $fecha = Carbon::parse($expediente->fecha_inicio);
    $afecha = $fecha->year;

    PDF::SetTitle('Documento');

    PDF::AddPage();
    $bMargin = PDF::getBreakMargin();
    $auto_page_break = PDF::getAutoPageBreak();
    PDF::SetAutoPageBreak(false, 0);
    PDF::Image('images/caratula.jpg', 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
    PDF::SetAutoPageBreak($auto_page_break, $bMargin);
    PDF::setPageMark();


    // $fecha = $expediente->created_at;
    // $hora = $expediente->created_at;
    if ($expediente->expedientetipo->color != null) {
      $hex = $expediente->expedientetipo->color;
      list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");

      PDF::SetFillColor($r, $g, $b);
      PDF::Rect(190, 0, 8, 80, 'F');
      //  PDF::Rect(118, 75, 100 , 8, 'F');
    }


    PDF::SetFont('helvetica', '', 7);
    // PDF::write2DBarcode($expediente->expediente_num, 'QRCODE,L', 85, 170, 35, 35, $style, 'N');
    // PDF::write2DBarcode(getExpedienteName($expediente), 'QRCODE,L', 85, 170, 35, 35, $style, 'N');
    PDF::write2DBarcode(getExpedienteName($expediente), 'QRCODE,L', 85, 193, 35, 35, $style, 'N');

    // PDF::SetFont('helvetica', 'B', 8);
    // PDF::MultiCell(120, 5, $fecha->format('d-m-Y'), 0, 'L', 0, 1, 42, 32, true);

    PDF::SetFont('helvetica', 'B', 15);
    PDF::MultiCell(120, 5, $expediente->organismos->organismo, 0, 'C', 0, 1, 50, 70, true);

    PDF::SetFont('helvetica', 'B', 12);
    PDF::MultiCell(120, 5, getExpedienteName($expediente), 0, 'L', 0, 1, 48, 109, true);
    PDF::MultiCell(120, 5, $expediente->organismos->organismo, 0, 'L', 0, 1, 50, 120, true);

    PDF::MultiCell(120, 5, $expediente->expediente, 0, 'L', 0, 1, 52, 131, true);
    // PDF::MultiCell(120, 5, $organismo_localidad->localidad, 0, 'L', 0, 1, 53, 142, true);
    PDF::MultiCell(120, 5, $organismo_localidad->localidad, 0, 'L', 0, 1, 53, 167, true);
    // PDF::MultiCell(120, 5, $fecha->format('d-m-Y'), 0, 'L', 0, 1, 55, 156, true);
    PDF::MultiCell(120, 5, $fecha->format('d-m-Y'), 0, 'L', 0, 1, 55, 179, true);
    // PDF::MultiCell(120, 5, $afecha, 0, 'L', 0, 1, 103, 206, true);
    PDF::MultiCell(120, 5, $afecha, 0, 'L', 0, 1, 103, 229, true);

    // La carpeta de ubicacion es el id del organismo y el id del expediente
    $carpeta = $carpetaRaiz . DIRECTORY_SEPARATOR . strval($expediente->id);
    // La ubicacion contiene o implica el codigo del organismo que actua al crear el expediente, o al que pertenece el expediente
    $ubicacion = storage_path('app') . DIRECTORY_SEPARATOR . $carpeta;
    if (!file_exists($ubicacion)) {
      if (File::makeDirectory($ubicacion)) {
        $filePathAndName = $carpeta . DIRECTORY_SEPARATOR . "caratula_" . $expediente->expediente_num . ".pdf";
        PDF::Output(storage_path('app') . DIRECTORY_SEPARATOR . $filePathAndName, 'F');
      }
    } else {
      $filePathAndName = $carpeta . DIRECTORY_SEPARATOR . "caratula_" . $expediente->expediente_num . ".pdf";
      PDF::Output(storage_path('app') . DIRECTORY_SEPARATOR . $filePathAndName, 'F');
    }

    return $filePathAndName;
  }

  public function buscarUsuarios($sector_id, $idestadoexpediente)
  {
    $relations = ['users'];
    $organismosector = Organismossectorsuser::with($relations)->where('organismossectors_id', $sector_id)->whereHas('users', function($query) { $query->where('activo', 1); })->get();
    $sector = Organismossector::find($sector_id);
    $expediente_estado = Expedienteestado::find($idestadoexpediente);
    return response()->json(['organismosector' => $organismosector, 'sector' => $sector, 'expediente_estado' => $expediente_estado]);
  }

  public function asignarExpediente($id)
  {
    $expediente_estado = Expedienteestado::find($id);
    $ultimoestado = Expedienteestado::where('expedientes_id', $expediente_estado->expedientes_id)->get()->last();

    if ($ultimoestado->users_id !== NULL) {
      return response()->json([
        'respuesta' => 3
      ]);
    }

    $user = User::find(Auth::user()->id);

    $usersession_name = Auth::user()->name; // nombre del usuario que asigna el documento a un usuario cuando el usuario actual es vacio (notofication mail)

    DB::beginTransaction();
    try {
      // $expediente_estado->users_id = Auth::user()->id;
      // $expediente_estado->update();
      $expediente = Expediente::find($expediente_estado->expedientes_id);
      $estadoexpediente = new Expedienteestado;
      $estadoexpediente->expedientes_id = $expediente_estado->expedientes_id;
      $estadoexpediente->users_id =  $user->id;
      $estadoexpediente->expendientesestado = "procesando";
      $estadoexpediente->expedientesrutas_id = $expediente_estado->expedientesrutas_id;
      $textoLog = Auth::user()->name . " se autoasignó el " . org_nombreDocumento() . " a las " . Carbon::now()->toTimeString();
      $estadoexpediente->observacion = $textoLog;
      $estadoexpediente->save();

      $textoLog = "Se asignó el documento " . getExpedienteName($expediente);
      Logg::info($textoLog);
      //enviar email al usuario que se le asigno el  expediente
      $expedienteRuta = Expedientesruta::find($expediente_estado->expedientesrutas_id);
      $sector = Organismossector::find($expedienteRuta->organismossectors_id);
      $toUser = $user;
      // Notification::send($toUser, new PaseExpediente($toUser, ('Asignacion del documento ' . getExpedienteName($expediente)), $sector->organismossector));
      // Notification::send($toUser, new PaseExpediente($toUser, "Pase Expediente", $usersession_name, $sector->organismossector, "El documento no tenia usuario asociado"));
      $expediente_estado->notificacion_usuario = 'No leido';

      DB::commit();
      return response()->json([
        'respuesta' => 1,
        'expediente' => base64_encode($expediente_estado->expedientes_id)
      ]);
    } catch (\Exception $e) {
      DB::rollback();
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return response()->json([
        'respuesta' => 2
      ]);
    }
  }

  public function asignarExpedienteAdmin($index, $index2)
  {
    $expediente_estado = Expedienteestado::find($index2);
    $ultimoestado = Expedienteestado::where('expedientes_id', $expediente_estado->expedientes_id)->get()->last();

    if ($ultimoestado->users_id !== NULL) {
      return response()->json(['response' => 3]);
    }

    $usersession_name = Auth::user()->name; // nombre del usuario que asigna el documento a un usuario cuando el usuario actual es vacio (notofication mail)

    $user = User::find($index);
    DB::beginTransaction();
    try {
      $estadoexpediente = new Expedienteestado;
      $estadoexpediente->expedientes_id = $expediente_estado->expedientes_id;
      $estadoexpediente->users_id =  $index;
      $estadoexpediente->expendientesestado = "procesando";
      $estadoexpediente->expedientesrutas_id = $expediente_estado->expedientesrutas_id;
      $textoLog = Auth::user()->name . " asignó el ". org_nombreDocumento() ." al usuario ". $user->name ." a las " . Carbon::now()->toTimeString();
      $estadoexpediente->observacion = $textoLog;
      $estadoexpediente->save();

      $expediente = Expediente::find($expediente_estado->expedientes_id);
      $textoLog = "Se asignó el documento " . getExpedienteName($expediente) . " a usuario " . $user->name;
      Logg::info($textoLog);

      //enviar email al usuario que se le asigno el  expediente
      $expedienteRuta = Expedientesruta::find($expediente_estado->expedientesrutas_id);
      $sector = Organismossector::find($expedienteRuta->organismossectors_id);
      $toUser = $user;
      // Notification::send($toUser, new PaseExpediente($toUser, "Pase Expediente", $usersession_name, $sector->organismossector, "El documento no tenia usuario asociado"));
      $expediente_estado->notificacion_usuario = 'No leido';

      DB::commit();

      if (Auth::user()->id == $index)
      {
        return response()->json(['response' => 1, 'user' => $user, 'expediente' => base64_encode($expediente_estado->expedientes_id), 'redirect' => true]);
      }
      else{
        return response()->json(['response' => 1, 'user' => $user, 'expediente' => base64_encode($expediente_estado->expedientes_id), 'redirect' => false]);
      }
    } catch (\Exception $e) {
      DB::rollback();
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return response()->json(['response' => 2, 'error' => $e]);
    }
  }

  public function getvinculos(Request $request) {
    if ($request->ajax()) {
      $expediente_id = base64_decode($request->expediente_id);
      $expedientesOtros = Expediente::esteorganismoExcepto($expediente_id)->orderBy('id', 'desc')->get();
      $expediente = Expediente::findOrFail($expediente_id);

      return Datatables::of($expedientesOtros)
      ->addColumn('expediente_num', function($row){
        return getExpedienteName($row);
      })
      ->addColumn('extracto', function ($row) {
        return $row->expediente;
      })
      ->addColumn('fecha_inicio', function ($row) {
        return date("d/m/Y", strtotime($row->created_at));
      })
      ->addColumn('ult_modif', function ($row) {
        return date("d/m/Y", strtotime($row->expedientesestados->last()->updated_at));
      })
      ->addColumn('estado', function ($row) {
        $estado = "";
        if ($row->expedientesestados->last()->expendientesestado == "fusionado" 
          || $row->expedientesestados->last()->expendientesestado == "anulado") {
          $estado = "<span class='label label-warning'> Fue fusionado </span>";
        } else {
          if ($row->documentosFusionados->contains($row->id)) {
            $estado = "<span class='label label-success'>Fusionado</span>";
          } elseif ($row->documentosEnlazados->contains($row->id)) {
            $estado = "<span class='label label-success'>Enlazado</span>";
          } else {
            $estado = "<span class='label label-danger'>Sin vínculo</span>";
          }
        }
        return $estado;
      })
      ->addColumn('asociar', function($row) use ($expediente) {
        $actionBtn = "";
        if ($row->expedientesestados->last()->expendientesestado <> "fusionado" 
          and $row->expedientesestados->last()->expendientesestado <> "anulado") {
          if ($expediente->documentosEnlazados->contains($row->id) 
              && (session('permission')->contains('expediente.enlazar') ||
              session('permission')->contains('expediente.superuser'))) {
              $actionBtn .= '<div id="desenlazar" class="btn-group desvincular">';
              $actionBtn .= '<a otroexpediente_id='.$row->id.'" expediente_id="'.$expediente->id.'" tipo="Enlace" data-toggle="tooltip"';
              $actionBtn .= ' title="Desenlazar a este documento" class="btn btn-default"><i class="fa fa-chain-broken"></i></a></div>';
          } elseif ($expediente->documentosFusionados->contains($row->id) 
              && (session('permission')->contains('expediente.fusionar') 
              || session('permission')->contains('expediente.superuser'))) {
                $actionBtn .= '<div id="desfusionar" class="btn-group desvincular">';
                $actionBtn .= '<a otroexpediente_id="'.$row->id.'" expediente_id="'.$expediente->id.'" tipo="Fusion" data-toggle="tooltip"';
                $actionBtn .= ' title="Desfusionar a este documento" class="btn btn-default"><i class="fa fa-chain-broken"></i></a></div>';
          } else {
            if (session('permission')->contains('expediente.enlazar') || session('permission')->contains('expediente.superuser')) {
                $actionBtn .= '<div id="enlazar" class="btn-group vincular">';
                $actionBtn .= '<a otroexpediente_id="'.$row->id.'" expediente_id="'.$expediente->id.'" tipo="Enlace" data-toggle="tooltip"';
                $actionBtn .= ' title="Enlazar a este documento" class="btn btn-default"><i class="fa fa-chain"></i></a></div>';
            }

            if (session('permission')->contains('expediente.fusionar') || session('permission')->contains('expediente.superuser')) {
              $actionBtn .= '<div id="fusionar" class="btn-group vincular">';
              $actionBtn .= '<a otroexpediente_id="'.$row->id.'" expediente_id="'.$expediente->id.'" tipo="Fusion" data-toggle="tooltip"';
              $actionBtn .= ' title="Fusionar a este documento" class="btn btn-default"><i class="fa fa-crop"></i></a></div>';
            }
          }
        }
        
        return $actionBtn;
      })
      ->rawColumns(['expediente_num', 'extracto', 'fecha_inicio', 'ult_modif', 'estado', 'asociar'])
      ->make(true);
    }
  }

  public function indexvinculo($expediente_id, $buscador = "")
  {
    $id = $expediente_id;
    $expediente_id = base64_decode($expediente_id);

    try {
      $expediente = Expediente::findOrFail($expediente_id);
      $title = "Asociar documentos";
    } catch (\Exception $exception) {
      Logg::error($exception->getMessage(), ("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()));
      if ($exception instanceof ModelNotFoundException) {
        return redirect()->route('expedientes.index')->with('errors', ['El documento buscado no existe.']);
      } else {
        return redirect()->route('expedientes.index')->with('errors', ['No se puede acceder a los datos de los documentos en este momento.']);
      }
    }

    return view('expedientes.vincular', ['expediente_id' => $id, 'expediente' => $expediente, 'title' => $title, 'buscador' => $buscador]);
  }


  // public function searchvinculo(Request $request)
  // {
  //   $id = $request->expediente_id;
  //   $expediente = Expediente::findOrFail($id);
  //   $title = "Vincular Documentos ";
  //   // El termino a buscar
  //   $buscador = $request->buscador;

  //   try {

  //     // Se busca por extracto, numero de expediente y fecha de inicio
  //     $expediente_buscado =  Expediente::where('expediente', 'like', '%' . $buscador . '%')
  //       ->orWhere('expediente_num', 'like', '%' . $buscador . '%')
  //       ->orWhere('fecha_inicio', 'like', '%' . $buscador . '%')
  //       ->orderBy('id', 'desc')->get();

  //     $collection = $expediente_buscado->reject(function ($item) use ($id) {
  //       return $item->id == $id;
  //     })->paginate(5);

  //     $expediente_buscado = $collection;

  //     if (count($expediente_buscado) < 1) {
  //       throw new \Exception('No se encuentran resultados para esa busqueda');
  //     }
  //   } catch (\Exception $th) {
  //     Logg::error($th->getMessage(), ("Archivo:" . pathinfo($th->getFile())['basename'] . " - Línea:" . $th->getLine()));
  //   }

  //   return view('expedientes.vincular', ['expedientesOtros' => $expediente_buscado, 'expediente' => $expediente, 'title' => $title, 'buscador' => $buscador]);
  // }

  /**
   * vincular
   * Establece una relacion en la table intermedia entre Expedientes
   * @param  mixed $request
   * @return void
   */
  public function vincular(Request $request)
  {
    $expediente_id = $request->get('expediente_id');
    $expedientevinculo_id = $request->get('otroexpediente_id');
    $tipo = $request->get('tipoVinculo');

    try {
      $expediente = Expediente::find($expediente_id);
      $expedienteHijo = Expediente::find($expedientevinculo_id);
      $expedienteHijo->documentosVinculados()->attach($expediente_id, ['tipo' =>  $tipo]);

      if ($tipo == "Enlace") {
        $expediente->documentosVinculados()->attach($expedientevinculo_id, ['tipo' =>  $tipo]);
        
        // Ver a documento hijo como al otro documento Vinculo enlace
      
        $textoLog = Auth::user()->name . " vinculó el" . org_nombreDocumento() . " a ".  getExpedienteName($expedienteHijo) ." a las " . Carbon::now()->toTimeString(); 
        historial_doc(($expediente->id),$textoLog);
        
        $textoLog = Auth::user()->name . " vinculó el " . org_nombreDocumento() ." a ".  getExpedienteName($expediente) ." a las " . Carbon::now()->toTimeString(); 
        historial_doc(($expedienteHijo->id),$textoLog);

      } else {
        // if ( $tipo == "Fusion" ){
        // Cambiar de expedientes y orden las fojas del documento hijo al del "padre"
        $fojasPadre = $expediente->fojas;
        $nroMax = 0;

        foreach ($fojasPadre as $indice => $foja) {
          if ($foja->numero > $nroMax) {
            $nroMax = $foja->numero;
          }
        }

        $nroFoja = $nroMax;
        $fojasHijo =  $expedienteHijo->fojas;
        foreach ($fojasHijo as $indice => $foja) {
          if ($foja->numero == 1) {
            // Saltea la caratula 
          } else {
            $nroFoja += 1;

            $foja->descripcion = "Fusionado desde " . getExpedienteName($expedienteHijo);
            $foja->expedientes_id =  $expediente->id;
            $foja->numero = $nroFoja;

            $foja->update();
          }
        }

        // Nuevos Estado expediente padre
        
        $textoLog =  Auth::user()->name . " fusionó el ". org_nombreDocumento(). " " .  getExpedienteName($expedienteHijo) . " al " . getExpedienteName($expediente) . " a las " . Carbon::now()->toTimeString();
        Logg::info($textoLog);
        historial_doc(($expediente->id), $textoLog );

        // Nuevos Estado expediente fusionado (hijo)
      
        $textoLog =  Auth::user()->name . " fusionó el ". org_nombreDocumento(). " " .  getExpedienteName($expedienteHijo) . " al " . getExpedienteName($expediente) . " a las " . Carbon::now()->toTimeString();
        Logg::info($textoLog);
        historial_doc(($expedienteHijo->id), $textoLog,'fusionado' );
      }
      return response()->json(1);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return response()->json(2);
    }
  }

  public function desvincular(Request $request)
  {
    $expediente_id = $request->get('expediente_id');
    $expedientevinculo_id = $request->get('otroexpediente_id');

    try {
      $expediente = Expediente::find($expediente_id);
      $expedienteHijo = Expediente::find($expedientevinculo_id);
      $expediente->documentosVinculados()->detach($expedientevinculo_id);
      $expedienteHijo->documentosVinculados()->detach($expediente_id);

      $textoLog =  Auth::user()->name . " desvinculó  " . getExpedienteName($expedienteHijo) . " a las " . Carbon::now()->toTimeString();
      Logg::info($textoLog);
      historial_doc(($expediente->id), $textoLog );

      $textoLog =  Auth::user()->name . " desvinculó  " . getExpedienteName($expediente) . " a las " . Carbon::now()->toTimeString();
      Logg::info($textoLog);
      historial_doc(($expedienteHijo->id), $textoLog );

      return response()->json(1);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return response()->json(2);
    }
  }

  public function nextNumber(Request $request)
  {
    // return getNextExpedienteNumberYear(Auth::user()->userorganismo->first()->organismos_id);
    return getNextExpedienteNumber(Auth::user()->userorganismo->first()->organismos_id);
  }

  public function anular(Request $request)
  {
    try {
      $id = base64_decode($request->id);
      $expediente = Expediente::find($id);
      $expediente->descripcion = $request->descripcion;
      $expediente->save();

      
      $textoLog = org_nombreDocumento() . " anulado por " .  Auth::user()->name  . " a las " . Carbon::now()->toTimeString();
      Logg::info($textoLog);
      historial_doc(($expediente->id), $textoLog, 'anulado' );
    
      return response()->json(['response' => 1]);
    } catch (\Exception $e) {
      return response()->json(['response' => 2]);
    }
  }

  public function consultaranulado($id)
  {
    $id = base64_decode($id);
    $consultaranulado = Expediente::find($id);
    $log =  Expedienteestado::where('expedientes_id', $id)->get()->last();
    return response()->json(['consultaranulado' => $consultaranulado, 'log' => $log]);
  }

  public function eventoClick($id)
  {
    $idexpediente = $id;
    $user = Auth::user();
    $event = event(new ClickCompartirLink($user, $idexpediente));
    response()->json($event);
  }

  public function eventoClickPdf($id)
  {
    $idexpediente = $id;
    $user = Auth::user();
    $event = event(new ClickGenerarPdf($user, $idexpediente));
    response()->json($event);
  }

  public function liberarDocumento(Request $request) {

    $expediente_id = $request->expediente_id;

    try {
      $ultimoEstadoExp = Expedienteestado::where('expedientes_id', $expediente_id)->get()->last();
      $sector  = $ultimoEstadoExp->rutasector->sector;
      $sectorName = $ultimoEstadoExp->rutasector->sector->organismossector;

      $estadoexpediente = new Expedienteestado;
      $estadoexpediente->expedientes_id = $ultimoEstadoExp->expedientes_id;
      $estadoexpediente->users_id = NULL;
      $estadoexpediente->expendientesestado = 'pasado';
      $estadoexpediente->notificacion_usuario = NULL;
      $estadoexpediente->expedientesrutas_id = $ultimoEstadoExp->expedientesrutas_id;
      $textoLog = Auth::user()->name . " liberó el ". org_nombreDocumento() ." al sector " . $sectorName  . " a las " . Carbon::now()->toTimeString();
      $estadoexpediente->observacion = $textoLog;
      $estadoexpediente->comentario_pase = 'No posee comentarios';
      $estadoexpediente->save();

      // Para la notificacion se pasa como parametro origen y destino el mismo sector
      if ($sector->email !== NULL) {
        Notification::send($sector, new PaseSector('Pase Sector', $sectorName, $sectorName, 'No posee comentarios'));
      }

      return response()->json([[1]]);
    } catch (\Throwable $th){
      return response()->json([[2]]);
    }
    
  }

  // Esta funcion permite devolver un expediente al sector del cual lo tomo el usuario a travez del metodo asignarExpedienteGeneral
  public function devolverDocumento(Request $request) {

    $expediente_id = $request->expediente_id;
    $expedienteestado = Expedienteestado::where('expedientes_id', $expediente_id)->get()->last();

    if (!session('permission')->contains('expediente.enlazar') && !session('permission')->contains('expediente.fusionar')) {
      return response()->json([[3]]);
    }

    if ($expedienteestado->ruta_devolver == NULL) {
      return response()->json([[2]]);
    }

    try {

      $estadoexpediente = new Expedienteestado;
      $estadoexpediente->expedientes_id = $expedienteestado->expedientes_id;
      $estadoexpediente->users_id = NULL;
      $estadoexpediente->expendientesestado = 'pasado';
      $estadoexpediente->notificacion_usuario = NULL;
      $estadoexpediente->expedientesrutas_id = $expedienteestado->ruta_devolver; // se asigna la ruta de donde se asignó el documento para devolverlo
      $textoLog = Auth::user()->name . " devolvió el ". org_nombreDocumento() ." a las " . Carbon::now()->toTimeString();
      $estadoexpediente->observacion = $textoLog;
      $estadoexpediente->save();

      return response()->json([[1]]);
    } catch (\Throwable $th){
      return response()->json([[2]]);
    }

  }

  // Permite al documento volver al estado anterior a ser pasado
  public function revertirPase(Request $request)
  {
    $expediente_id = $request->expediente_id;

    if (!session('permission')->contains('expediente.pase')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    
    try {
      // se consulta por el ultimos estado del expediente seleccionado para revertir
      $filtroestados = Expedienteestado::where('expedientes_id', $expediente_id)->latest()->take(2)->get();
      
      $ultimoestadouser = $filtroestados->last();

      // si el ultimo estado del documento al momento de revertir el pase no es "pasado", se retorna un error
      if ($filtroestados->first()->expendientesestado !== "pasado") {
        return response()->json([[2]]);
      }
      
      $expediente_estado = new Expedienteestado;
      $expediente_estado->expedientes_id = $ultimoestadouser->expedientes_id;
      $expediente_estado->users_id = $ultimoestadouser->user_id;
      $expediente_estado->expendientesestado = $ultimoestadouser->expendientesestado;
      $expediente_estado->expedientesrutas_id = $ultimoestadouser->expedientesrutas_id;
      $expediente_estado->observacion = "El usuario " . Auth::user()->name . " revirtió el último pase generado del " . org_nombreDocumento() . ".";
      $expediente_estado->notificacion_usuario = NULL;
      $expediente_estado->comentario_pase = NULL;
      $expediente_estado->pasado_por = NULL;
      $expediente_estado->save();

      return response()->json([[1]]);
    } catch (\Throwable $th) {
      return response()->json([[2]]);
    }
  }

  // Esta funcion permite listar los pases que realizó el usuario y que pueden revertirse
  // siempre y cuando el último estado del documento siga siendo "pasado"
  public function revertirPasesIndex()
  {
    $auth_user = Auth::user();
    $id = $auth_user->id;
    $expedientes_id = [];
    $estadosactuales = [];

    // se consultan los estados "pasados" y que los haya hecho el usuario que se encuentra logueado
    $expedientespasados = Expedienteestado::where('expendientesestado', 'pasado')->where('pasado_por', $id)->get();
    
    // cargo en un array los exp_id que resultan de la consulta de $expedientespasados
    for ($k = 0; $k < count($expedientespasados); $k++) {
      array_push($expedientes_id, $expedientespasados[$k]->expedientes_id);
    }

    // se omiten los exp_id del array para traer luego los estados actuales
    $expedientes_id = array_unique($expedientes_id);

    // se recorre $expedientes_id con FOREACH porque al aplicar array_unique, se pierde el indexado original y no se puede recorrer con un FOR comun
    foreach ($expedientes_id as $expediente) {
      $consultaactuales = Expedienteestado::where('expedientes_id', $expediente)->get()->last();

      // si el ultimo estado del expediente es "pasado" y el usuario que realizó ese pase es el que se encuentra logueado, se carga en $estadosactuales
      if ($id == $consultaactuales->pasado_por && $consultaactuales->expendientesestado == "pasado") {
        array_push($estadosactuales, $consultaactuales);
      }
    }

    // dd($estadosactuales);

    $title = "Revertir pases realizados";

    return view('expedientes.revertirpases', ['title' => $title, 'estadosactuales' => $estadosactuales]);
  }

  public function indexSinUsuario() {
    $title = "Documentos sin usuario asignado";
    
    return view('expedientes.indexsinusuario', ['title' => $title]);
  }

  // Esta funcion permite obtener la lista de documentos sin usuarios para luego ser pasada al datatable con la metodologia server-side
  public function getDocumentosSinUsuario(Request $request) {
    $session = session('permission');
    $auth_user = Auth::user();

    // si existe una solicitud ajax, se llama al trait que consulta los expedientes que no tienen usuario asignado
    if ($request->ajax()) {
      $expedientes = $this->getExpedientesSinUsuario($session, $auth_user);

      // la consulta de los expedientes se pasa al modelo Datatables que se encarga de procesarlos segun la peticion
      // addColumn: permite agregar una columna al datatable en el cual el 1er parametro es el nombre del atributo (data) correspondiente a la consulta de $expedientes, y el 2do parametro es el valor del atributo (name) y se puede pasar el mismo nombre del atributo o bien pasar una function que retorne un valor para poder darle estilo a lo que queremos retornar
      // rawColumns: se pasa en un array los nombres de las columnas que se agregaron
      // make(true): convierte lo pasado en una coleccion
      // Aclaracion: es posible armar las columnas, con estilo o como se desee, desde el controlador o directamente desde la vista
      return Datatables::of($expedientes)
              ->addColumn('expediente_num', function($row){
                $nomenclatura_num = getExpedienteName($row);

                return $nomenclatura_num;
              })
              ->addColumn('action', function($row){
                  $actionBtn = '<a href="/vinculo/'. base64_encode($row->id) .'" data-toggle="tooltip" title="Asociar documentos" class="btn btn-vinculo mr-2"><span class="fa fa-plus-circle"></span></a> <a exp_id="'. $row->id .'" data-toggle="tooltip" title="Asignarse el documento" class="btn btn-blue-3 asignar-expediente-general"><i class="icon-user"></i></a>';

                  return $actionBtn;
              })
              ->rawColumns(['expediente_num', 'action'])
              ->make(true);
    }
  }

  public function asignarExpedienteGeneral($expediente_id, $sector_id) {

    if (!session('permission')->contains('expediente.enlazar') && !session('permission')->contains('expediente.fusionar')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    try {
      $expediente = Expediente::find($expediente_id);
      $exptipo_rutas = $expediente->expedientetipo->rutas;
      $destino = 0;

      foreach ($exptipo_rutas as $ruta) {
        // se recorren las rutas del tipo de documento y se consulta por el id del sector de esa ruta, y si coincide con el id del sector seleccionado, se guarda en una variable el id de esa ruta
        if ($ruta->organismossectors_id == $sector_id) {
          $destino = $ruta->id;
        }
      }

      $expediente_estado = new Expedienteestado;
      $expediente_estado->expedientes_id = $expediente->id;
      $expediente_estado->users_id = Auth::user()->id;
      $expediente_estado->expendientesestado = "procesando";
      $expediente_estado->expedientesrutas_id = $destino;
      $textoLog = "Se asignó el documento " . getExpedienteName($expediente);
      $expediente_estado->observacion = $textoLog;
      $expediente_estado->ruta_devolver = $expediente->expedientesestados->last()->expedientesrutas_id;
      $expediente_estado->save();

      Logg::info($textoLog);

      return response()->json([
        'respuesta' => 1,
        'docasignado' => $expediente->id]
        , 200);
    } catch (\Throwable $th) {
        return response()->json([
          'respuesta' => 2], 200);
    }
    
  }

  public function adjuntarArchivos($id)
  {
    if (!session('permission')->contains('expediente.adjuntar') && !session('permission')->contains('organismos.index.admin')) {
      return redirect()->route('expedientes.index')->with('errors', ['No tiene permisos para adjuntar archivos']);
    }

    $expediente = Expediente::find(base64_decode($id));
    $useractual = User::find($expediente->expedientesestados->last()->users_id);
    $fojas = $expediente->fojas;

    $adjuntos = $this->adjuntosActivos($expediente->adjuntos);

    $title = "Adjuntar archivos al documento";

    return view('expedientes.adjuntar', ['expediente' => $expediente, 'title' => $title, 'useractual' => $useractual, 'adjuntos' => $adjuntos, 'fojas' => $fojas]);
  }

  public function storeFiles (Request $request)
  {
    $archivos = $request->file('file_multiple');
    $foja = $request->foja_selected;
    $expediente = Expediente::find($request->expediente_id);

    if ($archivos == NULL) {
      session()->flash('error', 'No se ha seleccionado ningún archivo para adjuntar');

      return redirect()->back();
    }

    $validatorFiles = Validator::make($request->all(), [
      'file_multiple.*' => 'mimetypes:application/pdf,application/octet-stream,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip',
    ]);

    if ($validatorFiles->fails()) {
      session()->flash('error', 'La extensión del/los archivo/s no es permitida para adjuntar');

      return redirect()->back();
    }

    DB::beginTransaction();

    try {
    
    $carpetaRaizOrganismo = $expediente->organismos->id . DIRECTORY_SEPARATOR . $expediente->id;
    $nombreZip = "adjunto-" . Carbon::now()->format('d_m_Y') . "__" . Carbon::now()->format('h_m_s') . ".zip";
    $size = 0;
    $cantidad = count($archivos);

    // éste trait realiza la compresion de los archivos seleccionados en un zip y retorna el directorio donde se guarda. Temporalmente, se guarda en la carpeta public del proyecto.
    $path = $this->comprimirArchivos($archivos, $nombreZip, $carpetaRaizOrganismo);

    // éste trait permite verificar el tamaño del zip antes de subirlo. Si supera los 30MB, no se sube y se avisa el limite de tamaño con una alerta
    if ($this->tamanioArchivo($path)) {
      session()->flash('error', 'El tamaño total de los archivos supera los 30 MB');

      return redirect()->back();
    }
    else {
      $size = round(filesize($path)/1024/1024, 2);
    }

    // ruta en la que se almacenará el ZIP en el servidor
    $minioPath = $carpetaRaizOrganismo . DIRECTORY_SEPARATOR . "adjuntos" . DIRECTORY_SEPARATOR . $nombreZip;

    try {
      // se intenta subir el zip al minio
      if (Storage::cloud()->put($minioPath, file_get_contents($path))) {
        // si se sube correctamente, se elimina el zip temporal
        unlink($path);

        // se almacenan los datos de los archivos adjuntos en la base de datos para recuperarlos luego
        $expediente_adjunto = new Expedientesadjunto;
        $expediente_adjunto->expedientes_id = $expediente->id;
        if ($foja !== NULL) {
          $expediente_adjunto->fojas_id = $foja;
        }
        $expediente_adjunto->nombre = $nombreZip;
        $expediente_adjunto->peso = $size;
        $expediente_adjunto->cantidad = $cantidad;
        $expediente_adjunto->path = $minioPath;
        $expediente_adjunto->save();

        // se guardan los nombres de los archivos adjuntos en la tabla detallesadjuntos
        $estado_detalle = $this->guardarDetalle($archivos, $expediente_adjunto->id);
      }
      else {

        if (file_exists($path)) {
          unlink($path);
        }

        session()->flash('error', 'No se pudo guardar el archivo en el servidor');
        
        return redirect()->back();
      }
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));

      session()->flash('error', 'El servidor de archivos se encuentra en mantenimiento. Intente más tarde');
      
      return redirect()->back();
    }
    
    $textoLog = Auth::user()->name . " adjuntó archivos al " . org_nombreDocumento() . " a las " . Carbon::now()->toTimeString();
    historial_doc(($expediente->id),$textoLog);

    DB::commit();

    session()->flash('success', 'Se adjuntaron los archivos correctamente');
      
    return redirect()->back();

  } catch (\Exception $e) {
    DB::rollback();
    Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
    
    session()->flash('error', 'El servidor de archivos se encuentra en mantenimiento. Intente más tarde');
      
    return redirect()->back();
  }

  }

  public function downloadAdjunto($id) {
    try {
      $adjunto = Expedientesadjunto::findOrFail(base64_decode($id));

      $path = $adjunto->path;

      if (Storage::cloud()->exists($path)) {
        $textoLog = Auth::user()->name . " descargó un archivo adjunto del " . org_nombreDocumento() . " a las " . Carbon::now()->toTimeString();
        historial_doc(($adjunto->expedientes_id), $textoLog);

        return Storage::cloud()->download($path);
      }

    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      
      return redirect()->back();
    }
    
  }

  public function eliminarAdjunto(Request $request) {
    
    try {
      $id = $request->adjunto_id;

      $adjunto = Expedientesadjunto::findOrFail($id);

      $adjunto->activo = 0;
      $adjunto->update();

      $textoLog = Auth::user()->name . " eliminó un archivo adjunto del " . org_nombreDocumento() . " a las " . Carbon::now()->toTimeString();
      historial_doc(($adjunto->expedientes_id), $textoLog);

      return response()->json(['respuesta' => 1]);
    
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      
      return response()->json(['respuesta' => 2]);
    }
    
  }

  public function getSectoresDisponibles($exp_id) {

    $expediente = Expediente::findOrFail($exp_id);
    $ultimoestado = $expediente->expedientesestados->last();
    $user = Auth::user();

    try {

      // si al querer asignarse el documento éste ya tiene un usuario asignado, se retorna un mensaje de aviso
      if ($ultimoestado->users_id !== NULL) {
        return response()->json(['respuesta' => 3]);
      }

      $sectores_destino = [];

      $array_sectores = $this->arraySectoresActivos($expediente, $user);

      if (count($array_sectores) > 0) {
        $sectores_destino = $this->sectoresDestino($array_sectores);
      }
      else {
        return response()->json([
                                  'respuesta' => 2
                                ]);
      }
    }
    catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      
      return redirect()->back();
    }

    return response()->json([
                              'respuesta' => 1,
                              'sectores' => $sectores_destino
                            ]);
  }

  public function consultarEtiqueta($expedienteId, $etiquetaId)
  {
    $etiqueta = Organismosetiqueta::find($etiquetaId);

    if ($etiqueta->caduca === 1 && $etiqueta->pasar_caducado === 1)
    {
      $expediente = Expediente::find($expedienteId);

      $tipo = Expedientestipo::find($expediente->expedientestipos_id);
      if ($tipo->sin_ruta == 0)
      {
        $rutas = Expedientesruta::select('organismossectors.id', 'organismossectors.organismossector')
                                ->join('organismossectors', 'expedientesrutas.organismossectors_id', '=', 'organismossectors.id')
                                ->where('expedientesrutas.expedientestipos_id', $tipo->id)
                                ->where('expedientesrutas.activo', 1)
                                ->get();
      }
      else
      {
        $rutas = Organismossector::select('id', 'organismossector')
                                  ->where('organismos_id', Auth::user()->userorganismo->first()->organismos_id)
                                  ->where('activo', 1)
                                  ->get();
      }

      return response()->json([
        'respuesta' => 1,
        'rutas' => $rutas
      ]);
    }
    else if ($etiqueta->caduca === 1 && $etiqueta->pasar_caducado === 0)
    {
      return response()->json([
        'respuesta' => 2
      ]);
    }
    else
    {
      return response()->json([
        'respuesta' => 3
      ]);
    }
  }

  public function configuracionCaducidad(Request $request)
  {
    if (is_null($request->fechaCaducidad))
    {
      $errors = array('Todos los campos son obligatorios');

      return response()->json([
        'respuesta' => 3,
        'error' => $errors
      ]);
    }

    $expedienteetiqueta = new Expedienteorganismoetiqueta;
    $expedienteetiqueta->organismosetiqueta_id = $request->etiquetaId;
    $expedienteetiqueta->expediente_id = $request->expedienteId;
    $expedienteetiqueta->caducidad = $request->fechaCaducidad;

    if ($request->rutaDestino !== NULL)
    {
      // evaluacion de ruta segun el tipo de documento del expediente
      $expediente = Expediente::find($request->expedienteId);
      $expedientetipo = Expedientestipo::find($expediente->expedientestipos_id);
      if ($expedientetipo->sin_ruta == 1) {
        if ($expedientetipo->rutas->count() > 0) {
          $ruta_sector = NULL;
          $rutas_tipo = $expedientetipo->rutas->sortBy('orden');

          foreach ($rutas_tipo as $ruta) {
            if ($ruta->organismossectors_id == $request->rutaDestino) {
              $ruta_sector = $ruta;
              break;
            }
          }

          if (is_null($ruta_sector)) {
            $expedienteruta = new Expedientesruta;
            $expedienteruta->expedientestipos_id = $expedientetipo->id;
            $expedienteruta->organismossectors_id = $request->rutaDestino;
            $expedienteruta->orden = NULL;

            $expedienteruta->save();

            $ruta_sector = $expedienteruta;
          }
        }
        else {
          $expedienteruta = new Expedientesruta;
          $expedienteruta->expedientestipos_id = $expedientetipo->id;
          $expedienteruta->organismossectors_id = $request->rutaDestino;
          $expedienteruta->orden = NULL;

          $expedienteruta->save();

          $ruta_sector = $expedienteruta;
        }
      }
      else {
        if ($expedientetipo->rutas->count() > 0) {
          $ruta_sector = $expedientetipo->rutas->sortBy('orden');
          $ruta_activa = NULL;

          foreach ($ruta_sector as $ruta) {
            if ($ruta->organismossectors_id == $request->rutaDestino) {
              $ruta_activa = $ruta;

              break;
            }
          }

          if (is_null($ruta_activa)) {
            $errors = array('El tipo de documento seleccionado debe tener al menos 1 sector activo en su ruta');

            return response()->json([
              'respuesta' => 2,
              'error' => $errors
            ]);
          }
          else {
            $ruta_sector = $ruta_activa;
          }
        }
        else {
          $errors = array('El tipo de documento seleccionado debe tener al menos 1 sector en su ruta');

          return response()->json([
            'respuesta' => 2,
            'error' => $errors
          ]);
        }
      }

      $expedienteetiqueta->ruta_destino = $ruta_sector->id;
    }

    $expedienteetiqueta->save();

    return response()->json([
      'respuesta' => 1
    ]);
  }

  public function quitarEtiquetaSeleccionada($expedienteId, $etiquetaId)
  {
    $etiqueta = Expedienteorganismoetiqueta::where('expediente_id', $expedienteId)
                                            ->where('organismosetiqueta_id', $etiquetaId)
                                            ->first();                       

    if (!is_null($etiqueta))
    {
      if (!is_null($etiqueta->caducidad))
      {
        $etiqueta->delete();
      }
    }

    return response()->json([
      'respuesta' => 1
    ]);
  }
}
