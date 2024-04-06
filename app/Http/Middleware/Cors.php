<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //Origin Telco Forms
        if ($request->headers->get('origin') == "https://dev-front-forms.telco.com.ar") {
            return $next($request)
        ->header('Access-Control-Allow-Origin',  "https://dev-front-forms.telco.com.ar")
        ->header('Access-Control-Allow-Methods', 'GET,HEAD,OPTIONS,POST,PUT,DELETE')
        ->header('Access-Control-Allow-Headers', '*');  
        }
        //Test Local
        if ($request->headers->get('origin') == "http://localhost:8080") {
            return $next($request)
        ->header('Access-Control-Allow-Origin',  "http://localhost:8080")
        ->header('Access-Control-Allow-Methods', 'GET,HEAD,OPTIONS,POST,PUT,DELETE')
        ->header('Access-Control-Allow-Headers', '*');    
        }

        if ($request->headers->get('origin') == null) {
            return $next($request) ;
        }
        $origin= $request->headers->get('origin');
        return $next($request)
        ->header('Access-Control-Allow-Origin',  $origin)
        ->header('Access-Control-Allow-Methods', 'GET,HEAD,OPTIONS,POST,PUT,DELETE')
        ->header('Access-Control-Allow-Headers', '*');      
        }
}