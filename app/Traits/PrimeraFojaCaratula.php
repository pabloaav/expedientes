<?php

namespace App\Traits;

use App\Expediente;
use Carbon\Carbon;
use App\Localidad;
use PDF;
use Illuminate\Support\Facades\File;

trait PrimeraFojaCaratula
{
  /**
   * encrypt_decrypt
   *
   * @param  mixed $data La informacion a cifrar o descifrar
   * @param  mixed $action Cifrar o descifrar segun sea la accion deseada
   */
  public function crearPrimerFojaCaratula($id)
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


    $expediente = Expediente::findOrFail($id);

    //obtener la localidad 
    $expediente_organismo = Expediente::find($id)->organismos->localidads_id;
    $organismo_localidad = Localidad::find($expediente_organismo);

    $fecha = Carbon::parse($expediente->fecha_inicio);
    $afecha = $fecha->year;

    // dd($afecha)


    PDF::SetTitle('Documento');

    PDF::AddPage();
    $bMargin = PDF::getBreakMargin();
    $auto_page_break = PDF::getAutoPageBreak();
    PDF::SetAutoPageBreak(false, 0);
    PDF::Image('images/caratula.jpg', 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
    PDF::SetAutoPageBreak($auto_page_break, $bMargin);
    PDF::setPageMark();


    // $fecha = $expediente->created_at;
    // $hora = $expediente->created_at;
    if ($expediente->expedientetipo->color != null) {
      $hex = $expediente->expedientetipo->color;
      list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");

      PDF::SetFillColor($r, $g, $b);
      PDF::Rect(190, 10, 8, 80, 'F');
      //  PDF::Rect(118, 75, 100 , 8, 'F');
    }


    PDF::SetFont('helvetica', '', 7);
    // PDF::write2DBarcode($expediente->expediente_num, 'QRCODE,L', 85, 170, 35, 35, $style, 'N');
    // PDF::write2DBarcode(getExpedienteName($expediente), 'QRCODE,L', 85, 170, 35, 35, $style, 'N');
    PDF::write2DBarcode(getExpedienteName($expediente), 'QRCODE,L', 85, 193, 35, 35, $style, 'N');

    PDF::SetFont('helvetica', 'B', 8);
    PDF::MultiCell(120, 5, $fecha->format('d-m-Y'), 0, 'L', 0, 1, 42, 32, true);

    PDF::SetFont('helvetica', 'B', 15);
    PDF::MultiCell(120, 5, $expediente->organismos->organismo, 0, 'C', 0, 1, 50, 70, true);

    PDF::SetFont('helvetica', 'B', 12);
    PDF::MultiCell(120, 5, getExpedienteName($expediente), 0, 'L', 0, 1, 48, 109, true);
    PDF::MultiCell(120, 5, $expediente->organismos->organismo, 0, 'L', 0, 1, 50, 120, true);

    PDF::MultiCell(120, 5, $expediente->expediente, 0, 'L', 0, 1, 52, 131, true);
    // PDF::MultiCell(120, 5, $organismo_localidad->localidad, 0, 'L', 0, 1, 53, 142, true);
    PDF::MultiCell(120, 5, $organismo_localidad->localidad, 0, 'L', 0, 1, 53, 167, true);
    // PDF::MultiCell(120, 5, $fecha->format('d-m-Y'), 0, 'L', 0, 1, 55, 156, true);
    PDF::MultiCell(120, 5, $fecha->format('d-m-Y'), 0, 'L', 0, 1, 55, 179, true);

    // PDF::MultiCell(120, 5, $afecha, 0, 'L', 0, 1, 103, 206, true);
    PDF::MultiCell(120, 5, $afecha, 0, 'L', 0, 1, 103, 229, true);

    // La carpeta de ubicacion es el id del organismo y el id del expediente
    $carpeta = $expediente->organismos->id . DIRECTORY_SEPARATOR . strval($expediente->id);
    // La ubicacion contiene o implica el codigo del organismo que actua al crear el expediente, o al que pertenece el expediente
    $ubicacion = storage_path('app') . DIRECTORY_SEPARATOR . $carpeta;
    if (File::makeDirectory($ubicacion)) {
      $filePathAndName = $carpeta . DIRECTORY_SEPARATOR . "caratula_" . $expediente->expediente_num . ".pdf";
      PDF::Output(storage_path('app') . DIRECTORY_SEPARATOR . $filePathAndName, 'F');
    }

    return $filePathAndName;
  }
}
