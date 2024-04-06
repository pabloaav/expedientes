<?php

namespace App\Http\Resources;

use App\Organismossector;
use Illuminate\Http\Resources\Json\Resource;

class Sectores extends Resource
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
            'num_sector' => $this->organismossectors_id,
            'sector' => Organismossector::find($this->organismossectors_id)->organismossector,
        ];
    }
}
