<?php

namespace Aaronr0207\MailServiceHelper;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Arr;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\TransportFactoryInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;

class LapsoMailTransportFactory implements TransportFactoryInterface
{
    public function create(Dsn $dsn): TransportInterface
    {
        return new LapsoMailTransport();
    }

    public function supports(Dsn $dsn): bool
    {
        return 'lapso-mail-service' === $dsn->getScheme();
    }
}