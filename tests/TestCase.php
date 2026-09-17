<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Pengaman: test tidak boleh menyentuh DB kerja (lihat tests/bootstrap.php).
        if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            $this->fail(sprintf(
                'Test berjalan pada koneksi "%s" (%s), bukan sqlite :memory:. Periksa tests/bootstrap.php.',
                config('database.default'),
                config('database.connections.' . config('database.default') . '.database'),
            ));
        }
    }
}
