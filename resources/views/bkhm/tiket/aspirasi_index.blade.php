<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                Eskalasi Aspirasi Mahasiswa (dari BPM)
            </h2>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                Pusat Kebijakan Kemahasiswaan
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

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-base">Aspirasi Diteruskan dari BPM</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar aspirasi mahasiswa yang memerlukan kebijakan struktural atau koordinasi tingkat pimpinan kampus.</p>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($tikets as $tiket)
                        <div class="p-6 space-y-4 hover:bg-slate-50/50 transition">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono font-bold text-xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                                        {{ $tiket->kode_tiket }}
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        Diteruskan: {{ $tiket->diteruskan_ke_bkhm_at ? $tiket->diteruskan_ke_bkhm_at->format('d M Y, H:i') : $tiket->created_at->format('d M Y') }}
                                    </span>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $tiket->status_color }}">
                                    {{ $tiket->status_label }}
                                </span>
                            </div>

                            <div class="space-y-1">
                                <h4 class="font-bold text-slate-900 text-base">{{ $tiket->judul }}</h4>
                                <div class="text-xs text-slate-500">
                                    Diajukan oleh: <span class="font-semibold text-slate-700">{{ $tiket->nama_mahasiswa }}</span> ({{ $tiket->nim }} &bull; {{ $tiket->prodi ?? 'ITG' }})
                                    &bull; <a href="mailto:{{ $tiket->email }}" class="text-blue-600 hover:underline">{{ $tiket->email }}</a>
                                </div>
                            </div>

                            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 text-xs text-slate-700 whitespace-pre-line leading-relaxed">
                                {{ $tiket->isi }}
                            </div>

                            @if ($tiket->catatan_bpm)
                                <div class="bg-blue-50/70 border border-blue-200 rounded-xl p-3 text-xs text-blue-900">
                                    <strong class="font-semibold block mb-0.5 text-blue-950">📌 Catatan Rekomendasi BPM:</strong>
                                    {{ $tiket->catatan_bpm }}
                                </div>
                            @endif

                            @if ($tiket->catatan_bkhm)
                                <div class="bg-emerald-50/70 border border-emerald-200 rounded-xl p-3 text-xs text-emerald-900">
                                    <strong class="font-semibold block mb-0.5 text-emerald-950">✅ Tindak Lanjut Terakhir BKHM:</strong>
                                    {{ $tiket->catatan_bkhm }}
                                </div>
                            @endif

                            <!-- Form Tanggapan BKHM -->
                            <form action="{{ route('bkhm.tiket-aspirasi.update', $tiket) }}" method="POST" class="pt-3 border-t border-slate-100 flex flex-wrap items-end gap-3">
                                @csrf
                                <div class="flex-1 min-w-[240px]">
                                    <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Tanggapan / Catatan Kebijakan BKHM</label>
                                    <input type="text" name="catatan_bkhm" value="{{ old('catatan_bkhm', $tiket->catatan_bkhm) }}" required placeholder="Tuliskan keputusan atau langkah tindak lanjut..."
                                        class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-3">
                                </div>
                                <div class="w-48">
                                    <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Status Penanganan</label>
                                    <select name="status" class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-3">
                                        <option value="diproses_bkhm" {{ $tiket->status === 'diproses_bkhm' ? 'selected' : '' }}>Diproses BKHM</option>
                                        <option value="ditindaklanjuti" {{ $tiket->status === 'ditindaklanjuti' ? 'selected' : '' }}>Ditindaklanjuti</option>
                                        <option value="selesai" {{ $tiket->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="ditolak" {{ $tiket->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                </div>
                                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition shadow-sm">
                                    Perbarui & Emailkan
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="p-10 text-center text-slate-400 text-sm">
                            Tidak ada aspirasi mahasiswa yang sedang diteruskan ke BKHM.
                        </div>
                    @endforelse
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $tikets->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
