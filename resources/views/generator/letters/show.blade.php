<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Preview Surat') }}</h2></x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between mb-4">
                <a href="{{ route('generator.letters.create') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Kembali</a>
                <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">🖨️ Cetak / PDF</button>
            </div>
            @php $m=$letter->metadata ?? []; $penanda=$m['penandatangan'] ?? 'ketua'; $namaPenanda = $penanda==='sekretaris' ? (Auth::user()->nama_sekretaris ?? Auth::user()->name) : ($penanda==='bendahara' ? (Auth::user()->nama_bendahara ?? Auth::user()->name) : (Auth::user()->nama_ketua ?? Auth::user()->name)); @endphp
            <div class="bg-white p-10 shadow-lg mx-auto" style="width: 210mm; min-height: 297mm; font-family: 'Times New Roman';">
                <div style="display: flex; align-items: center; border-bottom: 3px double black; padding-bottom: 10px; margin-bottom: 30px;">
                    <div style="width: 80px;">LOGO</div>
                    <div style="text-align: center; flex-grow: 1;">
                        <div style="font-size: 12pt; font-weight: bold; text-transform: uppercase;">INSTITUT TEKNOLOGI GARUT</div>
                        <div style="font-size: 10pt; font-style: italic;">Jl. Mayor Syamsu No. 1 Jayaraga Garut 44151</div>
                    </div>
                    <div style="width: 80px; text-align: right;">LOGO</div>
                </div>
                <div style="text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 30px;">
                    @if($letter->type==='undangan') SURAT UNDANGAN
                    @elseif($letter->type==='tugas') SURAT TUGAS / MANDAT
                    @elseif($letter->type==='permohonan') SURAT PERMOHONAN
                    @elseif($letter->type==='keterangan_aktif') SURAT KETERANGAN AKTIF
                    @else SURAT @endif
                </div>
                <div style="margin-bottom: 20px;">Nomor: {{ $letter->nomor_surat ?? '___/___/___' }}<br>Perihal: {{ $letter->perihal }}</div>
                <div style="margin-bottom: 20px;">Yth. {{ $m['tujuan'] ?? '..........................' }}<br>Di Tempat</div>

                @if($letter->type==='undangan')
                    <div style="text-align: justify; line-height: 1.6;">{{ $m['kalimat_pembuka'] ?? $letter->content }}</div>
                    <table style="margin-top:16px; line-height:1.8;">
                        <tr><td>Nama Acara</td><td>: {{ $m['nama_acara'] ?? '-' }}</td></tr>
                        <tr><td>Hari / Tanggal</td><td>: {{ $m['hari_tanggal'] ?? '-' }}</td></tr>
                        <tr><td>Waktu</td><td>: {{ $m['waktu'] ?? '-' }}</td></tr>
                        <tr><td>Tempat</td><td>: {{ $m['tempat'] ?? '-' }}</td></tr>
                    </table>
                    <div style="text-align: justify; line-height: 1.6; margin-top:16px;">Demikian undangan ini kami sampaikan, atas perhatian dan kehadirannya kami ucapkan terima kasih.</div>
                @elseif($letter->type==='tugas')
                    <div style="text-align: justify; line-height: 1.6;">Dengan ini memberikan tugas kepada:</div>
                    <table style="margin-top:12px; line-height:1.8;">
                        <tr><td>Nama</td><td>: {{ $m['nama_petugas'] ?? '-' }}</td></tr>
                        <tr><td>NIM</td><td>: {{ $m['nim'] ?? '-' }}</td></tr>
                        <tr><td>Uraian Tugas</td><td>: {{ $m['uraian_tugas'] ?? '-' }}</td></tr>
                        <tr><td>Tanggal Pelaksanaan</td><td>: {{ $m['tanggal_pelaksanaan'] ?? '-' }}</td></tr>
                    </table>
                    <div style="text-align: justify; line-height: 1.6; margin-top:12px;">Untuk melaksanakan tugas tersebut dengan penuh tanggung jawab. Surat tugas ini dibuat untuk dipergunakan sebagaimana mestinya.</div>
                @elseif($letter->type==='permohonan')
                    <div style="text-align: justify; line-height: 1.6;">Bersama ini kami mengajukan permohonan peminjaman:</div>
                    <table style="margin-top:12px; line-height:1.8;">
                        <tr><td>Nama Alat / Tempat</td><td>: {{ $m['nama_alat_tempat'] ?? '-' }}</td></tr>
                        <tr><td>Waktu Penggunaan</td><td>: {{ $m['waktu_penggunaan'] ?? '-' }}</td></tr>
                        <tr><td>Alasan / Tujuan</td><td>: {{ $m['alasan_tujuan'] ?? '-' }}</td></tr>
                    </table>
                    <div style="text-align: justify; line-height: 1.6; margin-top:12px;">Demikian permohonan ini kami ajukan, atas perkenannya kami ucapkan terima kasih.</div>
                @elseif($letter->type==='keterangan_aktif')
                    <div style="text-align: justify; line-height: 1.6;">Yang bertanda tangan di bawah ini menerangkan bahwa:</div>
                    <table style="margin-top:12px; line-height:1.8;">
                        <tr><td>Nama</td><td>: {{ $m['nama_mahasiswa'] ?? '-' }}</td></tr>
                        <tr><td>NIM</td><td>: {{ $m['nim'] ?? '-' }}</td></tr>
                        <tr><td>Jabatan di Organisasi</td><td>: {{ $m['jabatan'] ?? '-' }}</td></tr>
                        <tr><td>Keperluan</td><td>: {{ $m['keperluan'] ?? '-' }}</td></tr>
                    </table>
                    <div style="text-align: justify; line-height: 1.6; margin-top:12px;">Adalah benar mahasiswa/anggota aktif pada organisasi kami. Surat keterangan ini dibuat untuk keperluan {{ $m['keperluan'] ?? '..........................' }}.</div>
                @else
                    <div style="text-align: justify; line-height: 1.6; white-space: pre-wrap;">{{ $letter->content }}</div>
                @endif

                <div style="margin-top: 50px; text-align: right;">Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                    <div style="text-align: center; width: 250px;">
                        <div>{{ ucfirst($penanda) }},</div>
                        <div style="height: 80px;"></div>
                        <div style="font-weight: bold; text-decoration: underline;">{{ $namaPenanda }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
