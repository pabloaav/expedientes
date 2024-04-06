<?php

namespace App\Repositories;


use GuzzleHttp\Client;
use UnexpectedValueException;
use App\Interfaces\PersonasInterfaces;
use GuzzleHttp\Exception\ClientException;

class PersonasRepositoryRemote implements PersonasInterfaces 
{
   
public function buscarPersonaRenaper($dni, $sexo, $token) 
    {
      $token = str_replace('"', '', $token);

      $endpoint = "http://devlogin.telco.com.ar:47625/Personas";

      $client = new Client();
      $intentos = 0;
      while ($intentos <= 4) {
        try {
          $respuesta = $client->request(
            'GET',
            $endpoint,
            [
              'headers' =>  [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
              ],
              'query' => [
                'Dni' => $dni,
                'Sexo' => $sexo
              ]
            ]
          );
          $respuesta = json_decode($respuesta->getBody(), true);

          if (isset($respuesta)) {
            $intentos = 10;
          }
        } catch (ClientException $e) {
        }
        $intentos += 1;
      }
      // return $respuesta;
      if (isset($respuesta)) {
        $dataObject = $respuesta['persona'];
        $persona = $dataObject[0];
        if ($persona['status'] == false) {
          return['success' =>false];
        } else {
          return['success' =>true, 'persona' => $respuesta['persona']];
        }
       } else {
        return['success' =>false];
      }
       

     }

}