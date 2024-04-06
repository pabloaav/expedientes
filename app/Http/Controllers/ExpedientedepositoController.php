<?php

namespace App\Http\Controllers;

use App\Deposito;
use App\Expediente;
use App\Expedientedeposito;
use App\Expedienteestado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Logg;

class ExpedientedepositoController extends Controller
{
  public function index($id)
  {
    $session = session('permission');
    if (!$session->contains('depositos.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $id = base64_decode($id);
    $expedientedeposito = Expedientedeposito::where('expedientes_id', $id)->get();
    $expedientes = Expediente::find($id);

    // Autorizacion
    try {
      $this->authorize('show', [$expedientes, $session]);
    } catch (\Exception $exception) {
      Logg::error($exception->getMessage(),("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()) );
      if ($exception instanceof AuthorizationException) {
        return redirect()->route('expedientes.index')->with('errors', ['No tiene permisos sobre el documento/sector.']);
      }
    }
    // $title = "Deposito del Documento n°" . $expedientes->expediente_num;
    $title = "Deposito del Documento N° " . getExpedienteName($expedientes);
    return view('expedientedeposito.index', ['title' => $title, 'expedientes' => $expedientes, 'expedientedeposito' => $expedientedeposito]);
  }

  public function show($expediente_id)
  {
    $relations = ['deposito'];
    //todos los depositos
    $deposito = Deposito::where('organismos_id', Expediente::find($expediente_id)->organismos_id)->where('activo', true)->get();
    //depositos donde se encuentra el expediente
    $depositosshow = Expedientedeposito::with($relations)->where("expedientes_id", $expediente_id)->get();

    //comparar 2 colleciones traer depositos libres
    $deposito = $deposito->map(function ($deposito) {
      return $deposito->id;
    });
    $depositosshow = $depositosshow->map(function ($depositosshow) {
      return $depositosshow->depositos_id;
    });
    $filtered = ($deposito->diff($depositosshow));
    $depositolibre =  Deposito::whereIn('id', $filtered)->get();

    $organismodeposito =  Expediente::find($expediente_id)->organismos->organismo;
    $num_exp = Expediente::find($expediente_id)->id;
    return response()->json(['organismodeposito' => $organismodeposito, 'num_exp' => $num_exp, 'depositolibre' => $depositolibre]);
  }

  public function asignardepositoexpediente($index1, $index2)
  {
    try {
    
      $encuentra = Expedientedeposito::where('expedientes_id', $index2)->get();
      $expediente = Expediente::find($index2);
      $deposito = Deposito::find($index1);
      
      $resultado = $encuentra->isEmpty();

    if (!$resultado ) {
      return response()->json(['3']);
    }
    
    $exp_deposito = new Expedientedeposito();
    $exp_deposito->depositos_id = $index1;
    $exp_deposito->expedientes_id = $index2;
    $exp_deposito->save();

    try {
    
    $textoLog = "Archivó " . org_nombreDocumento() . " " . getExpedienteName($expediente) . " a las " . Carbon::now()->toTimeString();
    Logg::info($textoLog);
    historial_doc(($expediente->id), $textoLog,"archivado" );

   
    } catch (\Throwable $th) {
      Logg::error($th->getMessage(),("Archivo:" . pathinfo($th->getFile())['basename'] . " - Línea:" . $th->getLine()) );
      return response()->json(['2']);
    }
   


    return response()->json(['1']);
  } catch (\Throwable $th) {
    Logg::error($th->getMessage(),("Archivo:" . pathinfo($th->getFile())['basename'] . " - Línea:" . $th->getLine()) );
    return response()->json(['2']);
  }

    
  }

  public function destroy($id)
  {
    $depositodestroy = Expedientedeposito::find($id);
    $expediente =  $depositodestroy->expediente;
    $depositodestroy->delete();

    try {
    //Estado Expediente
    //$expediente = Expediente::find($id);
   
    $textoLog = "Desarchivó " . org_nombreDocumento() . " " . getExpedienteName($expediente) . " a las " . Carbon::now()->toTimeString();
    Logg::info($textoLog);
    historial_doc(($expediente->id), $textoLog );
 
  } catch (\Throwable $th) {
    Logg::error($th->getMessage(),("Archivo:" . pathinfo($th->getFile())['basename'] . " - Línea:" . $th->getLine()) );
    return response()->json(['2']);
  }

    return response()->json(['1']);
  }


  public function consultar($id)
  {
    $consultardeposito = Expedientedeposito::find($id);
    return response()->json(['consultardeposito' => $consultardeposito]);
  }

  public function storeobservacion(Request $request)
  {
    $guardarobservacion = Expedientedeposito::find($request->id);
    $guardarobservacion->observacion = $request->observacion;
    $guardarobservacion->update();
    $Depo = Deposito::find( $guardarobservacion->depositos_id);
    $Docu = Expediente::find( $guardarobservacion->expedientes_id);
    $textoLog = "Escribió observación de documento " . getExpedienteName($Docu) . " en deposito  " . $Depo->deposito ;
    Logg::info($textoLog);
    return response()->json(['1']);
  }

  // Esta funcion permite archivar un expediente que se encuentra en un deposito pero el estado no sea "archivado"
  // se actualiza la fecha en que ingresó al deposito y se cambia el estado del expediente a "archivado"
  public function rearchivarExpediente ($expdeposito_id) {
    try {

      $expedientedeposito = Expedientedeposito::find($expdeposito_id);
      $expediente = Expediente::find($expedientedeposito->expedientes_id);
      $deposito = $expedientedeposito->deposito->deposito;

      $expedientedeposito->created_at = Carbon::now();
      $expedientedeposito->updated_at = Carbon::now();
      
      $expedientedeposito->update();

      $textoLog = Auth::user()->name . " rearchivó el " . org_nombreDocumento() . " " . getExpedienteName($expediente) . " a las " . Carbon::now()->toTimeString();
      Logg::info($textoLog);

      historial_doc(($expediente->id), $textoLog,"archivado");

      return response()->json(['respuesta' => '1', 'deposito' => $deposito]);
    } catch (\Throwable $th) {
      Logg::error($th->getMessage(),("Archivo:" . pathinfo($th->getFile())['basename'] . " - Línea:" . $th->getLine()) );
      return response()->json(['respuesta' => '2']);
    }
    
  }

  public function cambiarDeposito($deposito_id, $expediente_id)
  {
    try
    {
      // Eliminar el expediente del deposito
      $depositodestroy = Expedientedeposito::where('expedientes_id', $expediente_id)->first();
      $deposito_original = $depositodestroy->deposito->deposito; // nombre del deposito donde estaba el expediente
      $expediente = $depositodestroy->expediente;
      $depositodestroy->delete();

      // Asignar deposito nuevo
      $exp_deposito = new Expedientedeposito();
      $exp_deposito->depositos_id = $deposito_id;
      $exp_deposito->expedientes_id = $expediente_id;
      $exp_deposito->save();

      $deposito_actual = $exp_deposito->deposito->deposito; // nombre del deposito a donde se cambia el expediente

      $textoLog = Auth::user()->name ." cambió el " . org_nombreDocumento() . " " . getExpedienteName($expediente) . " del depósito ". $deposito_original ." al depósito ". $deposito_actual ." a las " . Carbon::now()->toTimeString();
      Logg::info($textoLog);
      historial_doc(($expediente->id), $textoLog, $expediente->expedientesestados->last()->expendienteestado);
    }
    catch (\Throwable $th)
    {
      Logg::error($th->getMessage(),("Archivo:" . pathinfo($th->getFile())['basename'] . " - Línea:" . $th->getLine()) );

      return response()->json([
        'respuesta' => '2'
      ]);
    }

    return response()->json([
      'respuesta' => 1
    ]);
  }
}
