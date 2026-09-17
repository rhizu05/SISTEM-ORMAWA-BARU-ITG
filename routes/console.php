<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// FR-022 §22 no.5: pengingat harian barang yang belum dikembalikan.
Schedule::command('notifikasi:barang-terlambat')->daily();
