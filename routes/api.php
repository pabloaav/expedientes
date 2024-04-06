<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});

Route::get('/tipoexpediente/{id}', 'ExpedientestipoController@tipoexpedienteruta');

// route para recibir la request de la firma de .net
Route::post('/callback-firma', 'FirmaController@recibirFirmada')->name('recibirFirmada');



//ENDPOINT CONSULTAR SECTORES/USUARIOS , TIPODOCUMENTO SEGUN SECTOR -> PUEDE CREAR DOCUMENTOS .  ESTADO DE UN DOCUMENTO
Route::post('/login', 'ApiLoginController@login');
Route::group(['middleware' => ['jwt.verify']], function() {
  Route::post('/usuario-sectores', 'ApiController@usuarioSector');
  Route::post('/tipo-documentos', 'ApiController@tiposDocumentos');
  Route::post('/tipo-documentos/sector', 'ApiController@tiposDocumentosSector'); 
  Route::post('/documentos', 'ApiController@documentos');
  Route::post('/documentos-novedades', 'ApiController@documentosNovedades');
  Route::post('/documentos-leidos', 'ApiController@documentosLeidos');
  Route::post('/documento', 'ApiController@crearDocumento');
  Route::post('/crear-foja-texto', 'ApiController@storeFojaTexto');
  Route::post('/crear-foja-pdf', 'ApiController@storeFojaPdf');
  Route::post('/crear-foja-imagen', 'ApiController@storeFojaImagen');
  Route::post('/documento-personas', 'ApiController@documentosPersonas');
 /* CONSULTAR TIPO DE DOCUMENTO POR CODIGO */
  Route::post('/tipo-documento', 'ApiController@tipoDocumento');
  /* CONSULTAR ESTADO DE UN DOCUMENTO */
  Route::post('/estado-documento', 'ApiController@estadoDocumento');
  Route::post('/sector-actual-documento', 'ApiController@documentoSectorActual');
});


