<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Expediente;
use App\Expedienteestado;
use App\Organismo;
use Carbon\Carbon;
use App\Persona;
use App\Organismostiposvinculo;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use UnexpectedValueException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Caffeinated\Shinobi\Facades\Shinobi;
use Validator;
use App\Logg;
use App\ExpedientetipoPersona;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($expediente_id)
  {
    $session = session('permission');

    if (!$session->contains('persona.vincular')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $expediente_id = base64_decode($expediente_id);

    try {
      $personas = Persona::where('organismos_id', Auth::user()->userorganismo->first()->organismos_id)->get();
      $expediente = Expediente::findOrFail($expediente_id);
      $tiposvinculo = Organismostiposvinculo::where('organismos_id', $expediente->organismos_id)
        ->where('activo', 1)
        ->get();
      $title = "Asignar Persona a Documento";
    } catch (\Exception $exception) {
      Logg::error($exception->getMessage(), ("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()));

      if ($exception instanceof ModelNotFoundException) {
        return redirect()->route('expedientes.index')->with('errors', ['El documento buscado no existe.']);
      } else {
        return redirect()->route('expedientes.index')->with('errors', ['No se puede acceder a los datos de las personas en este momento.']);
      }
    }

    return view('personas.index', ['personas' => $personas, 'expediente' => $expediente, 'title' => $title, 'tiposvinculo' => $tiposvinculo]);
  }

  public function search(Request $request)
  {
    if (null == $request->get('sexo1') and null == $request->get('sexo2')) {
      $errors[0] = "No selecciono ningun sexo en la busqueda";
      return redirect()->back()->with('errors', $errors)->withInput();
      die;
    };
    if (null !== $request->get('sexo1') and null !== $request->get('sexo2')) {
      $errors[0] = "Selecciono ambos sexos en la busqueda";
      return redirect()->back()->with('errors', $errors)->withInput();
      die;
    };

    $expediente = Expediente::findOrFail($request->expediente_id);
    $documento = $request->documento;
    $tiposvinculo = Organismostiposvinculo::where('organismos_id', $expediente->organismos_id)->where('activo', 1)->get();
    $title = "Datos encontrados de persona: ";
    // Primero se va a buscar en la base de datos local del organismo, la persona en cuestion
    try {
      // $personas = Persona::where('documento', 'like', '%' . $request->documento . '%')->paginate(10);
      $persona = Persona::where('organismos_id', Auth::user()->userorganismo->first()->organismos_id)->where('documento',  $documento)->paginate(10);
      if (count($persona) < 1) {
        throw new \Exception('No se encuentran resultados para esa busqueda');
      }
    } catch (\Exception $exception) {

      if (null != $request->get('sexo1')) {
        $sexo = "M";
      } else {
        $sexo = "F";
      }


      $token = str_replace('"', '', session('token_go'));

      // Verificar estado del servicio de RENAPER antes de realizar la consulta por los datos de la persona
      try
      {
        $endpoint_estadorenaper = ENV("ESTADO_RENAPER");

        $client2 = new Client();
        $respuesta_estado = $client2->request(
          'GET',
          $endpoint_estadorenaper,
          [
            'headers' =>  [
              'Authorization' => 'Bearer ' . $token,
              'Content-Type' => 'application/json',
            ],
            'query' => [
              null
            ]
          ]
        );
        $respuesta_estado = json_decode($respuesta_estado->getBody(), true);
      }
      catch (ClientException $e)
      {
        // Si el servicio no está funcionando, se redirecciona a la vista que permite ingresar manualmente los datos. Esto evita que arroje un error 501 Bad request
        $title = "El servicio de RENAPER está en mantenimiento. Por favor, ingrese manualmente los datos: ";

        return view('personas.create', ['title' => $title, 'expediente' => $expediente]);
      }

      // RENAPER
      $endpoint = ENV("RENAPER_ENDPOINT");

      $client = new Client();
      $intentos = 0;
      while ($intentos <= 3) {
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
                'dni' => $documento,
                'sexo' => $sexo
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
      try {
        if (isset($respuesta)) {
          // dd($respuesta);
          // $dataObject = $respuesta['data'];
          // $persona = $dataObject[0];
          // $domicilio = $persona['domicilio'];

          $persona = $respuesta['data'][0];
          $domicilio = $persona['domicilio'];

          if ($respuesta['status'] == false) {
            $title = "No se ha encontrado tal persona. Ingrese manualmente los datos: ";

            return view('personas.create', ['title' => $title, 'respuesta' => $persona, 'domicilio' => $domicilio, 'expediente' => $expediente]);
          } else {
            $title = "Datos de la persona encontrada: ";
            // $dataObject = $respuesta['persona'];
            // $persona= $dataObject[0];
            //$domicilio = $persona['domicilio'];

            return view('personas.create', ['title' => $title, 'respuesta' => $persona, 'domicilio' => $domicilio, 'expediente' => $expediente]);
          }
        } else {
          $title = "No se puso conectar con el servicio. Ingrese manualmente los datos: ";

          // return view('personas.create', ['title' => $title, 'respuesta' => $persona, 'domicilio' => $domicilio, 'expediente' => $expediente]);
          return view('personas.create', ['title' => $title, 'respuesta' => $persona, 'expediente' => $expediente]);
        }
      } catch (ClientException $e) {

        Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));

        $response = $e->getResponse();
        $responseBodyAsString = $response->getBody()->getContents();
        $obj = json_decode($responseBodyAsString);
        //return redirect('/')->with("error",$responseBodyAsString);
        // return redirect('/')->with("error","No tiene permisos para usar dicho modulo");
        $title = "No se pudo conectar con Renaper. Ingrese manualmente los datos: ";

        return view('personas.create', ['title' => $title, 'expediente' => $expediente]);
      }
    }

    return view('personas.index', ['personas' => $persona, 'expediente' => $expediente, 'title' => $title, 'tiposvinculo' => $tiposvinculo]);
  }

  function vincularips(Request $request) {
    $expediente_id = $request->get('expediente_id');
    $persona_id = $request->get('persona_id');
    $tipo_vinculo = $request->get('tipo_vinculo');
    $persona = Persona::find($persona_id);

    try {
      $expediente = Expediente::find($expediente_id);
      $expedientetipo = $expediente->expedientetipo;

      //control por titular sin otros documentos relacionados
      if ($expedientetipo->control_cuil == 1) {
        $tipovinculo = Organismostiposvinculo::find($tipo_vinculo);
        if ($tipovinculo->titular == 1) {
          $exptitular = DB::table('expedientes')
            ->select('expedientes.id')
            ->join('expediente_persona', 'expediente_persona.expediente_id', '=', 'expedientes.id')
            ->where('expedientes.organismos_id', Auth::user()->userorganismo->first()->organismos_id)
            ->where('expedientes.id', $expediente_id)
            ->where('expediente_persona.organismostiposvinculo_id', $tipo_vinculo)
            ->count();

          $documentocuil = ExpedientetipoPersona::where('personas_id', $persona_id)->count();

          if ($exptitular > 0) {
            return response()->json('Este expediente ya tiene una persona vinculada como titular.');
          }
          if ($documentocuil > 0) {
            return response()->json('No se puede vincular esta persona ya que está vinculada como titular en otro expediente de este tipo.');
          }
        }
      }
      
      if ($expediente->personas()->find($persona_id)) {
        return response()->json('Esta persona ya se encuentra vinculada al expediente.');
      } else {
        // $expediente->personas()->attach($persona_id);
        $expediente->personas()->attach($persona_id, ['organismostiposvinculo_id' => $tipo_vinculo]);
      }
      

      $textoLog = Auth::user()->name . " vinculó a persona con id n°" .  $persona->documento . " a las " . Carbon::now()->toTimeString();
      historial_doc(($expediente->id), $textoLog);

      return response()->json(1);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));

      return response()->json('Error al tratar de vincular. Intente nuevamente más tarde.');
    }
  }

  /**
   * vincular
   * Establece una relacion en la table intermedia entre Persona y Expediente
   * @param  mixed $request
   * @return void
   */
  public function vincular(Request $request)
  {
    $expediente_id = $request->get('expediente_id');
    $persona_id = $request->get('persona_id');
    $tipo_vinculo = $request->get('tipo_vinculo');
    $persona = Persona::find($persona_id);

    try {
      $expediente = Expediente::find($expediente_id);
     
      if ($expediente->personas()->find($persona_id)) {
        return response()->json('Esta persona ya se encuentra vinculada al expediente.');
      } else {
        // $expediente->personas()->attach($persona_id);
        $expediente->personas()->attach($persona_id, ['organismostiposvinculo_id' => $tipo_vinculo]);
      }
      

      $textoLog = Auth::user()->name . " vinculó a persona con id n°" .  $persona->documento . " a las " . Carbon::now()->toTimeString();
      historial_doc(($expediente->id), $textoLog);

      return response()->json(1);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));

      return response()->json('Error al tratar de vincular. Intente nuevamente más tarde.');
    }
  }

  function desvincularips(Request $request) {
    $expediente_id = $request->get('expediente_id');
    $persona_id = $request->get('persona_id');
    $persona = Persona::find($persona_id);
    try {
      $expediente = Expediente::find($expediente_id);

      if ($expediente->expedientetipo->control_cuil == 1) {
        $titular = $expediente->personasVinculo->filter(function ($item) {
            return $item->titular==1;
        })->first();
        if ($titular != null) {
          if ($titular->pivot->persona_id == $persona_id) {
            return response()->json('No es posible desvincular al Titular del expediente.');
          }
        }
      }

      if ($expediente->personas()->find($persona_id)) {
        $expediente->personas()->detach($persona_id);

        $textoLog = Auth::user()->name . " desvinculó a persona con id n°" .  $persona->documento . " a las " . Carbon::now()->toTimeString();
        historial_doc(($expediente->id), $textoLog);
      } else {
        return response()->json('Esta persona no se encuentra vinculada al expediente.');
      }

      return response()->json(1);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));

      return response()->json('Error al tratar de vincular. Intente nuevamente más tarde.');
    }
  }

  public function desvincular(Request $request)
  {
    $expediente_id = $request->get('expediente_id');
    $persona_id = $request->get('persona_id');
    $persona = Persona::find($persona_id);
    try {
      $expediente = Expediente::find($expediente_id);
      if ($expediente->personas()->find($persona_id)) {
        $expediente->personas()->detach($persona_id);

        $textoLog = Auth::user()->name . " desvinculó a persona con id n°" .  $persona->documento . " a las " . Carbon::now()->toTimeString();
        historial_doc(($expediente->id), $textoLog);
      } else {
        return response()->json('Esta persona no se encuentra vinculada al expediente.');
      }

      return response()->json(1);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));

      return response()->json('Error al tratar de vincular. Intente nuevamente más tarde.');
    }
  }

  public function create($expediente_id)
  {
    $title = "Formulario Nueva Persona";

    $expediente_id = base64_decode($expediente_id);
    $expediente = Expediente::findOrFail($expediente_id);

    return view('personas.create', ['title' => $title, 'expediente' => $expediente]);
  }

  public function store(Request $request)
  {
    try {
      if ($request->expediente_id != null) {
        $id64 = base64_encode($request->expediente_id);
      }
      $messages = [
        'persona_id.required' => 'Debe ingresar el DNI de la persona.',
        'required' => 'El campo de :attribute debe ser ingresado.',
      ];

      $validator = Validator::make($request->all(), [
        'persona_nombre' => 'required|max:25|regex:/(^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$)+/',
        'persona_apellido' => 'required|max:25|regex:/(^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$)+/',
        'persona_id' => 'required|max:8',
        // 'persona_cuil' => 'required',
        'persona_localidad' => 'required',
        'persona_direccion' => 'required',
        // 'persona_correo'=> 'required|email|max:254',
        'persona_provincia' => 'required',
        'persona_fecha' => 'required|date|before:today',

      ], $messages);

      $errors = [];
      if ($validator->fails()) {
        foreach ($validator->messages()->getMessages() as $field_name => $messages) {
          foreach ($messages as $message) {
            array_push($errors, $message);
          }
        }
      }

      // En esta seccion se permite cargar una persona con el mismo DNI siempre y cuando el sexo sea distinto.
      if (NULL !== $request->get('sexo1') && NULL == $request->get('sexo2')) {
        $personaExiste = Persona::where('organismos_id', Auth::user()->userorganismo->first()->organismos_id)
                                ->where('persona_id', $request->get('persona_id'))
                                ->where('sexo', $request->get('sexo1'))
                                ->first();
        if ($personaExiste != null) {
          array_push($errors, 'Ya existe una persona cargada con ese DNI y sexo.');
        }  
      }
      else if (NULL !== $request->get('sexo2') && NULL == $request->get('sexo1')) {
        $personaExiste = Persona::where('organismos_id', Auth::user()->userorganismo->first()->organismos_id)
                                ->where('persona_id', $request->get('persona_id'))
                                ->where('sexo', $request->get('sexo2'))
                                ->first();
        if ($personaExiste != null) {
          array_push($errors, 'Ya existe una persona cargada con ese DNI y sexo.');
        }  
      }

      // $personaExiste = Persona::where('organismos_id', Auth::user()->userorganismo->first()->organismos_id)->where('persona_id', $request->get('persona_id'))->first();
      // if ($personaExiste != null) {
      //   array_push($errors, 'Ya existe una persona cargada con ese DNI.');
      // }

      if (null == $request->get('sexo1') and null == $request->get('sexo2')) {
        array_push($errors, "No selecciono ningun sexo para la Persona");
      } elseif (null !== $request->get('sexo1') and null !== $request->get('sexo2')) {
        array_push($errors, "Selecciono ambos sexos para la Persona");
      };

      if (null !== $request->get('vive1') and null !== $request->get('vive2')) {
        array_push($errors, "Selecciono ambos valores del campo Vive");
      }

      if (count($errors) > 0) {
        if ($request->expediente_id != null) {
          // si envia expediente_id significa que esta creando la persona y vinculando al documento 
          return redirect()->route('personas.create',  $id64)->with('errors', $errors)->withInput();
          die;
        } else {
          // no se envio el expediente_id solo se crea la persona sin vinculo con el documento
          // retornar los errores en json 
          return response()->json(['mesagge' => $errors, 'response' => 1]);
          die;
        }
      }

      // Guardar los datos de un persona
      $persona = new Persona;
      $persona->persona_id = $request->get('persona_id');
      $persona->nombre = $request->get('persona_nombre');
      $persona->apellido = strtoupper($request->get('persona_apellido'));
      $persona->documento = $request->get('persona_id');
      $persona->telefono = $request->get('persona_telefono');
      $persona->tipo = "fisica";
      if (null != $request->get('sexo1')) {
        $persona->sexo = $request->get('sexo1');
      } else {
        $persona->sexo = $request->get('sexo2');
      }
      $persona->cuil = $request->get('persona_cuil');
      $persona->direccion = $request->get('persona_direccion');
      $persona->fecha_nacimiento = $request->get('persona_fecha');
      $persona->correo = $request->get('persona_correo');
      $persona->localidad = strtoupper($request->get('persona_localidad'));
      $persona->provincia = strtoupper($request->get('persona_provincia'));
      $persona->organismos_id = Auth::user()->userorganismo->first()->organismos_id;
      if (null != $request->get('vive1')) {
        $persona->vive = $request->get('vive1');
      } else {
        $persona->vive = $request->get('vive2');
      }
      $persona->estado_civil = $request->get('persona_estadocivil');


      $persona->save();

      $textoLog = "Agregó persona DNI " . $persona->documento . " - " . $persona->apellido;
      Logg::info($textoLog);

      if ($request->expediente_id != null) {
        $id64 = base64_encode($request->expediente_id);
        return redirect()->route('personas.index',  $id64);
      } else {
        // return response()->json(['response' => 2]); // codigo original
        $newPersonaDoc = Persona::all();
        $newPersona = $newPersonaDoc->last();
        return response()->json(['response' => 2, 'newPersona' => $newPersona], 201);
      }
    } catch (\Throwable $th) {
      Logg::error($th->getMessage(), ("Archivo:" . pathinfo($th->getFile())['basename'] . " - Línea:" . $th->getLine()));
      if ($request->expediente_id != null) {
        return redirect()->route('personas.create',  $id64)->withInput();
      } else {
        return response()->json(['response' => 3]);
      }
    }
  }

  public function edit($id, $id_persona)
  {
    $expediente_id = base64_decode($id);
    $expediente = Expediente::findOrFail($expediente_id);
    $persona = Persona::where('organismos_id', Auth::user()->userorganismo->first()->organismos_id)->where('id', $id_persona)->first();
    $title = "Editar datos de la persona " . $persona->apellido . " " . $persona->nombre;

    return view('personas.edit', ['title' => $title, 'expediente' => $expediente, 'persona' => $persona]);
  }

  public function update(Request $request)
  {
    $persona = Persona::where('organismos_id', Auth::user()->userorganismo->first()->organismos_id)->where('id', $request->get('id'))->first();

    $messages = [
      'persona_id.required' => 'Debe ingresar el DNI de la persona.',
      'required' => 'El campo de :attribute debe ser ingresado.',
    ];

    $validator = Validator::make($request->all(), [
      'persona_id' => 'required|max:8',
      'persona_nombre' => 'required|max:25|regex:/(^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$)+/',
      'persona_apellido' => 'required|max:25|regex:/(^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$)+/',
      'persona_localidad' => 'required',
      'persona_direccion' => 'required',
      // 'persona_correo'=> 'required|email|max:254',
      'persona_provincia' => 'required',
      'persona_fecha' => 'required|date|before:today',

    ], $messages);
    $id64 = base64_encode($request->expediente_id);

    if ($validator->fails()) {
      foreach ($validator->messages()->getMessages() as $field_name => $messages) {
        foreach ($messages as $message) {
          $errors[] = $message;
        }
      }
      return redirect('persona/edit/' . $id64 . '/' . $persona->id)->with('errors', $errors)->withInput();
      //return redirect()->back()->with('errors', $errors)->withInput();
      die;
    }


    if (null == $request->get('sexo1') and null == $request->get('sexo2')) {
      $errors[0] = "No selecciono ningun sexo para la Persona";
      return redirect('persona/edit/' . $id64 . '/' . $persona->id)->with('errors', $errors)->withInput();
      die;
    };
    if (null !== $request->get('sexo1') and null !== $request->get('sexo2')) {
      $errors[0] = "Selecciono ambos sexos para la Persona";
      return redirect('persona/edit/' . $id64 . '/' . $persona->id)->with('errors', $errors)->withInput();
      die;
    };
    if (null !== $request->get('vive1') and null !== $request->get('vive2')) {
      $errors[0] = "Selecciono ambos valores del campo Vive";
      return redirect('persona/edit/' . $id64 . '/' . $persona->id)->with('errors', $errors)->withInput();
      die;
    };

    // Guardar los datos de un persona

    $persona->persona_id = $request->get('persona_id');
    $persona->nombre = $request->get('persona_nombre');
    $persona->apellido = strtoupper($request->get('persona_apellido'));
    $persona->documento = $request->get('persona_id');
    $persona->telefono = $request->get('persona_telefono');
    $persona->tipo = "fisica";
    if (null != $request->get('sexo1')) {
      $persona->sexo = $request->get('sexo1');
    } else {
      $persona->sexo = $request->get('sexo2');
    }
    $persona->cuil = $request->get('persona_cuil');
    $persona->direccion = $request->get('persona_direccion');
    $persona->fecha_nacimiento = $request->get('persona_fecha');
    $persona->correo = $request->get('persona_correo');
    $persona->localidad = strtoupper($request->get('persona_localidad'));
    $persona->provincia = strtoupper($request->get('persona_provincia'));
    $persona->organismos_id = Auth::user()->userorganismo->first()->organismos_id;
    if (null != $request->get('vive1')) {
      $persona->vive = $request->get('vive1');
    } else {
      $persona->vive = $request->get('vive2');
    }
    $persona->estado_civil = $request->get('persona_estadocivil');

    $persona->save();

    $textoLog = "Editó persona DNI " . $persona->documento . " - " . $persona->apellido;
    Logg::info($textoLog);
    return redirect()->route('personas.index', base64_encode($request->get('expediente_id')));
  }

  public function show($id, $id_persona)
  {
    $expediente_id = base64_decode($id);
    $expediente = Expediente::findOrFail($expediente_id);
    $persona = Persona::where('id', $id_persona)->first();
    $title = "Datos de la persona " . $persona->apellido . " " . $persona->nombre;

    return view('personas.show', ['title' => $title, 'expediente' => $expediente, 'persona' => $persona]);
  }

  public function docCreate()
  {
    $title = "Agregar Persona";
    return view('personas.docCreate', ['title' => $title]);
  }

  public function docSearch(Request $request)
  {
    $token = str_replace('"', '', session('token_go'));

    // Verificar estado del servicio de RENAPER antes de realizar la consulta por los datos de la persona
    try
    {
      $endpoint_estadorenaper = ENV("ESTADO_RENAPER");

      $client2 = new Client();
      $respuesta_estado = $client2->request(
        'GET',
        $endpoint_estadorenaper,
        [
          'headers' =>  [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
          ],
          'query' => [
            null
          ]
        ]
      );
      $respuesta_estado = json_decode($respuesta_estado->getBody(), true);
    }
    catch (ClientException $e)
    {
      // Si el servicio no está funcionando, se redirecciona a la vista que permite ingresar manualmente los datos. Esto evita que arroje un error 501 Bad request
      return response()->json(['estado' => 'mantenimiento']);
    }

    // RENAPER
    $endpoint = ENV("RENAPER_ENDPOINT");

    $client = new Client();
    $intentos = 0;
    while ($intentos <= 3) {
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
              'dni' => $request->documento,
              'sexo' => $request->sexo
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

    try {
      if (isset($respuesta)) {

        // $dataObject = $respuesta['persona'];
        // $persona = $dataObject[0];
        // $domicilio = $persona['domicilio'];
        $persona = $respuesta['data'][0];
        $domicilio = $persona['domicilio'];

        if ($respuesta['status'] == false) {

          return response()->json(['estado' => false]);
        } else {

          return response()->json(['persona' => $persona, 'domicilio' => $domicilio, 'estado' => $respuesta['status']]);
        }
      } else {
        return response()->json(['estado' => false]);
      }
    } catch (ClientException $e) {

      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return response()->json(['estado' => false]);
    }
  }

  public function searchPersona(Request $request)
  {

    // ademas del nombre del usuario tiene que buscar por id del organismos 
    $documento = $request->documento;
    // se obtiene el sexo de la persona para realizar la busqueda
    if ($request->sexo !== NULL) {
      $sexo = $request->sexo;
    } else {
      $sexo = "vacio";
    }
    // buscar solo en los usuariso del organismo
    $organismo =  Auth::user()->userorganismo->first()->organismos_id;

    // Debe buscar usuarios por organismos (toma el id del usuario vinculado al organismo)
    $datos = DB::table('personas')
      ->where("organismos_id", "=", $organismo)
      ->where("documento", "=", $documento)
      ->where("sexo", "=", $sexo)
      ->get();

    if (count($datos) > 0) {
      return response()->json(['estado' => false, 'sexo' => $sexo]);
    } else {
      return response()->json(['estado' => true, 'sexo' => $sexo]);
    }
  }


  public function autoComplete(Request $request)
  {

    $term = $request->term;
    $organismo =  Auth::user()->userorganismo->first()->organismos_id;

    $datos = DB::table('personas')
      ->where("organismos_id", "=", $organismo)
      ->where(function ($query) use ($term) {
        $expresion = "'%$term%'";
        return  $query->orWhereRaw('nombre like ' . $expresion . ' or apellido like ' . $expresion . ' or documento like ' . $expresion);
      })
      ->get();

    if (count($datos) > 0) {
      foreach ($datos as $key => $dato) {
        $data[] = array('id' => $dato->id, 'text' =>  $dato->nombre . ' ' . $dato->apellido);
      }
      return json_encode($data);
    }

    $data[] = [];
    return  json_encode($data);
  }

  public function tiposVinculo($id)
  {
    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $organismo = Organismo::find($id);
    $tiposvinculo = Organismostiposvinculo::where('organismos_id', $organismo->id)->get();
    // dd($tiposvinculo);

    $title = "Tipos de vinculo a personas del organismo: " . $organismo->organismo;

    return view('personas.tiposvinculo', ['title' => $title, 'organismo' => $organismo, 'tiposvinculo' => $tiposvinculo]);
  }

  public function createTiposVinculo($id)
  {
    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $organismo = Organismo::find($id);

    $title = "Nuevo Tipo de vinculo a personas del organismo: " . $organismo->organismo;

    return view('personas.tiposvinculoCreate', ['title' => $title, 'organismo' => $organismo]);
  }

  public function storeTiposVinculo(Request $request)
  {
    $activo = 1;
    if ($request->activo == "") {
      $activo = 0;
    }

    $titular = 0;
    $titularexist = Organismostiposvinculo::where('titular', 1)->where('organismos_id', $request->organismos_id)->first();
    if (isset($request->titular) or $request->titular != "") {
      if ($titularexist != null) {
        $errors[0] = 'Ya existe un vínculo de tipo "Titular" en el organismo.';
        return redirect()->back()->with('errors', $errors)->withInput();
        die;
      } else {
        $titular = 1;
      }      
    }

    $existvinculo = Organismostiposvinculo::where(DB::raw('lower(vinculo)'), '=', strtolower($request->tipovinculo))->count();
    if ($existvinculo > 0) {
      $errors[0] = 'El nombre del vínculo ya se encuentra utilizado.';
      return redirect()->back()->with('errors', $errors)->withInput();
      die;
    }

    $validator = Validator::make($request->all(), [
      'tipovinculo' => 'required|max:45',
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

    $tiposvinculo = new Organismostiposvinculo;
    $tiposvinculo->organismos_id = $request->organismos_id;
    $tiposvinculo->vinculo = $request->tipovinculo;
    $tiposvinculo->activo = $activo;
    $tiposvinculo->titular = $titular;
    $tiposvinculo->save();

    return redirect('/organismos/' . $request->organismos_id . '/tiposvinculo');
  }

  public function editTiposVinculo($id, $tipos_id)
  {
    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $organismo = Organismo::find($id);
    $tiposvinculo = Organismostiposvinculo::find($tipos_id);

    $title = "Editar Tipo de vinculo a personas del organismo: " . $organismo->organismo;

    return view('personas.tiposvinculoEdit', ['organismo' => $organismo, 'tiposvinculo' => $tiposvinculo, 'title' => $title]);
  }

  public function updateTiposVinculo(Request $request)
  {
    $tiposvinculo = Organismostiposvinculo::find($request->tipos_id);

    $validator = Validator::make($request->all(), [
      'tipovinculo' => 'required|max:45',
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

    $titular = 0;
    $exist = Organismostiposvinculo::where('titular', 1)->where('organismos_id', $request->organismos_id)->first();

    if (isset($request->titular) or $request->titular != "") {
      if ($exist != null) {
        $errors[0] = 'Ya existe un vínculo de tipo "Titular" en el organismo.';
        return redirect()->back()->with('errors', $errors)->withInput();
        die;
      } else {
        $titular = 1;
      }
    }

    if ($exist != null && $exist->vinculo == $request->tipovinculo) {
      $errors[0] = 'El nombre del vínculo no puede ser el mismo que el marcado como tipo "Titular".';
      return redirect()->back()->with('errors', $errors)->withInput();
      die;
    }

    $tiposvinculo->organismos_id = $request->organismos_id;
    $tiposvinculo->vinculo = $request->tipovinculo;
    $tiposvinculo->updated_at = Carbon::now();
    $tiposvinculo->titular = $titular;

    $tiposvinculo->save();

    return redirect('/organismos/' . $request->organismos_id . '/tiposvinculo');
  }

  public function updateEstadoTiposVinculo(Request $request)
  {
    $tiposvinculo = Organismostiposvinculo::find($request->vinculoId);

    try {
      if ($tiposvinculo->activo == 1) {
        $estado = false;
      } else {
        $estado = true;
      }
      $activo = $estado;

      $tiposvinculo->activo = $activo;
      $tiposvinculo->save();

      return response()->json(['1']);
    } catch (\Throwable $th) {
      return response()->json(['2']);
    }
  }

  // Esta funcion permite cargar los datos de la persona en el select si al momento de ingresar al formulario para cargar una persona nueva, ésta existe
  // en la base de datos.
  public function cargar(Request $request)
  {
    $persona_dni = $request->input_documento;

    try {

      $organismo =  Auth::user()->userorganismo->first()->organismos_id;

      $persona = Persona::where('documento', $persona_dni)
                          ->where('organismos_id', $organismo)
                          ->first();

      if ($persona !== NULL) {
        return response()->json(['response' => '1',
                                  'persona' => $persona]);
      } else {
        return response()->json(['response' => '2']);
      }

    } catch (\Throwable $th) {
      return response()->json(['response' => '3']);
    }
  }

  public function personadocumentos(Request $request)
  {
    $id = $request->personaid;
    $datos = DB::table('expedientes')
    ->select(
      'expedientes.id as id',
      'expedientes.expediente as expediente',
      'expedientestipos.expedientestipo as tipoexpediente',
      'organismostiposvinculo.vinculo as vinculopersona'
    )
    ->join('expedientestipos', 'expedientes.expedientestipos_id', '=', 'expedientestipos.id')
    ->join('expediente_persona', 'expediente_persona.expediente_id', '=', 'expedientes.id')
    ->join('personas', 'expediente_persona.persona_id', '=', 'personas.id')
    ->join('organismostiposvinculo', 'expediente_persona.organismostiposvinculo_id', '=', 'organismostiposvinculo.id')
    ->where('personas.id', $id)
    ->get();
    
    $adevol = array();
    if (count($datos) > 0) {
      foreach ($datos as $dato) {
        $adevol[] = array(
          'id' => $dato->id,
          'value' => getExpedienteName(Expediente::find($dato->id)). ' '. $dato->expediente .' - Tipo: '. $dato->tipoexpediente .' - Vinculo: '. $dato->vinculopersona,
        );
      }
    } else {
      $adevol[] = array(
        'id' => 0,
        'value' => 'no hay documentos vinculados a esta persona.'
      );
    }

    return json_encode($adevol);
  }
}
