<?php

namespace App\Http\Resources;

use App\Expedientestipo;
use Illuminate\Http\Resources\Json\Resource;

class TiposDocumentos extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'num_tipo_doc' => $this->id,
            'nombre_tipo_documento' => $this->expedientestipo,
        ];
    }
}
