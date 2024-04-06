<?php

namespace App\Interfaces;

interface TiposDocumentosInterfaces
{
    public function getTipoDocumento($usuario,$codigo_tipo_documento, $organismo);
    public function getTiposDocumentos($usuario);

    public function getTiposDocumentosSector($usuario,$sector);

}