<?php

if (! function_exists('send_mail')) {
    function send_mail($email)
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
            'contents' => $email['asunto']
        ];
        $datos[] = [
            'name' => 'body',
            'contents' => view($email['vista'], $email['parametros'])->render()
        ];
        $datos[] = [
            'name' => 'sender',
            'contents' => $email['emisor']
        ];
        $datos[] = [
            'name' => 'recipients',
            'contents' => json_encode($email['receptores'])
        ];
        $datos[] = [
            'name' => 'cc',
            'contents' => json_encode($email['copia'])
        ];
        $datos[] = [
            'name' => 'bcc',
            'contents' => json_encode($email['copia_oculta'])
        ];
        $datos[] = [
            'name' => 'api_caller',
            'contents' => env('MAIL_SERVICE_API_CALLER','default')
        ];
        $datos[] = [
            'name' => 'instance_caller',
            'contents' => env('MAIL_SERVICE_INSTANCE','default')
        ];

        if(isset($email['archivos']) && $email['archivos'] != null && sizeof($email['archivos']) > 0){
            foreach($email['archivos'] as $nombre => $contenido){
                $datos[] = [
                    'name'     => 'archivos[]',
                    'contents' => $contenido, // el contenido debe ser un stream al archivo (ver documentacion mail-service)
                    'filename' => $nombre,     // Nombre del archivo
                    'headers'  => [
                        'Content-Type' => obtenerMimeType($nombre)    // Tipo MIME
                    ]
                ];
            }
        }


        return $cliente->post('/api/'.env('MAIL_SERVICE_API_VERSION','v1').'/incoming-mail', [
            'multipart' => $datos
        ]);
    }
    if (! function_exists('send_mail')) {
        function obtenerMimeType($nombre)
        {
            $extension = pathinfo($nombre, PATHINFO_EXTENSION);
            // Un mapa básico de extensiones a tipos MIME. Puedes expandirlo según tus necesidades.
            $mimes = [
                'pdf'  => 'application/pdf',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png'  => 'image/png',
                'gif'  => 'image/gif',
                'doc'  => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls'  => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'ppt'  => 'application/vnd.ms-powerpoint',
                'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'txt'  => 'text/plain',
                // ... [agregar más según sea necesario]
            ];

            // Retorna el tipo MIME basado en la extensión, o un valor por defecto si no se encuentra.
            return isset($mimes[strtolower($extension)]) ? $mimes[strtolower($extension)] : 'application/octet-stream';
        }
    }
}
