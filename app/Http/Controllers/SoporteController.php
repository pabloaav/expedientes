<?php

namespace App\Http\Controllers;

use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Http\Request;
use Validator;
use App\Soporte;
use App\Soportesrespuesta;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NuevoTicket;
use App\User;
use App\Organismo;

use Carbon\Carbon;
use Auth, PDF;

use App\Configuracion;


use Caffeinated\Shinobi\Facades\Shinobi;
use App\Logg;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;

class SoporteController extends Controller
{

  public function index()
  {    
  
    
    $session = session('permission');
    if (!$session->contains(function ($permiso) {
      return $permiso == 'soporte.index' || $permiso == 'soporteadmin.index';
    })){
      return redirect()->back()->with("error", 'No tiene acceso para ingresar a este modulo - Comunicarse con el administrador del sistema');
    }

    if (!session('permission')->contains('soporteadmin.index')) {
      // obtiene solo los tickets del usuario 
      $soportes = Soporte::where('users_id', Auth::user()->id)->Orderby('id', 'desc')->paginate(50);
    } else {
      // si es super admin todos los tickets de todos los organismos 
      // agregar de que organismo el usuario que agrego el tickets 
      $soportes = Soporte::Orderby('id', 'desc')->paginate(50);

      // $soportes = DB::table('soportes')
      //       ->join("users", "soportes.users_id", "=", "users.id")
      //       ->join("organismosusers", "organismosusers.users_id", "=", "users.id")
      //       ->join("organismos", "organismos.id", "=", "organismosusers.organismos_id")
      //       // ->select("expedientes.*", "expendientesestados.*", "users.name")
      //       // ->where('expedientes.organismos_id', $usersector)
      //       // ->where("users.name", 'LIKE', '%'.$search.'%')
      //       ->Orderby('soportes.id', 'desc')
      //       ->paginate(50);

      // dd($soportes);
    }


    $title = "Soporte";
    return view('soportes.index', ['soportes' => $soportes, 'title' => $title]);
  }


  public function create()
  {

    if (!session('permission')->contains('soporte.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $title = "Crear Ticket de Soporte";
    return view('soportes.create', ['title' => $title]);
  }


  public function store(Request $request)
  {

    if (!session('permission')->contains('soporte.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $validator = Validator::make($request->all(), [
      'consulta' => 'required',
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


    $soporte = new Soporte;
    $soporte->users_id = Auth::user()->id;
    $soporte->visto = false;
    $soporte->estado = 'espera';
    $soporte->consulta = $request->consulta;
    $soporte->save();

    // Se envia un mail de notificacion a un correo determinado para dar aviso del nuevo ticket
    $organismo = $soporte->users->organismo->organismo;
    Notification::route('mail', 'soportesoftware@telco.com.ar')
                ->notify(new NuevoTicket(("Nuevo ticket del Organismo: ". $organismo)));

    $textoLog = "Registró una consulta en soporte";
      Logg::info($textoLog);

    return redirect('/soportes');
  }


  public function show($id)
  {

    if (!session('permission')->contains(function ($permiso) {
      return $permiso == 'soporte.index' || $permiso == 'soporteadmin.index';
    })){
      return redirect()->route('index.home');
    }


    $soporte = Soporte::find($id);

    $soportesrespuestas = Soportesrespuesta::where('soportes_id', $soporte->id)->Orderby('id', 'asc')->get();

    $title = "Tickets Soporte Nro: " . str_pad($soporte->id, 6, "0", STR_PAD_LEFT);

    return view('soportes.show', ['soporte' => $soporte, 'soportesrespuestas' => $soportesrespuestas, 'title' => $title]);
  }





  public function resuelta($id)
  {
    if (!session('permission')->contains('soporteadmin.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $soporte = Soporte::find($id);
    $soporte->estado = 'resuelta';
    $soporte->save();
    $textoLog = "Cambió estado consulta ". $id ." Resuelta en soporte";
    Logg::info($textoLog);

    return redirect('/soportes/' . $soporte->id.'/show');
  }


  public function rechazada($id)
  {
    if (!session('permission')->contains('soporteadmin.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $soporte = Soporte::find($id);
    $soporte->estado = 'rechazada';
    $soporte->save();
    $textoLog = "Cambió estado consulta ". $id ." Rechazada en soporte";
    Logg::info($textoLog);


    return redirect('/soportes/' . $soporte->id.'/show');
  }

  public function resolviendo($id)
  {
    if (!session('permission')->contains('soporteadmin.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $soporte = Soporte::find($id);
    $soporte->estado = 'resolviendo';
    $soporte->save();
    $textoLog = "Cambió estado consulta ". $id ." Resolviendo en soporte";
    Logg::info($textoLog);


    return redirect('/soportes/' . $soporte->id.'/show');
  }

  public function pendientededesarrollo($id)
  {
    if (!session('permission')->contains('soporteadmin.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $soporte = Soporte::find($id);
    $soporte->estado = 'pendiente de desarrollo';
    $soporte->save();
    $textoLog = "Cambió estado consulta ". $id ." Pendiente en soporte";
    Logg::info($textoLog);


    return redirect('/soportes/' . $soporte->id.'/show');
  }


  public function cerrar($id)
  {
    if (!session('permission')->contains('soporteadmin.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $soporte = Soporte::find($id);
    $soporte->abierta = false;
    $soporte->save();
    $textoLog = "Cerró consulta ". $id ." en soporte";
    Logg::info($textoLog);

    return redirect('/soportes/' . $soporte->id.'/show');
  }

  public function abrir($id)
  {
    if (!session('permission')->contains('soporteadmin.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $soporte = Soporte::find($id);
    $soporte->abierta = true;
    $soporte->save();
    $textoLog = "Abrió consulta ". $id ." en soporte";
    Logg::info($textoLog);

    return redirect('/soportes/' . $soporte->id.'/show');
  }
}
