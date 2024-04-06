<?php

namespace App\Http\Controllers;

use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Http\Request;
use Validator;
use Caffeinated\Shinobi\Models\Permission;
use Caffeinated\Shinobi\Models\Role;
use App\Permissionrole;
use Caffeinated\Shinobi\Facades\Shinobi;
use App\Logg;

class PermissionroleController extends Controller
{


  public function index($id)
  {

    if (!session('permission')->contains('rol.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $role = Role::find($id);
    $permissions = Permission::orderby('name', 'asc')->paginate(100);
    $title = "Permisos del Rol: " . $role->name;
    return view('permissionsroles.index', [
      'role' => $role,
      'permissions' => $permissions,
      'title' => $title
    ]);
  }






  public function update($roleid, $permissionid)
  {

    if (!session('permission')->contains('rol.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $permissionrole = Permissionrole::where('role_id', $roleid)
      ->where('permission_id', $permissionid)
      ->first();

    if ($permissionrole) {
      $permissionrole->delete();
    } else {
      $permissionrol = new Permissionrole;
      $permissionrol->role_id = $roleid;
      $permissionrol->permission_id = $permissionid;
      $permissionrol->save();

      $textoLog = "Modificó rol ". $permissionrol->role_id;
      Logg::info($textoLog);
    }
    return redirect('/roles/' . $roleid . '/permissions');
  }
}
