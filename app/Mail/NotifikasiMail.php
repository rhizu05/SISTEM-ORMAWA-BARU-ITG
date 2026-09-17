<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * FR-025: kanal notifikasi email institusi (Q-MHS-03).
 */
class NotifikasiMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $pesan)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notifikasi SKIN - Sistem Informasi Kemahasiswaan ITG',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notifikasi',
        );
    }
}
