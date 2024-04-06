<?php

namespace App\Http\Controllers\Auth;

use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use UnexpectedValueException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Organismo;
use App\Logg;

use App\Events\LoginInicio;
use Illuminate\Support\Facades\Event;

class LoginController extends Controller
{
  use AuthenticatesUsers;

  public function login(Request $request)
  {
    ///aceeso a login service 
    $username = $request->get('username');
    $password = $request->get('password');

    // 1 primero se verifica si el usuario ingresado existe en base de datos local 
    // si el usuario existe significa que tiene un organismo asociado (se toma el sistema id para mandar en la peticion)
    $usuario_login =  User::where('email', $username)->first();
    if ($usuario_login == null) {
       return response()->json(['mesagge' => 'el correo que ingresaste no coincide con ninguna cuenta', 'response' => 1]);
    } else {
      //  si el usuario es superadmin tendra varios organismos asociados 
      //  se agrego un campo en la tabla users para verificar si es admin 
      $sistemaId = 0;
      if ($usuario_login->admin == 1){
        $organismosSistmas= Organismo::where("sistema_id", "<>", null)->get();
        $res = [
          "username" => $username,
          "password" =>  base64_encode($password),
          "organismos" => $organismosSistmas
         ];
        return response()->json(['data' => $res, 'response' => 2]);
        // si el usuario es super admim mostrar los organismos asociados
      }else{
      
      // si el usuario no es super admin (solo tendra asociado un organismo)
      // sistema id asociado al organismo 
      $sistemaId = intval($usuario_login->userorganismo->last()->organismos->sistema_id);

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
              'username' => $username,
              'password' => $password,
              'sistema_id' => strval($sistemaId)
            ])
          ]
        );
        // rspuesta de la api esto devuelve un formato de arreglo
        $login = json_decode($respuesta->getBody(), true);
        // dd($login);
        if ($login['token'] == "") {
          // email que ingreso y tiene su usuario bloqueado - dato que se carga en el formulario restore password
          $request->session()->put('confirmar-email', $username);
          $request->session()->put('confirmar-sistemaID', $sistemaId);
          // si el usuario fue registrado por primera vez se le envia un codigo al email para activar cuenta 
          return response()->json(['data' =>  session('confirmar-email'),'mesagge' =>  $login['activo'], 'response' => 6]);
          // return back()
          //   ->with("error-restorepass", $login['activo']);
        } else {
          $tks = \explode('.', $login['token']);
          if (\count($tks) != 3) {
            throw new UnexpectedValueException('Wrong number of segments');
          }
        }
        // guardar token en una variable de sesion 
        // los tokens web JSON constan de tres partes separadas Header, Payload, Signature separados por punto
        $request->session()->put('token_go', $login['token']);

        $decodificartoken = base64_decode($tks[1]);
        // convertir en una matriz u objeto de PHP
        $conversion = json_decode($decodificartoken);

        //ponemos en la variable de session el sistema id del usuario
        $request->session()->put('sistema_id', $conversion->sub);

        //verifica  si el usuario existe en la base de datos (tabla User)
        $usuario =  User::where('login_api_id', $conversion->id)->exists();

        // si existe crea una sesion para el usuario
        // de lo contrario retorna error
        if ($usuario <> null) {
          $login_usuario = User::where('login_api_id', $conversion->id)->first();
          Auth::login($login_usuario);

          // evento para guardar el inicio de sesion del usuario
          //event(new LoginInicio($login_usuario));
          $textoLog = "Inicio Sesion ";
          Logg::info($textoLog, true);
      
          // limpiar variables de sesion utilizada para restablecer contraseña de usuario
          $request->session()->forget('confirmar-email');
          $request->session()->forget('confirmar-sistemaID');
          
          // variables que se utilizaran una vez que el usuario inicia sesion 
          $roles = collect($conversion->roles);
          $permission = collect($conversion->permisos);
          $request->session()->put('rols', $roles);
          $request->session()->put('permission', $permission);
          $request->session()->put('id_user', $usuario);
          // el usuario inicia sesion 
          return response()->json(['response' => 3]);
        } else {
          return response()->json(['mesagge' => 'No tiene ninguna cuenta registrada', 'response' => 4]);
        }
         } catch (ClientException $e) {
          Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
        // error de api 
        $response = $e->getResponse();
        $responseBodyAsString = $response->getBody()->getContents();
        $obj = json_decode($responseBodyAsString);
        return response()->json(['mesagge' => $obj->{'message'}, 'response' => 5]);
        }
      }  
    }
  }

  public function logout()
  {
    Auth::logout();
    return view('auth.login');
  }

  public function restoreUser($sistemaId,$codigoEmail)
  {
    $sistemaId = base64_decode($sistemaId);
    $codigo = $codigoEmail;
    //  dd($sistemaId);

    return view('auth.restoreuser', compact('sistemaId','codigo'));
  }



   // crear usuario authentication_service (segun el organismo ingresado) 
   public function restorePassword(Request $request)
   {
      // email y sistema del usaurio que intenta restablecer contraseña
    //   if ($request->get('email_restablecer') == null){
    //  $email_restablecer = session('confirmar-email');
    // } else {
    //   $email_restablecer = $request->get('email_restablecer');
    //  }
    
     if ($request->get('codigo') == null){
      $sistemaId = session('confirmar-sistemaID');
     } else {
      $sistemaId = $request->get('sistemaId');
     }

     
      
     // codigo enviado al email del usuario 
     $Codigo = $request->get('codigo');
     // nueva contraseña del usuario 
     $Password = $request->get('password_nuevo');
 
 
     $appAUTH = env('AUTH_ENDPOINT');
   
     $endpoint = $appAUTH . "/users/restore-pass";
 
     $client = new Client();
     try {
       $respuesta = $client->request(
         'POST',
         $endpoint,
         [
           'headers' =>  [
             'Content-Type' => 'application/json',
           ],
           'body' => json_encode([
            //  'Email' => $email_restablecer,
             'Codigo' => $Codigo,
             'Password' => $Password,
             'SistemaId' => strval($sistemaId)
           ])
         ]
       );

       $respuesta = json_decode($respuesta->getBody(), true);
       //  $dataObject = $respuesta['data'];
       return response()->json(['data' => $respuesta, 'response' => 1]);
     } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
       $response = $e->getResponse();
       $responseBodyAsString = $response->getBody()->getContents();
       $obj = json_decode($responseBodyAsString);
       return response()->json(['mesagge' => $obj->{'message'}, 'response' => 2]);
     }
   }

   /// iniciar sesion como usuario superadmin
   public function loginadmin(Request $request)
   {
     ///aceeso a servicio de autenticacion
      $username = $request->get('emailadmin');
      $password =$request->get('pass');
      $sistema_id = $request->get('sistema_id');
 
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
               'username' => $username,
               'password' =>  base64_decode($password),
               'sistema_id' => strval($sistema_id)
             ])
           ]
         );
         // rspuesta de la api esto devuelve un formato de arreglo
         $login = json_decode($respuesta->getBody(), true);
         // dd($login);
         if ($login['token'] == "") {
           // si el usuario fue registrado por primera vez se le envia un codigo al email para activar cuenta 
           return back()
             ->with("error-restorepass", $login['activo']);
         } else {
           $tks = \explode('.', $login['token']);
           if (\count($tks) != 3) {
             throw new UnexpectedValueException('Wrong number of segments');
           }
         }
         // guardar token en una variable de sesion 
         // los tokens web JSON constan de tres partes separadas Header, Payload, Signature separados por punto
         $request->session()->put('token_go', $login['token']);
 
         $decodificartoken = base64_decode($tks[1]);
         // convertir en una matriz u objeto de PHP
         $conversion = json_decode($decodificartoken);
 
         //ponemos en la variable de session el sistema id del usuario
         $request->session()->put('sistema_id', $conversion->sub);
 
         //verifica  si el usuario existe en la base de datos (tabla User)
         $usuario =  User::where('login_api_id', $conversion->id)->exists();
 
         // si existe crea una sesion para el usuario
         // de lo contrario retorna error
         if ($usuario <> null) {
           $login_usuario = User::where('login_api_id', $conversion->id)->first();
           Auth::login($login_usuario);
    
           // variables que se utilizaran una vez que el usuario inicia sesion 
           $roles = collect($conversion->roles);
           $permission = collect($conversion->permisos);
           $request->session()->put('rols', $roles);
           $request->session()->put('permission', $permission);
           $request->session()->put('id_user', $usuario);
           // el usuario inicia sesion 
           return response()->json(['response' => 1]);
         } else {
           return response()->json(['mesagge' => 'No tiene ninguna cuenta registrada', 'response' => 2]);
         }
          } catch (ClientException $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
         // error de api 
         $response = $e->getResponse();
         $responseBodyAsString = $response->getBody()->getContents();
         $obj = json_decode($responseBodyAsString);
         return response()->json(['mesagge' => $obj->{'message'}, 'response' => 3]);
         }
       
     
   }

  // RESTABLECER CONTRASEÑA DE USUARIO EN 3 PASOS 
   // 1 - verificar que el usuario ingresado existe en la base de datos : se envia un codigo de verficacion al email 
   // el codigo se ingresa en el segundo paso
   public function sendCode(Request $request)
   { 
     //email del usuario que intenta restablecer contraseña 
     $email = $request->get('email_restablecer_users');

     $appAUTH = env('AUTH_ENDPOINT');
   
     $endpoint = $appAUTH . "/users/send-code";
 
     $client = new Client();
     try {
       $respuesta = $client->request(
         'POST',
         $endpoint,
         [
           'headers' =>  [
             'Content-Type' => 'application/json',
           ],
           'body' => json_encode([
             'Email' => $email,
           ])
         ]
       );
       $respuesta = json_decode($respuesta->getBody(), true);

       // SI LA RESPUESTA ES EXITOSA SE DEBE RECUPERAR EL SISTEMAID DEL ORGANISMO (USUARIO DEBE ESTAR VINCULADO A UN ORGANISMO)
       $user_organismo_sistema = User::where('email', "=", $email)->first();
       if ($user_organismo_sistema == null){
        return response()->json(['mesagge' => 'El usuario que ingreso no éxiste en la base de datos', 'response' => 3]);
       }else{
        $sistema_id =  $user_organismo_sistema->userorganismo->first()->organismos->sistema_id;
        $request->session()->put('sistema_restaurar_contraseña',  $sistema_id);
        $request->session()->put('email_restaurar_contraseña',  $email);
        return response()->json(['data' => $respuesta, 'response' => 1]);
       }
     } catch (ClientException $e) {
       $response = $e->getResponse();
       $responseBodyAsString = $response->getBody()->getContents();
       $obj = json_decode($responseBodyAsString);
       return response()->json(['mesagge' => $obj->{'message'}, 'response' => 2]);
     }
   }

  // 2 - VALIDAR CODIGO ENVIADO AL EMAIL  
   public function validateCode(Request $request)
   {
      // email y sistema del usaurio que intenta restablecer contraseña
     $email_restablecer_user = session('email_restaurar_contraseña');
     $sistema_restablecer_user = session('sistema_restaurar_contraseña');
      
     // codigo enviado al email del usuario 
     $codigo = $request->get('codigo_enviar');
     $sistemaID = $request->get('sistema_restaurar_contraseña');
   
     $appAUTH = env('AUTH_ENDPOINT');
   
     $endpoint = $appAUTH . "/users/validate-code";
 
     $client = new Client();
     try {
       $respuesta = $client->request(
         'POST',
         $endpoint,
         
         [
           'headers' =>  [
             'Content-Type' => 'application/json',
           ],
           'body' => json_encode([
             'codigo' => $codigo,
             'email' => $email_restablecer_user,
             'sistema_id' => "3",
             'esLogin' => false,
           ])
         ]
       );

       $respuesta = json_decode($respuesta->getBody(), true);
       //  $dataObject = $respuesta['data'];
       return response()->json(['data' => $respuesta, 'response' => 1]);
     } catch (ClientException $e) {
       $response = $e->getResponse();
       $responseBodyAsString = $response->getBody()->getContents();
       $obj = json_decode($responseBodyAsString);
       return response()->json(['mesagge' => $obj->{'message'}, 'response' => 2]);
     }
   }


}
