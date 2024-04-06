<?php

namespace App\Traits;

use App\Logg;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

trait UsuariosRol
{
    public function usuariosPorRol()
    {
        $rol_scope = "gd.EXPEDIENTES HISTORICOS";
        $sistemaId = session('sistema_id');
        $appAUTH = env('AUTH_ENDPOINT');
        $token = str_replace('"', '', session('token_go'));

        $endpoint = $appAUTH . "/adm/users-by-rol";
        $client = new Client();

        try {
            $respuesta = $client->request(
                'POST',
                $endpoint,
                [
                'headers' =>  [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                    'RolScope' => $rol_scope,
                    'SistemaId' => $sistemaId
                ])
                ]
            );

            $respuesta = json_decode($respuesta->getBody(), true);
            $rol_users = $respuesta['data'];

        } catch (ClientException $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
        }

        return $rol_users;
    }
}