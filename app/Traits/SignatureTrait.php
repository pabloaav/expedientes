<?php

namespace App\Traits;

use App\Firmada;
use App\Logg;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait SignatureTrait
{
  public function verificarFirmadas(array $ids_firmadas)
  {
    foreach ($ids_firmadas as $foja_id) {
      if (DB::table('firmadas')->where('foja_id', $foja_id)->doesntExist()) {
        // entra cuando encuetra una foja_id que no esta en la tabla firmadas
        // en ese caso se debe devolver falso en la verificacion
        return false;
      }
    }
    // Si todos los id estan en la tabla firmadas retorna verdadero
    return true;
  }

  /**
   * Tomar el contenido de cada archivo de foja firmada, y guardar en el object storage
   *
   * @param  mixed $firmadas
   * @return void
   */
  public function procesarFirmadas(array $firmadas)
  {
    foreach ($firmadas as $firmada) {

      try {
        // Storage::disk('local')->put('example.pdf',  base64_decode($contenido));
        $firmadaModel = Firmada::where('foja_id', $firmada['Id'])->get()->last();
        $contenido = $firmada['Contenido'];
        $success =  Storage::cloud()->put($firmadaModel->path, base64_decode($contenido));
        if (!$success) {
          throw new \Exception('error de object cloud service');
        } else {
          // cuando tuvo exito la insercion del objeto foja firmada en el object storage, se procede a actualizar el registro de la base de datos correspondiente a esa foja firmada
          $firmadaModel->estado = 'FIRMADA';
          $firmadaModel->fecha_firma = now();
          $firmadaModel->contenido = $contenido;
          $firmadaModel->save();
        }
      } catch (\Exception $e) {
        Logg::infoSoloTexto("Error en procesar foja firmada: " . $e->getMessage());
        return false;
      }
    }
    return true;
  }
}
