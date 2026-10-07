<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DiagnosticTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $diagnostics,
        public string $recipientEmail
    ) {
    }

    public function envelope(): Envelope
    {
        $fromAddress = config('mail.from.address', 'noreply@itg.ac.id');
        $fromName = config('mail.from.name', 'SKIN ITG');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: 'Uji Coba Diagnostik SMTP: SKIN ITG (' . now()->format('d M Y H:i') . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.diagnostic_test',
        );
    }
}
