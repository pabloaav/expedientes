<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Org_Heigl\Ghostscript\Ghostscript;
use setasign\Fpdi\Tcpdf\Fpdi;
use SplFileInfo;
use Intervention\Image\Facades\Image;

class FileHandlingTraitTest extends TestCase
{
  public function test_singlePdfToImage(String $p_patToPdf, String $p_filename = "prueba")
  {
    //Establecer una ruta a un pdf de prueba
    $p_patToPdf = 'C:\development\expedientes\public\pdfs\split.pdf';

    // getClientOriginalName() incluye la extension .pdf en el nombre del archivo. Ex: inputfile.pdf
    $filename = $p_filename;
    $source = $p_patToPdf;

    $pdf = new Fpdi();

    // numero de paginas del archivo
    $pages =  $pdf->setSourceFile($source);

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


    if (true === $gs->render()) {
      $url = $gs->getOutputFileName();
      $info = new SplFileInfo($url);
      $extension = $info->getExtension();
      return $new_filename  . "." . $extension;
    }
  }

  public function test_EncodeWebpResolution($source)
  {
    $imagen = Image::make($source);

    if (Storage::put($imagen->encode('webp', 90))) {
      return true;
    }

    return false;
  }
}
