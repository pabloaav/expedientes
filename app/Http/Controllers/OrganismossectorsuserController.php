<?php

namespace App\Http\Controllers;

use App\Logg;
use Auth;
use Validator;
use App\Organismo;
use App\User;
use App\Organismossector;
use App\Expediente;
use Illuminate\Http\Request;
use App\Organismossectorsuser;
use Illuminate\Support\Facades\DB;
use Caffeinated\Shinobi\Facades\Shinobi;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class OrganismossectorsuserController extends Controller
{

  public function index($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $organismossector = Organismossector::find($id);
    $organismo = Organismo::where('id',  $organismossector->organismos_id)->first();
    // $organismossectorsusers = Organismossectorsuser::where('organismossectors_id', $organismossector->id)->paginate(10);
    $organismossectorsusers = Organismossectorsuser::where('organismossectors_id', $organismossector->id)->get();
    $title = "Usuarios del sector " . $organismossector->organismossector;
    return view('organismossectorsusers.index', ['organismossector' => $organismossector, 'organismossectorsusers' => $organismossectorsusers, 'title' => $title, 'organismo' => $organismo]);
  }

  public function store(Request $request)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $users_exist = Organismossectorsuser::where('users_id','=',intval($request->users_id))->exists();
   if ($request->users_id == NULL) {
    return redirect('/organismossectors/' . $request->organismossectors_id . '/organismossectorsusers')->with('error', 'Campo vacio , ingrese un usuario');
    } else if (($users_exist == true) ) {
      $users_existHere = Organismossectorsuser::where('users_id','=',intval($request->users_id))->where('organismossectors_id','=',intval($request->organismossectors_id))->exists();
      if ($users_existHere) {
        return redirect('/organismossectors/' . $request->organismossectors_id . '/organismossectorsusers')->with('error', 'El usuario ya tiene asignado este sector');
      } else{
      $organismossectorsuser = new Organismossectorsuser;
      $organismossectorsuser->organismossectors_id = $request->organismossectors_id;
      $organismossectorsuser->users_id = $request->users_id;
      $organismossectorsuser->save();
      $user = User::find($organismossectorsuser->users_id);
      $sector = Organismossector::find( $organismossectorsuser->organismossectors_id);
      $textoLog = "Asignó usuario " .   $user->name . " al sector ".   $sector->organismossector;
      Logg::info($textoLog);

      return redirect('/organismossectors/' . $request->organismossectors_id . '/organismossectorsusers')->with('success', 'El usuario se agrego correctamente');
    }
    // } else if ($users_exist == true ) {
    //   $sector = (Organismossectorsuser::where('users_id','=',intval($request->users_id))->first())->organismosector->organismossector;
    //   return redirect('/organismossectors/' . $request->organismossectors_id . '/organismossectorsusers')->with('error', 'El usuario que intenta agregar ya está asignado al sector '. $sector);
    } else if ($request->users_id == 0) {
      return redirect('/organismossectors/' . $request->organismossectors_id . '/organismossectorsusers')->with('error', 'No existen coincidencias para el usuario buscado');
    }
    else {

      $organismossectorsuser = new Organismossectorsuser;
      $organismossectorsuser->organismossectors_id = $request->organismossectors_id;
      $organismossectorsuser->users_id = $request->users_id;
      $organismossectorsuser->save();

      $user = User::find($organismossectorsuser->users_id);
      $sector = Organismossector::find( $organismossectorsuser->organismossectors_id);
      $textoLog = "Asignó usuario " .   $user->name . " al sector ".   $sector->organismossector;
      Logg::info($textoLog);

      return redirect('/organismossectors/' . $request->organismossectors_id . '/organismossectorsusers')->with('success', 'El usuario se agrego correctamente');
    }
  }

  public function destroy($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return view('');
    }
    $organismossectorsuser = Organismossectorsuser::find($id);
    $sector= organismossector::find($organismossectorsuser->organismossectors_id);

     try {

     $querys01 = Expediente::all()->where('organismos_id', '=', $sector->organismos_id)->filter(function ($expediente) use ($organismossectorsuser) {
      return ($expediente->expedientesestados->last()->users_id == $organismossectorsuser->users_id and $expediente->expedientesestados->last()->rutasector->organismossectors->id == $organismossectorsuser->organismossectors_id);
    });
     
    if (count($querys01)>0) {
      //return redirect('/organismossectors/' . $organismossectorsuser->organismossectors_id . '/organismossectorsusers')->with('errors', ['El usuario tiene asignado documentos. No se pudo completar la accion']);
      return response()->json(['2']);
        }
         else{
          $user = User::find($organismossectorsuser->users_id);
          $sector = Organismossector::find( $organismossectorsuser->organismossectors_id);
          $textoLog = "Desasignó usuario " .   $user->name . " al sector ".   $sector->organismossector;
          Logg::info($textoLog);
          $organismossectorsuser->delete();
        }
     
    } catch (\Throwable $th) {
      Logg::error($th->getMessage(),("Archivo:" . pathinfo($th->getFile())['basename'] . " - Línea:" . $th->getLine()) );
      return response()->json(['3']);
      //return redirect('/organismossectors/' . $organismossectorsuser->organismossectors_id . '/organismossectorsusers')->with('errors', ['No se pudo completar la accion por algun fallo en los datos']);
    }
    return response()->json(['1']);
     //return redirect('/organismossectors/' . $organismossectorsuser->organismossectors_id . '/organismossectorsusers');
  }

  // Esta funcion permite obtener la lista de sectores que no pertenece el usuario y a los cuales puede ser asignado
  public function getSectoresUser($org_id, $user_id) {

    try {
      $organismo = Organismo::find($org_id);
      $user = User::where('login_api_id', $user_id)->first(); // se consulta el usuario seleccionado a partir de su login_api_id
      $name_sectores_user = collect();

      if (isset($user)) {
        $sectores_user = $user->usersector;
        $organismo_sectores = $organismo->sectores->where('activo', 1);

        foreach ($organismo_sectores as $key => $organismo_sector) {

          foreach ($sectores_user as $sector_user) {

            if ($organismo_sector->id == $sector_user->organismossectors_id) {
              $organismo_sectores->pull($key);
            }
          }
        }

        // se carga en una coleccion los nombres de los sectores a los que pertenece el usuario para mostrarlos en la vista
        if ($sectores_user->count() > 0) {
          foreach ($sectores_user as $name_sector) {
            $name_sectores_user->push(['id' => $name_sector->id, 'organismossector' => $name_sector->organismosector->organismossector]); // se carga en una coleccion el organismossectorsuser_id, necesario para la funcionalidad de eliminar usuario de sector, y el nombre del sector al que pertenece para mostrarse en ventana modal
          }
        }
      }
      else {
        return response()->json([
          'respuesta' => 3
        ]);
      }

    } catch (\Exception $e) {

      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return response()->json([
        'respuesta' => 2
      ]);
    }
    
    return response()->json([
      'respuesta' => 1,
      'sectores' => $organismo_sectores,
      'user' => $user->id,
      'sectores_user' => $name_sectores_user
    ]);
  }

  // Esta funcion permite crear una relacion entre el usuario y el/los sectores seleccionados desde la lista de usuarios del organismo
  public function storeMultiple(Request $request) {

    try {

      $user_exist = User::where('id', '=', $request->user_id)->exists(); // se consulta si el usuario seleccionado está cargado en la tabla de Users
      $sectores = $request->sectores;

      if (!$user_exist) {

        return response()->json([
          'respuesta' => 3
        ]);
      }
      else {
        if (isset($sectores)) {

          // si el usuario existe, se va recorriendo el array de sectores con sus respectivos id, se guarda la relacion de sector-usuario y se registra en el log del usuario que realizó la operación
          foreach ($sectores as $sector) {
            
            $organismossectorsuser = new Organismossectorsuser;
            $organismossectorsuser->organismossectors_id = $sector;
            $organismossectorsuser->users_id = $request->user_id;
            $organismossectorsuser->save();

            $user = User::find($organismossectorsuser->users_id);
            $sector = Organismossector::find($organismossectorsuser->organismossectors_id);
            $textoLog = "Asignó usuario " .   $user->name . " al sector ".   $sector->organismossector;
            Logg::info($textoLog);
          }
        }
        else {
  
          return response()->json([
            'respuesta' => 4
          ]);
        }
      }

    } catch (\Exception $e) {

      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return response()->json([
        'respuesta' => 2
      ]);
    }

    return response()->json([
      'respuesta' => 1
    ]);
    
  }
}
