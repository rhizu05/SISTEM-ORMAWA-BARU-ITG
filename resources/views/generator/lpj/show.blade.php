<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Preview LPJ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between mb-4">
                <a href="{{ route('generator.archive') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Kembali ke Arsip</a>
                <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">🖨️ Cetak / PDF</button>
            </div>

            <div class="bg-white p-10 shadow-lg mx-auto" style="width: 210mm; min-height: 297mm; font-family: 'Times New Roman';">
                <!-- Kop Surat -->
                <div style="display: flex; align-items: center; border-bottom: 3px double black; padding-bottom: 10px; margin-bottom: 30px;">
                    <div style="width: 80px;">LOGO</div>
                    <div style="text-align: center; flex-grow: 1;">
                        <div style="font-size: 12pt; font-weight: bold; text-transform: uppercase;">INSTITUT TEKNOLOGI GARUT</div>
                        <div style="font-size: 10pt; font-style: italic;">Jl. Mayor Syamsu No. 1 Jayaraga Garut 44151</div>
                    </div>
                    <div style="width: 80px; text-align: right;">LOGO</div>
                </div>

                <div style="text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 30px;">
                    LAPORAN PERTANGGUNGJAWABAN (LPJ)<br>
                    {{ strtoupper($proposal->nama_kegiatan ?? 'KEGIATAN') }}
                </div>

                <div class="content" style="text-align: justify; line-height: 1.6;">
                    <p><strong>I. Pelaksanaan Kegiatan</strong></p>
                    <p style="white-space: pre-wrap;">{{ $lpj->content }}</p>

                    <p style="margin-top: 20px;"><strong>II. Realisasi Anggaran</strong></p>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; border: 1px solid black;">
                        <thead>
                            <tr style="background: #eee;">
                                <th style="border: 1px solid black; padding: 8px; text-align: left;">Keterangan</th>
                                <th style="border: 1px solid black; padding: 8px; text-align: right;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="border: 1px solid black; padding: 8px;">Dana yang Diajukan (Sesuai Proposal)</td>
                                <td style="border: 1px solid black; padding: 8px; text-align: right;">
                                    Rp {{ number_format($proposal->rab->sum('total_harga'), 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 8px; font-weight: bold;">Total Realisasi Pengeluaran</td>
                                <td style="border: 1px solid black; padding: 8px; text-align: right; font-weight: bold;">
                                    Rp {{ number_format($lpj->metadata['realisasi_dana'], 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 50px; text-align: right;">
                    Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </div>

                <div style="margin-top: 20px; display: flex; justify-content: space-between;">
                    <div style="text-align: center; width: 200px;">
                        <div style="font-weight: bold;">Ketua Pelaksana,</div>
                        <div style="height: 80px;"></div>
                        <div style="font-weight: bold; text-decoration: underline;">{{ Auth::user()->nama_ketua }}</div>
                    </div>
                    <div style="text-align: center; width: 200px;">
                        <div style="font-weight: bold;">Sekretaris,</div>
                        <div style="height: 80px;"></div>
                        <div style="font-weight: bold; text-decoration: underline;">{{ Auth::user()->nama_sekretaris }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
