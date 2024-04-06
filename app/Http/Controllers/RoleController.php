<?php

namespace App\Http\Controllers;

use App\Logg;
use App\User;
use Validator;
use App\Roleuser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Caffeinated\Shinobi\Models\Role;
use Caffeinated\Shinobi\Facades\Shinobi;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class RoleController extends Controller
{

  // En esta funcion se listan los roles del sistema id 3
  //permisos disponible para cada rol 
  public function index()
  {
    if (!session('permission')->contains('rol.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
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

      $rolesCollection = RoleUser::hydrate($respuestaRoles);
      $roles = $rolesCollection->paginate(10);

      $title = "Roles";
      // return view('roles.index', ['roles' =>  $respuesta['data'], 'title' => $title]);
      return view('roles.index', ['roles' =>  $roles, 'title' => $title]);
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return redirect()->route('index.home')->with("error", $obj->message);
    }
  }

  /**
   * Devuelve la vista de crear rol
   * @return \Illuminate\View\View
   */
  public function create()
  {

    if (!session('permission')->contains('rol.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $title = "Agregar un nuevo rol";
    return view('roles.create', ['title' => $title]);
  }

  // En esta funcion se agrega  nuevo rol al sistema de autenticacion
  public function store(Request $request)
  {

    if (!session('permission')->contains('rol.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $rol = $request->get('Rol');
    // El scope de esete sistema es el quee sta en configuraciones
    $scope = config('configuraciones.Scope') . '.' . $rol;
    $descripcion = $request->get('Descripcion');



    // Endpoint de roles de autenticacion service = base url + ruta de crear roles
    /*  Example:  
    {
      "SistemasId": 1,
      "Rol": "Rol False Prueba",
      "Scope": "Scope Prueba",
      "Descripcion": "Descripcion prueba",
      "Activo": false
    } 
    */
    $endpoint = env('AUTH_ENDPOINT') . config('configuraciones.CreateRol');
    $token = str_replace('"', '', session('token_go'));
    $client = new Client();
    try {
      $respuesta = $client->post(
        $endpoint,
        [
          'headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token,],
          'body' => json_encode([
            'SistemasId' => intval(session()->get('sistema_id')),
            'Rol' => $rol,
            'Scope' => $scope,
            'Descripcion' => $descripcion,
            'Activo' => true,
          ])
        ]
      );
      $respuesta = json_decode($respuesta->getBody(), true);

      return response()->json(['dataObject' =>  $respuesta, 'response' => 1]);
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return response()->json(['mesagge' => $obj['message'], 'response' => 2]);
    }
  }

  /**
   * consultar los permisos disponibles para el del sistema id (los datos se observaran en una ventana modal)
   * @param  mixed $idrol
   * @return void
   */
  public function consultarpermisos($idrol)
  {
    $ScopeSuperAdmin = session('rols')->toArray();
    $scope = $ScopeSuperAdmin['0'];

    $sistema_id = session('sistema_id');
    $token = str_replace('"', '', session('token_go'));
    $endpoint = env('AUTH_ENDPOINT') . config('configuraciones.GetPermisosVincular');
    $client = new Client();
    try {
      $respuesta = $client->get($endpoint, [
        'headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token,],
        'query' => ['RolId' => $idrol, 'SistemaId' => $sistema_id, 'Scope' => $scope ]
      ]);
      $respuesta = json_decode($respuesta->getBody(), true);

      return response()->json(['respuesta' => $respuesta]);
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return redirect()->route('index.home')->with("error", $obj->message);
    }
  }

  function asignarpermisosrol(Request $request) {
    $token = str_replace('"', '', session('token_go'));
    $endpoint = env('AUTH_ENDPOINT') . config('configuraciones.CreateRolPermiso');

    $client = new Client();
    
    try {
      foreach ($request->permisos as $permiso) {
        $options = [
          'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
          ],
          'body' => json_encode([
            'RolId' => intval($request->idrol),
            'PermisoId' => intval($permiso),
            "SistemaId" => intval(session()->get('sistema_id')),
          ]),
        ];
        $respuesta = $client->post($endpoint, $options);
  
        $respuesta = json_decode($respuesta->getBody(), true);
      }
      return response()->json(['response' => 1]);
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return response()->json(['error' => $obj->message]);
    }
  }

  /**
   * Asignar un permiso a un rol
   * @param  mixed $index
   * @param  mixed $index2
   * @return void
   */
  public function asignarpermisorol($rol_id, $permiso_id)
  {
    $token = str_replace('"', '', session('token_go'));
    $endpoint = env('AUTH_ENDPOINT') . config('configuraciones.CreateRolPermiso');

    $client = new Client();
    $options = [
      'headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $token,
      ],
      'body' => json_encode([
        'RolId' => intval($rol_id),
        'PermisoId' => intval($permiso_id),
        "SistemaId" => intval(session()->get('sistema_id')),
      ]),
    ];
    try {
      $respuesta = $client->post($endpoint, $options);

      $respuesta = json_decode($respuesta->getBody(), true);
      return response()->json(['response' => 1]);
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return response()->json(['error' => $obj->message]);
    }
  }

  public function quitarrolpermiso($idpermiso, $idrol)
    {

      $PermisoId = intval($idpermiso);
      $RolId = intval($idrol);
      $sistema_id = session('sistema_id');
      $appAUTH = env('AUTH_ENDPOINT');
      $token = str_replace('"','',session('token_go'));

      $endpoint = $appAUTH."/adm/rol-permiso";
      
       $client = new Client();
           try {
             $respuesta = $client->request('DELETE', $endpoint,
                 ['headers' =>  [
                  'Authorization' => 'Bearer ' . $token,
                  'Content-Type' => 'application/json',
                  ],
                 'body' => json_encode([
                  'RolId' => $RolId,
                  'PermisoId' => $PermisoId,
                  'SistemaId' => intval($sistema_id),
                 ])
               ]);
              $respuesta = json_decode($respuesta->getBody(), true);
              return response()->json(['response' => 1]);

             } catch (ClientException $e) {
              Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
               
               return response()->json(['mesagge' => $e->getMessage(), 'response' => 2]);
         
        }       
    
    }

    public function updateRol(Request $request)
    {
       
      $appAUTH = env('AUTH_ENDPOINT');
      $sistema_id = session('sistema_id');
      $token = str_replace('"','',session('token_go'));
      $endpoint = $appAUTH."/adm/rol";
  
      $id_rol = intval($request->get('id'));
      $client = new Client();
      try {
        $respuesta = $client->request(
          'PUT', $endpoint, [
            'headers' => 
              [ 
              'Authorization' => 'Bearer ' . $token,
              'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
              'Id' => $id_rol,
              'Rol' => $request->get('rol'),
              'Scope' => $request->get('scope'),
              'Descripcion' => $request->get('descripcion'),
              'SistemasId' => intval($sistema_id),
              'Activo' => true
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
  





  public function search(Request $request)
  {
    $term = $request->term;

    $datos = Role::where('name', 'like', '%' . $request->term . '%')->get();
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
}
