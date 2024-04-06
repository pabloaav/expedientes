<?php

namespace App\Traits;

use App\Firmada;
use PDF;
use Illuminate\Database\Eloquent\Collection;
use setasign\Fpdi\Tcpdf\Fpdi;
use Org_Heigl\Ghostscript\Ghostscript;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use SplFileInfo;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use App\Logg;
use Dompdf\Dompdf;
use Symfony\Component\Filesystem\Filesystem;


trait FilehandlingTrait
{

  /**
   * Transforma un pdf de mime application/octet-stream en un pdf que se puede procesar
   * @param  UploadedFile $uploadedFile
   * @return UploadedFile $newUploadedFile
   */
  public function transformar(UploadedFile $uploadedFile)
  {
    // initiate FPDI
    $pdf = new Fpdi();
    // Como Fpdi extiende TCPDF, esta ultima pone una linea negra por defecto en el header. Evitamos eso con:
    $pdf->setPrintHeader(false);
    // La ubicacion temporal del archivo subido en el servidor
    $source = $uploadedFile->getPathName();
    // get the page count
    $pageCount = $pdf->setSourceFile($source);

    // iterate through all pages
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
      // import a page
      $templateId = $pdf->importPage($pageNo);
      // get the size of the imported page
      $size = $pdf->getTemplateSize($templateId);
      // create a page (landscape or portrait depending on the imported page size)
      if ($size['width'] > $size['height']) {
        $pdf->AddPage('L', array($size['width'], $size['height']));
      } else {
        $pdf->AddPage('P', array($size['width'], $size['height']));
      }
      // use the imported page
      $pdf->useTemplate($templateId);
    }
    // Damos un nuevo nombre y extension al archivo transformado
    $path_parts = pathinfo($source);
    $ruta_archivo_sin_nombre_archivo = $path_parts['dirname'];
    $nombre_archivo_sin_extension = $path_parts['filename'];
    // $extension = $path_parts['extension'];
    $extension = ".pdf";
    $nuevo_nombre_archivo =  $nombre_archivo_sin_extension . "_transform" . $extension;
    // filename es la direccion completa donde se guarda el archivo, incluido el nombre del archivo
    $filename = $ruta_archivo_sin_nombre_archivo . DIRECTORY_SEPARATOR . $nuevo_nombre_archivo;
    // guardar el archivo pdf transformado en la carpeta temporal del servidor
    $pdf->Output($filename, 'F');
    // Devolvemos el archivo transformado como un uploaded file
    $newUploadedFile = new UploadedFile($filename, $nuevo_nombre_archivo, 'application/pdf', filesize($filename), TRUE);
    return $newUploadedFile;
  }


  /**
   * Verifica si un archivo pdf tiene la bandera Encrypt
   * Verifica si el pdf está protegido
   *
   * @param  mixed $file Un archivo de pdf subido para ser tratado como foja
   * @return boolean
   */
  public function checkIfEncrypted($file)
  {
    $fileContent = file_get_contents($file->getRealPath());

    if (stristr($fileContent, "/Encrypt")) {
      return true;
    } else {
      return false;
    }
  }


  /**
   * splitPdfToImages
   * Recibe un Uploaded File
   * Por cada pagina del archivo pdf, genera una imagen de la pagina
   * Devuelve un array con la ubicacion de cada imagen generada
   * @param UploadedFile $uploadedFile
   * @return array Una lista de url a la carpeta temporal del server donde estan las imagenes resultantes del proceso
   */
  public function splitPdfToImages(UploadedFile $uploadedFile)
  {

    // getClientOriginalName() incluye la extension .pdf en el nombre del archivo. Ex: inputfile.pdf
    $filename = $uploadedFile->getClientOriginalName();
    $filename = control_nombre($filename);
    $source = $uploadedFile->getPathName();
    $path_gs = env('GS_PATH');
    $pdf = new Fpdi();
    try {
      // numero de paginas del archivo
      $pages =  $pdf->setSourceFile($source);
    } catch (\Exception $e) {

      if ($e instanceof \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException) {
        $nombre_sin_extension = pathinfo($source, PATHINFO_FILENAME);
        $extension = pathinfo($source, PATHINFO_EXTENSION);
        // $nombre_modificado = $nombre_sin_extension . '_modificado.' . $extension;
        $nombre_modificado = $nombre_sin_extension . '_modificado.pdf';
        $ruta_modificado = pathinfo($source, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $nombre_modificado;
        shell_exec("$path_gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$ruta_modificado $source");
      }
      $source = $ruta_modificado;
      // numero de paginas del archivo
      $pages =  $pdf->setSourceFile($source);
    }


    try {
      Ghostscript::setGsPath($path_gs);

      // Create the Ghostscript-Wrapper
      $gs = new Ghostscript();
      // Set the output-device
      $gs->setDevice('jpeg')
        // Set the input file
        ->setInputFile($source)
        // Set the output file that will be created in the same directory as the input
        ->setOutputFile($filename . '%d.jpeg')
        // Set the resolution to 96 pixel per inch
        ->setResolution(150)
        // Set Text-antialiasing to the highest level
        ->setTextAntiAliasing(Ghostscript::ANTIALIASING_HIGH);

      if (true === $gs->render()) {
        $arrayImagesUploaded = [];
        $url = $gs->getOutputFileName();
        $rutaTemporal = pathinfo($url)['dirname'];
        $info = new SplFileInfo($url);
        $extension = $info->getExtension();

        for ($i = 1; $i <= $pages; $i++) {
          // componer la url temporal donde esta cada archivo de imagen del pdf subido por el usuario
          $urlCompuesta =  $rutaTemporal . DIRECTORY_SEPARATOR . $filename . $i . "." . $extension;
          $imageFileUploaded = $this->getFile($urlCompuesta);
          array_push($arrayImagesUploaded, $imageFileUploaded);
        }

        return $arrayImagesUploaded;
      }
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      echo 'Ocurrio la siguiente excepcion: ',  $e->getMessage(), "\n";
    }
  }

  /**
   * getFile
   * Funcion auxiliar que transforma un archivo desde una url, en el tipo de objeto UploadedFile
   * @param  mixed $urlCompuesta
   * @return UploadedFile 
   */
  public function getFile($urlCompuesta)
  {
    //get name file by url and save in object-file
    $path_parts = pathinfo($urlCompuesta);
    /* //get image info (mime, size in pixel, size in bits)
    $newPath = $path_parts['dirname'] . '/tmp-files/';
    if (!is_dir($newPath)) {
      mkdir($newPath, 0777);
    }
    $newUrl = $newPath . $path_parts['basename'];
    copy($urlCompuesta, $newUrl); */
    $imgInfo = getimagesize($urlCompuesta);
    $file = new UploadedFile($urlCompuesta, $path_parts['basename'], $imgInfo['mime'], filesize($urlCompuesta), TRUE);
    return $file;
  }

  public function splitPdfToImages2($path, $nombrepdf)
  {

    $filename = $nombrepdf;
    $source = $path;

    $pdf = new Fpdi();

    // numero de paginas del archivo
    $pages =  $pdf->setSourceFile($source);

    try {
      Ghostscript::setGsPath(env('GS_PATH'));

      // Create the Ghostscript-Wrapper
      $gs = new Ghostscript();
      // Set the output-device
      $gs->setDevice('jpeg')
        // Set the input file
        ->setInputFile($source)
        // Set the output file that will be created in the same directory as the input
        ->setOutputFile($filename . '%d.jpeg')
        // Set the resolution to 96 pixel per inch
        ->setResolution(150)
        // Set Text-antialiasing to the highest level
        ->setTextAntiAliasing(Ghostscript::ANTIALIASING_HIGH);

      if (true === $gs->render()) {
        $arrayImagesUploaded = [];
        $url = $gs->getOutputFileName();
        $rutaTemporal = pathinfo($url)['dirname'];
        $info = new SplFileInfo($url);
        $extension = $info->getExtension();

        for ($i = 1; $i <= $pages; $i++) {
          // componer la url temporal donde esta cada archivo de imagen del pdf subido por el usuario
          $urlCompuesta =  $rutaTemporal . DIRECTORY_SEPARATOR . $filename . $i . "." . $extension;
          $imageFileUploaded = $this->getFile($urlCompuesta);
          array_push($arrayImagesUploaded, $imageFileUploaded);
        }

        return $arrayImagesUploaded;
      }
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      echo 'Ocurrio la siguiente excepcion: ',  $e->getMessage(), "\n";
    }
  }

  // Esta funcion no se usa por el momento. Dejo el codigo como referencia
  /* public function splitPdfToPdfs(Request $request)
  {
    $pdfFile = $request->file('file');
    // initiate FPDI
    $pdf = new Fpdi();
    $filename = $pdfFile->getClientOriginalName();
    $source = $pdfFile->getPathName();

    // set the source file y obtener el numero de paginas
    $pages =  $pdf->setSourceFile($source);

    // recorremos tantan veces como paginas existen 
    for ($i = 1; $i <= $pages; $i++) {
      $new_pdf = new Fpdi();
      // add a page
      $new_pdf->AddPage();
      $new_pdf->setSourceFile($source);
      $new_pdf->useTemplate($new_pdf->importPage($i));
      try {
        // El directorio donde se guardan los pdf individuales
        $end_directory = public_path("pdfs\\");
        $new_filename = $end_directory . str_replace('.pdf', '', $filename) . '_' . $i . ".pdf";
        $new_pdf->Output($new_filename, "F");
      } catch (\Exception $e) {
        Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
        echo 'Ocurrio la siguiente excepcion: ',  $e->getMessage(), "\n";
      }
    }
  } */

  /**
   * singlePdfToImage
   * Convierte un archivo pdf en una imagen. La imagen se almacena en el mismo origen o ruta,
   * desde donde se toma el archivo pdf a ser convertido en imagen
   * @param  String $p_patToPdf Ruta del pdf a ser transformado en imagen
   * @param  String $p_filename Nombre del archivo pdf a ser transformado en imagen
   * @return String Retorna el nombre de la foja de tipo imagen con su extension tipo imagen 
   */
  public function singlePdfToImage(String $p_patToPdf, String $p_filename)
  {

    // getClientOriginalName() incluye la extension .pdf en el nombre del archivo. Ex: inputfile.pdf
    $filename = $p_filename;
    $source = $p_patToPdf;

    $pdf = new Fpdi();

    // numero de paginas del archivo
    $pages =  $pdf->setSourceFile($source);

    try {
      // Ghostscript::setGsPath('C:\Program Files\gs\gs9.54.0\bin\gswin64c.exe');
      Ghostscript::setGsPath(env('GS_PATH'));

      // En este caso, quitamos la extension .pdf para formar el filename del nuevo archivo de imagen que procede del pdf
      $new_filename = str_replace(".pdf", "_" . uniqid(), $filename);

      // Create the Ghostscript-Wrapper
      $gs = new Ghostscript();
      // Set the output-device
      $gs->setDevice('jpeg')
        // Set the input file
        ->setInputFile($source)
        // Set the output file that will be created in the same directory as the input
        ->setOutputFile($new_filename . '.jpeg')
        // Set the resolution to 96 pixel per inch
        ->setResolution(150)
        // Set Text-antialiasing to the highest level
        ->setTextAntiAliasing(Ghostscript::ANTIALIASING_HIGH);
      $device = $gs->getDevice();

      if (true === $gs->render()) {

        $url = $gs->getOutputFileName();
        $info = new SplFileInfo($url);
        $extension = $info->getExtension();
        return $new_filename  . "." . $extension;
      }
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      echo 'Ocurrio la siguiente excepcion: ',  $e->getMessage(), "\n";
    }
  }

  /**
   * imageToPdf 
   * Busca una imagen en el remote object storage y la convierte en archivo pdf
   * @param  mixed $p_path La ruta donde se almacena la imagen de la foja en el remote object storage
   * @param  mixed $p_name
   * @return Strin $filePathAndName Retorna la ruta al storage de la app donde se almacena el archivo pdf
   */
  public function imageToPdf($p_path, $p_name, $cuil = "", $fecha = "")
  {
    // la variable carpeta es donde se guardan las fojas pdf para firmar
    $carpeta = pathinfo($p_path, PATHINFO_DIRNAME) . '/' . 'firmar_' . now()->format('d-m-Y');
    //la variable ubicacion es una ruta full desde la raiz del servidor a la carpeta de fojas a firmar
    $ubicacion = storage_path('app') . '/' . $carpeta;

    // quitar la extension de imagen de la foja
    $filename = pathinfo($p_name, PATHINFO_FILENAME);


    // Craer un pdf a partir de la imagen foja del servidor de archivos

    PDF::Reset();
    PDF::AddPage();
    PDF::SetTitle('Foja Firmada Digitalmente');
    PDF::Image('@' . Storage::cloud()->get($p_path), 0, 0, 210, 0, '', '', 'center', false, 300, '', false, false, 0);
    PDF::SetAutoPageBreak(false, 0);
    PDF::SetKeywords('PDF, Firmado Digitalmente');
    // Page footer
    // Position at 15 mm from bottom
    PDF::SetY(-15);
    // Set font
    PDF::SetFont('helvetica', '', 8);
    // $name = auth()->user()->name;
    // $mail = auth()->user()->email;
    $image = '<img src="/images/signature.png"  width="15" height="15">';
    // Custom footer HTML
    if ($cuil !== "" && $fecha !== "") {
      $html = "<hr><br><span>{$image} Firmado Digitalmente por {$cuil} - {$fecha}</span><br>";  
    }
    else {
      $html = "<hr><br><span>{$image} Firmado Digitalmente </span><br>";
    }
    PDF::writeHTML($html, true, false, true, false, '');

    if (!file_exists($ubicacion)) {
      // Crear una carpeta donde se almacena la foja a firmar
      if (File::makeDirectory($ubicacion, 0777, true)) {
        $filePathAndName = $carpeta . '/' . 'firma_' . $filename . '.pdf';
        $salida = PDF::Output(storage_path('app') . '/' . $filePathAndName, 'F');
      }
    } else {
      // la carpeta o ubicacion ya existe. No es necesaro crearla. solo referir a la ubicacion y poner el nombre del archivo
      $filePathAndName = $carpeta . '/' . 'firma_' . $filename . '.pdf';
      $salida = PDF::Output(storage_path('app') . '/' . $filePathAndName, 'F');
    }

    // si la salida no es nula, es porque se pudo hacer la operacion
    if (!is_null($salida)) {
      return $filePathAndName;
    } else {
      return false;
    }
  }

  /**
   * fojasImageToPdf
   * Toma la ubicacion de las imagenes de fojas almacenadas en el remote object storage,
   * y devuelve las ubicaciones de esas fojas en el storage local, convertidas en tipo archivo pdf
   * Devuelve un array con strings que son la/s ubicacion/es de las fojas en una carpeta temporal
   * @param  Collection $fojas Coleccion de objetos de tipo Foja
   * @return array Lista de rutas donde se almacena la/s foja/s convertidas en pdf
   */
  public function fojasImageToPdf(Collection $fojas, $cuil, $fecha)
  {
    $ubicaciones_fojas = array();

    foreach ($fojas as $foja) {
      $ubicacion = $this->imageToPdf($foja->path, $foja->nombre, $cuil, $fecha);
      // guardamos las ubicaciones donde se crean estas fojas a partir de las imagenes del server
      array_push($ubicaciones_fojas, $ubicacion);
    }

    return $ubicaciones_fojas;
  }

  /**
   * getAndStoreFirmada
   * Busca en la tabla de la base de datos la ruta de una foja firmada.
   * Si encuentra esa foja firmada, obtiene su contenido del remote object storage.
   * Guarda en el Storage local de la app, el archivo pdf de la foja firmada, usando la misma ruta relativa de la foja
   * @param  mixed $firmada
   * @return string
   */
  public function getAndStoreFirmada(Firmada $firmada)
  {
    // si la foja ya fue firmada, se debe firmar la misma foja que incluye esa firma previa
    // traemos la foja firmada desde el servidor al disco local
    if (Storage::disk('minio')->exists($firmada->path)) {
      // obtenemos el contenido de la foja
      $contenido_firmada = Storage::cloud()->get($firmada->path);
      // guardamos en la misma ubicacion que tiene la foja en su path, dentro del disco local
      if (Storage::disk('local')->put($firmada->path, $contenido_firmada)) {
        $ubicacion_firmada = $firmada->path;
      }
      return $ubicacion_firmada;
    } else {
      throw new ResourceNotFoundException();
    }
  }


  /**
   * Generar un hash a partir de un archivo con el metodo sha256
   *
   * @param  mixed $hashData
   * @param  mixed $hashPrevio
   * @param  mixed $dateTimeCreated
   * @return void
   */
  protected function generarHashSHA256(String $hashData, $hashPrevio, $dateTimeCreated)
  {
    try {
      if ($dateTimeCreated === null) {
        $dateTimeCreated = '';
      }
      if ($hashPrevio === null) {
        $hashPrevio = "genesis";
      }
      return hash('sha256', $hashData . $hashPrevio . $dateTimeCreated);
    } catch (\Exception $e) {
      $e->getMessage();
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
    }
  }

  public function splitPdfToImagesApi($pdf)
  {
    $pdfFile = $pdf;
    // getClientOriginalName() incluye la extension .pdf en el nombre del archivo. Ex: inputfile.pdf
    $filename = $pdfFile->getClientOriginalName();
    $filename = control_nombre($filename);
    $source = $pdfFile->getPathName();
    $path_gs = env('GS_PATH');
    $pdf = new Fpdi();
    try {
      // numero de paginas del archivo
      $pages =  $pdf->setSourceFile($source);
    } catch (\Exception $e) {
      if ($e instanceof \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException) {
        $nombre_sin_extension = pathinfo($source, PATHINFO_FILENAME);
        $extension = pathinfo($source, PATHINFO_EXTENSION);
        // $nombre_modificado = $nombre_sin_extension . '_modificado.' . $extension;
        $nombre_modificado = $nombre_sin_extension . '_modificado.pdf';
        $ruta_modificado = pathinfo($source, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $nombre_modificado;
        shell_exec("$path_gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$ruta_modificado $source");
      }
      $source = $ruta_modificado;
      // numero de paginas del archivo
      $pages =  $pdf->setSourceFile($source);
    }
    try {
      Ghostscript::setGsPath(env('GS_PATH'));
      // Create the Ghostscript-Wrapper
      $gs = new Ghostscript();
      // Set the output-device
      $gs->setDevice('jpeg')
        // Set the input file
        ->setInputFile($source)
        // Set the output file that will be created in the same directory as the input
        ->setOutputFile($filename . '%d.jpeg')
        // Set the resolution to 96 pixel per inch
        ->setResolution(150)
        // Set Text-antialiasing to the highest level
        ->setTextAntiAliasing(Ghostscript::ANTIALIASING_HIGH);
      if (true === $gs->render()) {
        $arrayImagesUploaded = [];
        $url = $gs->getOutputFileName();
        $rutaTemporal = pathinfo($url)['dirname'];
        $info = new SplFileInfo($url);
        $extension = $info->getExtension();
        for ($i = 1; $i <= $pages; $i++) {
          // componer la url temporal donde esta cada archivo de imagen del pdf subido por el usuario
          $urlCompuesta =  $rutaTemporal . DIRECTORY_SEPARATOR . $filename . $i . "." . $extension;
          $imageFileUploaded = $this->getFile($urlCompuesta);
          array_push($arrayImagesUploaded, $imageFileUploaded);
        }
        return $arrayImagesUploaded;
      }
    } catch (\Exception $e) {
      Logg::error($e->getMessage(), ("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()));
      echo 'Ocurrio la siguiente excepcion: ',  $e->getMessage(), "\n";
    }
  }
}
