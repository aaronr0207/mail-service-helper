<?php

namespace Aaronr0207\MailServiceHelper;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class GuzzleConfigurator
{
    public function configure(array $custom_options = [])
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->push(Middleware::retry(function($retry, $request, $response, $exception) {
            // Si la respuesta es 429, entonces reintenta
            if ($response && $response->getStatusCode() === 429) {
                sleep( env('MAIL_SERVICE_URI',3)); // esperar X segundos
                return true;
            }
            return false;
        }));

        $default_options = [
            'base_uri' => env('MAIL_SERVICE_URI','localhost'),
            'headers' => [
                'X-API-KEY' => ENV('MAIL_SERVICE_API_KEY','******'),
                'Content-Type' => 'multipart/form-data'
            ],
            'handler' => $handlerStack
        ];



        // Aquí puedes agregar cualquier lógica de configuración personalizada.
        // Por ejemplo, podrías agregar opciones predeterminadas o leer
        // configuración de un archivo.
        $options = array_merge($default_options,$custom_options);
        return new Client($options);
    }
}