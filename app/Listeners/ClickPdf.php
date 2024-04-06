<?php

namespace App\Listeners;

use App\Events\ClickGenerarPdf;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Expedienteestado;
use Carbon\Carbon;

class ClickPdf
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
     * @param  ClickGenerarPdf  $event
     * @return void
     */
    public function handle(ClickGenerarPdf $event)
    {
      
        $textoLog = "El usuario " .  $event->user->name  . " generó un PDF del " . org_nombreDocumento() ." a las " . Carbon::now()->toTimeString();
        historial_doc(($event->idexpediente), $textoLog );
    }
}
