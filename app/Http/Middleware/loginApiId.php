<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Organismossector;
use App\Organismosuser;
use App\User;
use App\Logg;

class loginApiId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {

          if(session('permission')->contains('usuario.superadmin'))
          {
            return $next($request);
          } 


            // Obtenemos el organismo de la consulta
            if (!is_null(Auth::user()->usersector->first()))
            {
              $Organismossector = Organismossector::findOrFail(Auth::user()->usersector->first()->organismossectors_id);
              $id = base64_decode($request->id);

              $user = User::where('login_api_id', $id )->first();
      
              $organismouser = Organismosuser::where('users_id', $user->id)->first();
            

              if ($Organismossector->organismos_id != $organismouser->organismos_id) {
                  return redirect('/')->with('error', 'Usuario ' . Auth::user()->name . ' no es del mismo organismo que dicho usuario');
              }
            }
            else
            {
              return redirect('/')->with('error', 'Usuario ' . Auth::user()->name . ' debe tener al menos 1 sector asignado');
            }

          } catch (\Exception $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
            return redirect('/')->with('error', 'No se ha encontrado dicho usuario o no es del mismo organismo al cual pertenece');
            
          }
      
      
      
          

        return $next($request);
    }
}
