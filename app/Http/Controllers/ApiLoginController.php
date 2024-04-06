<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Interfaces\LoginInterfaces;
use App\Interfaces\UsuariosInterfaces;
use App\Organismo;
use JWTAuth;

class ApiLoginController extends Controller
{
    public function __construct(LoginInterfaces $loginInterfaces, UsuariosInterfaces $usuariosInterfaces) 
    {
        $this->loginInterfaces = $loginInterfaces;
        $this->usuariosInterfaces = $usuariosInterfaces;
    } 

    public function login(LoginRequest $request) 
    {
        try {    
         // datos para inicio de sesion de usuarios
         $user = $request->all();
         $users = $this->usuariosInterfaces->getUsuario($user);
        //  return response()->json($users);
         if ($users == null ){
          return response()->json(['errors' =>'El usuario ingresado no existe'], 400);
         }else{
          $login = $this->loginInterfaces->login($users, $user['password']);
          // return response()->json($login);
         }
         if($login['success'] == false){
            return response()->json(['errors' => $login['mesagge']], 400);
         }else{
            //  crear token con los datos del usuario 
           $generate_token = $this->loginInterfaces->token($user);
           return response()->json(['token' =>  $generate_token,
                                    'organismo' =>  $login['organismo']->id,
                                    'usuario' =>  $login['usuario']],
                                     200);
           }
        } catch (Exception $exception) {
          return response()->json(['errors' => $exception->getMessage()], 500);
        }
    }


}
