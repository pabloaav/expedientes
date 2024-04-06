<?php

namespace App\Interfaces;

interface PersonasLocalInterfaces
{
    public function buscarPersonaLocal($dni, $sexo, $organismo);
    public function vincularPersonaOrganismo($persona,$organismo);
    public function crearPersonaLocal($organismo, $dni, $nombre, $apellido, $sexo);
}