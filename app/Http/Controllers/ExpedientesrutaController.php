<?php

namespace App\Http\Controllers;

use App\Logg;
use Auth, PDF;
use Validator;
use App\Organismo;
use App\Expedientesruta;
use App\Expedientestipo;
use App\Organismossector;
use Illuminate\Http\Request;
use Caffeinated\Shinobi\Facades\Shinobi;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Validation\Rule;


class ExpedientesrutaController extends Controller
{


  public function index($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $expedientestipo = Expedientestipo::find($id);
    $organismo = Organismo::find($expedientestipo->organismos_id);
    $configOrganismo = $organismo->configuraciones;
    $inactivos = false;

    // dd($expedientestipo);

    $expedientesrutas = Expedientesruta::where('expedientestipos_id', $id)
                                        ->where('activo', 1)
                                        ->orderBy('orden')
                                        ->paginate(50);
    $title = "Rutas del tipo de documento";
    return view('expedientesrutas.index', [
      'expedientestipo' => $expedientestipo,
      'expedientesrutas' => $expedientesrutas, 'title' => $title,
      'inactivos' => $inactivos,
      'configOrganismo' => $configOrganismo
    ]);
  }

  public function create($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $expedientestipo = Expedientestipo::find($id);
    $organismo = Organismo::find($expedientestipo->organismos_id);
    $configOrganismo = $organismo->configuraciones;
    //sectores del organismo
    $organismo_sector = Organismo::find($expedientestipo->organismos_id)->sectores;
    // dd($organismo_sector);
    $title = "Nueva ruta del documento: " . $expedientestipo->expedientestipo;
    return view('expedientesrutas.create', ['expedientestipo' => $expedientestipo, 'title' => $title, 'organismo_sector' => $organismo_sector, 'configOrganismo' => $configOrganismo]);
  }

  public function store(Request $request)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $activo = 1;
    if ($request->activo == "") {
      $activo = 0;
    };

    $messages = [
      'organismossectors_id.required' => 'Debe seleccionar un Sector',
  ];

    $validator = Validator::make($request->all(), [
      'expedientestipos_id' => 'required|exists:expedientestipos,id',
      'organismossectors_id' => 'required|exists:organismossectors,id',
      // 'orden' => 'required', // se comentó el requerimiento del orden porque se carga de forma automática
      'dias' => 'integer|between:1,365'
    ],$messages);
   

    if ($validator->fails()) {
      foreach ($validator->messages()->getMessages() as $field_name => $messages) {
        foreach ($messages as $message) {
          $errors[] = $message;
        }
      }
      return redirect()->back()->with('errors', $errors)->withInput();
      die;
    }

    //verificar si el sector esta en las rutas en este tipo de expediente
    // if (Expedientesruta::where('expedientestipos_id',$request->expedientestipos_id)->where('orden', $request->orden)->exists()) {
    //   return  redirect()->back()->with('error', 'El número de orden ya existe');
    // } else {
      $ordenRuta = Expedientesruta::where('expedientestipos_id', '=', $request->expedientestipos_id)->count(); // se guarda en una variable la cantidad de rutas de ese tipo de documento
      // falta expediente sector 
      $expedientesruta = new Expedientesruta;
      $expedientesruta->organismossectors_id = $request->organismossectors_id;
      $expedientesruta->expedientestipos_id = $request->expedientestipos_id;
      // $expedientesruta->orden = $request->orden;
      $expedientesruta->orden = $ordenRuta + 1; // al orden de la nueva ruta se asigna el orden consultado + 1
      $expedientesruta->dias = $request->dias;
      $expedientesruta->activo = $activo;
      $expedientesruta->save();

      $TipoExpediente = Expedientestipo::find($request->expedientestipos_id);
      $sector = Organismossector::find($expedientesruta->organismossectors_id);
      $textoLog = "Agregó el sector " . $sector->organismossector . " a la ruta del tipo " .   $TipoExpediente->expedientestipo;
      Logg::info($textoLog);

      return redirect('/expedientestipos/' . $request->expedientestipos_id . '/expedientesrutas')->with('success', 'El registro se agrego correctamente');
    // }
  }


  public function show($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $expedientesruta = Expedientesruta::find($id);
    $title = "Detalle rutas de Documentos";
    return view('expedientesrutas.show', ['expedientesruta' => $expedientesruta, 'title' => $title]);
  }



  public function edit($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    
    $expedientesruta = Expedientesruta::find($id);
    $expedientestipo = Expedientestipo::find($expedientesruta->expedientestipos_id);
    $organismo = ($expedientestipo->organismos_id);
    $organismo_sector = Organismo::find($expedientestipo->organismos_id)->sectores;
    $datosOrganismo = Organismo::find($expedientestipo->organismos_id);
    $configOrganismo = $datosOrganismo->configuraciones;

    $title = "Editar ruta del tipo de documentos: " . $expedientesruta->expedientestipos->expedientestipo;
    return view('expedientesrutas.edit', ['expedientestipo' => $expedientestipo, 'expedientesruta' => $expedientesruta, 'title' => $title, 'organismo_sector' => $organismo_sector, 'organismo' => $organismo, 'configOrganismo' => $configOrganismo]);
  }



  public function update(Request $request, $id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $activo = 1;
    if ($request->activo == "") {
      $activo = 0;
    };

    $expedientesrutaverificar = Expedientesruta::find($id);
    $validator = Validator::make($request->all(), [
      'expedientestipos_id' => 'required',
      'organismossectors_id' => [
        'required',
        //Rule::unique('expedientesrutas', 'organismossectors_id')->ignore($id, 'id')->where('expedientestipos_id', $request->expedientestipos_id)
      ],
      'orden' => 'required',
      'dias' => 'integer|between:1,365'
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

    //verificar si el sector esta en las rutas en este tipo de expediente

      $expedientesruta = Expedientesruta::find($id);
      $expedientesruta->organismossectors_id = $request->organismossectors_id;
      $expedientesruta->expedientestipos_id = $request->expedientestipos_id;
      $expedientesruta->orden = $request->orden;
     
      if (Expedientesruta::where('expedientestipos_id',$request->expedientestipos_id)->where('orden', $request->orden)->where('id','!=',$id)->exists()) {
        return  redirect()->back()->with('error', 'El número de orden ya existe');
      } else {

        $expedientesruta->dias = $request->dias;
        $expedientesruta->activo = $activo;
        $expedientesruta->save();

        $TipoExpediente = Expedientestipo::find($request->expedientestipos_id);
        $sector = Organismossector::find($expedientesruta->organismossectors_id);
        $textoLog = "Modificó la ruta del tipo " .   $TipoExpediente->expedientestipo . " - sector " . $sector->organismossector ;
       
        Logg::info($textoLog);

      return redirect('/expedientestipos/' . $expedientesruta->expedientestipos_id . '/expedientesrutas');
    }
  }

  public function destroy($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $expedientesruta = Expedientesruta::find($id);
    $expedientestipos_id = $expedientesruta->expedientestipos_id;
    $expedientesruta->delete();
    $TipoExpediente = Expedientestipo::find($expedientestipos_id);
    $textoLog = "Eliminó un sector de la ruta del tipo " .   $TipoExpediente->expedientestipo  ;
    Logg::info($textoLog);
    

    return redirect('/expedientestipos/' . $expedientestipos_id . '/expedientesrutas');
  }



  public function finder(Request $request)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $valorBusqueda = $request->buscar;
    $expedientestipo = Expedientestipo::find($request->id);

    if (empty($valorBusqueda)) {
      $expedientesrutas = Expedientesruta::where('expedientestipos_id', $request->id)->paginate(50);
      $title = "Buscando todas las rutas del tipo documento";
    } else {
      // Consultamos los tipos de expedientes de este organismo, y dentro de ese conjunto, los que hacen match con el valor de busqueda
      $expedientesrutas = Expedientesruta::with('organismossectors')
        ->where('orden', 'like', '%' . $valorBusqueda . '%')
        ->orWhereHas('organismossectors', function ($query) use ($valorBusqueda) {
          $query->where('organismossector', 'like', '%' . $valorBusqueda . '%');
        })
        ->paginate(15);
      $title = "Buscando: " . " '" . $valorBusqueda . " '";
    }
    return view('expedientesrutas.index', ['expedientestipo' => $expedientestipo, 'title' => $title, 'expedientesrutas' => $expedientesrutas]);
  }



  public function search(Request $request)
  {
    $term = $request->term;
    $datos = Expedientesruta::where('expedientesruta', 'like', '%' . $request->term . '%')
      ->where('activo', true)->get();
    $adevol = array();
    if (count($datos) > 0) {
      foreach ($datos as $dato) {
        $adevol[] = array(
          'id' => $dato->id,
          'value' => $dato->expedientesruta,
        );
      }
    } else {
      $adevol[] = array(
        'id' => 0,
        'value' => 'no hay coincidencias para ' .  $term
      );
    }
    return json_encode($adevol);
  }

  public function estado($id)
  {

    $ruta = ExpedientesRuta::find($id);
    $rutasExp = Expedientesruta::where('expedientestipos_id', $ruta->expedientestipos_id)->where('activo',1)->get();
    if ( $rutasExp->count() == 1 && $rutasExp->first()->id == $id) {

      return redirect()->back()->with('errors', ['Debe haber al menos una ruta activo por tipo documento.']);

    } else {

      if ($ruta->activo) {
        $ruta->activo = false;
      } else {
        $ruta->activo = true;
      }
      $ruta->save();
      $textoLog = "Cambió estado de una ruta del tipo de documento " .  $ruta->expedientestipos->expedientestipo;
      Logg::info($textoLog);

      return redirect()->back();
     
    }
    
  }

  public function updateOrden(Request $request) {
    try {
      if (!session('permission')->contains('organismos.index.admin')) {
        return response()->json([[2]]);
      }

      // se obtienen las rutas asociadas al tipo de documento pasado por js
      $expedientetipo_id = $request->tipodocumento_id;
      $expedienteruta = Expedientesruta::where('expedientestipos_id', $expedientetipo_id)->get();

      foreach ($expedienteruta as $ruta) {
        $ruta_id = $ruta->id;

        foreach ($request->order as $order) {
          if ($order['id'] == $ruta_id) { // se consulta por el id del arreglo "order" y el id de la ruta
            $ruta->orden = $order['position']; // si son iguales, se asigna el valor "position" al num de orden de la ruta
            $ruta->save();
          }
        }
      }

      return response()->json([[1]]);
    }
    catch (\Throwable $th) {
      return response()->json([[2]]);
    }
  }

  public function cargarInactivos($id) {
    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $expedientestipo = Expedientestipo::find($id);
    $organismo = Organismo::find($expedientestipo->organismos_id);
    $configOrganismo = $organismo->configuraciones;
    $inactivos = true;

    // dd($expedientestipo);

    $expedientesrutas = Expedientesruta::where('expedientestipos_id', $id)->orderBy('orden')->paginate(50);
    
    $title = "Rutas del tipo de documento";
    return view('expedientesrutas.index', [
      'expedientestipo' => $expedientestipo,
      'expedientesrutas' => $expedientesrutas, 'title' => $title,
      'inactivos' => $inactivos,
      'configOrganismo' => $configOrganismo
    ]);
  }

  public function getRutaTipo($id)
  {
    $tipo = Expedientestipo::find($id);
    if ($tipo->sin_ruta == 0)
    {
      $rutas = Expedientesruta::select('organismossectors.id', 'organismossectors.organismossector')
                              ->join('organismossectors', 'expedientesrutas.organismossectors_id', '=', 'organismossectors.id')
                              ->where('expedientesrutas.expedientestipos_id', $id)
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
      'response' => 1,
      'rutas' => $rutas
    ]);
  }
}
