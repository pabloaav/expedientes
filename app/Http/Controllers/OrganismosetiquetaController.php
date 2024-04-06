<?php

namespace App\Http\Controllers;

use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Http\Request;
use Validator;

use Caffeinated\Shinobi\Facades\Shinobi;
use App\Logg;
use Auth, PDF;

use App\Organismo;
use App\Organismosetiqueta;
use App\Organismossector;


class OrganismosetiquetaController extends Controller
{

  public function index($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $organismo = Organismo::find($id);

    $organismosetiquetas = Organismosetiqueta::withTrashed()->where('organismos_id', $id)->paginate(10);
    $title = "Etiquetas del Organismo: " . $organismo->organismo;
    return view('organismosetiquetas.index', [
      'organismo' => $organismo,
      'organismosetiquetas' => $organismosetiquetas, 'title' => $title
    ]);
  }

  public function create($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $organismo = Organismo::find($id);
    $sectores = Organismossector::where('organismos_id', $id)
                                  ->where('activo', 1)
                                  ->get();

    $title = "Nueva etiqueta del organismo " . $organismo->organismo;
    return view('organismosetiquetas.create', ['organismo' => $organismo, 'title' => $title, 'sectores' => $sectores]);
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

    $caduca = 1;
    if ($request->caduca == "") {
      $caduca = 0;
    };

    $pasar_caducado = 1;
    if ($request->pasar_caducado == "") {
      $pasar_caducado = 0;
    };

    $messages = [
      'organismosetiqueta.required' => 'El nombre de la etiqueta no puede ser vacío',
      'organismosetiqueta.max' => 'El nombre de la etiqueta no puede superar los 50 caracteres',
      // 'organismosetiqueta.unique' => 'El nombre de la etiqueta ya existe en el organismo'
  ];

    $validator = Validator::make($request->all(), [
      'organismos_id' => 'required|exists:organismos,id',
      // 'organismosetiqueta' => 'required|unique:organismosetiquetas,organismosetiqueta|max:50',
      'organismosetiqueta' => 'required|max:50',
    ],$messages);

    // se comprueba si esa etiqueta existe en ese organismo
    $etiquetaExiste = Organismosetiqueta::where('organismosetiqueta', $request->organismosetiqueta)
                                        ->where('organismos_id', Auth::user()->userorganismo->first()->organismos_id)
                                        ->first();

    $validator->after(function ($validator) use ($etiquetaExiste){
      if (!is_null($etiquetaExiste)) {
          $validator->errors()->add('organismosetiqueta', 'El nombre de la etiqueta ya existe en el organismo');
      }
    });

    if ($validator->fails()) {
      foreach ($validator->messages()->getMessages() as $field_name => $messages) {
        foreach ($messages as $message) {
          $errors[] = $message;
        }
      }
      return redirect()->back()->with('errors', $errors)->withInput();
      die;
    }

    $organismosetiqueta = new Organismosetiqueta;
    $organismosetiqueta->organismos_id = $request->organismos_id;
    $organismosetiqueta->organismosetiqueta = $request->organismosetiqueta;
    $organismosetiqueta->organismossectors_id = $request->organismossectors_id;
    $organismosetiqueta->activo = $activo;
    $organismosetiqueta->caduca = $caduca;
    $organismosetiqueta->pasar_caducado = $pasar_caducado;
    $organismosetiqueta->save();

    $textoLog = "Creó etiqueta " .  $organismosetiqueta->organismosetiqueta ;
    Logg::info($textoLog);


    return redirect('/organismos/' . $request->organismos_id . '/organismosetiquetas');
  }

  public function show($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $organismosetiqueta = Organismosetiqueta::find($id);
    $title = "Etiqueta actual";
    return view('organismosetiquetas.show', ['organismosetiqueta' => $organismosetiqueta, 'title' => $title]);
  }

  public function edit($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $organismosetiqueta = Organismosetiqueta::withTrashed()->find($id);
    $sectores = Organismossector::where('organismos_id', $organismosetiqueta->organismos_id)
                                  ->where('activo', 1)
                                  ->get();
    $organismo = Organismo::find($organismosetiqueta->organismos_id);

    $title = "Editar etiqueta";
    return view('organismosetiquetas.edit', ['organismo' => $organismo, 'organismosetiqueta' => $organismosetiqueta, 'title' => $title, 'sectores' => $sectores]);
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

    $caduca = 1;
    if ($request->caduca == "") {
      $caduca = 0;
    };

    $pasar_caducado = 1;
    if ($request->pasar_caducado == "") {
      $pasar_caducado = 0;
    };

    $messages = [
      'organismosetiqueta.required' => 'El nombre de la etiqueta no puede ser vacío',
      'organismosetiqueta.max' => 'El nombre de la etiqueta no puede superar los 50 caracteres',
      // 'organismosetiqueta.unique' => 'El nombre de la etiqueta ya existe en el organismo'
  ];


    $validator = Validator::make($request->all(), [
      // 'organismosetiqueta' => 'required|unique:organismosetiquetas,organismosetiqueta|max:50',
      'organismosetiqueta' => 'required|max:50',
    ],$messages);

    // se comprueba si esa etiqueta existe en ese organismo
    $etiquetaExiste = Organismosetiqueta::where('organismosetiqueta', $request->organismosetiqueta)
                                        ->where('organismos_id', Auth::user()->userorganismo->first()->organismos_id)
                                        ->first();

    $validator->after(function ($validator) use ($etiquetaExiste, $id){
      if (!is_null($etiquetaExiste) && intval($id) !== $etiquetaExiste->id) {
          $validator->errors()->add('organismosetiqueta', 'El nombre de la etiqueta ya existe en el organismo');
      }
    });

    if ($validator->fails()) {
      foreach ($validator->messages()->getMessages() as $field_name => $messages) {
        foreach ($messages as $message) {
          $errors[] = $message;
        }
      }
      return redirect()->back()->with('errors', $errors)->withInput();
      die;
    }


    $organismosetiqueta = Organismosetiqueta::withTrashed()->find($id);
    $organismosetiqueta->organismosetiqueta = $request->organismosetiqueta;
    $organismosetiqueta->organismossectors_id = $request->organismossectors_id;
    $organismosetiqueta->caduca = $caduca;
    $organismosetiqueta->pasar_caducado = $pasar_caducado;
    $organismosetiqueta->activo = $activo;
    $organismosetiqueta->save();

    $textoLog = "Modificó etiqueta " .  $organismosetiqueta->organismosetiqueta ;
    Logg::info($textoLog);

    if ($activo == 0) {
      // SoftDelete para esta etiqueta
      $organismosetiqueta->delete();

    } else {
      Organismosetiqueta::withTrashed()->find($id)->restore();
    }
    return redirect('/organismos/' . $organismosetiqueta->organismos_id . '/organismosetiquetas');
  }

  public function destroy($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $organismosetiqueta = Organismosetiqueta::findOrfail($id);
    // $organismos_id = $organismosetiqueta->organismos_id;
    try {
      $textoLog = "Eliminó etiqueta " . $organismosetiqueta->organismosetiqueta ;
      Logg::info($textoLog);
      // Illuminate Support Collection de los id de los expedientes que estan vinculados con la etiqueta que se quiere borrar.
      $col = $organismosetiqueta->expedientes->pluck('id');
      // Desatachar todos los id de los expedientes vinculados con esta etiqueta
      $organismosetiqueta->expedientes()->detach($col);
      // Ahora se puede borrar de la base de datos sin problemas de restriccion de clave foranea
      $organismosetiqueta->forceDelete();

     
      return response()->json(['1']);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      return response()->json("No se encuentra el recurso", 404);
    }
  }

  public function finder(Request $request)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $organismosetiquetas = Organismosetiqueta::where('organismosetiqueta', 'like', '%' . $request->buscar . '%')
      ->paginate(15);
    $organismo = Organismo::find($request->organismo_id);
    $title = "Etiquetas: buscando " . $request->buscar;
    return view(
      'organismosetiquetas.index',
      ['organismosetiquetas' => $organismosetiquetas, 'organismo' => $organismo, 'title' => $title]
    );
  }

  public function search(Request $request)
  {
    $term = $request->term;
    $organismo =  Auth::user()->userorganismo->first()->organismos_id;
    $datos = Organismosetiqueta::where("organismos_id", "=", $organismo)->where('organismosetiqueta', 'like', '%' . $request->term . '%')
      ->where('activo', true)->get();
    $adevol = array();
    if (count($datos) > 0) {
      foreach ($datos as $dato) {
        $adevol[] = array(
          'id' => $dato->id,
          'value' => $dato->organismosetiqueta,
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

    $etiqueta = Organismosetiqueta::find($id);

    if ($etiqueta->activo) {
      $etiqueta->activo = false;
    } else {
      $etiqueta->activo = true;
    }
    $etiqueta->save();
    $textoLog = "Cambió estado etiqueta " .  $etiqueta->organismosetiqueta;
    Logg::info($textoLog);


    return redirect()->back();
  }
}
