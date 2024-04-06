<?php

namespace App\Http\Resources;

use App\User;
use App\Expedienteestado;
use Illuminate\Http\Resources\Json\Resource;

class Documentos extends Resource
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
        return [
            'num_doc' => $this->expediente_num,
            'extracto' => $this->expediente,
            'fecha_inicio' => $this->fecha_inicio,
            'importancia' => $this->Importancia,
            'estado_actual' => Expedienteestado::where('expedientes_id', $this->id)->get()->last()->expendientesestado,
            'usuario_actual' =>  $user_documento,
        ];
    }
}
