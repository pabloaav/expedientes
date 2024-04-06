<?php

namespace App\Http\Controllers;

use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Http\Request;
use Validator;
use Caffeinated\Shinobi\Models\Role;
use App\Roleuser;
use App\User;
use Auth, PDF;
use Caffeinated\Shinobi\Facades\Shinobi;
use App\Logg;

class RoleuserController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($id)
  {

    if (!session('permission')->contains('rol.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $user = User::find($id);
    $rolesusers = Roleuser::where('user_id', $id)
      ->paginate(30);

    $title = "Roles del usuario: " . $user->name;
    return view('rolesusers.index', [
      'user' => $user,
      'rolesusers' => $rolesusers,
      'title' => $title
    ]);
  }


  public function store(Request $request)
  {

    if (!session('permission')->contains('rol.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }




    $validator = Validator::make($request->all(), [
      'user_id' => 'required|exists:users,id',
      'role_id' => 'required|exists:roles,id',
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


    $roleuser = new Roleuser;
    $roleuser->user_id = $request->user_id;
    $roleuser->role_id = $request->role_id;
    $roleuser->save();

    $user = User::find($request->user_id);
    $role = Role::find($request->role_id);

    Logg::info('Agregó el rol ' . $role->name . ' al usuario '. $user->name);

    return redirect('/users/' . $request->user_id . '/roles');
  }





  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    if (!session('permission')->contains('rol.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $roleuser = Roleuser::find($id);
    $user_id = $roleuser->user_id;
    $user = User::find($user_id);
    $role = Role::find($roleuser->role_id);
    $textoLog = "Eliminó el rol ". $role->name . " del usuario " . $user->name;
    Logg::info($textoLog);

   
    $roleuser->delete();
    

    return redirect('/users/' . $user_id . '/roles');
  }

  /*
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
  public function finder(Request $request)
  {
    if (!session('permission')->contains('rol.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $roles = Role::where('name', 'like', '%' . $request->buscar . '%')->paginate(15);
    $title = "Usuario: buscando " . $request->buscar;
    return view('roles.index', ['roles' => $roles, 'title' => $title]);
  }


  public function search(Request $request)
  {
    $term = $request->term;

    $datos = Role::where('name', 'like', '%' . $request->term . '%')->where('activo', true)->get();
    $adevol = array();
    if (count($datos) > 0) {
      foreach ($datos as $dato) {
        $adevol[] = array(
          'id' => $dato->id,
          'value' => $dato->name,
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
}
