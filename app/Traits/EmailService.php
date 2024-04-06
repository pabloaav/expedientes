<?php

namespace App\Traits;

use App\Logg;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Support\Facades\DB;

trait EmailService
{
    public function emailAdjunto($path, $expediente, $emails, $pdfcontent)
    {
        $asunto = "Notificación de expediente";
        $cuerpo = "Se te ha compartido el expediente ". getExpedienteName($expediente) ." perteneciente al organismo ". $expediente->organismos->organismo .".";
        $attachment = json_encode([
            'Name' => getExpedienteName($expediente).'.pdf',
            'ContentType' => 'application/pdf',
            'WithFile' => true
        ]);

        $endpoint = env('MAIL_SERVICE') ."/emails/enviar-email";
        $apikey = env('APIKEY_MAILSERVICE');

        $client = new Client();
        $from = 'DOCO';

        try{
            $respuesta = $client->request(
                'POST',
                $endpoint,
                [   
                    'headers' => [
                        // 'Content-Type' => 'application/json',
                        'ApiKey' => $apikey
                    ],
                    'multipart' => [
                        [
                            'name' => 'Asunto',
                            'contents' => $asunto,
                        ],
                        [
                            'name' => 'Email',
                            'contents' => json_encode($emails),
                        ],
                        [
                            'name' => 'From',
                            'contents' => $from,
                        ],
                        [
                            'name' => 'Nombre',
                            'contents' => '',
                        ],
                        [
                            'name' => 'Mensaje',
                            'contents' => $cuerpo,
                        ],
                        [
                            'name' => 'CamposReemplazar',
                            'contents' => json_encode([]),
                        ],
                        [
                            'name' => 'AdjuntarEstado',
                            'contents' => 'true',
                        ],
                        [
                            'name' => 'TipoEmail',
                            'contents' => 'mixto',
                        ],
                        [
                            'name' => 'NombreTemplate',
                            'contents' => 'doco_notificacion',
                        ],
                        [
                            'name' => 'InformarPago',
                            'contents' => 'false',
                        ],
                        [
                            'name' => 'Archivos',
                            'contents' => $pdfcontent,
                            'Mime-Type' => 'application/pdf',
                            'filename' => getExpedienteName($expediente).'.pdf'
                        ],
                        [
                            'name' => 'Attachment',
                            'contents' => $attachment
                        ]
                    ]
                ]
            );

            $info = json_decode($respuesta->getBody(), true);
            unlink($path);
            // return $info;
        } catch (\ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $obj = json_decode($responseBodyAsString);
            unlink($path);

            // return $obj;
        }
    }
}