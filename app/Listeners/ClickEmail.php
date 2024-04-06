<?php

namespace App\Listeners;

use App\Events\ClickCompartirLink;
use App\Expedienteestado;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClickEmail
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
     * @param  ClickCompartirLink  $event
     * @return void
     */
    public function handle(ClickCompartirLink $event)
    {       
        $textoLog = "El usuario " .  $event->user->name  . " compartio el ". org_nombreDocumento() ." por email/whatsapp a las " . Carbon::now()->toTimeString();
        historial_doc(($event->idexpediente), $textoLog );
        
    }
}
