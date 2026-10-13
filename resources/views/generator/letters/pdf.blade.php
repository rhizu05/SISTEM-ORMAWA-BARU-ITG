@php
    $m = $letter->metadata ?? [];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat - {{ $letter->perihal }}</title>
    <style>
        @page { size: A4; margin: 18mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; }
        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .kop-logo { width: 75px; height: 75px; object-fit: contain; }
        .kop-text { text-align: center; padding: 0 8px; }
        .kop-1 { font-size: 10pt; }
        .kop-2 { font-size: 13pt; font-weight: bold; text-transform: uppercase; }
        .kop-3, .kop-4 { font-size: 9pt; font-style: italic; line-height: 1.3; }
        .judul { text-align: center; font-weight: bold; text-decoration: underline; margin: 26px 0; }
        table.meta { line-height: 1.8; }
        table.meta td { padding: 0 8px 0 0; vertical-align: top; }
        .isi { text-align: justify; line-height: 1.6; }
    </style>
</head>
<body>
    @if($letter->user && $letter->user->hasAnyRole(['ormawa', 'bem', 'bpm']))
        @include('generator.partials.kop-ormawa', ['user' => $letter->user, 'isPdf' => true, 'pdf' => true])
    @else
        @include('generator.partials.kop', ['konfig' => $konfig, 'isPdf' => true, 'pdf' => true])
    @endif

    <div class="judul">
        @if($letter->type === 'undangan') SURAT UNDANGAN
        @elseif($letter->type === 'tugas') SURAT TUGAS / MANDAT
        @elseif($letter->type === 'permohonan') SURAT PERMOHONAN
        @elseif($letter->type === 'keterangan_aktif') SURAT KETERANGAN AKTIF
        @else SURAT @endif
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 11pt; line-height: 1.5;">
        <tr>
            <td style="width: 75px; vertical-align: top;">Nomor</td>
            <td style="width: 15px; vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $letter->nomor_surat ?? '___/___/___' }}</td>
            <td style="text-align: right; vertical-align: top;">Garut, {{ \Carbon\Carbon::parse($letter->created_at)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Lampiran</td>
            <td style="vertical-align: top;">:</td>
            <td colspan="2" style="vertical-align: top;">{{ $letter->lampiran ?? ($m['lampiran'] ?? '-') }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Perihal</td>
            <td style="vertical-align: top;">:</td>
            <td colspan="2" style="vertical-align: top; font-weight: bold;">{{ $letter->perihal }}</td>
        </tr>
    </table>

    <div style="margin-bottom: 20px; line-height: 1.4;">
        Kepada Yth.<br>
        <strong>{{ $m['tujuan'] ?? '..........................' }}</strong><br>
        di Tempat
    </div>

    @if($letter->type === 'undangan')
        <div class="isi">{{ $m['kalimat_pembuka'] ?? 'Dengan hormat, sehubungan dengan akan dilaksanakannya kegiatan kami, bersama ini kami bermaksud mengundang Bapak/Ibu untuk dapat hadir pada kegiatan yang diselenggarakan:' }}</div>
        <table class="meta" style="margin-top:14px; margin-bottom:14px; margin-left: 18px;">
            <tr><td style="width: 140px;">Nama Acara</td><td>: {{ $m['nama_acara'] ?? '-' }}</td></tr>
            <tr><td>Hari / Tanggal</td><td>: {{ $m['hari_tanggal'] ?? '-' }}</td></tr>
            <tr><td>Waktu</td><td>: {{ $m['waktu'] ?? '-' }}</td></tr>
            <tr><td>Tempat</td><td>: {{ $m['tempat'] ?? '-' }}</td></tr>
        </table>
        <div class="isi">{{ $m['kalimat_penutup'] ?? 'Demikian surat undangan ini kami sampaikan. Atas perhatian, kehadiran, dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.' }}</div>
    @elseif($letter->type === 'tugas')
        <div class="isi">{{ $m['kalimat_pembuka'] ?? 'Yang bertanda tangan di bawah ini Pengurus Organisasi Kemahasiswaan Institut Teknologi Garut, dengan ini memberikan tugas / mandat kepada:' }}</div>
        @php
            $petugasList = $m['petugas_list'] ?? [];
            if (empty($petugasList) && (!empty($m['nama_petugas']) || !empty($m['nim']))) {
                $petugasList = [['nama' => $m['nama_petugas'] ?? '-', 'nim' => $m['nim'] ?? '-']];
            }
        @endphp

        @if(count($petugasList) > 1)
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 12px; font-size: 11pt;">
                <thead>
                    <tr style="background-color: #f1f5f9; text-align: left;">
                        <th style="width: 35px; padding: 5px 8px; border: 1px solid #000; text-align: center;">No</th>
                        <th style="padding: 5px 8px; border: 1px solid #000;">Nama Penerima Tugas / Mandat</th>
                        <th style="width: 160px; padding: 5px 8px; border: 1px solid #000;">NIM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($petugasList as $idx => $p)
                        <tr>
                            <td style="padding: 5px 8px; border: 1px solid #000; text-align: center;">{{ $idx + 1 }}.</td>
                            <td style="padding: 5px 8px; border: 1px solid #000; font-weight: bold;">{{ $p['nama'] }}</td>
                            <td style="padding: 5px 8px; border: 1px solid #000; font-family: monospace;">{{ $p['nim'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <table class="meta" style="margin-bottom:12px; margin-left: 18px;">
                <tr><td style="width: 160px; vertical-align: top;">Uraian Tugas</td><td>: {{ $m['uraian_tugas'] ?? '-' }}</td></tr>
                <tr><td style="vertical-align: top;">Tanggal Pelaksanaan</td><td>: {{ $m['tanggal_pelaksanaan'] ?? '-' }}</td></tr>
            </table>
        @else
            <table class="meta" style="margin-top:14px; margin-bottom:14px; margin-left: 18px;">
                <tr><td style="width: 160px;">Nama</td><td>: {{ $petugasList[0]['nama'] ?? ($m['nama_petugas'] ?? '-') }}</td></tr>
                <tr><td>NIM</td><td>: {{ $petugasList[0]['nim'] ?? ($m['nim'] ?? '-') }}</td></tr>
                <tr><td>Uraian Tugas</td><td>: {{ $m['uraian_tugas'] ?? '-' }}</td></tr>
                <tr><td>Tanggal Pelaksanaan</td><td>: {{ $m['tanggal_pelaksanaan'] ?? '-' }}</td></tr>
            </table>
        @endif
        <div class="isi">{{ $m['kalimat_penutup'] ?? 'Untuk melaksanakan tugas tersebut dengan penuh tanggung jawab. Surat tugas ini dibuat untuk dapat dipergunakan sebagaimana mestinya.' }}</div>
    @elseif($letter->type === 'permohonan')
        <div class="isi">{{ $m['kalimat_pembuka'] ?? 'Dengan hormat, sehubungan dengan agenda kegiatan yang akan kami selenggarakan, bersama ini kami mengajukan permohonan peminjaman:' }}</div>
        <table class="meta" style="margin-top:14px; margin-bottom:14px; margin-left: 18px;">
            <tr><td style="width: 170px;">Nama Alat / Ruangan</td><td>: {{ $m['nama_alat_tempat'] ?? '-' }}</td></tr>
            <tr><td>Waktu Penggunaan</td><td>: {{ $m['waktu_penggunaan'] ?? '-' }}</td></tr>
            <tr><td>Alasan / Tujuan</td><td>: {{ $m['alasan_tujuan'] ?? '-' }}</td></tr>
        </table>
        <div class="isi">{{ $m['kalimat_penutup'] ?? 'Demikian permohonan ini kami ajukan. Atas perhatian, izin, dan bantuan Bapak/Ibu, kami ucapkan terima kasih.' }}</div>
    @elseif($letter->type === 'keterangan_aktif')
        <div class="isi">{{ $m['kalimat_pembuka'] ?? 'Yang bertanda tangan di bawah ini Pengurus Organisasi Kemahasiswaan Institut Teknologi Garut, dengan ini menerangkan bahwa:' }}</div>
        @php
            $mahasiswaList = $m['mahasiswa_list'] ?? [];
            if (empty($mahasiswaList) && (!empty($m['nama_mahasiswa']) || !empty($m['nim']))) {
                $mahasiswaList = [[
                    'nama'    => $m['nama_mahasiswa'] ?? '-',
                    'nim'     => $m['nim'] ?? '-',
                    'jabatan' => $m['jabatan'] ?? '-',
                ]];
            }
        @endphp

        @if(count($mahasiswaList) > 1)
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 12px; font-size: 11pt;">
                <thead>
                    <tr style="background-color: #f1f5f9; text-align: left;">
                        <th style="width: 35px; padding: 5px 8px; border: 1px solid #000; text-align: center;">No</th>
                        <th style="padding: 5px 8px; border: 1px solid #000;">Nama Mahasiswa</th>
                        <th style="width: 130px; padding: 5px 8px; border: 1px solid #000;">NIM</th>
                        <th style="padding: 5px 8px; border: 1px solid #000;">Jabatan di Organisasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mahasiswaList as $idx => $mhs)
                        <tr>
                            <td style="padding: 5px 8px; border: 1px solid #000; text-align: center;">{{ $idx + 1 }}.</td>
                            <td style="padding: 5px 8px; border: 1px solid #000; font-weight: bold;">{{ $mhs['nama'] }}</td>
                            <td style="padding: 5px 8px; border: 1px solid #000; font-family: monospace;">{{ $mhs['nim'] }}</td>
                            <td style="padding: 5px 8px; border: 1px solid #000;">{{ $mhs['jabatan'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <table class="meta" style="margin-bottom:12px; margin-left: 18px;">
                <tr><td style="width: 170px; vertical-align: top;">Keperluan</td><td>: {{ $m['keperluan'] ?? '-' }}</td></tr>
            </table>
        @else
            <table class="meta" style="margin-top:14px; margin-bottom:14px; margin-left: 18px;">
                <tr><td style="width: 170px;">Nama</td><td>: {{ $mahasiswaList[0]['nama'] ?? ($m['nama_mahasiswa'] ?? '-') }}</td></tr>
                <tr><td>NIM</td><td>: {{ $mahasiswaList[0]['nim'] ?? ($m['nim'] ?? '-') }}</td></tr>
                <tr><td>Jabatan di Organisasi</td><td>: {{ $mahasiswaList[0]['jabatan'] ?? ($m['jabatan'] ?? '-') }}</td></tr>
                <tr><td>Keperluan</td><td>: {{ $m['keperluan'] ?? '-' }}</td></tr>
            </table>
        @endif
        <div class="isi">{{ $m['kalimat_penutup'] ?? 'Adalah benar mahasiswa/anggota aktif pada organisasi kami. Surat keterangan ini kami buat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.' }}</div>
    @else
        @if(!empty($m['kalimat_pembuka']))
            <div class="isi" style="margin-bottom: 12px;">{{ $m['kalimat_pembuka'] }}</div>
        @endif
        <div class="isi" style="white-space: pre-wrap;">{{ $letter->content }}</div>
        @if(!empty($m['kalimat_penutup']))
            <div class="isi" style="margin-top: 12px;">{{ $m['kalimat_penutup'] }}</div>
        @endif
    @endif

    @include('generator.partials.penandatangan', [
        'penandatanganList' => $letter->penandatangan_list,
        'signatures' => $letter->signatures_by_index,
        'keteranganPeriode' => $letter->user->periode_kepengurusan ?? null,
        'namaOrganisasi' => $letter->user->name ?? null,
    ])
</body>
</html>
