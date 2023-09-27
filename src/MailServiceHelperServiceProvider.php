<?php

namespace Aaronr0207\MailServiceHelper;

use Illuminate\Support\ServiceProvider;

class MailServiceHelperServiceProvider extends ServiceProvider
{

    public function register()
    {
        require_once __DIR__ . '/helpers.php';
    }


    public function boot()
    {
    }

}