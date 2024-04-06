<?php
return [
  // "firma_base_url" => "https://tst.firmar.gob.ar/",
  // "firma_base_url" => "http://localhost:9976/", // DESARROLLO
  "firma_base_url" => "https://firma.telco.com.ar/", // PRODUCCION
  'ACCESS_TOKEN_URL' => "RA/oauth/token",
  'API_MULTIPLE_SIGNATURE_URL' => 'firmador/api/signatures/multiple',
  'API_SIGNATURE_URL' => 'firmador/api/signatures',
  'GetRoles' => '/adm/roles',
  'CreateRol' => '/adm/rol',
  'GetPermisosVincular' => '/adm/permisos-vincular',
  'CreateRolPermiso' => '/adm/rol-permiso',
  'Scope' => 'gd',
  'DOTNET_SIGNATURE_SERVICE_ENDPOINT' => 'firmar-documentos-base',
  'OBJECT_STORE_ENDPOINT' => "http://207.246.76.67:9000", // MinIO DESARROLLO
  // 'OBJECT_STORE_ENDPOINT' => "https://ewr1.vultrobjects.com", // MinIO PRODUCCION
  // 'AWS_BUCKET' => "sied" // Bucket DESARROLLO
  // 'AWS_BUCKET' => "doco" // Bucket PRODUCCION
];
