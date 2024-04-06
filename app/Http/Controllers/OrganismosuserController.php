<?php

namespace App\Http\Controllers;

use App\Logg;
use App\User;
use Carbon\Carbon;
use Validator;
use App\Organismo;
use App\Organismossector;
use App\Organismossectorsuser;
use App\Roleuser;
use GuzzleHttp\Client;
use App\Organismosuser;
use Illuminate\Http\Request;
use UnexpectedValueException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Caffeinated\Shinobi\Facades\Shinobi;
use GuzzleHttp\Exception\ClientException;
use App\Http\Requests\OrganismoUserRequest;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Traits\VerificarUserTrait;

class OrganismosuserController extends Controller
{

  use VerificarUserTrait;

  // 1 - listar los usuarios de cada organismo// 
  // 2 - consulta al servicio de autentificacion los usuarios por sistema_id
  public function index($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
  
    // DAtos del organismo 
    $organismo = Organismo::find($id);
    $title = "Usuarios del Organismo " . $organismo->organismo;

    // consultar por el usuario superadmin : usado para filtrar todos los usuarios menos el superadmin 
    $user = User::where('admin', '=', 1)->first();
    
    // SERVICIO DE AUTENTIFICACION 
     $sistemaId = session('sistema_id');    
    
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

             // me filtra todos los usuarios menos el superadministrador 
             $arrayUsers = $respuesta['data'];
             for ($i = 0; $i < count($arrayUsers); $i++) {
               if ($arrayUsers[$i]['User'] == $user->email) {
                 unset($arrayUsers[$i]);
               }
             }
           
            $permisosCollection = User::hydrate($arrayUsers);

            } catch (ClientException $e) {
              Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
              $response = $e->getResponse();
              $responseBodyAsString = $response->getBody()->getContents();
              $obj = json_decode($responseBodyAsString);
              return redirect('/')->with("error", $obj->{'message'});
              // return response()->json(['mesagge' => $obj->{'message'}, 'response' => 2]);
        
           }  
          //  $organismosusers = $permisosCollection->paginate(30);
          
          $organismosusers = $permisosCollection; // se consulta por la lista completa de usuarios sin paginar. La paginacion la hace el datatable

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

            for ($i = 0; $i < count($respuestaRoles); $i++) {
              if ($respuestaRoles[$i]['Scope'] == "gestionar.sistemas") {
                unset($respuestaRoles[$i]);
              }
            }

          } catch (ClientException $e) {
            Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
          }

            $roles = RoleUser::hydrate($respuestaRoles);

           $sectores= Organismossector::where("organismos_id",$organismo->id)->orderBy('organismossector', 'ASC')->get();
        
    return view('organismosusers.index', ['organismo' => $organismo, 'organismosusers' => $organismosusers, 'title' => $title,'sectores' => $sectores,'roles' => $roles]);
}

  

  public function edit($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $organismousers = Organismosuser::where('users_id', $id)->first();
    $organismo = organismo::find($organismousers->organismos_id);
    $title = "Editar usuario " . $organismousers->users->name;
    return view('organismosusers.edit', ['organismo' => $organismo, 'title' => $title, 'organismousers' => $organismousers]);
  }


  public function update(Request $request, $id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $activo = 1;
    if ($request->activo == "") {
      $activo = 0;
    };


    $validator = Validator::make($request->all(), [
      'name' => 'required|max:15',
      'email' => 'required|email',
      'direccion' => 'max:25',
      'telephone' => 'max:25',
      // 'logo'   =>  'required|mimes:jpg,jpeg,png'
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

    $organismo = organismo::find($id);
    //verificar campo logo 
    if ($request->file('logo') !== null) {
      $ruta_imagen = $request['logo']->store('logos-organismos', 'public');
      $imagen = Image::make(public_path("storage/{$ruta_imagen}"))->fit(450, 450);
      $imagen->save();
    } else {
      $ruta_imagen = $organismo->logo;
    }

    $organismo->codigo = $request->codigo;
    $organismo->organismo = $request->organismo;
    $organismo->direccion = $request->direccion;
    $organismo->email = $request->email;
    $organismo->telefono = $request->telefono;
    $organismo->activo = $activo;
    $organismo->logo = $ruta_imagen;
    $organismo->save();

    $textoLog = "Modificó Organismo " .  $organismo->organismo;
    Logg::info($textoLog);

    return redirect('/organismos');
  }

  public function store(Request $request)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    if ($request->users_id == NULL) {
      return  redirect()->back()->with('error', 'Campo vacio , ingrese un usuario');
    } else if (DB::table('organismosusers')->where('organismos_id', $request->organismos_id)->where('users_id', $request->users_id)->exists()) {
      return  redirect()->back()->with('error', 'El usuario que intenta agregar ya existe en la organización');
    } else {
      $organismosuser = new Organismosuser;
      $organismosuser->organismos_id = $request->organismos_id;
      $organismosuser->users_id = $request->users_id;
      $organismosuser->save();

      $user = User::find($organismossectorsuser->users_id);
     
      $textoLog = "Asignó usuario " .   $user->name . " al organismo.";
      Logg::info($textoLog);
      
      return redirect('/organismos/' . $request->organismos_id . '/users')->with('success', 'El usuario se agrego correctamente');
    }
  }

  public function storeUsers(OrganismoUserRequest $request)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    DB::beginTransaction();
    try {

      $activo = 1;
      if ($request->activo == "") {
        $activo = 0;
      };

      $user = new User;
      $user->name = $request->name;
      $user->email = $request->email;
      $user->address = $request->address;
      $user->telephone = $request->telephone;
      $user->activo = $activo;
      $user->password = Hash::make($request->password);
      $user->save();

      $usersorganismo = new Organismosuser;
      $usersorganismo->users_id = $user->id;
      $usersorganismo->organismos_id = $request->organismo_id;
      $usersorganismo->activo = $activo;
      $usersorganismo->save();

      $textoLog = "Creó usuario ". $user->name ." en organismo.";
      Logg::info($textoLog);

      DB::commit();
      return redirect('/organismos/' . $request->organismo_id . '/users')->with('success', 'El usuario se agrego correctamente');
    } catch (\Exception $e) {
      DB::rollback();
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
    }
  }

  public function destroy($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $organismosuser = Organismosuser::find($id);
    if ($organismosuser->activo == 1) {
      $activo = 0;
    } else {
      $activo = 1;
    };
    $organismosuser->activo = $activo;
    $organismos_id = $organismosuser->organismos_id;
    $organismosuser->update();
    $user = User::find($organismosuser->users_id);

    $textoLog = "Cambio estado usuario ".  $user->name . " en organismo.";
    Logg::info($textoLog);

    return redirect('/organismos/' . $organismos_id . '/users');
  }

  public function finder(Request $request)
  {
    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $valorBuscado = $request->buscar;
    $organismo_id = $request->organismo_id;
    $organismo = Organismo::find($organismo_id);
    // $organismosusers = Organismosuser::with('users')
    //   ->orWhereHas('users', function ($query) use ($valorBuscado, $organismo_id) {
    //     $query->where('organismos_id', $organismo_id);
    //     $query->where('name', 'like', '%' . $valorBuscado . '%');
    //     $query->orWhere('email', 'like', '%' . $valorBuscado . '%');
    // })->paginate(10);
    
    if ((strpos($valorBuscado, '@')) == true || (empty($valorBuscado))) {
      // Se asigna a un array el valor del input de busqueda, donde cada posicion del mismo es una palabra separada por un espacio
      $arrayBusqueda = explode(' ', $valorBuscado);
      $longitudBusqueda = count($arrayBusqueda);
      // Se asigna a una variable el valor de la ultima posicion del array
      $arrayBusqueda = $arrayBusqueda[$longitudBusqueda - 1];
      $valorBuscado = $arrayBusqueda;

      $user = User::where('admin', '=', 1)->first();
      $sistemaId = session('sistema_id');

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
                  'Email' => $valorBuscado,
                  'SistemaId' => $sistemaId
                ])
              ]);
            $respuesta = json_decode($respuesta->getBody(), true);

              // me filtra todos los usuarios menos el superadministrador 
              $arrayUsers = $respuesta['data'];
              for ($i = 0; $i < count($arrayUsers); $i++) {
                if ($arrayUsers[$i]['User'] == $user->email) {
                  unset($arrayUsers[$i]);
                }
              }
            
            $permisosCollection = User::hydrate($arrayUsers);

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

            } catch (ClientException $e) {
              Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
              $response = $e->getResponse();
              $responseBodyAsString = $response->getBody()->getContents();
              $obj = json_decode($responseBodyAsString);
              return redirect('/')->with("error", $obj->{'message'});
              // return response()->json(['mesagge' => $obj->{'message'}, 'response' => 2]);
        
            }  

            $organismosusers = $permisosCollection->paginate(15);

            if (empty($valorBuscado)) {
              $title = "Usuarios: buscando todos los usuarios";
            }
            else {
              $title = "Usuarios: buscando " .$valorBuscado;
            }
    }
    else {
      return $this->index($organismo_id);
    }

    $roles = RoleUser::hydrate($respuestaRoles);

    $sectores= Organismossector::where("organismos_id",$organismo->id)->orderBy('organismossector', 'ASC')->get();
 
    return view('organismosusers.index', ['organismo' => $organismo, 'organismosusers' => $organismosusers, 'title' => $title,'sectores' => $sectores,'roles' => $roles]);
  }


  // crear usuario authentication_service (usuario administrador de cada organismo, ver organismo/sistema id)
  public function createUser(Request $request)
  {
        $User=$request->get('email');
        $Nombre=$request->get('apell_nomb');
        $Password = "Lucasg2022@@";
        $Activo=true;

        // tomar el sistema vinculado al usuario/organismo
        $sistemaId = session('sistema_id');    

        // tomar el id del organismo vinculado al usuario que crea el usuario(este usuario estarta vinculado a un solo organismo)
        $organismo_id = Auth::user()->userorganismo->last()->organismos_id;

        // Se llama al trait verificarUser que verifica si el usuario existe en la base local antes de mandar a autenticacion service
        if ($this->verificarUser($User)) {
          return response()->json(['mesagge' => 'El usuario que intenta registrar ya existe en la base de datos', 'response' => 3]);
        }

        $appAUTH = env('AUTH_ENDPOINT');
        $token = str_replace('"','',session('token_go'));

        $endpoint = $appAUTH."/adm/user-create";
        
         $client = new Client();
             try {
               $respuesta = $client->request('POST', $endpoint,
                   ['headers' =>  [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    ],
                   'body' => json_encode([
                    'User' => $User,
                    'Nombre' => $Nombre,
                    'password' => $Password,
                    'Activo' => $Activo,
                    'SistemaId' =>  $sistemaId
                   ])
                 ]);
               $respuesta = json_decode($respuesta->getBody(), true);
              //  $dataObject = $respuesta['data'];

               //  1me debe retornar los datos del usuario creado en auth service 
               //  2 crear un usuario(base de datos local local)

               //  3 verificar si el usuario que intenta registra existe en la base de datos 
               //  ver situazion de usaurios con mas de un organismo
        
                $usuario_verificar = User::where("email","=",$respuesta['User'])->first();
              
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
                $userorganismo->organismos_id = $organismo_id;
                $userorganismo->activo = 1;
                $userorganismo->save();
                $textoLog = "Creó usuario ".  $user->name ." en organismo. ";
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
               };
               } catch (ClientException $e) {
                 Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
           
                 $response = $e->getResponse();
                 $responseBodyAsString = $response->getBody()->getContents();
                 $obj = json_decode($responseBodyAsString);
                 return response()->json(['mesagge' => $obj->{'message'}, 'response' => 2]);
           
              }  
  }

  public function logUser(Request $request,$idUserOrganismo )
  {
    if (!session('permission')->contains('organismos.index.admin') AND !session('permission')->contains('organismos.index.superadmin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $id = base64_decode($idUserOrganismo);

    $user = User::where('login_api_id', $id )->first();
    
    $organismouser = Organismosuser::where('users_id', $user->id)->first();

    if (isset($request->fecha_inicio) and isset($request->fecha_final))
    {
     
      $startDate = Carbon::parse($request->fecha_inicio);
      $endDate =  Carbon::parse($request->fecha_final);

      $logsFiltrados= DB::table('logs')->where('users_id', $user->id)->whereBetween(DB::raw('date(created_at)'), 
      [$startDate->toDateString(),
      $endDate->toDateString()])->get();
      
      $consultaLogs = $logsFiltrados;
      
      $title = "Logs Usuario " . $user->name . " entre el " . $startDate->format('d/m/Y') . " y el " . $endDate->format('d/m/Y');
    } else {
      $consultaLogs= DB::table('logs')->where('users_id', $user->id)->orderBy('id', 'desc')->get();

      $title = "Logs Usuario " . $user->name;
    }

    return view('organismosusers.log', ['organismo' => $organismouser->organismos_id, 'usuario'=> $user, 'logs' => $consultaLogs, 'title' => $title]);

  }

 
}
