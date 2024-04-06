<?php

namespace App\Traits;

use ZipArchive;
use App\Detallesadjunto;
use Illuminate\Support\Facades\Storage;

trait AdjuntarArchivos
{
  /*
    Este método se encarga de comprimir los archivos seleccionados por el usuario en un archivo ZIP para luego
    ser guardados en el minio
  */
  public function comprimirArchivos($archivos, $nombreZip, $carpetaRaizOrganismo)
  {
    // se instancia un objeto de la clase ZipArchive
    $zip = new ZipArchive;

    // el path está conformado por: organismo_id/expediente_id/nombre_zip
    $path = $carpetaRaizOrganismo . DIRECTORY_SEPARATOR . $nombreZip;

    // se obtiene el directorio donde se va a guardar temporalmente el ZIP y se ubica en la carpeta storage/app/{organismo_id}/{expediente_id}
    $path_local = Storage::disk('local')->path($path);
    
    // se abre el archivo en modo de creación con permisos de lectura y escritura
    $newreg = $zip->open($path_local, ZipArchive::CREATE);

    // si el zip se abre correctamente, se procede a agregar los archivos
    if ($newreg === TRUE) {

      for($i = 0; $i < count($archivos); $i++) {
        $source = $archivos[$i]->getPathName();
  
        // para agregar los archivos al zip se le pasa 2 parametros: el path del archivo y el nombre del mismo
        $zip->addFile($source, $archivos[$i]->getClientOriginalName());
      }

      // una vez agregados todos los archivos, se cierra el zip
      $zip->close();
    }

    return $path_local;
  }


  /*
    Este metodo se encarga de verificar el tamaño del ZIP antes de subirlo. Si el mismo es mayor a 30MB, se quita del directorio public
    y se devuelve un mensaje de error.
  */
  public function tamanioArchivo($path) {
    // se obtiene el tamaño del archivo en bytes y se lo converte a MB
    $size = filesize($path)/1024/1024;

    if ($size > 30) {
      unlink($path);

      return true;
    } else {
      return false;      
    }
  }

  /*
    Este metodo se encarga de filtrar la lista de archivos adjuntos para que solo se muestren los adjuntos que tengan estado activo
  */
  public function adjuntosActivos($adjuntos) {
    $activo = 1;

    $adjuntos = $adjuntos->filter(function($adjunto) use ($activo) {
      return $adjunto->activo == $activo;
    });

    return $adjuntos;
  }

  /*
    Este metodo se encarga de guardar el detalle de los archivos que se adjuntaron al documento para luego mostrar el nombre de archivo
    contenido en el ZIP en la vista
  */
  public function guardarDetalle($archivos, $adjunto_id) {
    for($i = 0; $i < count($archivos); $i++) {
      $detalle_adjunto = new Detallesadjunto;
      $detalle_adjunto->expedientesadjuntos_id = $adjunto_id;
      $detalle_adjunto->nombre = $archivos[$i]->getClientOriginalName();
      $detalle_adjunto->save();
    }

    return true;
  }

}