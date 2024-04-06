<?php

namespace App\Http\Controllers;

use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Http\Request;
use Validator;
use App\Soporte;
use App\Soportesrespuesta;

use Carbon\Carbon;
use Auth, PDF;

use App\Configuracion;
use NumeroALetras;

use Caffeinated\Shinobi\Facades\Shinobi;
use App\Logg;

use Illuminate\Support\Str;

class SoportesrespuestaController extends Controller
{


  public function store(Request $request)
  {

    if (!session('permission')->contains(function ($permiso) {
      return $permiso == 'soporte.index' || $permiso == 'soporteadmin.index';
    })){
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $validator = Validator::make($request->all(), [
      'respuesta' => 'required',
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
    $soportesrespuesta = new Soportesrespuesta;
    $soportesrespuesta->users_id = Auth::user()->id;
    $soportesrespuesta->soportes_id = $request->soportes_id;
    $soportesrespuesta->respuesta = $request->respuesta;
    $soportesrespuesta->vista = 0;
    $soportesrespuesta->save();
    $textoLog = "Respondió consulta ". $soportesrespuesta->soportes_id;
    Logg::info($textoLog);
    return redirect('/soportes/' . $request->soportes_id.'/show');
  }
}
