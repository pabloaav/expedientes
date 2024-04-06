<?php

namespace App\Interfaces;

interface DocumentosRutasInterfaces
{
    public function verificarRutasDocumentos($tipo_documento,$sector); /* VERIFICAR SI LA RUTA EXISTE PARA EL TIPO DE DOC Y SECTOR QUE SE INGRESA*/
    public function verificarRutas($tipo_documento,$sector); /* VERIFICAR SI LA RUTA EXISTE PARA EL TIPO DE DOC Y SECTOR QUE SE INGRESA*/
 
}