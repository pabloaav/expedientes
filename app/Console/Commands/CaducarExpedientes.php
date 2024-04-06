<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

use App\Expediente;
use App\Expedienteestado;
use App\Expedienteorganismoetiqueta;
use App\Expedientesruta;

class CaducarExpedientes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caducar:expedientes-etiquetas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permite caducar expedientes segun la configuracion de su etiqueta';

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
        $fechaActual = Carbon::now();
        $etiquetas = Expedienteorganismoetiqueta::where('caducidad', '<=', $fechaActual)->get();

        foreach ($etiquetas as $etiqueta)
        {
            $expediente = Expediente::find($etiqueta->expediente_id)->expedientesestados->last();
            $nuevoestado = new Expedienteestado;
            $nuevoestado->expedientes_id = $expediente->expedientes_id;
            if (!is_null($etiqueta->ruta_destino)) // si la etiqueta tiene configurada la ruta de destino al caducar, se pasa el expediente a esa ruta, sino, se lo libera y queda en el mismo sector
            {
                $ruta_sector = Expedientesruta::select('organismossectors.organismossector')
                                                ->join('organismossectors', 'organismossectors.id', '=', 'expedientesrutas.organismossectors_id')
                                                ->where('expedientesrutas.id', $etiqueta->ruta_destino)
                                                ->first();

                $nuevoestado->users_id = NULL;
                $nuevoestado->expendientesestado = "pasado";
                $nuevoestado->expedientesrutas_id = $etiqueta->ruta_destino;
                $nuevoestado->observacion = "El documento ha caducado el ". Carbon::parse($etiqueta->caducidad)->format('d/m/Y') ." y ha sido pasado al sector ". $ruta_sector->organismossector ." según la configuración de su etiqueta";
            }
            else
            {
                $nuevoestado->users_id = $expediente->users_id;
                $nuevoestado->expendientesestado = $expediente->expendientesestado;
                $nuevoestado->expedientesrutas_id = $expediente->expedientesrutas_id;
                $nuevoestado->observacion = "El documento ha caducado el ". Carbon::parse($etiqueta->caducidad)->format('d/m/Y') ." según la configuración de su etiqueta";
            }
            $nuevoestado->comentario_pase = "El documento ha caducado según la configuración de su etiqueta";

            $nuevoestado->save();

            // una vez pasado el expediente, se elimina la etiqueta del mismo
            $etiqueta->delete();
        }

        $this->info('Registros actualizados con éxito.');
    }
}
