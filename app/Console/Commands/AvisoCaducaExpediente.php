<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

use App\Expedienteorganismoetiqueta;
use App\Expediente;
use App\Expedienteestado;

class AvisoCaducaExpediente extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aviso:caduca-expediente';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera una notificacion al usuario que tiene el expediente 48 hs antes de su caducidad';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fechaLimite = Carbon::now()->addDays(2);
        $etiquetas = Expedienteorganismoetiqueta::where('caducidad', '<=', $fechaLimite)->get();

        foreach ($etiquetas as $etiqueta)
        {
            $expediente = Expediente::find($etiqueta->expediente_id)->expedientesestados->last();
            $nuevoestado = new Expedienteestado;
            $nuevoestado->expedientes_id = $expediente->expedientes_id;
            $nuevoestado->users_id = $expediente->users_id;
            $nuevoestado->expendientesestado = $expediente->expendientesestado;
            $nuevoestado->expedientesrutas_id = $expediente->expedientesrutas_id;
            $nuevoestado->observacion = "El documento caducará el dia ". Carbon::parse($etiqueta->caducidad)->format('d/m/Y') ." según la configuración de su etiqueta";
            $nuevoestado->comentario_pase = "AVISO: El documento caducará en 48 HS según la configuración de su etiqueta";
            $nuevoestado->notificacion_usuario = (!is_null($expediente->users_id) ? 'No leido' : NULL);

            $nuevoestado->save();
        }

        $this->info('Registros notificados con éxito.');
    }
}
