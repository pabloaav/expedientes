<?php

namespace App\Listeners;

use App\Events\LoginInicio;
use App\Logg;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpParser\Node\Expr\New_;

class Sendlistener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LoginInicio  $event
     * @return void
     */
    public function handle(LoginInicio $event)
    {
        $logs = New Logg();
        $logs->users_id = $event->user->id;
        $logs->log = "Inicio sesion usuario ". $event->user->name;
        $logs->save();
    }
}
