<?php

namespace App\Http\Controllers;

use App\Expediente;
use App\Expedienteestado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Logg;

class NotificacionesController extends Controller
{
    public function index()
    {     
      $notifications = Expediente::join("expendientesestados", "expedientes.id", "=", "expendientesestados.expedientes_id")
      ->where('notificacion_usuario', 'No leido')
      ->where('users_id', Auth::user()->id)
      ->orderBy('expendientesestados.created_at', 'DESC')
      ->paginate(5);  
      $title = "Mis notificaciones";
      return view('notificaciones.index', ['title' => $title , 'notifications' => $notifications ]);
  
    }
  
    public function leidas()
    {
      $notifications_leidas = Expediente::join("expendientesestados", "expedientes.id", "=", "expendientesestados.expedientes_id")
      ->where('notificacion_usuario', 'Leído')
      ->where('users_id', Auth::user()->id)
      ->orderBy('expendientesestados.created_at', 'DESC')
      ->paginate(5);  
      $title = "Notificaciones leídas";
      return view('notificaciones.notificaciones-leidas', ['title' => $title , 'notifications' => $notifications_leidas ]);
  
    }

  public function update($id)
    {
       $notificacion = Expedienteestado::find($id);
       DB::beginTransaction();
       try {
          $notificacion->notificacion_usuario = 'Leído';
          $notificacion->update();
          DB::commit();
          return redirect()->back();
       } catch (\Exception $e) {
          DB::rollback();
          Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
       }
    }

    public function updateRedirect($id)
    {
      $notificacion = Expedienteestado::find($id);
      NotificacionesController::update($id);
      return redirect()->route('expediente.show', base64_encode($notificacion->expedientes_id));

    }

    public function alerta()
    {             
             $user = User::find(Auth::user()->id);
             $notifications =  $user->notificaciones->where('notificacion_usuario', 'No leido')->count();

             return json_decode($notifications);

    }


}
