<?php

namespace App\Http\Controllers;

use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Http\Request;
use Validator;
use Caffeinated\Shinobi\Models\Permission;
use Caffeinated\Shinobi\Facades\Shinobi;
use App\Logg;
use App\Organismo;
use App\Permiso;
use App\Permissionrole;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {

    if (!session('permission')->contains('permiso.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
      // sistema id del usuario que inicio sesion
     
    $sistemaId = session('sistema_id');    
    $client = new Client();

    $appAUTH = env('AUTH_ENDPOINT');
    $token = str_replace('"','',session('token_go'));

    $endpoint = $appAUTH."/adm/permisos";

    try {
      $respuesta = $client->request('GET', $endpoint, [
        'headers' =>  [
          'Authorization' => 'Bearer ' . $token,
          'Content-Type' => 'application/json',
          ],
         'query' => [
          'SistemaId' => intval($sistemaId)
         ]
        ]);

      $bodyResponse = json_decode($respuesta->getBody(), true);
      $dataPermisosResponse = $bodyResponse['data'];
      // Creamos una Collection con los datos de los permisos que vienen de la BD maestros
      $permisosCollection = Permiso::hydrate($dataPermisosResponse);
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );

      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return redirect('/')->with("error", $obj->{'message'});
      // return back()->with("error", $obj->{'message'});
    }

    // la vista esta paginada, asi que debemos transformar la collection a paginator
    $permissions = $permisosCollection->paginate(10);

    $title = "Permisos";
    return view('permissions.index', ['permissions' => $permissions, 'title' => $title]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {

    if (!session('permission')->contains('permiso.create')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $title = "Permisos";
    return view('permissions.create', ['title' => $title]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {

    if (!session('permission')->contains('permiso.create')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    // dd($request->scope);

    $validator = Validator::make($request->all(), [
      'scope' => 'required|max:125',
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
    
    $sistemaId = session('sistema_id');    

    $appAUTH = env('AUTH_ENDPOINT');
    $token = str_replace('"','',session('token_go'));
    $endpoint = $appAUTH."/adm/permiso";


    $client = new Client();
    try {
      $respuesta = $client->request(
        'POST', $endpoint, [
          'headers' => 
            [ 
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
          ],
          'body' => json_encode([
            'Permiso' => $request->get('permiso'),
            'Scope' => $request->get('scope'),
            'Descripcion' => $request->get('descripcion'),
            'Activo' => true,
            'SistemasId' => intval($sistemaId)
          ])
        ]
      );
      $respuesta = json_decode($respuesta->getBody(), true);
     
      //$dataObject = $respuesta['data'];
      /*  La reespuesta exitosa es:    
            array:3 [▼
            "data" => array:8 [▼
            "id" => 16
            "created_at" => "2021-07-14 14:32:02"
            "updated_at" => "2021-07-14 14:32:02"
            "permiso" => "organismos"
            "scope" => "organismos.index"
            "descripcion" => "listado de los organismos del sistema"
            "activo" => 1
            "sistemas_id" => 0
        ]
        "message" => "Permiso guardado con éxito."
        "status" => true
      ] */
      return redirect('/permissions');
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );

      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return response()->json(['mesagge' => $obj->{'message'}, 'response' => 1]);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {

    if (!session('permission')->contains('permiso.show')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $permission = Permission::find($id);

    $permissionroles = Permissionrole::where('permission_id', $id)->get();

    $title = "Permiso";
    return view('permissions.show', [
      'permission' => $permission,
      'permissionroles' => $permissionroles,
      'title' => $title
    ]);
  }

  public function edit($id)
  {

    if (!session('permission')->contains('permiso.update')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $sistemaId = session('sistema_id');    

    $appAUTH = env('AUTH_ENDPOINT');
    $token = str_replace('"','',session('token_go'));
    $endpoint = $appAUTH."/adm/permiso";


    $client = new Client();

    try {
      $respuesta = $client->request('GET', $endpoint, [
        'headers' =>  [
          'Authorization' => 'Bearer ' . $token,
          'Content-Type' => 'application/json',
          ],
        'query' => [
          'id' => $id,
          'SistemaId' => $sistemaId
        ]
      ]);
      $bodyResponse = json_decode($respuesta->getBody(), true);
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );

      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return redirect()->back();
    }
  
    $title = "Permiso";
    return view('permissions.edit', ['permiso' => $bodyResponse, 'title' => $title]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request)
  {

    if (!session('permission')->contains('permiso.update')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }



    $sistemaId = session('sistema_id');    

    $appAUTH = env('AUTH_ENDPOINT');
    $token = str_replace('"','',session('token_go'));
    $endpoint = $appAUTH."/adm/permiso";

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
            'Id' => intval($request->get('id')), 
            'Permiso' => $request->get('permiso'),
            'Scope' => $request->get('scope'),
            'Descripcion' => $request->get('descripcion'),
            'Activo' => true,
            'SistemasId' => intval($sistemaId)
          ])
        ]

      );
      $respuesta = json_decode($respuesta->getBody(), true);
      session(['status' => 'Se guardaron los cambios correctamente !']);
      return redirect('/permissions');
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );


      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return redirect()->back()->with("error", $obj->message);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request)
  {

    if (!session('permission')->contains('permiso.delete')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $endpoint = "http://45.63.111.223:3395/usuarios/permisos";
    $client = new Client();
    $id = $request->get('id');
    $permiso = $request->get('permiso');
    try {
      $respuesta = $client->request(
        'delete',
        $endpoint,
        ['query' => [
          'permiso' => $request->get('id'),
        ]],
        [
          'headers' => ['Content-Type' => 'application/json'],
          'body' => json_encode([
            'id' => intval($request->get('id')), // necesita ser un valor entero para que se haga el update
            'permiso' => $request->get('permiso'),
            'scope' => $request->get('scope'),
            'descripcion' => $request->get('descripcion'),
            'activo' => false,
            'sistemas_id' => 3
          ])
        ]
      );
      $respuesta = json_decode($respuesta->getBody(), true);
      return response()->json(['1']);
    } catch (ClientException $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );

      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      $obj = json_decode($responseBodyAsString);
      return response()->json(['mesagge' => $obj->{'message'}, 'response' => 1]);
    }
  }

  /*
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
  public function finder(Request $request)
  {

    if (!session('permission')->contains('permisos.index')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $permissions = Permission::where('name', 'like', '%' . $request->buscar . '%')->paginate(15);
    $title = "Permiso: buscando " . $request->buscar;
    return view('permissions.index', ['permissions' => $permissions, 'title' => $title]);
  }

  public function search(Request $request)
  {
    $term = $request->term;

    $datos = Permission::where('name', 'like', '%' . $request->term . '%')->where('activo', true)->get();
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
