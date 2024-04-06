<?php

namespace App\Http\Controllers;

use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Http\Request;
use Validator;

use Caffeinated\Shinobi\Facades\Shinobi;
use App\Logg;
use Auth, PDF;

use App\Organismo;
use App\Expedientestipo;


class ExpedientestipoController extends Controller
{

  public function index($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $organismo = Organismo::find($id);
    $configOrganismo = $organismo->configuraciones;
    // cargar los tipos de documentos del organismo
    // $expedientestipos = Expedientestipo::where('organismos_id', $id)->paginate(50);
    $expedientestipos = Expedientestipo::where('organismos_id', $id)->get();
    $title = "Tipos de documentos del organismo " . $organismo->organismo;

    return view('expedientestipos.index', [
      'organismo' => $organismo,
      'expedientestipos' => $expedientestipos, 'title' => $title, 'configOrganismo' => $configOrganismo
    ]);
  }

  public function create($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $organismo = Organismo::find($id);
    $configOrganismo = $organismo->configuraciones;

    $title = "Nuevo tipo de documento del Organismo: " . $organismo->organismo;
    return view('expedientestipos.create', ['organismo' => $organismo, 'title' => $title, 'configOrganismo' => $configOrganismo]);
  }

  public function store(Request $request)
  {
    // variables necesarias para validar los codigos del organismo
    $organismo_id = $request->organismos_id;
    $codigo = $request->codigo;

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $activo = 1;
    if ($request->activo == "") {
      $activo = 0;
    };

    $financiero = 0;
    if ($request->financiero == "") {
      $financiero = 0;
    } else {
      $financiero = 1;
    }

    $sinRuta = 0;
    if ($request->sinRuta == "") {
      $sinRuta = 0;
    } else {
      $sinRuta = 1;
    }

    $publico = 0;
    if ($request->publico == "") {
      $publico = 0;
    } else {
      $publico = 1;
    }

    $historial_publico = 0;
    if ($request->historial_publico == "") {
      $historial_publico = 0;
    } else {
      $historial_publico = 1;
    }

    $fecha_editable = 0;
    if ($request->fecha_editable == "") {
      $fecha_editable = 0;
    } else {
      $fecha_editable = 1;
    }

    $siguiente_num = 1;
    if ($request->sig_num == "") {
      $siguiente_num = 0;
    }

    $repite_num = 0;
    if ($request->repite_num == "") {
      $repite_num = 0;
    } else {
      $repite_num = 1;
    }

    $controlcuil = 0;
    if (isset($request->controlcuil) and $request->controlcuil != "") {
      $controlcuil = 1;
    }

    $validator = Validator::make($request->all(), [
      'organismos_id' => 'required|exists:organismos,id',
      'codigo' => 'required|max:8',
      'expedientestipo' => 'required|max:254',
    ]);

    // // Agragmos una condicion de validacion en forma de funcion para limitar codigos repetidos solo en esta organizacion
    $validator->after(function ($validator) use ($codigo, $organismo_id) {
      $res =  Expedientestipo::where('organismos_id', $organismo_id)
        ->where('codigo', $codigo)
        ->get();

      if (!$res->isEmpty()) {
        $validator->errors()->add(
          'codigo',
          'El campo codigo ya esta en uso'
        );
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


    $expedientestipo = new Expedientestipo;
    $expedientestipo->organismos_id = $request->organismos_id;
    $expedientestipo->codigo = $request->codigo;
    // Si el tipo de documento es si  ruta - concatenar al nombre
    if ($sinRuta == 1) {
      $expedientestipo->expedientestipo = $request->expedientestipo . '-' . " Sin ruta definida.";
    } else {
      $expedientestipo->expedientestipo = $request->expedientestipo;
    }
    $expedientestipo->activo = $activo;
    $expedientestipo->financiero = $financiero;
    $expedientestipo->sin_ruta = $sinRuta;
    $expedientestipo->publico = $publico;
    $expedientestipo->historial_publico = $historial_publico;
    $expedientestipo->fecha_editable = $fecha_editable;

    if ($request->color != "") {
      $expedientestipo->color = $request->color;
    } else {
      $expedientestipo->color = "";
    }
    $expedientestipo->sig_num = $siguiente_num;
    $expedientestipo->repite_num = $repite_num;
    $expedientestipo->control_cuil = $controlcuil;
    $expedientestipo->save();

    $textoLog = "Creo un nuevo tipo de documento " .  $expedientestipo->expedientestipo;
    Logg::info($textoLog);

    return redirect('/organismos/' . $request->organismos_id . '/expedientestipos');
  }


  public function show($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $expedientestipo = Expedientestipo::find($id);
    $title = "Tipo de Documento";
    return view('expedientestipos.show', ['expedientestipo' => $expedientestipo, 'title' => $title]);
  }


  public function edit($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $expedientestipo = Expedientestipo::find($id);
    $organismo = Organismo::find($expedientestipo->organismos_id);
    $configOrganismo = $organismo->configuraciones;

    // dd($expedientestipo);
    if ($expedientestipo->sin_ruta == 1) {
      $tipo_documento = stristr($expedientestipo->expedientestipo, "- Sin ruta definida.", true);
    } else {
      $tipo_documento = $expedientestipo->expedientestipo;;
    }

    $title = "Editar tipo de documento " . $tipo_documento;
    return view('expedientestipos.edit', ['organismo' => $organismo, 'expedientestipo' => $expedientestipo, 'title' => $title, 'tipo_documento' => $tipo_documento, 'configOrganismo' => $configOrganismo]);
  }


  public function update(Request $request, $id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    // variables necesarias para validar los codigos del organismo (deben ser unicos)
    $expedientestipo = Expedientestipo::find($id);
    $organismo_id = $expedientestipo->organismos_id;
    $codigo = $request->codigo;

    $activo = 0;
    if ($request->activo == "") {
      $activo = 0;
    } else {
      $activo = 1;
    };

    $financiero = 0;
    if ($request->financiero == "") {
      $financiero = 0;
    } else {
      $financiero = 1;
    }

    $publico = 0;
    if ($request->publico == "") {
      $publico = 0;
    } else {
      $publico = 1;
    };

    $historial_publico = 0;
    if ($request->historial_publico == "") {
      $historial_publico = 0;
    } else {
      $historial_publico = 1;
    };

    $fecha_editable = 0;
    if ($request->fecha_editable == "") {
      $fecha_editable = 0;
    } else {
      $fecha_editable = 1;
    };

    $siguiente_num = 0;
    if ($request->sig_num == "") {
      $siguiente_num = 0;
    } else {
      $siguiente_num = 1;
    };

    $repite_num = 0;
    if ($request->repite_num == "") {
      $repite_num = 0;
    } else {
      $repite_num = 1;
    };

    $controlcuil = 0;
    if (isset($request->controlcuil) and $request->controlcuil != "") {
      $controlcuil = 1;
    }
    
    $validator = Validator::make($request->all(), [
      'codigo' => 'required|max:8',
      'expedientestipo' => 'required|max:254',
    ]);

    // // Agragmos una condicion de validacion en forma de funcion para limitar codigos repetidos solo en esta organizacion
    $validator->after(function ($validator) use ($codigo, $organismo_id, $id) {
      $res =  Expedientestipo::where('organismos_id', $organismo_id)
        ->where('codigo', $codigo)
        ->where('id', '!=', $id)
        ->get();
      if (!$res->isEmpty()) {
        $validator->errors()->add(
          'codigo',
          'El campo codigo ya esta en uso'
        );
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


    $expedientestipo->codigo = $request->codigo;
    if ($expedientestipo->sin_ruta == 1) {
      $expedientestipo->expedientestipo = $request->expedientestipo . '-' . " Sin ruta definida.";
    } else {
      $expedientestipo->expedientestipo = $request->expedientestipo;
    }
    $expedientestipo->financiero = $financiero;
    $expedientestipo->activo = $activo;
    $expedientestipo->publico = $publico;
    $expedientestipo->historial_publico = $historial_publico;
    $expedientestipo->fecha_editable = $fecha_editable;
    if ($request->color != "") {
      $expedientestipo->color = $request->color;
    } else {
      $expedientestipo->color = "";
    }
    $expedientestipo->sig_num = $siguiente_num;
    $expedientestipo->repite_num = $repite_num;
    $expedientestipo->control_cuil = $controlcuil;

    $expedientestipo->update();

    $textoLog = "Modificó tipo de documento " . $expedientestipo->expedientestipo;
    Logg::info($textoLog);

    return redirect('/organismos/' . $expedientestipo->organismos_id . '/expedientestipos');
  }


  public function destroy($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $expedientestipo = Expedientestipo::find($id);
    $organismos_id = $expedientestipo->organismos_id;

    $textoLog = "Eliminó tipo de documento " . $expedientestipo->expedientestipo;
    Logg::info($textoLog);
    $expedientestipo->delete();


    return redirect('/organismos/' . $organismos_id . '/expedientestipos');
  }


  /**
   * Este metodo se usa para el buscador en la vista index de tipos de tramites
   *
   * @param  mixed $request
   * @return void
   */
  public function finder(Request $request)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    // obtenemos los datos necesarios de la request
    $valorBusqueda = $request->buscar;
    $organismo_id = $request->organismo_id;

    $organismo = Organismo::find($organismo_id);

    if (empty($valorBusqueda)) {
      // si el campo esta vacio se devuelve todos los tipos de esta organizacion
      $expedientestipos = Expedientestipo::where('organismos_id', $organismo->id)->paginate(15);
      $title = "Buscando todos los tipos de documentos";
    } else {
      // Consultamos los tipos de expedientes de este organismo, y dentro de ese conjunto, los que hacen match con el valor de busqueda
      $expedientestipos = Expedientestipo::where('organismos_id', $organismo->id)
        ->where('expedientestipo', 'like', '%' . $valorBusqueda . '%')
        // ->orWhere('codigo', 'like', '%' . $valorBusqueda . '%')
        ->paginate(15);
      $title = "Buscando: " . " '" . $valorBusqueda . " '";
    }

    // Dado que se introduce nueva variable en la devolucion de la vista index, se debe devolver tambien aqui en la respuesta de la busqueda
    $configOrganismo = $organismo->configuraciones;

    return view('expedientestipos.index', ['expedientestipos' => $expedientestipos, 'organismo' => $organismo, 'title' => $title, 'configOrganismo' => $configOrganismo]);
  }


  public function search(Request $request)
  {
    $term = $request->term;
    $datos = Expedientestipo::where('expedientestipo', 'like', '%' . $request->term . '%')
      ->where('activo', true)->get();
    $adevol = array();
    if (count($datos) > 0) {
      foreach ($datos as $dato) {
        $adevol[] = array(
          'id' => $dato->id,
          'value' => $dato->expedientestipo,
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

  public function tipoexpedienteruta($id)
  {
    return Expedientestipo::find($id)->rutas;
  }

  public function estado($id)
  {

    $tipo = Expedientestipo::find($id);

    if ($tipo->activo) {
      $tipo->activo = false;
    } else {
      $tipo->activo = true;
    }
    $tipo->save();
    $textoLog = "Cambió estado tipo de documento " .  $tipo->expedientestipo;
    Logg::info($textoLog);


    return redirect()->back();
  }

  /* 
    * Esta funcion permite consultar el tipo de documento seleccionado en el formulario de creacion de expediente, y si para ese tipo de documento
    + la fecha es editable, se habilita el campo en el formulario
  */
  public function tipoSelected($id, $permiso) 
  {
    $expedientetipo = Expedientestipo::findOrFail($id);

    if ($expedientetipo->fecha_editable == 1 || $permiso == 1) {
      return response()->json([
        'respuesta' => 1,
        'sig_num' => $expedientetipo->sig_num
      ]);
    }
    else {
      return response()->json([
        'respuesta' => 2,
        'sig_num' => $expedientetipo->sig_num
      ]);
    }
  }
}
