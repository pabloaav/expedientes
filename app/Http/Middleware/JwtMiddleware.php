<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
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
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json([
                    'status' => false,
                    'code' => 401,
                    'message' => 'El token enviado es inválido'
                ], 401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json([
                    'status' => false,
                    'code' => 401,
                    'message' => 'El token enviado ha expirado'
                ], 401);
            }else{
                return response()->json([
                    'status' => false,
                    'code' => 401,
                    'message' => 'No se encontró el token de autorización'
                ], 401);
            }
        }
        return $next($request);
    }
}
