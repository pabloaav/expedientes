<?php

namespace App\Providers;

use App\Expediente;
use App\Foja;
use App\Organismo;
use App\Policies\ExpedientePolicy;
use App\Policies\FojaPolicy;
use App\Policies\OrganismoPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * The policy mappings for the application.
   *
   * @var array
   */
  protected $policies = [
    Expediente::class => ExpedientePolicy::class,
    Foja::class => FojaPolicy::class,
    Organismo::class => OrganismoPolicy::class
  ];

  /**
   * Register any authentication / authorization services.
   *
   * @return void
   */
  public function boot()
  {
    $this->registerPolicies();

    // Gate para saber si un usuario tiene asignado un expediente y puede crear una foja
    Gate::define('foja-create', function ($user, $expediente) {
      if ($expediente->expedientesestados->last()->users_id === $user->id) {
        return true;
      } else {
        return false;
      }
    });
  }
}
