<?php

namespace App\Http\Controllers;

use App\Deposito;
use App\Organismo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Caffeinated\Shinobi\Facades\Shinobi;
use Illuminate\Support\Facades\Validator;
use App\Logg;
use Auth;

class DepositoController extends Controller
{
  
  public function index($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
     return redirect()->route('index.home');
    }

    $organismo = Organismo::find($id);
    $depositos = Deposito::where("organismos_id","=",$organismo->id)->get();
    $title = "Depósitos " . $organismo->organismo;
    return view('depositos.index', ['title' => $title, 'depositos' => $depositos, 'organismo' => $organismo]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $organismo = Organismo::find($id);
    //sectores del organism
    $title = "Nuevo depósito  : " . $organismo->organismo;
    return view('depositos.create', ['organismo' => $organismo, 'title' => $title]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
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

    $validator = Validator::make($request->all(), [
      'deposito' => 'required',
      'direccion' => 'required',
      'localidad' => 'required',
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

    // falta expediente sector 
    $deposito = new Deposito;
    $deposito->deposito = $request->deposito;
    $deposito->direccion = $request->direccion;
    $deposito->localidad = $request->localidad;
    $deposito->activo = $activo;
    $deposito->organismos_id = $request->organismo_id;
    $deposito->save();
    $textoLog = "Creo el deposito " .  $deposito->deposito ;
    Logg::info($textoLog);

    return redirect('/organismos/' . $request->organismo_id . '/depositos')->with('success', 'El registro se agrego correctamente');
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Deposito  $deposito
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $deposito = Deposito::find($id);
    $organismo = Organismo::find($deposito->organismos_id);
    $title = "Depósito del organismo : " . $organismo->organismo;
    return view('depositos.show', ['deposito' => $deposito, 'organismo' => $organismo, 'title' => $title]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Deposito  $deposito
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $deposito = Deposito::find($id);
    $organismo = Organismo::find($deposito->organismos_id);
    $title = "Editar depósito del organismo : " . $organismo->organismo;
    return view('depositos.edit', ['deposito' => $deposito, 'organismo' => $organismo, 'title' => $title]);
  }

  public function update(Request $request)
  {
    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $activo = 1;
    if ($request->activo == "") {
      $activo = 0;
    };


    $validator = Validator::make($request->all(), [
      'deposito' => 'required',
      'direccion' => 'required',
      'localidad' => 'required',
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


    $deposito = Deposito::find($request->deposito_id);
    $deposito->deposito = $request->deposito;
    $deposito->direccion = $request->direccion;
    $deposito->localidad = $request->localidad;
    $deposito->activo = $activo;
    $deposito->save();
    $textoLog = "Modificó el deposito " .  $deposito->deposito;
    Logg::info($textoLog);

    $organismo = Organismo::find($deposito->organismos_id);

    return redirect('/organismos/' . $organismo->id . '/depositos')->with('success', 'El registro se agrego correctamente');
  }

  public function destroy(Deposito $deposito)
  {
    //
  }

  public function finder(Request $request)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $valorBusqueda = $request->buscar;
    $organismo_id = $request->organismo_id;

    $organismo = Organismo::find($organismo_id);

    if (empty($valorBusqueda)) {
      // si el campo esta vacio se devuelve todos los tipos de esta organizacion
      $depositos = Deposito::where('organismos_id', $organismo->id)->get();
      $title = "Buscando todos los depósitos";
    } else {
      // Consultamos los tipos de expedientes de este organismo, y dentro de ese conjunto, los que hacen match con el valor de busqueda
      $depositos = Deposito::where('organismos_id', $organismo->id)
        ->where('deposito', 'like', '%' . $valorBusqueda . '%')
        // ->orWhere('direccion', 'like', '%' . $valorBusqueda . '%')
        // ->orWhere('localidad', 'like', '%' . $valorBusqueda . '%')
        ->paginate(15);
      $title = "Buscando: " . " '" . $valorBusqueda . " '";
    }

    return view('depositos.index', ['title' => $title, 'depositos' => $depositos, 'organismo' => $organismo]);
  }

  public function search(Request $request)
  {
    $term = $request->term;
    $organismo =  Auth::user()->userorganismo->first()->organismos_id;
    $datos = Deposito::where("organismos_id", "=", $organismo)->where('deposito', 'like', '%' . $request->term . '%')
      ->where('activo', true)->get();
    $adevol = array();
    if (count($datos) > 0) {
      foreach ($datos as $dato) {
        $adevol[] = array(
          'id' => $dato->id,
          'value' => $dato->deposito,
        );
      }
    } else {
      $adevol[] = array(
        'id' => 0,
        'value' => 'No hay coincidencias para ' .  $term
      );
    }
    return json_encode($adevol);
  }


  public function estado($id)
  {

    $deposito = Deposito::find($id);

    if ($deposito->activo) {
      $deposito->activo = false;
    } else {
      $deposito->activo = true;
    }
    $deposito->save();
    $textoLog = "Cambió estado deposito " .  $deposito->deposito;
    Logg::info($textoLog);


    return redirect()->back();
  }

}
