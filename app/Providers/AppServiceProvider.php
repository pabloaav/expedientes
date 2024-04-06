<?php

namespace App\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
// use Carbon\Carbon;


class AppServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    //
    //Carbon::setLocale(env('LOCALE', 'en'));

    //die;

    // Paginator::useBootstrap();
    // Enable pagination
    Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
      $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
      return new LengthAwarePaginator(
        $this->forPage($page, $perPage),
        $total ?: $this->count(),
        $perPage,
        $page,
        [
          'path' => LengthAwarePaginator::resolveCurrentPath(),
          'pageName' => $pageName,
        ]
      );
    });
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    
  }
}
