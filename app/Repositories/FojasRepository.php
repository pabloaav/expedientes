<?php

namespace App\Repositories;

use App\Expediente;
use App\Interfaces\FojasInterfaces;
use App\Foja;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Traits\FileHandlingTrait;
use App\Traits\PrimeraFojaCaratula;
use Intervention\Image\Facades\Image;

class FojasRepository implements FojasInterfaces
{
  use FilehandlingTrait, PrimeraFojaCaratula; // Este trait tiene las funciones de dividir los pdf

  public function primeraFoja($documento_nuevo)
  {
    // Foja Primera del Expediente
    $primeraFoja = new Foja([
      'expedientes_id' => $documento_nuevo->id,
      // 'foja' => $expediente->id . '1', 
      // concatenamos el expediente_id con la foja y resulta un string
      'tipofoja' => "texto",
      'texto' => "Caratula del Expediente: " . getExpedienteName($documento_nuevo) . ", con extracto: " . $documento_nuevo->expediente,
      'file' => null,
      'numero' => 1,
      'nombre' => "",
      'hashPrevio' => "genesis",
      'created_at' => new Carbon,
    ]);

    $codigoDelOrganismo = $documento_nuevo->organismos->id;
    // Crear la ruta a la carpeta del Organismo dado para esta operacion
    Storage::disk('local')->makeDirectory($documento_nuevo->organismos->id);

    // Se transforma la foja a pdf. Deberia retornar la ubicacion del archivo y su nombre
    $fojaPathAndName =  $this->crearPrimerFojaCaratula($documento_nuevo->id);

    // El path del archivo se recupera. Este $path es una ruta completa o absoluta del servidor o contenedor donde esta la app
    $path = Storage::disk('local')->path($fojaPathAndName);

    // Pdf a imagen. $fojaImagen es el nombre de la foja caratula en este caso, pero con la extension de tipo imagen mas un UUID unico
    $fojaImagen = $this->singlePdfToImage($path, "caratula_" . $documento_nuevo->expediente_num . ".pdf");

    // La foja imagen debe estar ahora en:
    $newPath = $documento_nuevo->organismos->id . DIRECTORY_SEPARATOR . $documento_nuevo->id . DIRECTORY_SEPARATOR . $fojaImagen;

    // Con el path y el nuevo nombre de la foja convertida en imagen, se recupera el contenido de la foja convertida a imagen.
    // Poner cuidado que disco se esta usando.
    $imageContent = Storage::disk('local')->get($newPath);

    /* concatenamos el nuevo nombre de la foja imagen para formar la ruta donde se almacena la foja imagen.
         La ruta consta de:
         - El codigo del organismo;
         - El id del expediente;
         - El nombre de la foja imagen; */
    $minioPathFojaCaratulaImagen = $codigoDelOrganismo . DIRECTORY_SEPARATOR . $documento_nuevo->id . DIRECTORY_SEPARATOR . $fojaImagen;

    // Se llama al servidor de imagenes para guardar la foja en el almacenamiento externo
    try {
      // La respuesta es true o verdadera en el caso de que se pueda guardar la imagen en el servidor
      $storageMinioResult = Storage::cloud()->put($minioPathFojaCaratulaImagen, $imageContent);
    } catch (\Exception $e) {
      return response()->json([
        'success' => 'false',
        'errors'  => ["El servidor de fojas no esta disponible"],
      ], 400);
    }

    // Guardar la ruta con el slash comun
    $pathCaratula = $documento_nuevo->organismos->id . '/' . $documento_nuevo->id . '/' . $fojaImagen;
    $primeraFoja->path =  $pathCaratula;
    // En el caso de quere guardar la url full al recurso foja:
    // $primeraFoja->path =  Storage::disk('minio')->url($pathCaratula);
    $primeraFoja->updated_at = $primeraFoja->created_at;
    $primeraFoja->save();

    return $primeraFoja;
  }


  public function createFojaTexto($parametros)
  {
          // Obtener el expediente  y calcular el numero de foja actual para crear nueva 
      $expediente = Expediente::where('expediente_num', $parametros['num_documento'])->where('organismos_id',$parametros['organismo'])->whereYear('created_at', $parametros['año'])->first();
      // $expediente 
      if ($expediente == null) {
        return null;
      }else{
         // La carpeta de la foja, es el id del Organismo concatenado con el id del expediente
      $carpetaRaizOrganismo = $expediente->organismos->id . DIRECTORY_SEPARATOR . $expediente->id;
      $nombreFoja = uniqid() . ".pdf";

      $dompdf = \App::make('dompdf.wrapper');
      $dompdf->loadHtml($parametros['content']);

      // (Optional) Setup the paper size and orientation
      $dompdf->setPaper('a4', 'portrait');

      // Se guarda la foja en formato pdf en el storage local
      Storage::disk('local')->put($carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $nombreFoja, $dompdf->Output());

      // El path donde esta la foja en pdf por el momento:
      $pathFojaPdf = Storage::disk('local')->path($carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $nombreFoja);

      $files = $this->splitPdfToImages2($pathFojaPdf, $nombreFoja);

      $nextFojaNumber = $expediente->fojas->count();

      // una variable para controlar el numero de iteracion actual del for de los files
      $iteracion = 0;
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
         $foja->created_at = new Carbon;
         $foja->updated_at = $foja->created_at;

    
        // obtener la ruta completa de la foja imagen codificada en formato webp del storage local para el hash
        $pathCompletoFoja = Storage::disk('local')->path($pathLocal);

        try {
          // recuperar contenido de la imagen foja
          $imageContent = Storage::disk('local')->get($pathLocal);
          // guardar en la ruta correspondiente la foja imagen en el servidor de imagenes
          if (Storage::disk('minio')->put($pathLocal, $imageContent)) {
            // Si el server de imagenes responde true borramos la foja en el storage local
            // Se borra la foja en formato pdf y en formato imagen
            $res1 =  unlink($pathCompletoFoja);
            $res2 =  unlink($pathFojaPdf);
          }
        } catch (\Exception $e) {
          return response()->json("No se puede acceder al servidor de ssfojas", 500);
        }

        // Si no hubo problemas en el storage remoto, se guarda en base de datos la foja
        $foja->save();
      }
      return $expediente;
    }
  }

  public function createFojaImagen($parametros) /*crear foja de tipo imagen*/
  {
    // Obtener el expediente  y calcular el numero de foja actual para crear nueva 
    $expediente = Expediente::where('expediente_num', $parametros['num_documento'])->where('organismos_id',$parametros['organismo'])->whereYear('created_at', $parametros['año'])->first();
    // $expediente 

    if ($expediente == null) {
      return null;
    }else{
      $nextFojaNumber = $expediente->fojas->count();

      // Carpeta raiz de la foja
      $carpetaRaizOrganismo = $expediente->organismos->id . "/" . $expediente->id;

      $files = $parametros['content'];/*array de archivos que se recibe por parametro */

      // una variable para controlar el numero de iteracion actual del for de los files
      $iteracion = 0;
  
      //recorrer el array de archivos 
      foreach ($files as $file) {
  
        $iteracion += 1;
        //generar nombre de archivo unico dentro del expediente
        $file_nombre = $file->getClientOriginalName();
        $file_nombre = control_nombre($file_nombre);
        $file_extension = pathinfo($file_nombre, PATHINFO_FILENAME);
        $fileName =   $file_extension . '-' . uniqid() . '.webp';
  
        $path =  $carpetaRaizOrganismo . "/" . $fileName;
  
        $imagen = Image::make($file);
  
        Storage::disk('local')->put($path, $imagen->encode('webp', 90));
  
  
        // obtener la ruta del archivo para generar hash
        $path1 = Storage::disk('local')->path($path);
  
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
        $foja->created_at = new Carbon;
        $foja->updated_at = $foja->created_at;
  
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
  
          return response()->json("No se puede acceder al servidor de fojas", 500);
        }
  
        // Si no hubo problemas en el storage remoto, se guarda en base de datos la foja
        $foja->save();
      }
      return $expediente;
    }
    
  }


  public function createFojaPdf($parametros)
  {
     // Obtener el expediente  y calcular el numero de foja actual para crear nueva 
     $expediente = Expediente::where('expediente_num', $parametros['num_documento'])->where('organismos_id',$parametros['organismo'])->whereYear('created_at', $parametros['año'])->first();
     // $expediente 
 
     if ($expediente == null) {
       return null;
     }else{
       $nextFojaNumber = $expediente->fojas->count();
 
       // Carpeta raiz de la foja
       $carpetaRaizOrganismo = $expediente->organismos->id . "/" . $expediente->id;
 
       $files = $this->splitPdfToImagesApi($parametros['content']);
       // una variable para controlar el numero de iteracion actual del for de los files
       $iteracion = 0;
   
       //recorrer el array de archivos 
       foreach ($files as $file) {
   
         $iteracion += 1;
         //generar nombre de archivo unico dentro del expediente
         $file_nombre = $file->getClientOriginalName();
         $file_nombre = control_nombre($file_nombre);
         $file_extension = pathinfo($file_nombre, PATHINFO_FILENAME);
         $fileName =   $file_extension . '-' . uniqid() . '.webp';
   
         $path =  $carpetaRaizOrganismo . "/" . $fileName;
   
         $imagen = Image::make($file);
   
         Storage::disk('local')->put($path, $imagen->encode('webp', 90));
   
   
         // obtener la ruta del archivo para generar hash
         $path1 = Storage::disk('local')->path($path);
   
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
         $foja->created_at = new Carbon;
         $foja->updated_at = $foja->created_at;
   
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
   
           return response()->json("No se puede acceder al servidor de fojas", 500);
         }
   
         // Si no hubo problemas en el storage remoto, se guarda en base de datos la foja
         $foja->save();
       }
       return $expediente;
     }
  }


}
