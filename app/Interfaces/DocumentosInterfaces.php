<?php

namespace App\Interfaces;

interface DocumentosInterfaces
{
  public function createDocumento(array $doc);
  public function createDocumentoEstado($documento_nuevo, $verificar_rutas, $estado_documento);
  public function getAllDocumentos($organismo);
  public function getDocumentoById($documentoId);
  public function vincularDocumentoPersona($persona, $num_doc , $año, $organismo);
  public function getDocumento($request);
  public function getDocumentosNovedades($request);
  public function marcarDocumentosLeidos($request);
  public function getDocumentoSectorNow($request);
}
