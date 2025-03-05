<?php

use App\Notifications\NotifikasiPermintaanBarang;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Artisan::command('send-notifikasi-email', function () {
//     Mail::to('testreceiver@gmail.com')->send(new NotifikasiPermintaanBarang("Jon"));
//     // Also, you can use specific mailer if your default mailer is not "mailtrap" but you want to use it for welcome mails
//     // Mail::mailer('mailtrap')->to('testreceiver@gmail.com')->send(new WelcomeMail("Jon"));
// })->purpose('Send welcome mail');
