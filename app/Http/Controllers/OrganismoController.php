<?php

namespace App\Http\Controllers;

use App\Logg;
use Auth, PDF;
use Validator;
use App\Organismo;
use App\Organismosuser;
use App\Localidad;
use App\Configuracion;
use Illuminate\Http\Request;

//use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Caffeinated\Shinobi\Facades\Shinobi;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class OrganismoController extends Controller
{

  public function index()
  {

    // 1 organismo.index.superadmin filtra y trae todos los organismos 
    // 2 si no solo el organismo del usuario administrador 
    $session = session('permission');

    if ($session->contains('organismos.index.admin')) {
      $title = "Organismo";
      $organismouser = Organismosuser::where('users_id', Auth::user()->id)->first();
      $organismos = Organismo::where('id', $organismouser->organismos_id)->first();
      $configOrganismo = $organismos->configuraciones;
      // dd($organismos);
      return view('organismos.indexadmin',['organismo' => $organismos, 'title' => $title, 'configOrganismo' => $configOrganismo]);
      // return view('organismos.index', ['organismos' => $organismos, 'title' => $title]);
    } elseif ($session->contains('organismos.index.superadmin')) {
      $organismos = Organismo::all();
      $title = "Organismos";
      return view('organismos.index', ['organismos' => $organismos, 'title' => $title]);
    } else {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
  }

  public function create()
  {

    if (!session('permission')->contains('organismos.index.superadmin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $title = "Nuevo organismo";
    return view('organismos.create', ['title' => $title]);
  }

  public function store(Request $request)
  {

    if (!session('permission')->contains('organismos.index.superadmin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $activo = 1;
    if ($request->activo == "") {
      $activo = 0;
    };


    $validator = Validator::make($request->all(), [
      'codigo' => 'required|unique:organismos,codigo|max:5',
      'organismo' => 'required|unique:organismos,organismo|max:254',
      'direccion' => 'required|max:254',
      'email' => 'required|email|max:254',
      "telefono"=> 'numeric',
      'logo'   =>  'required|mimes:jpg,jpeg,png',
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

    // verificacion campo logo de organismo
    if ($request->file('logo') == NULL) {
      $ruta_imagen = '/assets/img/default.jpg';
    } else {
      // ruta donde se guardar la imagen logos-organismos/77dsagdh.jpg
      $ruta_imagen = $request->file('logo')->store('logos-organismos', 'public');

      //reducir el archivo
      $imagen = Image::make($request->file('logo'));
      $imagen->widen(604);

      Storage::disk('local')->put($ruta_imagen, $imagen->encode('webp', 90));
    }

    $organismo = new Organismo;
    $organismo->codigo = $request->codigo;
    $organismo->organismo = $request->organismo;
    $organismo->direccion = $request->direccion;
    $organismo->email = $request->email;
    $organismo->telefono = $request->telefono;
    $organismo->activo = $activo;
    $organismo->localidads_id = 9;
    $organismo->logo = $ruta_imagen;
    $organismo->save();

    // cuando se crea un organismo, se crea automaticamente un registro de Configuraciones para el mismo con datos por defecto
    $configOrganismo = new Configuracion;
    $configOrganismo->expediente_num = 1;
    $configOrganismo->foja_num = 1;
    $configOrganismo->foja_fecha = 1;
    $configOrganismo->foja_hora = 1;
    $configOrganismo->foja_user = 1;
    // $configOrganismo->url = "https://docs.google.com/document/d/1nmeG6STx9UtGnossf6Z9_1zFqIRirhdh/edit?usp=sharing&ouid=105387240971524540162&rtpof=true&sd=true";
    $configOrganismo->url = "https://docs.google.com/document/d/1A5VNifh4BSc-xjy0vbFU6F7sf4QGGaUp/edit?usp=share_link&ouid=110371566330306683164&rtpof=true&sd=true";
    $configOrganismo->organismos_id = $organismo->id;
    $configOrganismo->sector = 1;
    $configOrganismo->sector_telefono = 1;
    $configOrganismo->sector_correo = 1;
    $configOrganismo->nomenclatura = NULL;
    $configOrganismo->filtros_documentos = 1;
    $configOrganismo->save();

    $textoLog = "Creó el organismo " .  $organismo->organismo;
    Logg::info($textoLog);

    //Session::flash('flash_message', 'El organismo se guardó correctamente.');
    //Session::save();

    return redirect('/organismos');
  }

  public function show($id)
  {

    $session = session('permission');
    if ($session->contains(function ($permiso) {
      return $permiso == 'organismos.index.superadmin' || $permiso == 'organismos.index.admin';
    })){

        try {
      //code...
      $organismo = organismo::findOrFail($id);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      session(['status' => 'No tiene autorizacion para realizar esta acción']);
      return redirect()->route('index.home');
    }

    $title = "Datos del organismo  " . $organismo->organismo;
    return view('organismos.show', ['organismo' => $organismo, 'title' => $title]);

    }else{
      return redirect('/')->with('status', 'No tiene acceso para ingresar a este modulo');
    }
  }

  public function edit($id)
  {

    $session = session('permission');
    if ($session->contains(function ($permiso) {
      return $permiso == 'organismos.index.superadmin' || $permiso == 'organismos.index.admin';
    })){
      try {
        //code...
        $organismo = organismo::findOrFail($id);
        $localidades = Localidad::orderBy('localidad', 'ASC')->get();

      } catch (\Exception $e) {
        Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
        return redirect('/');
      }
  
      // Autorizacion
      try {
        $this->authorize('show', $organismo);
      } catch (\Exception $exception) {
        Logg::error($exception->getMessage(),("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()) );
        if ($exception instanceof AuthorizationException) {
          session(['status' => 'No tiene autorizacion para realizar esta acción']);
          return redirect('/')->with('status', 'No tiene acceso para ingresar a este modulo');
        }
      }
      
      $title = "Editar organismo " . $organismo->organismo;
      return view('organismos.edit', ['organismo' => $organismo, 'title' => $title, 'localidades'=>$localidades]);
    }else{
      return redirect('/')->with('status', 'No tiene acceso para ingresar a este modulo');
    }
  
  }

  public function update(Request $request, $id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $activo = 1;
    // if ($request->activo == "") {
    //   $activo = 0;
    // };


    $validator = Validator::make($request->all(), [
      'codigo' => 'required|unique:organismos,codigo,' . $id . '|max:5',
      'organismo' => 'required|unique:organismos,organismo,' . $id . '|max:254',
      'direccion' => 'required|max:254',
      'email' => 'required|email|max:254',
      "telefono"=> 'numeric',
      'logo'   =>  '|mimes:jpg,jpeg,png'
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
      $ruta_imagen = $request->file('logo')->store('logos-organismos', 'public');

      //reducir el archivo
      $imagen = Image::make($request->file('logo'));
      $imagen->widen(604);
      Storage::disk('local')->put($ruta_imagen, $imagen->encode('webp', 90));
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
    $organismo->localidads_id = $request->localidad;
    $organismo->save();

    $textoLog = "Modificó el organismo " .  $organismo->organismo;
    Logg::info($textoLog);


    return redirect('/organismos');
  }

  public function destroy($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $organismo = organismo::find($id);
    $textoLog = "Eliminó organismo " . $organismo->organismo;
    Logg::info($textoLog);
    $organismo->delete();
    

    return redirect('/organismos');
  }

  public function finder(Request $request)
  {
    if (!session('permission')->contains('organismos.index.superadmin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $organismos = organismo::where('organismo', 'like', '%' . $request->buscar . '%')->paginate(15);

    $title = "organismo: buscando " . $request->buscar;
    return view('organismos.index', ['organismos' => $organismos, 'title' => $title]);
  }

  public function search(Request $request)
  {
    $term = $request->term;
    $datos = organismo::where('organismo', 'like', '%' . $request->term . '%')
      ->where('activo', true)->get();
    $adevol = array();
    if (count($datos) > 0) {
      foreach ($datos as $dato) {
        $adevol[] = array(
          'id' => $dato->id,
          'value' => $dato->organismo,
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

  public function configuraciones ($id) {
    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $organismo = Organismo::find($id);
    $configuraciones = $organismo->configuraciones;
    $title = $organismo->organismo;

    return view('organismos.configuraciones', ['organismo' => $organismo, 'title' => $title, 'configuraciones' => $configuraciones]);
  }

  public function updateConfig(Request $request, $id) {
    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $organismo = Organismo::find($id);
    $configuraciones = $organismo->configuraciones;

    $nroDocumento = 1;
    if ($request->nroDocumento == "") {
      $nroDocumento = 0;
    };

    $nroFoja = 1;
    if ($request->nroFoja == "") {
      $nroFoja = 0;
    }

    $fechaAlta = 1;
    if ($request->fechaAlta == "") {
      $fechaAlta = 0;
    }

    $horaAlta = 1;
    if ($request->horaAlta == "") {
      $horaAlta = 0;
    }

    $sector = 1;
    if ($request->sector == "") {
      $sector = 0;
    }

    $sectorTelefono = 1;
    if ($request->sectorTelefono == "") {
      $sectorTelefono = 0;
    }

    $sectorCorreo = 1;
    if ($request->sectorCorreo == "") {
      $sectorCorreo = 0;
    }

    $fojaUser = 1;
    if ($request->fojaUser == "") {
      $fojaUser = 0;
    }

    $filtrosDocumentos = 1;
    if ($request->filtrosDocumentos == "") {
      $filtrosDocumentos = 0;
    }

    $controlExt = 1;
    if ($request->controlExt == "") {
      $controlExt = 0;
    }

    $repiteNum = 1;
    if ($request->repiteNum == "") {
      $repiteNum = 0;
    }

    $cantdocs = 50;
    if ($request->cantdocs != "") {
      $cantdocs = $request->cantdocs;
    }

    $configuraciones->expediente_num = $nroDocumento;
    $configuraciones->foja_num = $nroFoja;
    $configuraciones->foja_fecha = $fechaAlta;
    $configuraciones->foja_hora = $horaAlta;
    $configuraciones->foja_user = $fojaUser;
    $configuraciones->sector = $sector;
    $configuraciones->sector_Telefono = $sectorTelefono;
    $configuraciones->sector_Correo = $sectorCorreo;
    $configuraciones->nomenclatura = $request->nomenclatura;
    $configuraciones->filtros_documentos = $filtrosDocumentos;
    $configuraciones->repite_num = $repiteNum;
    $configuraciones->cant_registros = $cantdocs;
    $configuraciones->control_ext = $controlExt;

    $configuraciones->save();

    return redirect('/organismos');

  }
}
