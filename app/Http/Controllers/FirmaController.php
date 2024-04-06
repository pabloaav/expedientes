<?php

namespace App\Http\Controllers;

use App\Logg;
use App\Traits\SignatureTrait;
use Illuminate\Http\Request;

class FirmaController extends Controller
{
  use SignatureTrait;
  /**
   * castear la request a un objeto foja o firmada
   * guardar en la base de datos
   * guardar en el servidor de fojas
   * En parametros se reciben objetos en posiciones de un array, tantos como documentos se ayan firmado
   * cada documento tiene id y el contenido en base64
   * El id es el id de la foja que se envio para firmar
   * 
   */
  public function recibirFirmada(Request $request)
  {
    /**
     * Example de Request. Es un array de arrays, cada uno representa una foja firmada.
     * [
     * ["Id": "1", "Contenido": "abc"],
     * ["Id": "2", "Contenido": "def"],
     * ]
     */
    Logg::infoSoloTexto("Ingresa en la funcion recibir firmada");
    $firmadas = $request->all();
    // obtenemos los valores de id de la request. Cada id corresponde a una foja firmada.
    // el id es el mismo que se envia en la peticion de firma de cada foja
    if ($this->verificarFirmadas(array_column($firmadas, 'Id'))) {
      // proceso de trasformar el contenido de cada foja firmada en un pdf y guardar en el object server
      Logg::infoSoloTexto("Procesa con exito las fojas firmadas");
      return $this->procesarFirmadas($firmadas);
    } else {
      Logg::infoSoloTexto("Ocurre un error al procesar las fojas firmadas");
      return response()->json(['Los datos de las fojas no se encuentran en la base de datos.'], 422);
    }
  }
}
