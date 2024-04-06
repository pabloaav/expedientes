<?php

namespace App\Http\Controllers;

use App\Foja;
use PDF;
use Exception;
use Carbon\Carbon;
use App\Expediente;
use App\Expedientesruta;
use App\Expedienteestado;
use App\Firmada;
use App\Organismossector;
use App\Organismosetiqueta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\FirmaRequest;
use App\Repositories\Firmador;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Caffeinated\Shinobi\Facades\Shinobi;
use Illuminate\Support\Facades\Validator;
use App\Traits\FileHandlingTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use stdClass;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use App\Logg;
use Dompdf\Dompdf;
use Illuminate\Support\Str;

class FojaController extends Controller
{
  // Este trait tiene las funciones de dividir los pdf
  use FilehandlingTrait;

  /**
   * Guardar fojas asociadas a un expediente
   */
  public function store(Request $request)
  {

    DB::beginTransaction();
    try {

      if (!session('permission')->contains('foja.crear')) {
        session(['status' => 'No tiene acceso para ingresar a este modulo']);
        return redirect()->route('index.home');
      }

      $expediente = Expediente::find($request->expedientes_id);
      $expedienteestado = $expediente->expedientesestados->last();
      $expedientesector = $expedienteestado->rutasector->sector->id;

      // Autorizacion
      $this->authorize('agregar_foja', $expediente);


      // VALIDACION DE INTEGRIDAD DE HASH

      // obtener una coleccion de las fojas de ese expediente
      /* $fojas = $expediente->fojas;
      $contador = 0;
      $fojaAlterada = "";

      for ($i = 0; $i < count($fojas); $i++) {
        $pathFoja = Storage::disk('public')->path($fojas[$i]->path);
        $hashFojaActual = $fojas[$i]->hash;
        // volvemos a generar el hash de esta foja para controlar su integridad
        $hashFojaActualRecalculado = $this->generarHashSHA256($expediente->makeHashFromFile($pathFoja), $fojas[$i]->hashPrevio, $fojas[$i]->created_at);
        if ($hashFojaActualRecalculado !== $hashFojaActual) {
          $contador += 1;
          $fojaAlterada = $fojas[$i]->nombre;
        }
      }

      for ($i = 1; $i < count($fojas); $i++) {
        if ($fojas[$i]->hashPrevio !== $fojas[$i - 1]->hash) {
          $contador += 1;
          $fojaAlterada = $fojas[$i]->nombre;
        }
      } */
      $messages = [
        'editor.required' => 'El contenido de la foja no puede ser vacio',
        'editor.max' => 'El contenido de la foja supera el tamaño recomendado',
      ];


      $validator = Validator::make($request->all(), [
        'editor' => 'required|max:3000',
        'expedientes_id' => 'required',
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
      /*  elseif ($contador > 0) {
        return redirect()->route('expedientes.index')->with('error', "El expediente fue modificado en la foja: " . $fojaAlterada);
      } */


      // calcular el numero de foja actual a crear
      $nextFojaNumber = $expediente->fojas->count() + 1;


      if ($request->has('foja_textual')) {
        $nuevaFoja = new Foja([
          'expedientes_id' => $request->expedientes_id,
          // 'foja' => $request->expedientes_id . $nextFojaNumber,
          'tipofoja' => "texto",
          'texto' => $request->editor,
          'file' => null,
          'numero' => $nextFojaNumber,
          'users_id' => Auth::user()->id,
          'organismossectors_id' => $expedientesector,
          'created_at' => new Carbon,
        ]);
      }
      if ($request->has('descripcion')) {
        $nuevaFoja->descripcion = $request->get('descripcion');
      }
      // Guardando foja en la base de datos
      $nuevaFoja->save();

      // obtener la ruta actual para elnuevo estado
      $ruta_actual = $expediente->expedientesestados->last()->expedientesrutas_id;

      $estadoexpediente = new Expedienteestado;
      $estadoexpediente->expedientes_id = $expediente->id;
      $estadoexpediente->users_id = Auth::user()->id;
      $estadoexpediente->expendientesestado = 'procesando';
      $estadoexpediente->expedientesrutas_id =  $ruta_actual;
      $textoLog = Auth::user()->name . " agregó una nueva foja " .  $nuevaFoja->foja  . " a las " . Carbon::now()->toTimeString();
      Logg::info($textoLog);
      $estadoexpediente->observacion = $textoLog;
      $estadoexpediente->ruta_devolver = $expedienteestado->ruta_devolver;
      $estadoexpediente->save();

      // La carpeta de la foja, es el id del Organismo concatenado con el id del expediente
      $carpetaRaizOrganismo = $expediente->organismos->id . DIRECTORY_SEPARATOR . $expediente->id;
      $nombreFoja = "foja_" . $nextFojaNumber . ".pdf";

      // En este punto el nombre de la foja es con extension pdf
      $nuevaFoja->nombre = $nombreFoja;

      // la funcion guardarFojaPdf() devuelve un booleano resultado de la conversion en pdf del texto 
      $sePudoGuardar =  $this->guardarFojaPdf($nuevaFoja->texto, $carpetaRaizOrganismo, $nombreFoja);
      if ($sePudoGuardar) {
        // El path donde esta la foja en pdf por el momento:
        $path = Storage::disk('local')->path($carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $nombreFoja);

        // La foja de imagen se guarda ya dentro de la funcion singlePdfToImage() del Trait. Se guarda en el mismo lugar donde esta la foja en formato pdf:
        $fojaImagen = $this->singlePdfToImage($path, $nombreFoja);

        // Ahora el nuevo path cambia porque cambia el nombre del archivo foja de tipo imagen:
        $absolute_new_path = Storage::disk('local')->path($carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $fojaImagen);

        $relative_new_path = $carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $fojaImagen;

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
        try {
          // Requerimos un true de Minio para borrar la foja en el storage local
          $imageContent = Storage::disk('local')->get($relative_new_path);
          if (Storage::cloud()->put($relative_new_path, $imageContent)) {
            // Se elimina la foja en formato pdf del storage
            $res1 =  unlink($path);
            // Se elimina la foja en formato imagen del storage
            $res2 =  unlink($absolute_new_path);
          }
        } catch (\Exception $e) {
          Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
          return redirect()->route('expedientes.index')->with('errors', ['El servidor de fojas no esta disponible']);
        }
        $nuevaFoja->save();
      }

      DB::commit();

      return redirect()->route('expediente.show', base64_encode($expediente->id))->with('success', 'La foja se agrego correctamente');;
    } catch (Exception $exception) {
      DB::rollback();
      //Logg::error($exception->getMessage(),("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()) );
      if ($exception instanceof AuthorizationException) {
        return redirect()->route('expedientes.index')->with('errors', ["No posee los permisos para realizar esta acción"]);
      } else {
        $errors[0] = "Error con los datos enviados. Intente con otros datos.";
        return redirect()->back()->with('errors',  $errors)->withInput();
        die;
      }
    }
  }

  /**
   * Guardar las fojas que se crean a partir del editor de texto
   *
   * @param  mixed $request
   * @return void
   */
  public function storeFojas(Request $request)
  {
    DB::beginTransaction();
    try {

      if (!session('permission')->contains('foja.crear')) {
        session(['status' => 'No tiene acceso para ingresar a este modulo']);
        return redirect()->route('index.home');
      }

      $expediente = Expediente::find($request->expediente_id);
      $expedienteestado = $expediente->expedientesestados->last();
      $expedientesector = $expedienteestado->rutasector->sector->id;

      // Autorizacion
      $this->authorize('agregar_foja', $expediente);

      $messages = [
        'editor.required' => 'El contenido de la foja no puede ser vacio',

      ];


      $validator = Validator::make($request->all(), [
        'editor' => 'required'
      ], $messages);

      if ($validator->fails()) {
        foreach ($validator->messages()->getMessages() as $field_name => $messages) {
          foreach ($messages as $message) {
            $errors[] = $message;
          }
        }
        return response()->json(['mesagge' => 'El contenido de la foja no puede ser vacio', 'response' => 2]);
        //return redirect()->back()->with('errors', $errors)->withInput();
        die;
      }

      // La carpeta de la foja, es el id del Organismo concatenado con el id del expediente
      $carpetaRaizOrganismo = $expediente->organismos->id . DIRECTORY_SEPARATOR . $expediente->id;
      $nombreFoja = "foja.pdf";

      $dompdf = \App::make('dompdf.wrapper');
      $dompdf->loadHtml($request->editor);

      // (Optional) Setup the paper size and orientation
      $dompdf->setPaper('a4', 'portrait');

      // Render the HTML as PDF
      //$dompdf->render();

      // // Output the generated PDF to Browser
      //$dompdf->stream();

      Storage::disk('local')->put($carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $nombreFoja, $dompdf->Output());

      // El path donde esta la foja en pdf por el momento:
      $pathOG = Storage::disk('local')->path($carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $nombreFoja);

      $files = $this->splitPdfToImages2($pathOG, $nombreFoja);

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

        $path =  $carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $fileName;

        //reducir el archivo
        $imagen = Image::make($file);
        // $imagen->widen(604);
        Storage::disk('local')->put($path, $imagen->encode('webp', 90));
        $unlinkJPEG = Storage::disk('local')->path($carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $file_nombre);
        $res =  unlink($unlinkJPEG);


        // obtener la ruta del archivo para generar hash
        $path1 = Storage::disk('local')->path($carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $fileName);

        //calcular numero de pagina
        $nextFojaNumber += 1;

        // Almacenar con Modelo 
        $foja = new Foja;
        $foja->expedientes_id = $expediente->id;
        // $foja->foja = $request->expediente_id . $nextFojaNumber;
        $foja->tipofoja = "imagen";
        $foja->nombre = $fileName;
        $foja->path = $path;
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

        // Generar un nuevo hash para la foja actual
        $foja->hash =  $this->generarHashSHA256($expediente->makeHashFromFile($path1), $foja->hashPrevio, $foja->created_at);

        // Guardar la foja en el server de imagenes
        try {
          // recuperar contenido de la imagen foja
          $imageContent = Storage::disk('local')->get($path);
          // guardar en la ruta correspondiente la foja imagen en el servidor de imagenes
          if (Storage::disk('minio')->put($path, $imageContent)) {
            // Si el server de imagenes responde true borramos la foja en el storage local
            $res =  unlink($path1);
          }
        } catch (\Exception $e) {
          Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
          // Si hay un error de conexion o escritura en el server remoto devolvemos error
          return response()->json(['mesagge' => 'El servidor de fojas no esta disponible', 'response' => 2]);
          //return redirect()->back()->with('errors', ['El servidor de fojas no esta disponible']);
        }

        // Si no hubo problemas en el storage remoto, se guarda en base de datos la foja
        $foja->save();

        //generar un nuevo estado del expediente
        $estadoexpediente = new Expedienteestado;
        $estadoexpediente->expedientes_id = $expediente->id;
        $estadoexpediente->users_id = Auth::user()->id;
        $estadoexpediente->expendientesestado = 'procesando';
        $estadoexpediente->expedientesrutas_id =  $ruta_actual;
        $textoLog = Auth::user()->name . " agrego la foja " . $foja->nombre . " a las " .  Carbon::now()->toTimeString();
        Logg::info($textoLog);
        $estadoexpediente->observacion = $textoLog;
        $estadoexpediente->ruta_devolver = $expedienteestado->ruta_devolver;
        $estadoexpediente->save();

        if ($request->get('tags')) {
          $tags = $request->get('tags');

          for ($i = 0; $i < count($tags); $i++) {
            // $expediente->organismosetiquetas()->attach([$tags[$i]]);
            $foja->organismosetiquetas()->syncWithoutDetaching([$tags[$i]]);
          }
        }
      }

      DB::commit();
      $resOG =  unlink($pathOG);
      //return redirect()->route('expediente.show', base64_encode($expediente->id))->with('success', 'La/s foja/s se agregaron correctamente');
      return response()->json(['mesagge' => 'La/s foja/s se agregaron correctamente', 'response' => 1]);
    } catch (Exception $exception) {
      DB::rollback();
      //Logg::error($exception->getMessage(),("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()) );
      if ($exception instanceof AuthorizationException) {
        return response()->json(['mesagge' => 'No posee los permisos para realizar esta acción', 'response' => 2]);
        //return redirect()->route('expedientes.index')->with('errors', ["No posee los permisos para realizar esta acción"]);
      } else {
        $exception->getMessage();
        Logg::error($exception->getMessage(), ("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()));
        $errors[0] = "Error con los datos enviados. Intente con otros datos.";
        return response()->json(['mesagge' => 'Error con los datos enviados. Intente con otros datos.', 'response' => 2]);
        //return redirect()->back()->with('errors',  $errors)->withInput();
        die;
      }
    }
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

  public function guardarFojaPdf($contenido, $ubicacion, $nombreFoja)
  {
    //$pdf = \App::make('dompdf.wrapper');
    $pdf = new Dompdf();
    $pdf->loadHTML($contenido);

    // (Optional) Setup the paper size and orientation
    $pdf->setPaper('a4', 'portrait');

    $pdf->stream();

    return Storage::disk('local')->put($ubicacion . DIRECTORY_SEPARATOR . $nombreFoja, $pdf->Output());
  }

  protected function errorResponse($message = null, $code)
  {
    return response()->json(['status' => 'error',  'message' => $message, 'data' => null], $code);
  }

  // --------------dropzone--------------------
  public function storefile(Request $request)
  {

    // Evaluar si la request proviene de un PDF que sube el usuario en su tab correspondiente
    if ($request->has('pdfs')) {
      $file = $request->file('pdfs');
      $validatorPDF = Validator::make($request->all(), [
        'pdfs' => 'mimetypes:application/pdf,application/octet-stream|max:51200',
      ]);

      // $res = $validatorPDF->fails();
      // Chekear si el pdf esta protegido
      if ($this->checkIfEncrypted($file)) {
        return $this->errorResponse("El pdf esta protegido y no se puede procesar.", 422);
      }

      // Existe una excepcion en donde el archivo se sube con mime application/pdf pero es en realidad application/octet-stream
      $mime = $file->getMimeType();
      //Como mime es un string vemos si es de tipo octet-stream
      if (Str::contains($mime, 'octet-stream')) {
        // Si contiene ese tipo de mime, se debe transformar y regenerar el pdf para poder continuar con el proceso
        $transformFile = $this->transformar($file);
        // El archivo subido se reemplaza por un archivo que se puede procesar como pdf
        $file = $transformFile;
      }

      if (!$validatorPDF->fails()) {
        $files = $this->splitPdfToImages($file);
      } else {
        return $this->errorResponse("error", 422);
      }
    } else {
      // Obtiene el array de archivos que se envio por dropzone.
      $files = $request->file('file');
    }

    // Obtener el expediente  y calcular el numero de foja actual para crear nueva 
    $expediente = Expediente::find($request->expediente_id);
    $expedienteestado = $expediente->expedientesestados->last();
    $expedientesector = $expedienteestado->rutasector->sector->id;

    // Autorizacion
    try {
      $this->authorize('agregar_foja', $expediente);
    } catch (\Exception $exception) {
      Logg::error($exception->getMessage(), ("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()));
      if ($exception instanceof AuthorizationException) {
        return response()->json("No posee los permisos para realizar esta accion", 403);
      }
    }

    $nextFojaNumber = $expediente->fojas->count();

    // Obtiene la cantidad de fojas del expediente al momento de ejecutar el metodo para luego asignar las etiquetas
    // a las fojas siguientes
    $cantidadinicial = $expediente->fojas->count();


    // obtener la ruta actual para el nuevo estado
    $ruta_actual = $expediente->expedientesestados->last()->expedientesrutas_id;

    // crear el nombre de la carpeta del expediente
    $carpetaRaizOrganismo = $expediente->organismos->id . DIRECTORY_SEPARATOR . $expediente->id;

    // VALIDACION DE INTEGRIDAD DE HASH
    // obtener una coleccion de las fojas de ese expediente
    /*     $fojas = $expediente->fojas;
    $contador = 0;
    $fojaAlterada = "";
    for ($i = 0; $i < count($fojas); $i++) {
      $pathFoja = Storage::disk('public')->path($fojas[$i]->path);
      $hashFojaActual = $fojas[$i]->hash;
      // volvemos a generar el hash de esta foja para controlar su integridad
      $hashFojaActualRecalculado = $this->generarHashSHA256($expediente->makeHashFromFile($pathFoja), $fojas[$i]->hashPrevio, $fojas[$i]->created_at);
      if ($hashFojaActualRecalculado !== $hashFojaActual) {
        $contador += 1;
        $fojaAlterada = $fojas[$i]->nombre;
      }
    }

    for ($i = 1; $i < count($fojas); $i++) {
      if ($fojas[$i]->hashPrevio !== $fojas[$i - 1]->hash) {
        $contador += 1;
        $fojaAlterada = $fojas[$i]->nombre;
      }
    }
    */

    /* if ($contador > 0) {
      return $this->errorResponse("error", 422);
      // return redirect()->route('expedientes.index')->with('error', "El expediente fue modificado en la foja: " . $fojaAlterada);
      // die;
    } */

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

      $path =  $carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $fileName;

      $imagen = Image::make($file);

      Storage::disk('local')->put($path, $imagen->encode('webp', 90));

      // obtener la ruta del archivo para generar hash
      $path1 = Storage::disk('local')->path($path);

      //calcular numero de pagina
      $nextFojaNumber += 1;

      // Almacenar con Modelo 
      $foja = new Foja;
      $foja->expedientes_id = $request->expediente_id;
      // $foja->foja = $request->expediente_id . $nextFojaNumber;
      $foja->tipofoja = "imagen";
      $foja->nombre = $fileName;
      $foja->path = $path;
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

      // Generar un nuevo hash para la foja actual
      $foja->hash =  $this->generarHashSHA256($expediente->makeHashFromFile($path1), $foja->hashPrevio, $foja->created_at);

      // Guardar la foja en el server de imagenes
      try {
        // recuperar contenido de la imagen foja
        $imageContent = Storage::disk('local')->get($path);
        // guardar en la ruta correspondiente la foja imagen en el servidor de imagenes
        if (Storage::disk('minio')->put($path, $imageContent)) {
          // Si el server de imagenes responde true borramos la foja en el stogare local
          $res =  unlink($path1);
        }
      } catch (\Exception $e) {
        Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
        // Si hay un error de conexion o escritura en el server remoto devolvemos error
        return response()->json("No se puede acceder al servidor de fojas", 500);
      }

      // Si no hubo problemas en el storage remoto, se guarda en base de datos la foja
      $foja->save();

      //generar un nuevo estado del expediente
      $estadoexpediente = new Expedienteestado;
      $estadoexpediente->expedientes_id = $expediente->id;
      $estadoexpediente->users_id = Auth::user()->id;
      $estadoexpediente->expendientesestado = 'procesando';
      $estadoexpediente->expedientesrutas_id =  $ruta_actual;
      $textoLog = Auth::user()->name . " agrego la foja " . $foja->nombre . " a las " .  Carbon::now()->toTimeString();
      Logg::info($textoLog);
      $estadoexpediente->observacion = $textoLog;
      $estadoexpediente->ruta_devolver = $expedienteestado->ruta_devolver;
      $estadoexpediente->save();
    }

    // Se crea la variable $tags para guardar las etiquetas que se asignan al subir PDF o imagen
    $tags = NULL;

    if ($request->tag_selected_pdf !== NULL) {
      $tags = $request->tag_selected_pdf;
    }

    if ($request->tag_selected_imagen !== NULL) {
      $tags = $request->tag_selected_imagen;
    }

    if (!is_null($tags)) {
      try {

        // Si el campo de etiquetas no está vacio, se traen las fojas del expediente mediante la funcion WHERE
        // para asi recuperar las fojas recientemente agregadas
        $etiquetarfojas = Foja::where('expedientes_id', $expediente->id)->get();

        foreach ($etiquetarfojas as $etiquetarfoja) {
        
          // Se compara el numero de la foja con la cantidad de fojas que tenia el documento al ingresar al metodo
          // para saber cuales fueron cargadas recientemente
          if ($etiquetarfoja->numero > $cantidadinicial) {
            for ($i = 0; $i < count($tags); $i++) {
              $etiquetarfoja->organismosetiquetas()->syncWithoutDetaching([$tags[$i]]);
            }
          }

        }

      } catch (\Exception $e) {
        Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
        return redirect()->back()->with('errors', ['No se pudo asignar la etiqueta']);
      }
    }

    return response()->json([[1]]);
  }

  public function show($id)
  {
    try {
      $foja = Foja::findOrFail(base64_decode($id));
      return Storage::disk('minio')->response($foja->path);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      // En el caso de que el servidor este activo pero no se encuentra la foja
      return redirect()->route('expedientes.index')->with('errors', ['No existe la foja consultada en el servidor.']);
    }
  }

  public function showFirmada($id)
  {
    try {
      $foja = Foja::findOrFail(base64_decode($id));
      return Storage::disk('minio')->response($foja->firmada->path);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      // En el caso de que el servidor este activo pero no se encuentra la foja
      return redirect()->route('expedientes.index')->with('errors', ['No existe la foja consultada en el servidor.']);
    }
  }


  public function updateFoja(Request $request)
  {
    try {
      DB::beginTransaction();


      if (!session('permission')->contains('foja.ordenar')) {
        return response()->json([[2]]);
      }

      $exp_num = $request->id;
      $num = reset($exp_num);

      $fojas = Foja::where('expedientes_id', $num)->get();

      foreach ($fojas as $foja) {
        $foja->timestamps = false;
        $id = $foja->id;

        foreach ($request->order as $order) {
          if ($order['id'] == $id) {
            $foja->update(['numero' => $order['position']]);
          }
        }
      }

      //estados del expediente


      $expediente = Expediente::find($request->id)->first();
      $code_exp_id = base64_encode($expediente->id); // expediente_id codificado para pasar por url
      $textoLog = Auth::user()->name . " cambio orden fojas del " . org_nombreDocumento() . " a las " . Carbon::now()->toTimeString();
      Logg::info($textoLog);
      historial_doc(($expediente->id), $textoLog);



      DB::commit();
      // return response()->json([[1]]);
      return response()->json(['respuesta' => 1,
                                'code_exp_id' => $code_exp_id]);
    } catch (\Throwable $th) {
      Logg::error($th->getMessage(), ("Archivo:" . pathinfo($th->getFile())['basename'] . " - Línea:" . $th->getLine()));
      // return response()->json([[2]]);
      return response()->json(['respuesta' => 2]);
    }
  }

  public function deleteFoja(Request $request)
  {

    DB::beginTransaction();
    try {
      if (is_array($request->id_foja)) {
        $numero = "";
        foreach ($request->id_foja as $indice => $foja_id) {
          $foja_eliminar = Foja::find($foja_id);
          
          $numero = $numero . $foja_eliminar->numero . "-";
          $foja_eliminar->update(['numero' => null]);
          $foja_eliminar->delete();
        }

        // $exp_fojas = Expediente::find($foja_eliminar->expedientes_id)->fojas;
        $exp_fojas = Foja::where('expedientes_id', $foja_eliminar->expedientes_id)
                        ->orderBy('numero', 'asc')
                        ->get();
        // dd($exp_fojas);
        $iteracion = 0;

        // dd($exp_fojas->numero);

        foreach ($exp_fojas as $exp) {
          $iteracion += 1;
          $exp->update(['numero' => $iteracion]);
        }
      }
      // else {

      //   $foja_eliminar = Foja::find($request->id_foja);
      //   $numero = $foja_eliminar->numero;
      //   $foja_eliminar->update(['numero' => null]);
      //   $foja_eliminar->delete();

      //   // $exp_fojas = Expediente::find($request->id)->fojas;
      //   $exp_fojas = Expediente::find($foja_eliminar->expedientes_id)->fojas;
      //   // dd($exp_fojas);
      //   $iteracion = 0;

      //   // dd($exp_fojas->numero);

      //   foreach ($exp_fojas as $exp) {
      //     $iteracion += 1;
      //     $exp->update(['numero' => $iteracion]);
      //   }
      // }
      //estados del expediente

      $expediente = Expediente::find($request->id)->first();
      $code_exp_id = base64_encode($expediente->id); // expediente_id codificado para pasar por url
      $textoLog = Auth::user()->name . " eliminó foja n°" . $numero . " del " . org_nombreDocumento() . "  a las " . Carbon::now()->toTimeString();
      Logg::info($textoLog);
      historial_doc(($expediente->id), $textoLog);


      DB::commit();
      return response()->json(['respuesta' => 1,
                                'code_exp_id' => $code_exp_id]);
    } catch (\Throwable $th) {
      DB::rollback();
      Logg::error($th->getMessage(), ("Archivo:" . pathinfo($th->getFile())['basename'] . " - Línea:" . $th->getLine()));
      return response()->json(['respuesta' => 2,
      'code_exp_id' => $code_exp_id]);
    }
  }

  public function firmar_index(Request $request, $id)
  {
    try {

      $id = base64_decode($id);
      $expediente = Expediente::findOrFail($id);
      $title = "Gestion Firmas";

      // Ver si existen fojas que quedaron pendientes de firma por cancelar el proceso de firma
      if ($request->has('reload')) {
        // Las fojas oendientes se eliminan en caso de no haber sido firmadas con exito
        $fojas_firmadas = $expediente->fojas()->whereHas('firmada')->with('firmada')->get();
        foreach ($fojas_firmadas as $foja) {
          $firmada = $foja->firmada;
          if ($firmada->estado == 'pendiente') {
            $firmada->delete();
          }
        }
      }

      $cuil = DB::table('preferencies')->where('users_id',  Auth::user()->id)->where('filterNombre', 'cuil')->value('filterPref');
      $cuil = ($cuil != "" ? $cuil : "");

      $sectoresUsuario = Auth::user()->usersector->pluck('organismossectors_id')->toArray();

      $textoLog = Auth::user()->name . " ingreso a la sección de firma del " . org_nombreDocumento() . " a las " . Carbon::now()->toTimeString();
      historial_doc(($expediente->id),$textoLog);

      // dd($sectoresUsuario);
      // las fojas del expediente paginadas
      $fojas = $expediente->fojas->paginate(10);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return response()->json('error');
    }
    return view('firmas.index', ['expediente' => $expediente, 'fojas' => $fojas, 'title' => $title, 'cuilValor' => $cuil, 'sectoresUsuario' => $sectoresUsuario]);
  }

  public function firmarMultible(FirmaRequest $request)
  {
    // dd($request->mychecks);
    // Esto es un operador ternario. funciona como un if else
    // is_null($request->cuil) ? $cuil = "20-30890182-4" : $cuil = $request->cuil;

    $cuil_guiones =  $this->ponerGuionesCuil($request->cuil);
    $fecha = Carbon::now()->format('d/m/Y H:m:s');

    try {
      // mycheck es un array con los id de las fojas
      if ($request->has('mychecks')) {

        // devuelve una Eloquent Collection, aun cuando sea una sola foja la seleccionada
        $fojas = Foja::whereIn('id', $request->mychecks)->get();
        // las fojas pueden estar firmadas o sin ninguna firma aun
        // se guardan en arrays diferentes
        $ubicaciones_firmadas = [];
        $ubicaciones_fojas = [];
        foreach ($fojas as $foja) {
          if ($foja->isFirmada()) {
            $firmada = $foja->firmada;
            array_push($ubicaciones_firmadas, $this->getAndStoreFirmada($firmada));
          }
          // elseif ($foja->isPendiente()) {
          //   return redirect()->route('firmar.index',)->with('errors', [$e->getMessage()]);
          // } 
          else {
            // Si no esta firmada, puede estar pendiente y debe borrarse el registro pendiente para que no se dupliquen registros
            if ($foja->isPendiente()) {
              $firmada = $foja->firmada;
              $firmada->delete();
            }
            // es necesario una coleccion de Eloquent para pasar despues al metodo fojasImageToPdf(...)
            $c = new \Illuminate\Database\Eloquent\Collection;
            $c->add($foja);
            // Tiene que ser un array merge porque las fojas firmadas y sin firmar pueden venir intercaladas
            $ubicaciones_fojas = array_merge($ubicaciones_fojas, $this->fojasImageToPdf($c, $cuil_guiones, $fecha));
          }
        }
        // Union de los dos arrays de ubicaciones de las fojas firmadas y sin firmar
        $ubicaciones = array_merge($ubicaciones_fojas, $ubicaciones_firmadas);

        // En este punto, tenemos fojas pdf en el disco local para ser firmadas
        // la funcion firmar(...) devuelve la ruta de redirect al recurso a firmar
        $firmaLocationRoute =  $this->firmar($ubicaciones, $cuil_guiones, $fojas);
        if ($firmaLocationRoute instanceof Exception) {
          throw $firmaLocationRoute;
        }
        return Redirect::to($firmaLocationRoute);
      } else {
        throw new ResourceNotFoundException();
      }
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return redirect()->route('expedientes.index')->with('errors', [$e->getMessage()]);
    }
  }

  public function download($id)
  {
    $foja =  Foja::findOrFail($id);
    $path = $foja->path;
    $name = $foja->nombre;
    // esta funcion esta en el FileHandlingTrait
    $pdf = $this->imageToPdf($path, $name);
  }

  /**
   * Obtiene el acces token para la validacion de tipo client credentials
   * @return void
   */
  public function getAccessToken()
  {
    $client = new Client(['base_uri' => config('configuraciones.firma_base_url')]);
    $firmador = new Firmador($client);
    $respuesta_exitosa = $firmador->getAccessToken();
    return $respuesta_exitosa->access_token;
  }

  public function firmar($ubicaciones_fojas, $cuil, $fojas)
  {
    // solitud de access token al RA de la plataforma firma digital.
    // Se hace en el servicio .NET actualmente
    // El token que actualmente se envia es el del servicio de autenticacion propio
    $token = session()->get('token_go');
    try {
      // $client = new Client(['base_uri' => config('configuraciones.firma_base_url'), 'verify' => false]);
      $client = new Client(['base_uri' => config('configuraciones.firma_base_url')]);
      $firmador = new Firmador($client);
      $response = $firmador->enviarDocumentoDotnetService($cuil, $ubicaciones_fojas, $token, $fojas);

      if ($response && $response->getStatusCode() == 200) {
        // Eliminar las fojas que se generaron provisoriamente para ser firmadas en el storage
        $this->eliminarFojasResiduales($ubicaciones_fojas);
        // La ruta de redireccion al recurso de firma, viene en el body de la response
        $body = json_decode($response->getBody()->getContents());
        $ruta = $body->rutaDocumentoAFirmar;
        return $ruta;
      } else {
        throw new Exception("Se produjo un error al comunicarse con el servico de firma.");
      }
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      // Eliminar las fojas que se generaron provisoriamente para ser firmadas en el storage
      $this->eliminarFojasResiduales($ubicaciones_fojas);
      return $e;
    }
  }

  public function eliminarFojasResiduales($ubicaciones_fojas)
  {
    // Limpiar fojas para firmar residuales
    foreach ($ubicaciones_fojas as $ubicacion) {
      $directorio = dirname($ubicacion);
      $full_directory = storage_path('app') . DIRECTORY_SEPARATOR . $directorio;
      File::deleteDirectory($full_directory);
    }
  }

  /**
   * ponerGuionesCuil
   * Toma un cuil sin guiones y le pone los guiones de la forma estandar
   * @param  mixed $p_cuil
   * @return void
   */
  protected function ponerGuionesCuil($p_cuil)
  {
    $cuil_sin_guiones = str_replace('-', '', $p_cuil);

    return  substr($cuil_sin_guiones, 0, 2) . '-' . substr($cuil_sin_guiones, 2, 8) . '-' . substr($cuil_sin_guiones, strlen($cuil_sin_guiones) - 1, 1);
  }

  /**
   * firmantes
   * Devuelve los firmantes de una foja
   * @param  mixed $id El id de una foja
   */
  public function firmantes($id)
  {
    try {
      $id = base64_decode($id);
      // obtenemos los firmantes de una foja con el metodo signers del Model Foja
      $firmantes = Foja::findOrFail($id)->signers();
      // se retorna en formato json la response ya que es una consulta de tipo ajax
      return response()->json(['firmantes' => $firmantes]);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return response()->json(['error' => "No se puede encontrar firmantes para esta Foja"]);
    }
  }

  public function downloadPDF(Request $request)
  {

    $pdf = \App::make('dompdf.wrapper');

    $pdf->loadHTML($request->editor);
    $fechaString = Carbon::now();
    $nombreFile = ("DOCO-" . $fechaString . ".pdf");
    return $pdf->download($nombreFile);
    // return $pdf->stream();

  }

  public function asignarEtiquetas(Request $request)
  {
    try {
      $tags = $request->get('tagsPut');

      // if (!isset($tags) || is_null($tags)) {
      //   return redirect()->back()->with('errors', ['Debe selecccionar una etiqueta para asignar']);
      // }    

      //Remover todo para luego asignar 

      $foja = Foja::findOrFail($request->get('foja_id'));

      // FUNCION ORIGINAL DE FOJAS
      // if ($foja->organismosetiquetas()) {
      //   $foja->organismosetiquetas()->detach();
      // }
      // if ($tags) {
      //   for ($i = 0; $i < count($tags); $i++) {
      //     $foja->organismosetiquetas()->syncWithoutDetaching([$tags[$i]]);
      //   }
      // }
      // $expediente = $foja->expediente;
      // FUNCION ORIGINAL DE FOJAS

      // Si el usuario posee éste permiso puede administrar las etiquetas de cualquier foja del expediente
      if (session('permission')->contains('expediente.etiqueta')) {
        if ($foja->organismosetiquetas()) {
          $foja->organismosetiquetas()->detach();
        }
        if ($tags) {
          for ($i = 0; $i < count($tags); $i++) {
            $foja->organismosetiquetas()->syncWithoutDetaching([$tags[$i]]);
          }
        }
      }
      // Si el usuario posee éste permiso solo puede administrar las etiquetas que pertenecen al sector donde está el expediente actualmente
      elseif (session('permission')->contains('expediente.etiqueta.sector')) {

          $diferenciaetiquetas = new Organismosetiqueta;
          $sectoresUsuario = Auth::user()->usersector->pluck('organismossectors_id')->toArray(); // array con id de sectores del usuario
        
          if ($foja->organismosetiquetas()) {
            
            // Si el campo que contiene las etiquetas de la foja no es vacio, se guarda en una coleccion la diferencia entre las etiquetas que figuran en el select2
            // y las que ya tiene asignadas y no puede ver el usuario por su permiso
            if ($tags !== NULL) {
              $diferenciaetiquetas = $foja->organismosetiquetas->filter(function ($etiqueta) use ($tags) {
                return !in_array($etiqueta->id, $tags);
              });
            }
            // Si el campo que contiene las etiquetas de la foja es vacio, se guarda en una coleccion la diferencia entre el valor constante de $tags
            // y las que ya tiene asignadas y no puede ver el usuario por su permiso
            // Aclaración: el valor constante de ['999999'] para $tags se usa para poder filtrar y obtener las etiquetas ya asignadas y que el usuario no puede
            // ver por su permiso
            else {
              $tags = ['999999'];

              $diferenciaetiquetas = $foja->organismosetiquetas->filter(function ($etiqueta) use ($tags) {
                return !in_array($etiqueta->id, $tags);
              });

              // se vuelve a asignar el valor original de $tags
              $tags = NULL;
            }
            
            // Una vez recuperados los valores de las etiquetas que no se pueden quitar, se quita el vinculo de todas las etiquetas de esa foja
            $foja->organismosetiquetas()->detach();
          }

          if ($tags) {
            
            // Si $tags tiene etiquetas cargadas, se recorre la coleccion $diferenciaetiquetas y se quitan las que son globales o del sector al que pertenece
            // el usuario, para que en el caso de que se quite una etiqueta en $tags que cumpla ésta condición, no se vuelva a cargar desde
            // la coleccion $diferenciaetiquetas
            // foreach ($diferenciaetiquetas as $key => $etiqueta) {
            //   if ($etiqueta->organismossectors_id == $foja->expediente->expedientesestados->last()->rutasector->sector->id) {
            //     unset($diferenciaetiquetas[$key]);
            //   }
            //   elseif ($etiqueta->organismossectors_id == NULL) {
            //     unset($diferenciaetiquetas[$key]);
            //   }
            // }
            
            // PRUEBA SECTORES DEL USUARIO
            foreach ($diferenciaetiquetas as $key => $etiqueta) {
              if (in_array($etiqueta->organismossectors_id, $sectoresUsuario)) {
                unset($diferenciaetiquetas[$key]);
              }
              elseif ($etiqueta->organismossectors_id == NULL) {
                unset($diferenciaetiquetas[$key]);
              }
            }

            // se obtiene los id de las etiquetas cargadas en $deferenciaetiquetas y se cargan en un array
            $arrayEtiquetas = $diferenciaetiquetas->pluck('id')->toArray();
            // se combinan los arrays de $tags y $arrayEtiquetas para que sean los que se carguen en la base de datos
            $tags = array_merge($arrayEtiquetas, $tags);

            for ($i = 0; $i < count($tags); $i++) {
              $foja->organismosetiquetas()->syncWithoutDetaching([$tags[$i]]);
            }
          }
          else {
            
            // Si $tags no tiene etiquetas cargadas, se recorre la coleccion $diferenciaetiquetas y se quitan las que son globales o del sector al que pertenece el usuario
            // porque significa que el usuario quitó las etiquetas que cumplen esa condicion y no se vuelva a cargar desde la coleccion $diferenciaetiquetas
            // foreach ($diferenciaetiquetas as $key => $etiqueta) {
            //   if ($etiqueta->organismossectors_id == $foja->expediente->expedientesestados->last()->rutasector->sector->id) {
            //     unset($diferenciaetiquetas[$key]);
            //   }
            //   elseif ($etiqueta->organismossectors_id == NULL) {
            //     unset($diferenciaetiquetas[$key]);
            //   }
            // }
            
            // PRUEBA SECTORES DEL USUARIO
            foreach ($diferenciaetiquetas as $key => $etiqueta) {
              if (in_array($etiqueta->organismossectors_id, $sectoresUsuario)) {
                unset($diferenciaetiquetas[$key]);
              }
              elseif ($etiqueta->organismossectors_id == NULL) {
                unset($diferenciaetiquetas[$key]);
              }
            }

            // $arrayEtiquetas = $diferenciaetiquetas->pluck('id')->toArray();
            // se asigna a $tags las etiquetas que el usuario no puede quitar
            $tags = $diferenciaetiquetas->pluck('id')->toArray();

            for ($i = 0; $i < count($tags); $i++) {
              $foja->organismosetiquetas()->syncWithoutDetaching([$tags[$i]]);
            }
          }
      }
      $expediente = $foja->expediente;

      $textoLog = Auth::user()->name . " gestionó etiqueta/s foja n°" . $foja->numero . " a las " . Carbon::now()->toTimeString();
      historial_doc(($expediente->id), $textoLog);
    } catch (\Exception $e) {
      Logg::error(($request), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      return redirect()->back()->with('errors', ['No se pudo gestionar la/s etiqueta/s']);
    }
    // En caso de exito redirecciona a 
    // return redirect()->back();
    // Si se realizó una accion con las etiquetas de las fojas, se redirecciona al index pero con la pestaña de "gestionar fojas" activa
    return redirect('/expediente/' . base64_encode($foja->expediente->id) . '/fojas/1');
  }

  public function etiquetasAsignadas(Request $request, $foja_id)
  {
    try {

      // Ver que las etiquetas no esten repetidas

      $foja = Foja::findOrFail($foja_id);
      $expediente = Expediente::find($foja->expedientes_id);
      $data = $foja->organismosetiquetas;

      // Si el usuario posee éste permiso, solo se muestran las etiquetas globales y las que pertenecen al sector al que pertenece el expediente actualmente
      // sino se muestran todas las etiquetas asignadas (admin)
      if (session('permission')->contains('expediente.etiqueta.sector')) {
        $sectoractual = $expediente->expedientesestados->last()->rutasector->sector->id;
        $global = NULL;
        $sectoresUsuario = Auth::user()->usersector->pluck('organismossectors_id')->toArray(); // etiquetas solo de los sectores que pertenece el usuario (PRUEBA)

        // $dataPorSector = $data->filter(function ($etiqueta) use ($sectoractual) {
        //   return $etiqueta->organismossectors_id == $sectoractual;
        // });

        // etiquetas solo de los sectores que pertenece el usuario (PRUEBA)
        $dataPorSector = $data->filter(function ($etiqueta) use ($sectoresUsuario) {
          return in_array($etiqueta->organismossectors_id, $sectoresUsuario);
        });

        $dataGlobales = $data->filter(function ($etiqueta) use ($global) {
          return $etiqueta->organismossectors_id == $global;
        });
        
        // se concatenan los 2 resultados filtrados en un solo array y se pasa a la vista
        $data = $dataPorSector->concat($dataGlobales);
      }

      return json_encode($data);
    } catch (\Exception $e) {
      $data = "Prueba fallida :" . $foja_id;
      return json_encode($data);
    }
  }

  public function etiquetasNoAsignadas(Request $request, $foja_id)
  {
    try {

      $foja = Foja::findOrFail($foja_id);
      $expediente = Expediente::find($foja->expedientes_id);
      $organismo = Auth::user()->userorganismo->first()->organismos_id;
      $arrayEtiquetas = $foja->organismosetiquetas->pluck('id')->toArray();
      $activas = 1;

      $etiquetas = Organismosetiqueta::where('organismos_id', $organismo)->get()->filter(function ($etiqueta) use ($arrayEtiquetas) {
        return !in_array($etiqueta->id, $arrayEtiquetas);
      });

      // Si el usuario posee éste permiso, solo se muestran las etiquetas globales y las que pertenecen al sector al que pertenece el expediente actualmente
      // sino se muestran todas las etiquetas asignadas (admin)
      if (session('permission')->contains('expediente.etiqueta.sector')) {
        $sectoractual = $expediente->expedientesestados->last()->rutasector->sector->id;
        $global = NULL;
        $sectoresUsuario = Auth::user()->usersector->pluck('organismossectors_id')->toArray(); // etiquetas solo de los sectores que pertenece el usuario (PRUEBA)

        $etiquetasGlobales = $etiquetas->filter(function ($etiqueta) use ($global) {
          return $etiqueta->organismossectors_id == $global;
        });

        // $etiquetasPorSector = $etiquetas->filter(function ($etiqueta) use ($sectoractual) {
        //   return $etiqueta->organismossectors_id == $sectoractual;
        // });

        // etiquetas solo de los sectores que pertenece el usuario (PRUEBA)
        $etiquetasPorSector = $etiquetas->filter(function ($etiqueta) use ($sectoresUsuario) {
          return in_array($etiqueta->organismossectors_id, $sectoresUsuario);
        });

        // se concatenan los 2 resultados filtrados en un solo array y se pasa a la vista
        $etiquetas = $etiquetasPorSector->concat($etiquetasGlobales);
      }

      // solo se pueden vincular etiquetas que esten activas
      $etiquetas = $etiquetas->filter(function ($etiqueta) use ($activas) {
        return $etiqueta->activo == $activas;
      });

      $etiquetasArray = [];

      foreach ($etiquetas as $i => $etiq) {
        array_push($etiquetasArray, $etiq);
      }

      return json_encode($etiquetasArray);
    } catch (\Exception $e) {
      $data = $foja_id . " con Prueba fallida";
      return json_encode($etiquetas);
    }
  }

  public function downloadFoja($id)
  {

    $foja = Foja::find(base64_decode($id));
    $path = $foja->path;
    $name = $foja->nombre;

    $expediente = Expediente::find($foja->expediente->id);
    $textoLog = Auth::user()->name . " descargo foja n°" . $foja->numero . " del " . org_nombreDocumento() . " a las " . Carbon::now()->toTimeString();

    historial_doc(($expediente->id), $textoLog);

    // esta funcion esta en el FileHandlingTrait    
    // Craer un pdf a partir de la imagen foja del servidor de archivos

    PDF::Reset();
    PDF::AddPage();
    PDF::SetTitle('Foja Descargada DOCO');
    PDF::Image('@' . Storage::cloud()->get($path), 0, 0, 210, 0, '', '', 'center', false, 300, '', false, false, 0);

    // quitar la extension de imagen de la foja
    $filename = pathinfo($name, PATHINFO_FILENAME);
    $filePathAndName = $filename . '.pdf';
    $salida = PDF::Output(storage_path('app') . '/' . $filePathAndName, 'F');

    return response()->download(storage_path('app') . '/' . $filePathAndName);
  }

  public function printFoja($id)
  {

    $foja = Foja::find(base64_decode($id));
    $path = $foja->path;
    $name = $foja->nombre;

    $expediente = Expediente::find($foja->expediente->id);
    $textoLog = Auth::user()->name . " accedió para imprimir foja n°" . $foja->numero . " del " . org_nombreDocumento() . " a las " . Carbon::now()->toTimeString();
    historial_doc(($expediente->id), $textoLog);

    // esta funcion esta en el FileHandlingTrait    
    // Craer un pdf a partir de la imagen foja del servidor de archivos

    PDF::Reset();
    PDF::AddPage();
    PDF::SetTitle('Foja Descargada DOCO');
    PDF::Image('@' . Storage::cloud()->get($path), 0, 0, 210, 0, '', '', 'center', false, 300, '', false, false, 0);

    // quitar la extension de imagen de la foja
    $filename = pathinfo($name, PATHINFO_FILENAME);
    $filePathAndName = $filename . '.pdf';
    return PDF::Output(storage_path('app') . '/' . $filePathAndName);
  }

  /**
   * Este metodo se encarga de subir una foja que ya está firmada y vincularla con la foja seleccionada
   */	
  public function subirFirmada(Request $request)
  {
    $foja_id = $request->appened_foja_id;
    $cuil = $this->ponerGuionesCuil($request->cuil_firmante);

    if ($request->has('input_file')) {
      $firmada = $request->file('input_file');

      $validatorPDF = Validator::make($request->all(), [
        'input_file' => 'mimetypes:application/pdf,application/octet-stream|max:30000',
      ]);

      if ($validatorPDF->fails()) {
        return $this->errorResponse("El formato de archivo no es PDF o se superó el tamaño máximo permitido (30MB)", 422);
      }
    }

    try {
      $foja = Foja::findOrFail($foja_id);

      $carpetaRaizOrganismo = $foja->expediente->organismos_id . DIRECTORY_SEPARATOR . $foja->expediente->id . DIRECTORY_SEPARATOR . "firmadas" . DIRECTORY_SEPARATOR . $foja->id;
      $nombreFoja = "firmada_adjunta_" . $firmada->getClientOriginalName();

      $path = $carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $nombreFoja;

      // Se intenta guardar la imagen directamente en el servidor de archivos sin usar el directorio local
      if (Storage::cloud()->put($path, file_get_contents($firmada))) {
        // $full_minio_server_url = config('configuraciones.OBJECT_STORE_ENDPOINT') . '/' . config('configuraciones.AWS_BUCKET') . '/' . $path;
        $full_minio_server_url = env('MINIO_ENDPOINT') . '/' . env('AWS_BUCKET') . '/' . $path;

        DB::table('firmadas')->insert([
          'foja_id' => $foja->id,
          'cuil' => $cuil,
          'path' => $path,
          'url' => $full_minio_server_url,
          'estado' => 'FIRMADA',
          'fecha_firma' => now(),
          'user_id' =>  auth()->user()->id, // Asociar la foja firmada con el firmante
          'fecha_envio' => now(),
        ]);

        return response()->json(['respuesta' => 1]);
      }
      else {
        return $this->errorResponse("El servidor de fojas se encuentra en mantenimiento. Por favor, intente más tarde", 500);
      }
    } catch (\Exception $e) {
      return $this->errorResponse("No se pudo asociar la foja con el adjunto seleccionado", 404);
    }
    
  }
}
