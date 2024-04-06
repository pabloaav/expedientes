<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface FojasInterfaces
{
  public function primeraFoja($documento_nuevo);
  public function createFojaTexto($parametros);
  public function createFojaImagen($parametros);
  public function createFojaPdf($parametros);
}
