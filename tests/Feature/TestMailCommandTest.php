<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TestMailCommandTest extends TestCase
{
    public function test_mail_test_command_rejects_invalid_email(): void
    {
        $this->artisan('mail:test', ['email' => 'bukan-email'])
            ->expectsOutputToContain('Alamat email tidak valid: bukan-email')
            ->assertExitCode(1);
    }

    public function test_mail_test_command_sends_diagnostic_email_successfully(): void
    {
        Mail::fake();

        $this->artisan('mail:test', ['email' => 'devops@itg.ac.id'])
            ->expectsOutputToContain('DIAGNOSTIK PENGIRIMAN EMAIL')
            ->expectsOutputToContain('devops@itg.ac.id')
            ->expectsOutputToContain('BERHASIL!')
            ->assertExitCode(0);

        Mail::assertSent(\App\Mail\DiagnosticTestMail::class, function ($mail) {
            return $mail->recipientEmail === 'devops@itg.ac.id';
        });
    }

    public function test_mail_test_command_runs_with_real_mailer_log(): void
    {
        config(['mail.default' => 'log']);

        $this->artisan('mail:test', ['email' => 'admin@itg.ac.id'])
            ->expectsOutputToContain('BERHASIL!')
            ->expectsOutputToContain('storage/logs/laravel.log')
            ->assertExitCode(0);
    }
}
