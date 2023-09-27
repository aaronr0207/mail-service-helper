<?php

if (! function_exists('send_mail')) {
    function send_mail($emisor,$asunto,$vista,$parametros = [],$receptores,$copia = [],$copia_oculta = [])
    {
        $configurator = new \Aaronr0207\MailServiceHelper\GuzzleConfigurator();
        $cliente = $configurator->configure();

        // Ahora puedes usar $client para hacer solicitudes HTTP al microservicio.
        $datos = [];


        $datos[] = [
            'name' => 'tipo',
            'contents' => 'email'
        ];
        $datos[] = [
            'name' => 'subject',
            'contents' => $asunto
        ];
        $datos[] = [
            'name' => 'body',
            'contents' => view($vista, $parametros)->render()
        ];
        $datos[] = [
            'name' => 'sender',
            'contents' => $emisor
        ];
        $datos[] = [
            'name' => 'recipients',
            'contents' => json_encode($receptores)
        ];
        $datos[] = [
            'name' => 'cc',
            'contents' => json_encode($copia)
        ];
        $datos[] = [
            'name' => 'bcc',
            'contents' => json_encode($copia_oculta)
        ];
        $datos[] = [
            'name' => 'api_caller',
            'contents' => env('MAIL_SERVICE_API_CALLER','default')
        ];
        $datos[] = [
            'name' => 'instance_caller',
            'contents' => env('MAIL_SERVICE_INSTANCE','default')
        ];



        return $cliente->post('/api/v1/incoming-mail', [
            'multipart' => $datos
        ]);
    }
}
