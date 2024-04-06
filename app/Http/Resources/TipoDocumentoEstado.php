<?php

namespace App\Http\Resources;

use App\User;
use App\Expedienteestado;
use App\Expedientesruta;
use App\Organismossector;
use Illuminate\Http\Resources\Json\Resource;

class TipoDocumentoEstado extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $estado_documento = Expedienteestado::where('expedientes_id', $this->id)->get();
        // $recorrido =  $estado_documento->load('rutasector');
        // $sector = $recorrido->->load('sector');

        // $response = [];
        //       foreach ($recorrido  as $i) {
        //        $tipos_doc = Organismossector::select(['organismossector AS sector', 'created_at AS fecha_ingreso_sector'])->where('id',$i->rutasector->organismossectors_id)->first();
        //        array_push($response,$tipos_doc);
        // }

        $recorrido = [];

        // Sector donde se inició el expediente
        $query1 = Expedienteestado::select('organismossectors.organismossector', 'expendientesestados.created_at')
                                    ->join('expedientesrutas', 'expendientesestados.expedientesrutas_id', '=', 'expedientesrutas.id')
                                    ->join('organismossectors', 'expedientesrutas.organismossectors_id', '=', 'organismossectors.id')
                                    ->where('expendientesestados.expedientes_id', '=', $this->id)
                                    ->first();

        // Coleccion de sectores por donde fue pasando el expediente
        $query2 = Expedienteestado::select('organismossectors.organismossector', 'expendientesestados.created_at')
                                    ->join('expedientesrutas', 'expendientesestados.expedientesrutas_id', '=', 'expedientesrutas.id')
                                    ->join('organismossectors', 'expedientesrutas.organismossectors_id', '=', 'organismossectors.id')
                                    ->where([
                                        ['expendientesestados.expedientes_id', '=', $this->id],
                                        ['expendientesestados.pasado_por', '<>', NULL]
                                    ])
                                    ->get();

        array_push($recorrido, $query1);

        foreach ($query2 as $sectores)
        {
            array_push($recorrido, $sectores);
        }

        return [
            'num_doc' => $this->expediente_num,
            'extracto' => $this->expediente,
            'fecha_inicio' => $this->fecha_inicio,
            'importancia' => $this->Importancia,
            'estado' => Expedienteestado::where('expedientes_id', $this->id)->get()->last()->expendientesestado,
            'recorrido' => $recorrido,
            // 'recorrido' => array_unique($response, SORT_REGULAR),
            // 'usuario' =>  $user_documento,
        ];
    }
}
