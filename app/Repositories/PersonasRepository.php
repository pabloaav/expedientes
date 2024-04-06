<?php

namespace App\Repositories;


use App\Persona;
use Carbon\Carbon;
use App\Interfaces\PersonasLocalInterfaces;

class PersonasRepository implements PersonasLocalInterfaces 
{
   
    public function buscarPersonaLocal($dni, $sexo, $organismo) 
    {
        $persona = Persona::where('documento', $dni)->where('sexo', $sexo)->where('organismos_id',$organismo)->first();
        // $result = (array) $persona;
        return $persona;
    }

    public function vincularPersonaOrganismo($persona, $organismo)
    {
        $documento =  $persona[0]['documento'];
        $nombre =  $persona[0]['nombres'];
        $apellido =  $persona[0]['apellido'];
        $sexo =  $persona[0]['sexo'];
        $direccion =  $persona[0]['domicilio']['calle'];;
        $fecha_nacimiento =  $persona[0]['documento'];
        $localidad =   $persona[0]['domicilio']['localidad'];;
        $provincia =  $persona[0]['domicilio']['provincia'];;

        $persona = new Persona;
        $persona->persona_id = intval($documento) ;
        $persona->nombre =  $nombre;
        $persona->apellido = $apellido;
        $persona->documento =$documento;
        $persona->sexo =  $sexo;
        $persona->tipo = "fisica";
        $persona->direccion = $direccion;
        $persona->fecha_nacimiento = $fecha_nacimiento;
        $persona->localidad = $localidad;
        $persona->provincia = $provincia;
        $persona->organismos_id = $organismo;

        $persona->save();

        return  $persona;
     }

    public function crearPersonaLocal($organismo, $dni, $nombre, $apellido, $sexo)
    {
        $persona = new Persona;
        $persona->persona_id = intval($dni);
        $persona->nombre = $nombre;
        $persona->apellido = strtoupper($apellido);
        $persona->documento = intval($dni);
        $persona->sexo = $sexo;
        $persona->tipo = "fisica";
        $persona->direccion = "COMPLETAR";
        $persona->fecha_nacimiento = "1969-12-31";
        $persona->localidad = "COMPLETAR";
        $persona->provincia = "COMPLETAR";
        $persona->organismos_id = $organismo;

        $persona->save();

        return $persona;
    }
}