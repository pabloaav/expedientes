<?php

namespace App\Repositories;


use JWTAuth;
use App\User;
use App\Organismo;
use GuzzleHttp\Client;
use UnexpectedValueException;
use App\Interfaces\LoginInterfaces;
use Tymon\JWTAuth\Facades\JWTFactory;
use GuzzleHttp\Exception\ClientException;

class LoginRepositoryRemoto implements LoginInterfaces 
{
    public function login($users, $password) 
    {
        $sistema = Organismo::where('id',$users->userorganismo->first()->organismos_id)->first();
        $appAUTH = env('AUTH_ENDPOINT');
        $endpoint = $appAUTH . "/users/login";
        $client = new Client();
        try {
          $respuesta = $client->request(
            'POST',
            $endpoint,
            [
              'headers' => [
                'Content-Type' => 'application/json'
              ],
              'body' => json_encode([
                'username' => $users['email'],
                'password' => $password,
                'sistema_id' => strval($sistema->sistema_id)
              ])
            ]
          );
          // rspuesta de la api esto devuelve un formato de arreglo
          $login = json_decode($respuesta->getBody(), true);
          // dd($login);
          if ($login['token'] == "") {
            return['mesagge' => 'Error inicio de sesión','success' =>false];
          } else {
            $tks = \explode('.', $login['token']);
            if (\count($tks) != 3) {
              throw new UnexpectedValueException('Wrong number of segments');
            }
          }
            /*guardar en una variable de sesion la respuesta */
            session(['token_go' => $login['token']]);
            return['success' =>true,'organismo'=>Organismo::where('id',$users->userorganismo->first()->organismos_id)->first(),'usuario' =>$users['id']];
         
           } catch (ClientException $e) {
                $response = $e->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $obj = json_decode($responseBodyAsString);
                return['mesagge' => $obj->{'message'},'success' =>false];
          } 
     }

     public function token($user) 
     {
        $user = User::where( 'email', $user['username'] )->first();
        $data = JWTAuth::fromUser($user);
        return $data;   
     }

     public function decodetoken() 
     {
        $token = JWTAuth::getToken();
        $apy = JWTAuth::getPayload($token)->toArray();
        return $apy ;
     }
     
     public function autorizacion($params,$token) 
     {
      if ($params === $token) {
        return true;
      } else {
        return false;
      }
     }

}