<?php

/**
 * Bootstrap khusus PHPUnit.
 *
 * Sebagian mesin (mis. Laragon) mengekspor APP_ENV/DB_CONNECTION di level OS.
 * PHP menyalin environment OS ke $_SERVER, dan Laravel membaca $_SERVER lebih dulu,
 * sehingga konfigurasi di phpunit.xml <env> tidak menang — test pun berjalan pada
 * koneksi MySQL dan `RefreshDatabase` menyapu data DB kerja.
 *
 * Di sini nilai dipaksa ke putenv + $_ENV + $_SERVER sebelum autoload/app boot.
 */
$forced = [
    'APP_ENV' => 'testing',
    'APP_MAINTENANCE_DRIVER' => 'file',
    'BCRYPT_ROUNDS' => '4',
    'BROADCAST_CONNECTION' => 'null',
    'CACHE_STORE' => 'array',
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => ':memory:',
    'DB_URL' => '',
    'MAIL_MAILER' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'SESSION_DRIVER' => 'array',
    'PULSE_ENABLED' => 'false',
    'TELESCOPE_ENABLED' => 'false',
    'NIGHTWATCH_ENABLED' => 'false',
];

foreach ($forced as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

require __DIR__.'/../vendor/autoload.php';
