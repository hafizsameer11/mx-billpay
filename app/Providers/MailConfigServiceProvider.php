<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Fetch the first SMTP configuration from the database
        $smtp = DB::table('smtps')->first();

        if ($smtp) {
            Config::set('mail.mailer', 'smtp');
            Config::set('mail.host', $smtp->host);
            Config::set('mail.port', $smtp->port);
            Config::set('mail.username', $smtp->username);
            Config::set('mail.password', $smtp->password);
            Config::set('mail.encryption', $smtp->encryption);
            Config::set('mail.from.address', $smtp->from_email);
            Config::set('mail.from.name', $smtp->from_name);
        }
    }
}
