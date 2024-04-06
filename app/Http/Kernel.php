<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
  /**
   * The application's global HTTP middleware stack.
   *
   * These middleware are run during every request to your application.
   *
   * @var array
   */
  protected $middleware = [
    \App\Http\Middleware\Cors::class,
    \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
    \App\Http\Middleware\TrimStrings::class,
    \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    \App\Http\Middleware\TrustProxies::class,
  ];

  /**
   * The application's route middleware groups.
   *
   * @var array
   */
  protected $middlewareGroups = [
    'web' => [
      \App\Http\Middleware\EncryptCookies::class,
      \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
      \Illuminate\Session\Middleware\StartSession::class,
      // Illuminate\Session\Middleware\AuthenticateSession::class,
      \Illuminate\View\Middleware\ShareErrorsFromSession::class,
      \App\Http\Middleware\VerifyCsrfToken::class,
      \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],

    'api' => [
      'throttle:60,1',
      'bindings',
    ],
  ];

  /**
   * The application's route middleware.
   *
   * These middleware may be assigned to groups or used individually.
   *
   * @var array
   */
  protected $routeMiddleware = [
    'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'has.role' => \Caffeinated\Shinobi\Middleware\UserHasRole::class,
    'has.permission' => \Caffeinated\Shinobi\Middleware\UserHasPermission::class,
    'organismo' => \App\Http\Middleware\SameOrganism::class,
    'has.sector'=> \App\Http\Middleware\HasSector::class,
    'sector'=> \App\Http\Middleware\SameSectorOrganism::class,
    'documento' => \App\Http\Middleware\SameDocumentoOrganismo::class,
    'expedienteTipos' => \App\Http\Middleware\SameTiposDocumentos::class,
    'foja'=> \App\Http\Middleware\SameFojaOrganismo::class,
    'docInt'=> \App\Http\Middleware\SameFirmaDocOrg::class,
    'deposito' => \App\Http\Middleware\SameDepositoOrganismo::class,
    'plantilla' => \App\Http\Middleware\SamePlantillaOrganismo::class,
    'sectorUser' => \App\Http\Middleware\sectoruserOrganismo::class,
    'loginApiId' => \App\Http\Middleware\loginApiId::class,
    'sameUserId' => \App\Http\Middleware\SameUserId::class,
    'soporte' => \App\Http\Middleware\SoporteSame::class,
    'ruta' => \App\Http\Middleware\RutasSameOrg::class,
    'requisito' => \App\Http\Middleware\RequisitosOrg::class,
    'jwt.verify' => \App\Http\Middleware\JwtMiddleware::class,
  ];
}
