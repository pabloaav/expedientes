<?php

namespace App\Repositories;

use App\Foja;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class Firmador
{
  protected $client;

  public function __construct(Client $client)
  {
    $this->client = $client;
  }

  public function getAccessToken()
  {
    $url = config('configuraciones.ACCESS_TOKEN_URL');
    $params = ['grant_type' => 'client_credentials'];
    $uri = new Uri($url);
    $query_string_params = http_build_query($params);
    $uri_query = $uri->withQuery($query_string_params);
    $client_id = config('configuraciones.client_id');
    $cliente_secret = config('configuraciones.cliente_secret');

    $headers = [
      'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $cliente_secret),
      'Accept' => 'application/json',
    ];

    // Esto es una GuzzleHttp\Psr7\Request 
    $request = new Request('POST', $uri_query, $headers);
    $response = $this->client->send($request);

    $bodyResponse =  $response->getBody();
    $dataBodyResponse = $bodyResponse->getContents();

    return json_decode($dataBodyResponse);
  }


  public function enviarDocumentoDotnetService($cuil, $ubicacion_documento, $token, $fojas)
  {
    $url = config('configuraciones.DOTNET_SIGNATURE_SERVICE_ENDPOINT');

    $array_documentos = array();
    $nombres_fojas_firmar = [];

    foreach ($fojas as $key => $foja) {

      $full_path_to_file = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $ubicacion_documento[$key];
      $contenido_documento = file_get_contents($full_path_to_file);
      $base64_documento = base64_encode($contenido_documento);
      // El nombre del archivo sin su extension
      $nombre_completo_sin_extension = pathinfo(basename($full_path_to_file), PATHINFO_FILENAME);
      // almacenar los nombre temporales de las fojas a firmar en un array para procesar posteriormente
      $nombres_fojas_firmar[$foja->id] = basename($full_path_to_file);

      array_push($array_documentos, [
        "Id" => strval($foja->id),
        "Nombre" => $nombre_completo_sin_extension,
        "Contenido" => $base64_documento
      ]);
    } // Fin del foreach $fojas


    $headers = [
      'Content-Type' => 'application/json;charset=UTF-8',
      'Authorization' => 'Bearer ' . $token,
      'Accept' => 'application/json'
    ];

    // este helper devuelve la ruta a la vista de firmar fojas de un expediente
    $ruta = route("firmar.index", base64_encode($foja->expedientes_id));

    $rawBody = [
      "Cuil" => $cuil,
      "RutaRedireccion" => $ruta,
      "Documentos" => $array_documentos,
    ];

    // se forma la request para el servicio .net de firma
    $request = new Request('POST', $url, $headers, json_encode($rawBody));
    // Enviar el archivo al servicio .net de firma y esperar la URI para redireccionar al usuario
    // a la plataforma de firma digital donde coloca su pin o private key para el proceso de firma del hash del pdf

    try {
      $response = $this->client->send($request);
      if ($response && $response->getStatusCode() == 200) {
        $this->guardarFirmada($array_documentos, $nombres_fojas_firmar, $cuil);
      }
      return $response;
    } catch (RequestException $e) {
      return $e->getResponse();
    }
  }

  /**
   * Registra en la tablas firmadas las fojas que esperan confirmacion de firma
   *
   * @param  mixed $array_documentos
   * @param  mixed $nombres_fojas_firmar
   * @param  mixed $cuil
   * @return boolean
   */
  protected function guardarFirmada($array_documentos, $nombres_fojas_firmar, $cuil)
  {
    $fojas = Foja::whereIn('id', array_column($array_documentos, 'Id'))->get();

    $ids_firmadas = [];
    foreach ($fojas as $key => $foja) {
      // Ejemplo: ME011/32/firmadas/193/firma_nota_modelo-6166b3bc92a80.pdf
      $path = pathinfo($foja->path, PATHINFO_DIRNAME) . '/firmadas' . '/' . $foja->id . '/' . $nombres_fojas_firmar[$foja->id];

      // Ejemplo: http://207.246.76.67:9000/sied/ME011/31/firmadas/190/firma_Nota.pdf1-6165d1ba25763.pdf
      // $full_minio_server_url = config('configuraciones.OBJECT_STORE_ENDPOINT') . '/' . config('configuraciones.AWS_BUCKET') . '/' . $path;
      $full_minio_server_url = env('MINIO_ENDPOINT') . '/' . env('AWS_BUCKET') . '/' . $path;

      // Se guarda la foja firmada con un estado pendiente
      $id = DB::table('firmadas')->insertGetId([
        'foja_id' => $foja->id,
        'cuil' => $cuil,
        'path' => $path,
        'url' => $full_minio_server_url,
        'user_id' =>  auth()->user()->id, // Asociar la foja firmada con el firmante
        'fecha_envio' => now(),
      ]);
      array_push($ids_firmadas, $id);
    } // Fin de foreach de cada foja a ser firmada en la tabla firmadas de la base de datos

    $all_saved = count($fojas) == count($ids_firmadas);
    return $all_saved;
  }
}
