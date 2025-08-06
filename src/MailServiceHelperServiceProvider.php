<?php

namespace Aaronr0207\MailServiceHelper;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;

class MailServiceHelperServiceProvider extends ServiceProvider
{

    public function register()
    {
        require_once __DIR__ . '/helpers.php';
        
        // Merge mail configuration
        $this->mergeConfigFrom(__DIR__.'/../config/lapso-mail.php', 'mail.mailers.lapso-mail-service');
    }

    public function boot()
    {
        // Register mail driver
        $this->app->extend('mail.manager', function (MailManager $mailManager) {
            $mailManager->extend('lapso-mail-service', function (array $config = []) {
                return new LapsoMailTransport();
            });
            return $mailManager;
        });
    }

}