<?php

namespace App\Http\Controllers;

use App\Expedientesruta;
use Illuminate\Http\Request;
use App\Expedientesrutasrequisitos;
use App\Http\Controllers\Controller;
use Caffeinated\Shinobi\Facades\Shinobi;
use Validator;
use App\Logg;
use App\Organismo;

class ExpedientesrutasrequisitosController extends Controller
{
  public function index($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $expedientesruta = Expedientesruta::find($id);
    $organismo = Organismo::find($expedientesruta->organismossectors->organismos_id);
    $configOrganismo = $organismo->configuraciones;

    // dd($expedientesruta);
    $title = "Requisitos para el tipo de documento : " . $expedientesruta->expedientestipos->expedientestipo;
    $requisitos =  Expedientesruta::find($id)->requisitos;
    return view('expedientesrutasrequisitos.index', ['requisitos' => $requisitos, 'expedientesruta' => $expedientesruta, 'title' => $title, 'configOrganismo' => $configOrganismo]);
  }

  public function create($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $expedientesruta = Expedientesruta::find($id);
    $title = "Nuevo requisito : " . $expedientesruta->expedientestipos->expedientestipo;
    return view('expedientesrutasrequisitos.create', ['expedientesruta' => $expedientesruta, 'title' => $title]);
  }

  /**
   * Este metodo crea un nuevo requisito de ruta, mediante un nuevo registro en la tabla expedientesrequisitos
   * Ruta web: Route::post('/expedientesrutas/requisitos/store', 'ExpedientesrutasrequisitosController@store')
   * ->name('requisitos.store');
   * @param  mixed $request
   * @return void
   */
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
    $obligatorio = 1;
    if ($request->obligatorio == "") {
      $obligatorio = 0;
    };

    // Controlar si ya existe un requisito de firmar para este nodo de ruta
    $yaExisteRequisitoFirma = Expedientesruta::find($request->expedientesrutas_id)->requisitos->contains('firmar', 1);
    //No se puede volver a pedir el requisito de firma si ya existe en este nodo de ruta
    if ($yaExisteRequisitoFirma && $request->requisito_firmar == 1) {
      return redirect()->back()->with('errors', ['No puede volver a requerir la firma porque ya existe este requisito en este nodo de ruta.'])->withInput();
    }

    // Si el requisito de que algunas fojas del expediente esten firmadas para realizar el pase:
    $requisito_firmar = 1;
    if ($request->requisito_firmar == "") {
      $requisito_firmar = 0;
    };
    // Si el requisito de firmar es activado, forzamos que sea obligatorio
    if ($requisito_firmar == 1) {
      $obligatorio = 1;
    };


    $validator = Validator::make($request->all(), [
      'expedientesrutas_id' => 'required',
      'expedientesrequisito' => 'required|max:150',
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

    // Crear un objeto Expedientesrutasrequisitos y guardar el registro en tabla expedientesrequisitos
    $requisitos = new Expedientesrutasrequisitos;
    $requisitos->expedientesrequisito = $request->expedientesrequisito;
    $requisitos->expedientesrutas_id = $request->expedientesrutas_id;
    $requisitos->activo = $activo;
    $requisitos->obligatorio = $obligatorio;
    $requisitos->firmar = $requisito_firmar;
    $requisitos->save();

    $expedientesruta = Expedientesruta::find($request->expedientesrutas_id);

    $textoLog = "Agregó el requisito " . $requisitos->expedientesrequisito . " a ruta del tipo " .  $expedientesruta->expedientestipos->expedientestipo;
    Logg::info($textoLog);

    $requisitos = Expedientesruta::find($requisitos->expedientesrutas_id)->requisitos;

    // Vuelve al index de requisitos para esa ruta
    return redirect()->route('requisitos.rutas', [$expedientesruta->id])->with('success', 'El registro se agrego correctamente');
  }

  public function edit($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $expedientesrequisito = Expedientesrutasrequisitos::find($id);
    if ($expedientesrequisito->firmar == 1) {
      return redirect()->back()->with('failed', "No se puede editar el requisito de firma");
    }
    $rutaRequisito = $expedientesrequisito->expedientesrutas_id;
    $organismo = Organismo::find($expedientesrequisito->requisitoruta->organismossectors->organismos_id);
    $configOrganismo = $organismo->configuraciones;

    $title = "Editar requisito del tipo de documento: " . $expedientesrequisito->requisitoruta->expedientestipos->expedientestipo;
    return view('expedientesrutasrequisitos.edit', ['expedientesrequisito' => $expedientesrequisito, 'title' => $title, 'rutaRequisito' => $rutaRequisito, 'configOrganismo' => $configOrganismo]);
  }

  public function update(Request $request, $id, $req_id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $activo = 1;
    if ($request->activo == "") {
      $activo = 0;
    };
    $obligatorio = 1;
    if ($request->obligatorio == "") {
      $obligatorio = 0;
    };


    $validator = Validator::make($request->all(), [
      'expedientesrutas_id' => 'required',
      'expedientesrequisito' => 'required|max:150',
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


    $expedientesrequisito = Expedientesrutasrequisitos::find($req_id);
    $expedientesrequisito->expedientesrequisito = $request->expedientesrequisito;
    $expedientesrequisito->expedientesrutas_id = $request->expedientesrutas_id;
    $expedientesrequisito->activo = $activo;
    $expedientesrequisito->obligatorio = $obligatorio;

    $expedientesrequisito->update();

    $expedientesruta = Expedientesruta::find($request->expedientesrutas_id);
    $title = "Requisitos documento rutas : " . $expedientesruta->expedientestipos->expedientestipo;

    $textoLog = "Modificó requisito " . $expedientesrequisito->expedientesrequisito . " de ruta del tipo " .  $expedientesruta->expedientestipos->expedientestipo;
    Logg::info($textoLog);
    $requisitos = Expedientesrutasrequisitos::all();

    return redirect()->route('requisitos.rutas', $request->expedientesrutas_id)->with('message', 'Actualizado');
  }

  public function estado($id)
  {

    $expedientesrequisito = Expedientesrutasrequisitos::find($id);

    if ($expedientesrequisito->activo) {
      $expedientesrequisito->activo = false;
    } else {
      $expedientesrequisito->activo = true;
    }
    $expedientesrequisito->save();

    $expedientesruta = Expedientesruta::find($expedientesrequisito->expedientesrutas_id);
    $textoLog = "Cambió estado del requisito " .  $expedientesrequisito->expedientesrequisito . " de ruta del tipo " .  $expedientesruta->expedientestipos->expedientestipo;;
    Logg::info($textoLog);

    return redirect()->back();
  }
}
