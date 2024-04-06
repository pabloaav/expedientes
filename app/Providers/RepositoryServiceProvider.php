<?php

namespace App\Providers;
use App\Interfaces\LoginInterfaces;
use App\Repositories\LoginRepositoryRemoto;

/*documentos */
use App\Interfaces\DocumentosInterfaces;
use App\Repositories\DocumentosRepository;

/*rutas de documentos */
use App\Interfaces\DocumentosRutasInterfaces;
use App\Repositories\DocumentosRutasRepository;

/*Fojas */
use App\Interfaces\FojasInterfaces;
use App\Repositories\FojasRepository;

/*Sectores */
use App\Interfaces\SectoresInterfaces;
use App\Repositories\SectoresRepository;

/*Tipos de documentos de un organismo */
use App\Interfaces\TiposDocumentosInterfaces;
use App\Repositories\TiposDocumentosRepository;

/*Usuarios */
use App\Interfaces\UsuariosInterfaces;
use App\Repositories\UsuariosRepository;

/*Renaper */
use App\Interfaces\PersonasInterfaces;
use App\Repositories\PersonasRepositoryRemote;

use App\Interfaces\PersonasLocalInterfaces;
use App\Repositories\PersonasRepository;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(LoginInterfaces::class, LoginRepositoryRemoto::class);
        $this->app->bind(DocumentosInterfaces::class, DocumentosRepository::class);
        $this->app->bind(DocumentosRutasInterfaces::class, DocumentosRutasRepository::class);
        $this->app->bind(FojasInterfaces::class, FojasRepository::class);
        $this->app->bind(SectoresInterfaces::class, SectoresRepository::class);
        $this->app->bind(TiposDocumentosInterfaces::class, TiposDocumentosRepository::class);
        $this->app->bind(UsuariosInterfaces::class, UsuariosRepository::class);
        $this->app->bind(PersonasInterfaces::class, PersonasRepositoryRemote::class);
        $this->app->bind(PersonasLocalInterfaces::class, PersonasRepository::class);
    }
}
