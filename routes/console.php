<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('sidang:kirim-pengingat')->dailyAt('04:00');
Schedule::command('sidang:auto-approve')->everyThreeMinutes(); // auto-approve jika TGL_AJUKAN_KPPS <= now-3m
Schedule::command('email:kirim-antrian')->everyMinute(); // kirim email ajukan/notifikasi di background
