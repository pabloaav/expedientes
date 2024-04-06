<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Logg;
use App\User;
use Validator;
use App\Roleuser;
use App\Organismo;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Organismosuser;
use App\Organismossector;
use App\Organismossectorsuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Caffeinated\Shinobi\Facades\Shinobi;
use GuzzleHttp\Exception\ClientException;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class UserController extends Controller
{

  // usuarios de todos los organismos 
  public function index()
  {

    if (!session('permission')->contains('usuario.superadmin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    // tomar el sistema vinculado al usuario/organismo
    $sistemaId = session('sistema_id');
    // Datos del organismo 
    $organismo = Organismo::where('sistema_id', "=", $sistemaId)->first();
    $title = "Usuarios del Organismo " . $organismo->organismo;



    // SERVICIO DE AUTENTIFICACION 
    $appAUTH = env('AUTH_ENDPOINT');
    $token = str_replace('"', '', session('token_go'));

    $endpoint = $appAUTH . "/adm/users";

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
            'CargarSistema' => true,
            'CargarUserSistema' => true,
            'SistemaId' => $sistemaId
          ])
        ]
      );
      $respuesta = json_decode($respuesta->getBody(), true);

      $permisosCollection = User::hydrate($respuesta['data']);
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return redirect('/')->with("error", $obj->{'message'});
      // return response()->json(['mesagge' => $obj->{'message'}, 'response' => 2]);

    }

    $users = $permisosCollection->paginate(15);
    $sectores= Organismossector::where("organismos_id",$organismo->id)->orderBy('organismossector', 'ASC')->get();
     // Endpoint de roles de autenticacion service = base url + ruta de roles
     $endpoint =  $appAUTH = env('AUTH_ENDPOINT') . config('configuraciones.GetRoles');
     $token = str_replace('"', '', session('token_go'));

     $client = new Client();
     try {
       $respuesta = $client->request('GET', $endpoint, [
         'headers' =>  [
           'Authorization' => 'Bearer ' . $token,
         ],
         'query' =>
         [
           'Number' => 1,
           // 'Size' => 10,
           'SistemaId' => intval(session()->get('sistema_id')),
           'CargarUserSistemaRol' => true,
           'CargarPermisos' => true,
           'CargarSuperAdministrador' => false,
         ]
       ]);

     $bodyRespose = json_decode($respuesta->getBody(), true);
     $respuestaRoles = $bodyRespose['data'];
   } catch (ClientException $e) {
     Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
   }

     $roles = RoleUser::hydrate($respuestaRoles);

    return view('users.index', ['organismo' => $organismo, 'users' => $users, 'title' => $title,'sectores' => $sectores,'roles' => $roles]);
  }


  //  cargar en modal crear usuarios todos los organismos : servicio login organismo:sistema_id
  public function organismosuser()
  {
    $organismos = Organismo::all();
    return response()->json(['organismos' =>  $organismos]);
  }


  // crear usuario authentication_service (segun el organismo ingresado) 
  public function storeUsersService(Request $request)
  {
    $User = $request->get('email');
    $Nombre = $request->get('apell_nomb');
    $Password = "Lucasg2022@@";
    $Activo = true;

    // Sistema id tomado en el token 
    $sistemaId = session('sistema_id');

    // buscar el organismos que coincida con el sistema id del token 
    $organismo =  DB::table('organismos')->where('sistema_id', '=', $sistemaId)->first();
    $organismoId = $organismo->id;

    $appAUTH = env('AUTH_ENDPOINT');
    $token = str_replace('"', '', session('token_go'));

    $endpoint = $appAUTH . "/adm/user-create";

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
            'User' => $User,
            'Nombre' => $Nombre,
            'password' => $Password,
            'Activo' => $Activo,
            'SistemaId' => $sistemaId
          ])
        ]
      );
      $respuesta = json_decode($respuesta->getBody(), true);

      // verificar si el usuario que intento registrar existe en la base de datos 
      $usuario_verificar = User::where("email", "=", $respuesta['User'])->first();

      //  si el usuario no existe en la db doco se crea un registro en user y organimo user 
      if ($usuario_verificar == null) {
        $user = new User;
        $user->name = $respuesta['Nombre'];
        $user->email = $respuesta['User'];
        $user->login_api_id = $respuesta['Id'];
        $user->save();
        //   // vincular usuario al organismo(db local)
        //  necesito el id del organismo 
        $userorganismo = new Organismosuser();
        $userorganismo->users_id = $user->id;
        $userorganismo->organismos_id = $organismoId;
        $userorganismo->activo = 1;
        $userorganismo->save();
        $textoLog = "Creó usuario " . $user->name  . " en organismo.";
        Logg::info($textoLog);

      //Sector Opcional 
      $sector = $request->get('sectorSelect');

      if ($sector != null) {
        $organismossectorsuser = new Organismossectorsuser;
        $organismossectorsuser->organismossectors_id = $sector;
        $organismossectorsuser->users_id = $user->id;
        $organismossectorsuser->save();

        $user = User::find($organismossectorsuser->users_id);
        $sector = Organismossector::find( $organismossectorsuser->organismossectors_id);
        $textoLog = "Asignó usuario " .   $user->name . " al sector ".   $sector->organismossector;
        Logg::info($textoLog);
      }

        //Roles Opcional 
      $roles = $request->get('rolSelect');

      if ($roles != null) {            
        $appAUTH = env('AUTH_ENDPOINT');
        $token = str_replace('"','',session('token_go'));
        $endpoint = $appAUTH."/adm/users";                  
        $client = new Client();
          try {
            $respuesta = $client->request('POST', $endpoint,
                ['headers' =>  [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                'CargarSistema' => true,
                'CargarUserSistema' => true,
                'SistemaId' => $sistemaId
                ])
              ]);
            $respuesta = json_decode($respuesta->getBody(), true);

            $arrayUsers = $respuesta['data'];
            for ($i = 0; $i < count($arrayUsers); $i++) {
              if ($arrayUsers[$i]['User'] != $User) {
                unset($arrayUsers[$i]);
              }
            }
          
            $organismosusers = User::hydrate($arrayUsers);

          } catch (ClientException $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
          }

          $organismosuser=  $organismosusers->last();

          $userSistemaId= $organismosuser->UserSistema[0]['ID'];
          foreach ($roles as $indice => $rol) {

            $UsersistemaId = intval($userSistemaId);
            $RolId = intval($rol);
            $appAUTH = env('AUTH_ENDPOINT');
            $token = str_replace('"', '', session('token_go'));
        
            $endpoint = $appAUTH . "/adm/user-rol";
        
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
                    'RolId' => $RolId,
                    'UsersistemaId' => $UsersistemaId,
                  ])
                ]
              );

            } catch (ClientException $e) {
              Logg::error(($e->getMessage()),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
            }
          } 
        }                    

        return response()->json(['response' => 1]);
      } else {
        return response()->json(['mesagge' => 'El usuario que intenta registrar ya éxiste en la base de datos', 'response' => 3]);
      }
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      // error api 
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return response()->json(['mesagge' => $obj->{'message'}, 'response' => 2]);
    }
  }

  //formulario para reestablecer contraseña del usuario (administrador) 
  // sistemaId del usuario para enviar en peticion put actualizar 
  public function editpassword($id, $sistemaId)
  {
    $organismo_user = Auth::user()->userorganismo;
    $id = base64_decode($id);
    $sistemaId = base64_decode($sistemaId);
    $user = User::where('login_api_id', $id)->first();
    $title = "Cambiar contraseña del usuario " . $user->name;
    return view('users.edit', ['title' => $title, 'organismo_user' => $organismo_user, 'user' => $user, 'sistemaId' => $sistemaId]);
  }

  //consultar los roles vinculados al usuario 
  public function permisosUser($id, $id_user)
  {

    // user sistema del usuario 
    $id = base64_decode($id);

    $id_user = base64_decode($id_user);
    // dd($id);
    $organismo_user = Auth::user()->userorganismo;
    $sistemaId = session('sistema_id');

    $user = User::where("login_api_id", $id_user)->first();
    $appAUTH = env('AUTH_ENDPOINT');
    $token = str_replace('"', '', session('token_go'));

    $endpoint = $appAUTH . "/adm/roles";

    $client = new Client();
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
            'CargarSistema' => 1,
            'SistemaId' => intval($sistemaId),
            'CargarUserSistemaRol' => true,
            'UserSistemaId' => $id,
            'CargarPermisos' => true,
            'CargarSuperAdministrador' => false,
          ]
        ]
      );
      $respuesta = json_decode($respuesta->getBody(), true);

      //  dd($respuesta);
      // UserSistemaId necesito el usersistema de cada usuario para vincular a un rol
      if ($respuesta['data'] == null) {
        $title = "No existen roles vinculados al usuario ";
        return view('users.permisos', ['title' => $title, 'organismo_user' => $organismo_user, 'user' => $user, 'UserSistemaId' => $id, 'respuesta' => $respuesta['data']]);
      } else {
        $title = "Roles del usuario ";
        return view('users.permisos', ['title' => $title, 'organismo_user' => $organismo_user, 'user' => $user, 'UserSistemaId' => $id, 'respuesta' => $respuesta['data']]);
      }
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return redirect('/')->with("error", $obj->{'message'});
    }
  }

  //consultar los roles del sistema id para ese usuario consultado (los datos se observaran en una ventana modal)
  public function consultarroles($id)
  {
    // id coresponde a id del usuario 
    $UserId = $id;
    // toma el sistema id al que esta vinculado el usuario logueado (admin)      
    $sistemaId = session('sistema_id');

    // consultar roles para el usuario (distinto al usuario super admin)
    // $ScopeSuperAdmin = session('rols');    

    // variable de entorno y token para enviar en peticion
    $appAUTH = env('AUTH_ENDPOINT');
    $token = str_replace('"', '', session('token_go'));

    $endpoint = $appAUTH . "/adm/rol-vincular";
    $client = new Client();
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
            'SistemaId' => intval($sistemaId),
            'UserId' => $UserId,
            'CargarSuperAdministrador' => false
          ]
        ]
      );

      $respuesta = json_decode($respuesta->getBody(), true);
      return response()->json(['respuesta' => $respuesta]);
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return response()->json(['mesagge' => $obj->{'message'}, 'response' => 2]);
    }
  }


  // en esta funcion se le asigane el rol al usuario (recibe 2 parametros el usersistemaId y  el rol seleccionado)
  public function asignarrol($index, $index2)
  {
    $UsersistemaId = intval($index);
    $RolId = intval($index2);
    $appAUTH = env('AUTH_ENDPOINT');
    $token = str_replace('"', '', session('token_go'));

    $endpoint = $appAUTH . "/adm/user-rol";

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
            'RolId' => $RolId,
            'UsersistemaId' => $UsersistemaId,
          ])
        ]
      );
      $respuesta = json_decode($respuesta->getBody(), true);
      return response()->json(['response' => 1]);
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return response()->json(['mesagge' => $obj->{'message'}, 'response' => 2]);
    }
  }


  // en esta funcion se borra el rol al usuario (recibe 2 parametros el usersistemaId y  el rol seleccionado)
  public function quitarrol($userSistemaIdDelete, $idrol)
  {

    $UsersistemaId = intval($userSistemaIdDelete);
    $RolId = intval($idrol);
    $appAUTH = env('AUTH_ENDPOINT');
    $token = str_replace('"', '', session('token_go'));

    $endpoint = $appAUTH . "/adm/user-rol";

    $client = new Client();
    try {
      $respuesta = $client->request(
        'DELETE',
        $endpoint,
        [
          'headers' =>  [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
          ],
          'body' => json_encode([
            'RolId' => $RolId,
            'UsersistemaId' => $UsersistemaId,
          ])
        ]
      );
      $respuesta = json_decode($respuesta->getBody(), true);
      return response()->json(['response' => 1]);
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return response()->json(['mesagge' => $obj->{'message'}, 'response' => 2]);
    }
  }



  // ---------------------------------------------------------------------------------------------------------------


  public function edit($id)
  {

    // permisio tendria que estar logeado .... 
    if (!Auth::check()) {
      session(['status' => 'No esta registrado en el sistema.']);
      return redirect()->route('index.home');
    }
    $idInt = base64_decode($id);

    $user = User::find($idInt);
    $sistemaId = session('sistema_id');
    $title = "Usuario: " . $user->name;
    return view('users.edit', [
      'user' => $user,
      'title' => $title,
      'sistemaId' => $sistemaId
    ]);
  }


  //  actualizar datos de usuarios ( servicio autentificacion Email, Nombre, Activo) 
  public function updateUser(Request $request)
  {
    if (!session('permission')->contains('usuario.update')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $validator = Validator::make($request->all(), [
      'nombre' => 'required|max:50',
      'email' => 'required|email',
      // 'clave' => 'required|min:5|max:10',
      // 'repetirclave' => 'required|min:5|max:10|same:clave',
    ]);

    if ($validator->fails()) {
      foreach ($validator->messages()->getMessages() as $field_name => $messages) {
        foreach ($messages as $message) {
          $errors[] = $message;
        }
      }
      return redirect()->back()->with('errors', $errors)->withInput();
      die;
    }

    $activo = true;
    // if ($request->activo == null) {
    //   $activo = false;
    // };

    $user = User::where('login_api_id', $request->get('id'))->first();
    // dd($user);

    $appAUTH = env('AUTH_ENDPOINT');
    $token = str_replace('"', '', session('token_go'));
    $endpoint = $appAUTH . "/adm/user-update";

    $id_usuaio_actualizar = intval($request->get('id'));

    $control_email = ($request->get('email') != $user->email) ? $request->get('email') : null;

    // Controlo si ya existe ese correo en la base de datos
    if ($control_email != null) {
      $UserCorreos = User::all()->where('email', $control_email)->reject(function ($user) use ($id_usuaio_actualizar) {
        return $user->login_api_id == $id_usuaio_actualizar;
      });
      if (count($UserCorreos) > 0) {
        return redirect()->back()->with("error", "El correo ya existe en el sistema");
      }
    }

    $client = new Client();
    try {
      $respuesta = $client->request(
        'PUT',
        $endpoint,
        [
          'headers' =>
          [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
          ],
          'body' => json_encode([
            'Id' => $id_usuaio_actualizar,
            'User' => $control_email,
            'Nombre' => $request->get('nombre'),
            //'Password' => $request->get('clave'),
            // 'Activo' => $activo,
            'SistemaId' => $request->get('sistemaId'),
          ])
        ]
      );
      if ($control_email != null) {
      $user->email = $request->get('email');
      }
      $user->name = $request->get('nombre');
      $user->save();
      $textoLog = "Cambió datos del Usuario " . $user->name;
      Logg::info($textoLog);
      $respuesta = json_decode($respuesta->getBody(), true);


    if ($control_email != null) {
      
      $email = $control_email;
      $sistemaId = $request->get('sistemaId');

      $appAUTH = env('AUTH_ENDPOINT');
      $token = str_replace('"', '', session('token_go'));
      $endpoint = $appAUTH . "/users/send-code";

      $client = new Client();

        $respuesta = $client->request(
          'POST',
          $endpoint,
          [
            'headers' =>
            [
              'Authorization' => 'Bearer ' . $token,
              'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
              'Email' => $email,
              'SistemaId' => $sistemaId,
            ])
          ]
        );

        $respuesta = json_decode($respuesta->getBody(), true);

        session(['status' => '¡Se envió un código de confirmación a tu nuevo correo!']);
        return redirect()->back();
      } else {

      session(['status' => '¡Se guardaron los cambios correctamente!']);
      return redirect()->back();
      }
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return redirect()->back()->with("error", $obj->{'message'});
    }
  }

  //  actualizar datos de usuarios ( servicio autentificacion Contraseña) 
  public function updatePassword(Request $request)
  {
    if (!session('permission')->contains('usuario.update')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $messages = [
      'regex' => 'Contraseña debe contener al menos una Letra minúscula,una Letra mayúscula,un Número y un Símbolo.'
    ];

    $validator = Validator::make($request->all(), [
      //'name' => 'required|max:30',
      'clave' => [
        'required', 'min:8', 'max:20',
        'regex:/[a-z]/',      // must contain at least one lowercase letter
        'regex:/[A-Z]/',      // must contain at least one uppercase letter
        'regex:/[0-9]/',      // must contain at least one digit
        'regex:/[@$!%*#?&.,:;]/'
      ], // must contain a special character],
      'repetirclave' => 'required|min:8|max:20|same:clave',
    ], $messages);

    if ($validator->fails()) {
      foreach ($validator->messages()->getMessages() as $field_name => $messages) {
        foreach ($messages as $message) {
          $errors[] = $message;
        }
      }
      return redirect()->back()->with('errors', $errors)->withInput();
      die;
    }

    // $activo = true;
    // if ($request->activo == null) {
    //   $activo = false;
    // };

    $user = User::where('login_api_id', $request->get('id'))->first();
    // dd($user);

    $appAUTH = env('AUTH_ENDPOINT');
    $token = str_replace('"', '', session('token_go'));
    $endpoint = $appAUTH . "/users/change-pass";

    $id_usuaio_actualizar = intval($request->get('id'));


    $client = new Client();
    try {
      $respuesta = $client->request(
        'POST',
        $endpoint,
        [
          'headers' =>
          [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
          ],
          'body' => json_encode([
            'Password' => $request->get('clave'),
            'UsuerId' => $id_usuaio_actualizar,
            //'User' => $request->get('email'),
            //'Nombre' => $request->get('name'),
            //'Activo' => $activo,
            'SistemaId' => $request->get('sistemaId'),
          ])
        ]
      );

      //$user->activo = $activo;
      $user->save();
      $textoLog =  "Cambió contraseña del Usuario " . $user->name;
      Logg::info($textoLog);
      $respuesta = json_decode($respuesta->getBody(), true);

      session(['status' => 'Se guardaron los cambios correctamente !']);
      return redirect()->back();
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return redirect()->back()->with("error", $obj->{'message'});
    }
  }


  public function finder(Request $request)
  {
    //
    if (!session('permission')->contains('users.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }



    $users = User::where('name', 'like', '%' . $request->buscar . '%')->paginate(15);
    $title = "Usuario: buscando " . $request->buscar;
    return view('users.index', ['users' => $users, 'title' => $title]);
  }

  public function search(Request $request)
  {

    // ademas del nombre del usuario tiene que buscar por id del organismos 
    $term = $request->term;
    // buscar solo en los usuariso del organismo
    $organismo =  Auth::user()->userorganismo->first()->organismos_id;

    // Debe buscar usaurios por organismos (toma el id del usuario vinculado al organismo)
    $datos = DB::table('users')
      ->join("organismosusers", "organismosusers.users_id", "=", "users.id")
      ->select("users.id", "users.email", "users.name", "users.admin", "organismosusers.*")
      ->where("organismos_id", "=", $organismo)
      ->where("admin", "<>", 1)
      ->where("users.activo","=", 1)
      ->where(function ($query) use ($term) {
        $expresion = "'%$term%'";
        return  $query->orWhereRaw('users.email like ' . $expresion . ' or users.name like ' . $expresion);
      })
      ->get();

    $adevol = array();
    if (count($datos) > 0) {
      foreach ($datos as $dato) {
        $adevol[] = array(
          'id' => $dato->users_id,
          'value' => $dato->name . ' - Email: ' . $dato->email
        );
      }
    } else {
      $adevol[] = array(
        'id' => 0,
        'value' => 'no hay coincidencias para ' .  $term
      );
    }
    return json_encode($adevol);
  }

  public function searchmemo(Request $request)
  {
    $term = $request->term;
    $datos = User::where('name', 'like', '%' . $request->term . '%')
      ->where('activo', true)
      ->where('recibe_memo', true)
      ->get();

    $adevol = array();
    if (count($datos) > 0) {
      foreach ($datos as $dato) {
        $adevol[] = array(
          'id' => $dato->id,
          'value' => $dato->name,
        );
      }
    } else {
      $adevol[] = array(
        'id' => 0,
        'value' => 'no hay coincidencias para ' .  $term
      );
    }
    return json_encode($adevol);
  }

//  dar de baja a usuarios ( servicio autentificacion Estado) 
public function bajaUser(Request $request)
{
  if (!session('permission')->contains('usuario.update')) {
    session(['status' => 'No tiene acceso para ingresar a este modulo']);
    return redirect()->route('index.home');
  }

  $user = User::where('login_api_id', $request->get('userId'))->first();
  $email = $user->email;
  $nombre= $user->name;

  if ($user->activo == 1) {
    $estado = false;
  } else {
    $estado = true;
  }
  $activo = $estado;

  $appAUTH = env('AUTH_ENDPOINT');
  $token = str_replace('"', '', session('token_go'));
  $endpoint = $appAUTH . "/adm/user-update";

  $id_usuario_actualizar = intval($request->get('userId'));


  $client = new Client();
  try {
    $respuesta = $client->request(
      'PUT',
      $endpoint,
      [
        'headers' =>
        [
          'Authorization' => 'Bearer ' . $token,
          'Content-Type' => 'application/json'
        ],
        'body' => json_encode([
          'Id' => $id_usuario_actualizar,
          'User' => $email,
          'Nombre' => $nombre,
          'Activo' => $activo,
          'SistemaId' => $request->get('sistemaId'),
        ])
      ]
    );

    $user->activo = $activo;
    $user->name = $nombre;
    $user->save();

    // Se cambia estado activo en todos los lugares que pertenesca
    $OrgUser = OrganismosUser::where('users_id', $user->id)->first();
    $OrgUser->activo = $estado;
    $OrgUser->save();

    // Considerando que el user puede ser admin y estar en varios sectores
    $SectoresUser = Organismossectorsuser::where('users_id', $user->id)->get();

    foreach ($SectoresUser as $i => $SectorUser) {
      $SectorUser->activo = $estado;
      $SectorUser->save();
    }
    $SectorUser->activo = $estado;
    $SectorUser->save();

    $textoLog = "Cambio estado de usuario " . $user->name;
    Logg::info($textoLog);
    $respuesta = json_decode($respuesta->getBody(), true);

    return response()->json(['1']);
  } catch (ClientException $e) {
    Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
    return response()->json(['response' => 2, 'error' => $e]);
  }
}

// Opcion de reenviar el mail verificación de usuario, Admin reenvia mail de verificación de usuario para que pueda cambiar la contraseña a travez del link pasado por correo
public function reenviarMail(Request $request) {
  
  $email = $request->email;
  $sistemaId = $request->sistemaId;

  $appAUTH = env('AUTH_ENDPOINT');
  $token = str_replace('"', '', session('token_go'));
  $endpoint = $appAUTH . "/users/send-code";

  $client = new Client();
  try {
    $respuesta = $client->request(
      'POST',
      $endpoint,
      [
        'headers' =>
        [
          'Authorization' => 'Bearer ' . $token,
          'Content-Type' => 'application/json'
        ],
        'body' => json_encode([
          'Email' => $email,
          'SistemaId' => $sistemaId,
        ])
      ]
    );

    $respuesta = json_decode($respuesta->getBody(), true);

    return response()->json(['response' => 1]);
  } catch (ClientException $e) {
    Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
    $response = $e->getResponse();
    $responseBodyAsString = $response->getBody()->getContents();
    $obj = json_decode($responseBodyAsString);

    return response()->json(['response' => 2, 'error' => $obj->message]);
  }

}

public function updatePasswordOut(Request $request) {
  
  $email = $request->email;
  $endpoint = env('AUTH_ENDPOINT'). "/users/send-code-from-outside";

  $client = new Client();

  try {

      $respuesta = $client->request('POST', $endpoint, [
          'headers' => [
              'Content-Type' => 'application/json'
          ],
          'body' => json_encode([
              'Email' => $email
          ])
      ]);

      $body = json_decode($respuesta->getBody(), true);
  }
  catch (ClientException $e) {

    Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
    $response = $e->getResponse();
    $responseBodyAsString = $response->getBody()->getContents();
    $obj = json_decode($responseBodyAsString);

    return response()->json(['response' => 2, 'error' => $obj->message]);
  }

  return response()->json([
    'response' => 1
  ]);
}

}
