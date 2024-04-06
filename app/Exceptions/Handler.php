<?php

namespace App\Exceptions;

use Exception;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
  /**
   * A list of the exception types that are not reported.
   *
   * @var array
   */
  protected $dontReport = [
    //
  ];

  /**
   * A list of the inputs that are never flashed for validation exceptions.
   *
   * @var array
   */
  protected $dontFlash = [
    'password',
    'password_confirmation',
  ];

  /**
   * Report or log an exception.
   *
   * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
   *
   * @param  \Exception  $exception
   * @return void
   */
  public function report(Exception $exception)
  {
    parent::report($exception);
  }

  /**
   * Render an exception into an HTTP response.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Exception  $exception
   * @return \Illuminate\Http\Response
   */
  public function render($request, Exception $exception)
  {
    // EN el caso de que la excepcion provenga de la no respuesta del servicio de autenticacion, se presenta la vista
    // de error 500 pero con el mensaje de que fallo la autenticacion
    // Ver: resources\views\errors\500.blade.php
    if ($exception instanceof ConnectException) {
      abort(500, 'El servicio de autenticación no esta disponible en este momento.');
    }

    // if ($exception instanceof QueryException) {
    //   abort(500, 'La base de datos no responde.');
    // }

    if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
      return response()->json(['error'=>1,'message'=> 'ModelNotFoundException handled for API' ], 400);
   }

    

    return parent::render($request, $exception);
  }
}
