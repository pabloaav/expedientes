<?php

namespace App\Http\Resources;

use App\User;
use App\Expedienteestado;
use App\Organismossector;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;

class DocumentosNovedades extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = User::select(['id', 'email'])->where('id',Expedienteestado::where('expedientes_id', $this->id)->get()->last()->users_id)->first();

        if($user == null){
            $user_documento = "Sin usuario asignado";
        }else {
            $user_documento = $user;
        }

        $estado_documento = Expedienteestado::where('expedientes_id', $this->id)
                            ->where('expendientesestado', 'pasado')
                            ->get();

        $recorrido =  $estado_documento->load('rutasector');

        $response = [];

        foreach ($recorrido as $i) {
            $tipos_doc = Organismossector::select(['organismossector AS sector'])->where('id',$i->rutasector->organismossectors_id)->first();
            array_push($response,['sector' => $tipos_doc->sector, 'fecha_ingreso_sector' => date("Y-m-d H:i:s", strtotime($i->created_at))]);
        }

        return [
            'identificador' => $this->id,
            'num_doc' => $this->expediente_num,
            'extracto' => $this->expediente,
            'fecha_inicio' => $this->fecha_inicio,
            'importancia' => $this->Importancia,
            'estado_actual' => Expedienteestado::where('expedientes_id', $this->id)->get()->last()->expendientesestado,
            'sector_actual' => Expedienteestado::where('expedientes_id', $this->id)->get()->last()->rutasector->sector->organismossector,
            'usuario_actual' =>  $user_documento,
            'recorrido' => $response
        ];
    }
}
