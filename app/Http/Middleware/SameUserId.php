<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Logg;

class SameUserId
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
            // Obtenemos el organismo de la consulta
            
            $idLogueado = Auth::user()->id;
            $id = base64_decode($request->id);

            $user = User::where('id', $id)->first();
           

            if ($idLogueado !=  $user->id) {
                return redirect('/')->with('error', 'No puede realizar dicha operación.');
            }

          } catch (\Exception $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
            return redirect('/')->with('error', 'No se ha encontrado dicho usuario o no es del mismo organismo al cual pertenece');
            
          }
      
      
      
          

        return $next($request);
    }
}
