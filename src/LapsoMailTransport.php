<?php

namespace Aaronr0207\MailServiceHelper;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class LapsoMailTransport implements TransportInterface
{
    private GuzzleConfigurator $configurator;

    public function __construct()
    {
        $this->configurator = new GuzzleConfigurator();
    }

    public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage
    {
        if (!$message instanceof Email) {
            throw new \InvalidArgumentException('LapsoMailTransport only supports Email messages.');
        }

        $client = $this->configurator->configure();
        $sender = $this->getSender($envelope);

        $data = [
            [
                'name' => 'tipo',
                'contents' => 'email'
            ],
            [
                'name' => 'subject',
                'contents' => $message->getSubject() ?? ''
            ],
            [
                'name' => 'body',
                'contents' => $this->getBody($message)
            ],
            [
                'name' => 'sender',
                'contents' => $sender
            ],
            [
                'name' => 'recipients',
                'contents' => json_encode($this->formatAddresses($message->getTo()))
            ],
            [
                'name' => 'cc',
                'contents' => json_encode($this->formatAddresses($message->getCc()))
            ],
            [
                'name' => 'bcc',
                'contents' => json_encode($this->formatAddresses($message->getBcc()))
            ],
            [
                'name' => 'api_caller',
                'contents' => env('MAIL_SERVICE_API_CALLER', 'default')
            ],
            [
                'name' => 'instance_caller',
                'contents' => env('MAIL_SERVICE_INSTANCE', 'default')
            ]
        ];

        \Log::info('LapsoMailTransport: About to send email', [
            'sender' => $sender,
            'subject' => $message->getSubject(),
            'recipients' => $this->formatAddresses($message->getTo()),
            'service_uri' => env('MAIL_SERVICE_URI'),
            'api_key' => env('MAIL_SERVICE_API_KEY') ? 'SET' : 'NOT SET',
            'api_caller' => env('MAIL_SERVICE_API_CALLER', 'default'),
            'instance' => env('MAIL_SERVICE_INSTANCE', 'default')
        ]);

        try {
            $response = $client->post('/api/v1/incoming-mail', [
                'multipart' => $data
            ]);
            \Log::info('LapsoMailTransport: Mail service response', [
                'status' => $response->getStatusCode(),
                'body' => $response->getBody()->getContents()
            ]);
        } catch (\Exception $e) {
            \Log::error('LapsoMailTransport: Mail service failed', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'class' => get_class($e)
            ]);
            throw $e;
        }

        return new SentMessage($message, $envelope ?? Envelope::create($message));
    }

    public function __toString(): string
    {
        return 'lapso-mail-service';
    }

    private function getSender(?Envelope $envelope): string
    {
        if ($envelope && $envelope->getSender()) {
            return $envelope->getSender()->getAddress();
        }

        return env('MAIL_FROM_ADDRESS', 'noreply@example.com');
    }

    private function getBody(Email $message): string
    {
        if ($message->getHtmlBody()) {
            return $message->getHtmlBody();
        }
        
        if ($message->getTextBody()) {
            return $message->getTextBody();
        }

        return '';
    }

    private function formatAddresses(array $addresses): array
    {
        return array_map(function (Address $address) {
            return $address->getAddress();
        }, $addresses);
    }
}