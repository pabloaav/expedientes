<?php
namespace App\Traits;

use App\User;
use App\Deposito;
use Carbon\Carbon;
use App\Expediente;
use GuzzleHttp\Psr7\Query;
use App\Expedientedeposito;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use App\Logg;

trait LoginTrait {

    public function verificar_usuario($username,$password,$sistemaId)
    {
        // $data = DB::table('expedientes')
        // ->join("expendientesestados", "expendientesestados.expedientes_id", "=", "expedientes.id")
        // ->select("expedientes.*", "expendientesestados.*")
        // ->where('expedientes.organismos_id', $auth_user)
        // ->where('expendientesestados.expendientesestado', 'nuevo')
        // ->where('expedientes.fecha_inicio',date('Y-m-d'))
        // ->count();
        // return $data;

         // esta peticion en trait
       $appAUTH = env('AUTH_ENDPOINT');
       $endpoint = $appAUTH . "/users/login";
       $client = new Client();
       try {
         $data = $client->request(
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
         $login = json_decode($data->getBody(), true);
 
         // dd($login);
 
         if ($login['token'] == "") {
           // email que ingreso y tiene su usuario bloqueado - dato que se carga en el formulario restore password
           session(['confirmar-email' => $username]);
           $request->session()->put('confirmar-email', $username);
           $request->session()->put('confirmar-sistemaID', $sistemaId);
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
 
           // limpiar variables de sesion utilizada para restablecer contraseña de usuario
           $request->session()->forget('confirmar-email');
           $request->session()->forget('confirmar-sistemaID');
           
           // variables que se utilizaran una vez que el usuario inicia sesion 
           $roles = collect($conversion->roles);
           $permission = collect($conversion->permisos);
           $request->session()->put('rols', $roles);
           $request->session()->put('permission', $permission);
           $request->session()->put('id_user', $usuario);
 
           return redirect()->route('index.home');
         } else {
           return back()->with("error", 'No tiene ninguna cuenta registrada');
         }
           } catch (ClientException $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
         // error de api 
         $response = $e->getResponse();
         $responseBodyAsString = $response->getBody()->getContents();
         $obj = json_decode($responseBodyAsString);
         return back()->with("error", $obj->{'message'});
       }
    }
   
}