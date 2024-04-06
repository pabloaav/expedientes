<?php

namespace App\Http\Controllers;

use PDF;
// reference the Dompdf namespace
use Dompdf\Dompdf;

use App\Foja;
use Exception;
use App\Organismo;
use App\Plantilla;
use Carbon\Carbon;
use App\Expediente;
use App\User;
use App\Expedienteestado;
use App\Organismossector;
use App\Organismosetiqueta;
use Illuminate\Http\Request;
use App\Traits\FileHandlingTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Caffeinated\Shinobi\Facades\Shinobi;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Access\AuthorizationException;
use App\Logg;

class PlantillasController extends Controller
{
  use FileHandlingTrait;

  public function index($id)
  {

    //   if (!session('permission')->contains('organismos.index.admin')) {
    //     session(['status' => 'No tiene acceso para ingresar a este modulo']);
    //     return redirect()->route('index.home');
    //   }
    $plantillas = Plantilla::where("organismossectors_id", $id)->get();
    $sector = Organismossector::find($id);
    $organismo = Organismo::where('id', $sector->organismos_id)->first();
    $title = "Gestionar plantillas de " . $sector->organismossector;
    return view('plantillas.index', ['title' => $title, 'plantillas' => $plantillas, 'id' => $id,  'sector' => $sector,  'organismo' => $organismo]);
  }

  public function show($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $plantilla = Plantilla::find($id);

    $pdf = \App::make('dompdf.wrapper');
    $pdf->loadHTML($plantilla->contenido);
    return $pdf->stream();
  }

  public function create($id)
  {
    //Se comenta verificacion de permiso para que el rol Crear Plantilla pueda acceder a la funcion

    // if (!session('permission')->contains('organismos.index.admin')) {
    //   session(['status' => 'No tiene acceso para ingresar a este modulo']);
    //   return redirect()->route('index.home');
    // }

    if (DB::table('organismosusers')->where('users_id', Auth::user()->id)->exists()) {
    } else {
      return  redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' no tiene una organización no puede agregar plantillas');
    }

    if (DB::table('organismossectorsusers')->where('users_id', Auth::user()->id)->exists()) {
    } else {
      return redirect()->back()->with('error', 'Usuario ' . Auth::user()->name . ' no tiene un sector asignado no puede agregar plantillas');
    }

    $organismosector_id = Organismossector::find($id);

    $title = "Nueva plantilla para sector " . $organismosector_id->organismossector;
    return view('plantillas.create', ['title' => $title, 'organismosector_id' => $organismosector_id]);
  }

  public function store(Request $request)
  {
    DB::beginTransaction();
    try {
      //Se comenta verificacion de permiso para que el rol Crear Plantilla pueda acceder a la funcion

      // if (!session('permission')->contains('organismos.index.admin')) {
      //   session(['status' => 'No tiene acceso para ingresar a este modulo']);
      //   return redirect()->route('index.home');
      // }

      $validator = Validator::make($request->all(), [
        'plantilla' => 'required|max:100|min:2',
        'contenido' => 'required',
      ]);

      if ($validator->fails()) {
        foreach ($validator->messages()->getMessages() as $field_name => $messages) {
          foreach ($messages as $message) {
            $errors[] = $message;
          }
        }
        return response()->json(['response' => 2, 'error' => $errors]);
        //return redirect()->back()->with('errors', $errors)->withInput();
        die;
      }
      $sector_id = $request->organismossectors_id;

      $plantilla = new Plantilla;
      $plantilla->plantilla = $request->plantilla;
      $plantilla->contenido = $request->contenido;
      $plantilla->activo = 1;
      $plantilla->organismossectors_id =  $sector_id;

      $plantillaGlobal = 0 ;
    if ($request->plantillaGlobal != null) {
      $plantilla->global = 1 ;
    }
      $plantilla->save();

      $textoLog = "Creó plantilla " .  $plantilla->plantilla;
      Logg::info($textoLog);

      DB::commit();
      return response()->json(1);
      //return redirect()->route('expediente.plantillas', [ $sector_id])->with('success','Plantilla creada correctamente');


    } catch (Exception $e) {
      DB::rollback();
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));

      //return  $e->getMessage();
      $errors[0] = "Error con los datos enviados.";
      return response()->json(['response' => 2, 'error' => $e->getMessage()]);
      //return redirect()->back()->with('errors',  $errors)->withInput();
      die;
    }
  }

  public function edit($id, $idsector)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $idsector = base64_decode($idsector);
    $plantilla = Plantilla::find($id);
    $title = "Editar plantilla: " . $plantilla->plantilla;
    return view('plantillas.edit', ['title' => $title, 'plantilla' => $plantilla, 'idsector' => $idsector]);
  }


  public function update(Request $request)
  {
    DB::beginTransaction();
    try {
      if (!session('permission')->contains('organismos.index.admin')) {
        session(['status' => 'No tiene acceso para ingresar a este modulo']);
        return redirect()->route('index.home');
      }

      $validator = Validator::make($request->all(), [
        'plantilla' => 'required|max:100|min:5',
        'contenido' => 'required|min:50',
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

      $plantilla = Plantilla::find($request->id);
      $plantilla->plantilla = $request->plantilla;
      $plantilla->contenido = $request->contenido;
      $plantillaGlobal = 0 ;
      if ($request->plantillaGlobal != null) {
        $plantilla->global = 1 ;
      }
      else{
        $plantilla->global = 0;
      }
      $plantilla->update();

      $textoLog = "Modificó plantilla " . $plantilla->plantilla;
      Logg::info($textoLog);


      DB::commit();
      return redirect('/plantillas/' .  $request->sector_id . '/organismosector')->with('success', 'Plantilla modificada correctamente');
    } catch (Exception $e) {
      DB::rollback();
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));

      //return  $e->getMessage();
      $errors[0] = "Error con los datos enviados. Puede ser problemas con los caractéres de la plantilla";
      return redirect()->back()->with('errors',  $errors)->withInput();
      die;
    }
  }

  public function estado($id)
  {

    // if (!session('permission')->contains('noticias.index')) {
    //    session(['status' => 'No tiene acceso para ingresar a este modulo']);
    //    return redirect()->route('index.home');
    // }

    $plantilla = Plantilla::find($id);

    if ($plantilla->activo) {
      $plantilla->activo = false;
    } else {
      $plantilla->activo = true;
    }
    $plantilla->save();
    $textoLog = "Cambió estado plantilla " .  $plantilla->plantilla;
    Logg::info($textoLog);


    return redirect()->back();
  }

  public function fojas($id, $idexpediente)
  {

    // if (!session('permission')->contains('organismos.index.admin')) {
    //   session(['status' => 'No tiene acceso para ingresar a este modulo']);
    //   return redirect()->route('index.home');
    // }
    $expediente = Expediente::find($idexpediente);
    $plantilla = Plantilla::find($id);
    $etiquetas = Organismosetiqueta::where("organismos_id", $expediente->organismos_id)->where('activo', 1)->get();

    if (session('permission')->contains('expediente.etiqueta.sector')) {
      $sectoresUsuario = Auth::user()->usersector->pluck('organismossectors_id')->toArray(); // etiquetas solo de los sectores que pertenece el usuario (PRUEBA)
      $global = NULL;

      $etiquetasPorSector = $etiquetas->filter(function ($etiqueta) use ($sectoresUsuario) {
        return in_array($etiqueta->organismossectors_id, $sectoresUsuario);
      });

      $etiquetasGlobales = $etiquetas->filter(function ($etiqueta) use ($global) {
        return $etiqueta->organismossectors_id == $global;
      });

      $etiquetas = $etiquetasPorSector->concat($etiquetasGlobales);
    }

    // dd($plantilla);
    $title = "Crear foja con plantilla " . $plantilla->plantilla;
    return view('plantillas.foja_plantilla_store', ['plantilla' => $plantilla, 'title' => $title, 'expediente' => $expediente, 'etiquetas' => $etiquetas]);
  }

  public function storeFoja(Request $request)
  {

    DB::beginTransaction();
    try {

      if (!session('permission')->contains('organismos.index.admin')) {
        session(['status' => 'No tiene acceso para ingresar a este modulo']);
        return redirect()->route('index.home');
      }

      $expediente = Expediente::find($request->expedientes_id);
      $expedienteestado = $expediente->expedientesestados->last();
      $expedientesector = $expedienteestado->rutasector->sector->id;

      // Autorizacion
      try {
        $this->authorize('agregar_foja', $expediente);
      } catch (\Exception $exception) {
        Logg::error($exception->getMessage(), ("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()));

        if ($exception instanceof AuthorizationException) {
          return redirect()->route('expedientes.index')->with('errors', ["No posee los permisos para realizar esta acción"]);
        }
      }


      $validator = Validator::make($request->all(), [
        'expedientes_id' => 'required',
        'contenido' => 'required',
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

      // calcular el numero de foja actual a crear
      $nextFojaNumber = $expediente->fojas->count() + 1;

      $nuevaFoja = new Foja([
        'expedientes_id' => $request->expedientes_id,
        // 'foja' => $request->expedientes_id . $nextFojaNumber,
        'tipofoja' => "texto",
        'texto' => $request->contenido,
        'file' => null,
        'numero' => $nextFojaNumber,
        'users_id' => Auth::user()->id,
        'organismossectors_id' => $expedientesector,
        'created_at' => new Carbon,
      ]);

      if ($request->has('descripcion')) {
        $nuevaFoja->descripcion = $request->get('descripcion');
      }


      // obtener la ruta actual para elnuevo estado
      $ruta_actual = $expediente->expedientesestados->last()->expedientesrutas_id;

      $estadoexpediente = new Expedienteestado;
      $estadoexpediente->expedientes_id = $expediente->id;
      $estadoexpediente->users_id = Auth::user()->id;
      $estadoexpediente->expendientesestado = 'procesando';
      $estadoexpediente->expedientesrutas_id =  $ruta_actual;
      $textoLog = "El usuario " . Auth::user()->name . " agregó una nueva foja " .  $nuevaFoja->foja  . " a las " . Carbon::now()->toTimeString();
      Logg::info($textoLog);
      $estadoexpediente->observacion = $textoLog;
      $estadoexpediente->ruta_devolver = $expedienteestado->ruta_devolver;
      $estadoexpediente->save();

      // guardar la foja en la carpeta del expediente
      $carpetaRaizOrganismo = $expediente->organismos->id . '/' . $expediente->id;
      $nombreFoja = "foja_" . $nextFojaNumber . ".pdf";

      // guardar el nombre del archivo foja en el campo de base de datos de esa foja
      $nuevaFoja->nombre = $nombreFoja;

      $sePudoGuardar =  $this->guardarFojaPdf($nuevaFoja->texto, $carpetaRaizOrganismo, $nombreFoja);

      if ($sePudoGuardar) {
        // El path donde esta la foja en pdf por el momento:
        $path = Storage::disk('local')->path($carpetaRaizOrganismo . '/' . $nombreFoja);

        // La foja de imagen se guarda ya dentro de la funcion singlePdfToImage() del Trait. Se guarda en el mismo lugar donde esta la foja en formato pdf:
        $fojaImagen = $this->singlePdfToImage($path, $nombreFoja);

        // Ahora el nuevo path cambia porque cambia el nombre del archivo foja de tipo imagen:
        $absolute_new_path = Storage::disk('local')->path($carpetaRaizOrganismo . '/' . $fojaImagen);

        $relative_new_path = $carpetaRaizOrganismo . '/' . $fojaImagen;

        // guardamos el hash de la foja previa (hashPrevio) en la foja actual
        // para eso traemos la coleccion de fojas de este expediente, y obtenemos la anterior a la actual foja:
        $fojaAnterior = $expediente->fojas->last();
        $nuevaFoja->hashPrevio = $fojaAnterior->hash;

        // Generar un nuevo hash para la foja actual
        $nuevaFoja->hash =  $this->generarHashSHA256($expediente->makeHashFromFile($absolute_new_path), $nuevaFoja->hashPrevio, $nuevaFoja->created_at);
        $nuevaFoja->path = $relative_new_path;
        $nuevaFoja->updated_at = $nuevaFoja->created_at;

        // La foja tiene un nuevo nombre que es el del archivo tipo imagen de la misma
        $nuevaFoja->nombre = $fojaImagen;
        // Guardar la foja en el server de imagenes
        try {
          // recuperar contenido de la imagen foja
          $imageContent = Storage::disk('local')->get($relative_new_path);
          // guardar en la ruta correspondiente la foja imagene en el servidor de imagenes
          if (Storage::disk('minio')->put($relative_new_path, $imageContent)) {
            // Si el server de imagenes responde true borramos la foja en el stogare local
            // Se elimina la foja en formato pdf del storage
            $res1 =  unlink($path);
            // Se elimina la foja en formato imagen del storage
            $res2 =  unlink($absolute_new_path);
          }
        } catch (\Exception $e) {
          Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));

          // Si hay un error de conexion o escritura en el server remoto devolvemos error
          DB::rollback();
          //return  $e->getMessage();
          return response()->json(['mesagge' => "Error con la conexion con el servidor", 'response' => 2]);
        }
        $nuevaFoja->save();

        if ($request->get('tagsPlantiila')) {
          $tags = $request->get('tagsPlantiila');

          for ($i = 0; $i < count($tags); $i++) {
            // $expediente->organismosetiquetas()->attach([$tags[$i]]);
            $nuevaFoja->organismosetiquetas()->syncWithoutDetaching([$tags[$i]]);
          }
        }
      }

      DB::commit();
      return response()->json(['response' => 1, 'expediente' => base64_encode($expediente->id)]);
    } catch (Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));

      DB::rollback();
      //return  $e->getMessage();
      return response()->json(['mesagge' => "Error con los datos enviados. Pruebe ingresando otra información", 'response' => 2]);
    }
  }

  public function storeFojas(Request $request)
  {
    DB::beginTransaction();
    try {

      if (!session('permission')->contains('foja.crear')) {

        return response()->json(['mesagge' => "No tiene acceso para ingresar a este modulo", 'response' => 2]);
      }

      $expediente = Expediente::find($request->expedientes_id);
      $expedienteestado = $expediente->expedientesestados->last();
      $expedientesector = $expedienteestado->rutasector->sector->id;

      // Autorizacion
      $this->authorize('agregar_foja', $expediente);

      $messages = [
        'contenido.required' => 'El contenido de la foja no puede ser vacio',
      ];


      $validator = Validator::make($request->all(), [
        'expedientes_id' => 'required',
        'contenido' => 'required',
      ], $messages);

      if ($validator->fails()) {
        foreach ($validator->messages()->getMessages() as $field_name => $messages) {
          foreach ($messages as $message) {
            $errors[] = $message;
          }
        }
        return response()->json(['mesagge' => $errors, 'response' => 2]);
        die;
      }

      // La carpeta de la foja, es el id del Organismo concatenado con el id del expediente
      $carpetaRaizOrganismo = $expediente->organismos->id . DIRECTORY_SEPARATOR . $expediente->id;
      $nombreFoja = "foja.pdf";

      $dompdf = \App::make('dompdf.wrapper');
      $dompdf->loadHtml($request->contenido);

      // (Optional) Setup the paper size and orientation
      $dompdf->setPaper('a4', 'portrait');

      Storage::disk('local')->put($carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $nombreFoja, $dompdf->Output());

      // El path donde esta la foja en pdf por el momento:
      $pathFojaPdf = Storage::disk('local')->path($carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $nombreFoja);

      $files = $this->splitPdfToImages2($pathFojaPdf, $nombreFoja);

      $nextFojaNumber = $expediente->fojas->count();

      // obtener la ruta actual para el nuevo estado
      $ruta_actual = $expediente->expedientesestados->last()->expedientesrutas_id;

      // una variable para controlar el numero de iteracion actual del for de los files
      $iteracion = 0;

      // un array auxiliar para guardar las fojas de los files para tener siempre la ultima
      $ultimaFoja = [];

      //recorrer el array de archivos 
      foreach ($files as $file) {

        $iteracion += 1;
        //generar nombre de archivo unico dentro del expediente
        $file_nombre = $file->getClientOriginalName();
        $file_nombre = control_nombre($file_nombre);
        $file_extension = pathinfo($file_nombre, PATHINFO_FILENAME);
        $fileName =   $file_extension . '-' . uniqid() . '.webp';

        $pathLocal =  $carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $fileName;

        //reducir el archivo
        $imagen = Image::make($file);

        // Guardar la foja imagen codificada en formato webp en el storage local
        Storage::disk('local')->put($pathLocal, $imagen->encode('webp', 90));

        //calcular numero de pagina
        $nextFojaNumber += 1;

        // Almacenar con Modelo 
        $foja = new Foja;
        $foja->expedientes_id = $expediente->id;
        // $foja->foja = $request->expediente_id . $nextFojaNumber;
        $foja->tipofoja = "imagen";
        $foja->nombre = $fileName;
        $foja->path = $pathLocal;
        $foja->numero = $nextFojaNumber;
        $foja->users_id = Auth::user()->id;
        $foja->organismossectors_id = $expedientesector;
        $foja->created_at = new Carbon;
        $foja->updated_at = $foja->created_at;


        /* En el caso de varios archivos, no se puede hacer solo $expediente->fojas->last() 
      porque trae siempre la misma foja. Se recurre a una variable tipo array 
      para guardar la ultima foja y obtener el hash previo para calcular el hash actual. 
      Si la iteracion es la primera sirve hacer el metodo antes citado. Sino, se recurre al array temporal de fojas */
        if ($iteracion == 1) {
          $fojaAnterior = $expediente->fojas->last();
          $foja->hashPrevio = $fojaAnterior->hash;
          array_push($ultimaFoja, $foja);
        } else {
          $fojaAnterior = end($ultimaFoja);
          $foja->hashPrevio = $fojaAnterior->hash;
          array_push($ultimaFoja, $foja);
        }

        // obtener la ruta completa de la foja imagen codificada en formato webp del storage local para el hash
        $pathCompletoFoja = Storage::disk('local')->path($pathLocal);

        // Generar un nuevo hash para la foja actual
        $foja->hash =  $this->generarHashSHA256($expediente->makeHashFromFile($pathCompletoFoja), $foja->hashPrevio, $foja->created_at);

        // Guardar la foja en el server de imagenes
        try {
          // recuperar contenido de la imagen foja
          $imageContent = Storage::disk('local')->get($pathLocal);
          // guardar en la ruta correspondiente la foja imagen en el servidor de imagenes
          if (Storage::disk('minio')->put($pathLocal, $imageContent)) {
            // Si el server de imagenes responde true borramos la foja en el storage local
            // Se borra la foja en formato pdf y en formato imagen
            $res1 =  unlink($pathCompletoFoja);
            // $res2 =  unlink($pathFojaPdf); // se comenta ésta linea y se coloca fuera del commit porque genera conflictos con plantillas de mas de una pagina
          }
        } catch (\Exception $e) {
          Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
          // Si hay un error de conexion o escritura en el server remoto devolvemos error
          return redirect()->back()->with('errors', ['El servidor de fojas no esta disponible']);
        }

        // Si no hubo problemas en el storage remoto, se guarda en base de datos la foja
        $foja->save();

        //generar un nuevo estado del expediente
        $estadoexpediente = new Expedienteestado;
        $estadoexpediente->expedientes_id = $expediente->id;
        $estadoexpediente->users_id = Auth::user()->id;
        $estadoexpediente->expendientesestado = 'procesando';
        $estadoexpediente->expedientesrutas_id =  $ruta_actual;
        $textoLog = "El usuario " . Auth::user()->name . " agrego la foja " . $foja->nombre . " a las " .  Carbon::now()->toTimeString();
        Logg::info($textoLog);
        $estadoexpediente->observacion = $textoLog;
        $estadoexpediente->ruta_devolver = $expedienteestado->ruta_devolver;
        $estadoexpediente->save();

        if ($request->get('tagsPlantiila')) {
          $tags = $request->get('tagsPlantiila');

          for ($i = 0; $i < count($tags); $i++) {
            // $expediente->organismosetiquetas()->attach([$tags[$i]]);
            $foja->organismosetiquetas()->syncWithoutDetaching([$tags[$i]]);
          }
        }
      }

      DB::commit();
      $res2 =  unlink($pathFojaPdf);
      return response()->json(['expediente' => base64_encode($expediente->id), 'response' => 1]);
    } catch (Exception $exception) {
      DB::rollback();
      Logg::error($exception->getMessage(), ("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()));
      if ($exception instanceof AuthorizationException) {
        return response()->json(['mesagge' => "No tiene permisos para realizar la operación.", 'response' => 2]);
      } else {
        return response()->json(['mesagge' => "Error con los datos/operación solicitada.", 'response' => 2]);
        die;
      }
    }
  }

  public function guardarFojaPdf1($contenido)
  {
    // set style for barcode
    $style = array(
      'border' => 0,
      'vpadding' => 'auto',
      'hpadding' => 'auto',
      'fgcolor' => array(0, 0, 0),
      'bgcolor' => false, //array(255,255,255)
      'module_width' => 1, // width of a single module in points
      'module_height' => 1 // height of a single module in points
    );

    PDF::SetTitle('Documento');

    PDF::AddPage();
    $bMargin = PDF::getBreakMargin();
    $auto_page_break = PDF::getAutoPageBreak();
    PDF::SetAutoPageBreak(false, 0);
    PDF::Image('images/vacia.png', 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
    PDF::SetAutoPageBreak($auto_page_break, $bMargin);
    PDF::setPageMark();



    PDF::MultiCell(120, 5, $contenido, 0, 'L', 0, 1, 45, 30, true);


    // $expedientes_fojas = Expediente::find($expediente->id)->fojas;
    // foreach ($expedientes_fojas as $data){ 
    //   PDF::AddPage();
    //   PDF::SetAutoPageBreak(false, 0);
    //   PDF::Write(5,'A continuación mostramos las fojas ');

    //   //obtener la ruta del archivo(foja)
    //   PDF::Image(Storage::disk('public')->path($data->path), 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
    //   // dd($page->path);  

    //   } 
    PDF::Output('documento-' . 1 . '.pdf');
    die;


    // PDF::Output('vale-' . $expediente->id . '.pdf');
  }


  public function guardarFojaPdf($contenido, $ubicacion, $nombreFoja)
  {
    $pdf = \App::make('dompdf.wrapper');
    $pdf->loadHTML($contenido);
    // return $pdf->stream();
    return  Storage::disk('local')->put($ubicacion . '/' . $nombreFoja, $pdf->output());
  }

  protected function generarHashSHA256(String $hashData, $hashPrevio, $dateTimeCreated)
  {
    try {
      if ($dateTimeCreated === null) {
        $dateTimeCreated = '';
      }
      if ($hashPrevio === null) {
        $hashPrevio = '';
      }
      return hash('sha256', $hashData . $hashPrevio . $dateTimeCreated);
    } catch (\Exception $e) {
      $e->getMessage();
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
    }
  }

  public function storeBorrador(Request $request)
  {
    DB::beginTransaction();
    try {
      if ($request->plantilla_id) {
        // if(Plantilla::find($request->plantilla_id)->exists()){


        $plantilla = Plantilla::find($request->plantilla_id);
        $user = User::find(Auth::user()->id);
        $org_id = $plantilla->organismosector->id;

        $plantilla->contenido = $request->contenido;
        $plantilla->plantilla = "Borrador " .  $user->email . " - " . Carbon::now()->toTimeString();
        $plantilla->organismossectors_id = $org_id;
        $plantilla->save();
        $textoLog = "Actualizó plantilla " .  $plantilla->plantilla;
        Logg::info($textoLog);

        DB::commit();
        return response()->json([[1]]);
      } else {
        $org_id = $request->organismossectors_id;

        $user = User::find(Auth::user()->id);
        $plantilla = new Plantilla;
        $plantilla->plantilla = "Borrador " .  $user->email . " - " . Carbon::now()->toTimeString();
        $plantilla->contenido = $request->editor;
        $plantilla->activo = 1;
        $plantilla->organismossectors_id = $org_id;
        $plantilla->save();
        $textoLog = "Creó plantilla " .  $plantilla->plantilla;
        Logg::info($textoLog);


        DB::commit();
        return response()->json([[1]]);
      }
    } catch (Exception $e) {
      DB::rollback();
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return response()->json([[2]]);
    }
  }
}
