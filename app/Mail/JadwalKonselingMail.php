<?php

namespace App\Mail;

use App\Models\TiketLayanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JadwalKonselingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TiketLayanan $tiket,
        public string $pesanBkhm,
        public ?string $jadwalTemu = null,
        public ?string $lokasiAtauLink = null
    ) {
    }

    public function envelope(): Envelope
    {
        $fromAddress = config('mail.from.address', 'noreply@itg.ac.id');
        $fromName = config('mail.from.name', 'BKHM ITG - Layanan Konseling Mahasiswa');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: '[RAHASIA] Penetapan Jadwal Sesi Konseling Mahasiswa (' . $this->tiket->kode_tiket . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.jadwal_konseling',
        );
    }
}
