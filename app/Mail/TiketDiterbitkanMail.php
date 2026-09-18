<?php

namespace App\Mail;

use App\Models\TiketLayanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TiketDiterbitkanMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public TiketLayanan $tiket)
    {
    }

    public function envelope(): Envelope
    {
        $fromAddress = config('mail.from.address', 'noreply@itg.ac.id');
        $fromName = config('mail.from.name', 'SKIN ITG - Sistem Informasi Kemahasiswaan');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: 'Tiket Layanan Anda: ' . $this->tiket->kode_tiket . ' (' . ucfirst($this->tiket->kategori) . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tiket_diterbitkan',
        );
    }
}
