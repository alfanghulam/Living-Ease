<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Batalkan pesanan yang tidak dibayar & kembalikan stok (butuh scheduler berjalan).
Schedule::command('orders:expire')->everyFiveMinutes()->withoutOverlapping();