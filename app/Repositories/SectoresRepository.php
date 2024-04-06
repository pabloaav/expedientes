<?php

namespace App\Repositories;

use App\Organismo;
use App\Organismossector;
use App\Organismossectorsuser;
use App\Http\Resources\Sectores;
use App\Interfaces\SectoresInterfaces;
use App\Http\Resources\SectoresCollection;

class SectoresRepository implements SectoresInterfaces 
{
    public function sectoresUsuario($usuario) 
    {
            $user = Organismossectorsuser::where('users_id',$usuario)->get();
            return new SectoresCollection($user);
     }

     public function sectoresOrganismo($sector, $usuario) 
     {
             $sector = Organismossectorsuser::where('organismossectors_id',$sector)->where('users_id',$usuario)->firstOrFail();
             if ($sector == null){
                 return 0;
             }else{
                $result = Organismossector::where('id',$sector->organismossectors_id)->firstOrFail();
                return $result->organismos_id;
             }
             
      }
}