<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('bkhm.konseling.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                    &larr; Kembali
                </a>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    Detail Konseling Mahasiswa [{{ $tiket->kode_tiket }}]
                </h2>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $tiket->status_color }}">
                {{ $tiket->status_label }}
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 text-sm">
                    <strong class="font-bold block mb-1">Periksa isian Anda:</strong>
                    <ul class="list-disc pl-5 text-xs space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Info Pengaju & Kendala -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-5">
                        <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                            <h3 class="font-bold text-base text-slate-800">Identitas Mahasiswa</h3>
                            <span class="text-xs text-slate-400 font-mono">{{ $tiket->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                            <div>
                                <span class="text-slate-400 block mb-1">NIM</span>
                                <span class="font-bold text-slate-800 text-sm">{{ $tiket->nim }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-1">Nama Mahasiswa</span>
                                <span class="font-bold text-slate-800 text-sm">{{ $tiket->nama_mahasiswa }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-1">Program Studi</span>
                                <span class="font-semibold text-slate-800">{{ $tiket->prodi ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-1">Email Mahasiswa</span>
                                <a href="mailto:{{ $tiket->email }}" class="font-semibold text-blue-600 hover:underline">{{ $tiket->email }}</a>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-1">No. WhatsApp / HP</span>
                                <span class="font-semibold text-slate-800">{{ $tiket->no_hp ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-1">Metode Pilihan</span>
                                <span class="font-semibold text-slate-800">{{ $tiket->metode_konseling }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 space-y-4">
                            <div>
                                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Topik Permasalahan</span>
                                <div class="font-bold text-slate-900 text-base mt-0.5">{{ $tiket->topik_konseling }}</div>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Uraian / Curahan Kendala Mahasiswa</span>
                                <div class="mt-1 bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-slate-800 whitespace-pre-line leading-relaxed">
                                    {{ $tiket->deskripsi_masalah }}
                                </div>
                            </div>

                            @if ($tiket->lampiran)
                                <div class="pt-2">
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Dokumen Pendukung</span>
                                    <a href="{{ route('layanan.lampiran', $tiket) }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-teal-700 bg-teal-50 border border-teal-200 hover:bg-teal-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Unduh Lampiran Mahasiswa
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Form Tindak Lanjut Konselor BKHM -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <div class="border-b border-slate-100 pb-3 mb-4">
                            <h3 class="font-bold text-base text-slate-800">Tindak Lanjut Konselor</h3>
                            <p class="text-xs text-slate-500">Respons dan jadwal akan dikirimkan langsung ke email mahasiswa.</p>
                        </div>

                        @if($tiket->jadwal_temu)
                            <div class="mb-4 p-3.5 rounded-xl border text-xs {{ $tiket->konfirmasi_mahasiswa === 'bersedia_hadir' ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : ($tiket->konfirmasi_mahasiswa === 'minta_reschedule' ? 'bg-amber-50 border-amber-200 text-amber-900' : ($tiket->konfirmasi_mahasiswa === 'dibatalkan_mahasiswa' ? 'bg-rose-50 border-rose-200 text-rose-900' : 'bg-slate-50 border-slate-200 text-slate-700')) }}">
                                <span class="font-bold block mb-1">Status Respons Mahasiswa:</span>
                                @if($tiket->konfirmasi_mahasiswa === 'bersedia_hadir')
                                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-700">✅ Mahasiswa bersedia hadir sesuai jadwal.</span>
                                @elseif($tiket->konfirmasi_mahasiswa === 'minta_reschedule')
                                    <span class="inline-flex items-center gap-1 font-semibold text-amber-700">⏳ Mahasiswa meminta penyesuaian jadwal (Reschedule).</span>
                                @elseif($tiket->konfirmasi_mahasiswa === 'dibatalkan_mahasiswa')
                                    <span class="inline-flex items-center gap-1 font-semibold text-rose-700">❌ Mahasiswa membatalkan sesi konseling.</span>
                                @else
                                    <span class="text-slate-500 italic">⏳ Menunggu konfirmasi kehadiran dari mahasiswa.</span>
                                @endif

                                @if($tiket->catatan_konfirmasi_mahasiswa)
                                    <div class="mt-2 p-2 bg-white/80 rounded border border-slate-200 text-slate-800">
                                        <span class="font-semibold text-slate-600 block text-[11px]">Catatan Mahasiswa:</span>
                                        {{ $tiket->catatan_konfirmasi_mahasiswa }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <form action="{{ route('bkhm.konseling.update', $tiket) }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label for="status" class="block text-xs font-semibold text-slate-700 uppercase mb-1">Perbarui Status <span class="text-rose-500">*</span></label>
                                <select name="status" id="status" required class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">
                                    <option value="ditinjau_bkhm" {{ old('status', $tiket->status) === 'ditinjau_bkhm' ? 'selected' : '' }}>Sedang Ditinjau BKHM</option>
                                    <option value="jadwal_ditentukan" {{ old('status', $tiket->status) === 'jadwal_ditentukan' ? 'selected' : '' }}>Jadwal Temu Ditentukan</option>
                                    <option value="selesai" {{ old('status', $tiket->status) === 'selesai' ? 'selected' : '' }}>Konseling Selesai</option>
                                    <option value="ditolak" {{ old('status', $tiket->status) === 'ditolak' ? 'selected' : '' }}>Ditolak / Dibatalkan</option>
                                </select>
                            </div>

                            <div>
                                <label for="jadwal_temu" class="block text-xs font-semibold text-slate-700 uppercase mb-1">Rencana Jadwal Temu</label>
                                <input type="datetime-local" name="jadwal_temu" id="jadwal_temu"
                                    value="{{ old('jadwal_temu', $tiket->jadwal_temu ? $tiket->jadwal_temu->format('Y-m-d\TH:i') : '') }}"
                                    class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">
                            </div>

                            <div>
                                <label for="lokasi_atau_link" class="block text-xs font-semibold text-slate-700 uppercase mb-1">Tempat / Link Pertemuan</label>
                                <input type="text" name="lokasi_atau_link" id="lokasi_atau_link"
                                    value="{{ old('lokasi_atau_link', $tiket->lokasi_atau_link) }}" placeholder="Cth: Ruang BKHM Lantai 2 / Tautan Meet"
                                    class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">
                            </div>

                            <div>
                                <label for="tanggapan_bkhm" class="block text-xs font-semibold text-slate-700 uppercase mb-1">Pesan / Tanggapan Konselor <span class="text-rose-500">*</span></label>
                                <textarea name="tanggapan_bkhm" id="tanggapan_bkhm" rows="5" required placeholder="Tuliskan pesan tanggapan, petunjuk pertemuan, atau simpulan konseling..."
                                    class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">{{ old('tanggapan_bkhm', $tiket->tanggapan_bkhm) }}</textarea>
                            </div>

                            <button type="submit" class="w-full py-2.5 px-4 rounded-xl text-sm font-bold text-white bg-teal-600 hover:bg-teal-700 transition shadow-md shadow-teal-500/20">
                                Simpan & Beritahu Mahasiswa
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
