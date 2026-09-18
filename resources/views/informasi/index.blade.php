<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Pusat Informasi & Regulasi') }}
            </h2>
            <p class="text-xs text-slate-500">Pusat dokumentasi berita ormawa dan regulasi resmi kemahasiswaan ITG</p>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ activeTab: 'pengumuman', showPengumumanModal: false, showRegulasiModal: false, searchDoc: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-sm">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>Terjadi kesalahan pada input:</span>
                    </div>
                    <ul class="list-disc pl-5 text-sm space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tabs Navigation Bar -->
            <div class="bg-white rounded-xl p-2 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <button @click="activeTab = 'pengumuman'" 
                            :class="activeTab === 'pengumuman' ? 'bg-indigo-600 text-white shadow-sm font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'" 
                            class="py-2.5 px-5 rounded-lg text-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        <span>Berita & Pengumuman</span>
                    </button>
                    <button @click="activeTab = 'regulasi'" 
                            :class="activeTab === 'regulasi' ? 'bg-indigo-600 text-white shadow-sm font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-medium'" 
                            class="py-2.5 px-5 rounded-lg text-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Regulasi & Pedoman</span>
                    </button>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @hasrole('bkhm')
                    <button x-show="activeTab === 'pengumuman'" @click="showPengumumanModal = true" class="inline-flex items-center gap-1.5 bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 px-3.5 rounded-lg text-xs sm:text-sm shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Pengumuman Resmi Kampus</span>
                    </button>
                    @endhasrole

                    @hasrole('bem')
                    <a href="{{ route('bem.kurasi.index') }}" x-show="activeTab === 'pengumuman'" class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-3 rounded-lg text-xs sm:text-sm shadow-sm transition-all">
                        <span>Kurasi Berita</span>
                        @if($antreanKurasiCount > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black bg-rose-600 text-white animate-pulse">{{ $antreanKurasiCount }}</span>
                        @endif
                    </a>
                    <button x-show="activeTab === 'pengumuman'" @click="showPengumumanModal = true" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-3.5 rounded-lg text-xs sm:text-sm shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Pengumuman BEM</span>
                    </button>
                    @endhasrole

                    @hasrole('ormawa')
                    <button x-show="activeTab === 'pengumuman'" @click="showPengumumanModal = true" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-3.5 rounded-lg text-xs sm:text-sm shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Ajukan Berita / Pamflet Acara</span>
                    </button>
                    @endhasrole

                    @hasrole('bpm')
                    <button x-show="activeTab === 'regulasi'" @click="showRegulasiModal = true" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg text-sm shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Tambah Regulasi / UU</span>
                    </button>
                    @endhasrole
                </div>
            </div>

            <!-- Tab Content: Pengumuman -->
            <div x-show="activeTab === 'pengumuman'" class="space-y-6">

                <!-- Filter Kategori Pengumuman -->
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="text-slate-500 font-semibold mr-1">Filter Kategori:</span>
                    <a href="{{ route('informasi.index') }}" class="px-3 py-1.5 rounded-lg font-medium transition {{ empty($kategoriFilter) || $kategoriFilter === 'semua' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                        Semua
                    </a>
                    <a href="{{ route('informasi.index', ['kategori' => 'resmi_kampus']) }}" class="px-3 py-1.5 rounded-lg font-medium transition {{ $kategoriFilter === 'resmi_kampus' ? 'bg-blue-700 text-white shadow-xs' : 'bg-white text-blue-700 border border-blue-200 hover:bg-blue-50' }}">
                        🏛️ Resmi Kampus (BKHM)
                    </a>
                    <a href="{{ route('informasi.index', ['kategori' => 'kegiatan_kemahasiswaan']) }}" class="px-3 py-1.5 rounded-lg font-medium transition {{ $kategoriFilter === 'kegiatan_kemahasiswaan' ? 'bg-indigo-700 text-white shadow-xs' : 'bg-white text-indigo-700 border border-indigo-200 hover:bg-indigo-50' }}">
                        ⚡ Agenda & Acara Ormawa
                    </a>
                </div>

                @hasrole('ormawa')
                @if($pengumumanSaya->isNotEmpty())
                    <!-- Status Pengajuan Berita Ormawa Saya -->
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-5 border border-emerald-200/80 shadow-xs mb-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">📢</span>
                                <h4 class="font-bold text-sm text-emerald-950">Status Pengajuan Berita Organisasi Saya</h4>
                            </div>
                            <span class="text-xs text-emerald-700 font-medium">{{ $pengumumanSaya->count() }} diajukan</span>
                        </div>
                        <div class="space-y-2">
                            @foreach($pengumumanSaya as $ps)
                                <div class="bg-white/90 border border-emerald-100 rounded-xl p-3 flex flex-wrap items-center justify-between gap-2 text-xs">
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-slate-800">{{ $ps->judul }}</span>
                                        <span class="text-slate-400 block text-[11px]">{{ $ps->created_at->format('d/m/Y H:i') }}</span>
                                        @if($ps->catatan_kurasi)
                                            <div class="text-rose-700 bg-rose-50 p-1.5 rounded mt-1 text-[11px]">
                                                <strong>Catatan BEM:</strong> {{ $ps->catatan_kurasi }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[11px] {{ $ps->status === 'published' ? 'bg-emerald-100 text-emerald-800' : ($ps->status === 'pending_kurasi' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                        {{ $ps->status_label }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                @endhasrole

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($pengumuman as $p)
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 hover:shadow-md transition-all overflow-hidden flex flex-col justify-between">
                        <div class="p-6">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $p->badge_color }}">
                                        {{ $p->badge_label }}
                                    </span>
                                    @if($p->tanggal_kegiatan)
                                        <span class="text-[11px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                            📅 {{ $p->tanggal_kegiatan->format('d M Y') }}
                                        </span>
                                    @endif
                                </div>
                                
                                @if(Auth::check() && (Auth::user()->hasAnyRole(['bem', 'bkhm', 'admin']) || Auth::id() === $p->user_id))
                                <form action="{{ route('informasi.pengumuman.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-semibold hover:underline">Hapus</button>
                                </form>
                                @endif
                            </div>

                            <h3 class="text-lg font-bold text-slate-900 mb-2 leading-snug">{{ $p->judul }}</h3>
                            <p class="text-slate-600 text-sm whitespace-pre-wrap leading-relaxed line-clamp-4">{{ $p->isi }}</p>
                        </div>

                        <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span>Diterbitkan oleh: <strong class="text-slate-700 font-medium">{{ $p->user->name ?? 'Kemahasiswaan' }}</strong></span>
                            
                            @if($p->file_lampiran)
                            <a href="{{ route('informasi.pengumuman.lampiran', $p) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-medium rounded shadow-2xs transition-colors">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <span>Lihat Lampiran</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-span-2 text-center py-12 bg-white rounded-xl border border-slate-200/80 shadow-sm text-slate-500">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        <p class="font-semibold text-slate-700">Belum ada pengumuman terbaru</p>
                        <p class="text-xs text-slate-400 mt-1">Pengumuman resmi kemahasiswaan akan ditampilkan di sini.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Tab Content: Regulasi -->
            <div x-show="activeTab === 'regulasi'" style="display: none;" class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-600 text-xs font-bold uppercase tracking-wider">
                                        <th class="py-3.5 px-4 text-left">Judul Dokumen</th>
                                        <th class="py-3.5 px-4 text-left">Kategori</th>
                                        <th class="py-3.5 px-4 text-left">Diterbitkan Oleh</th>
                                        <th class="py-3.5 px-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    @forelse($regulasi as $r)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-4 px-4">
                                            <p class="font-bold text-slate-900 leading-tight">{{ $r->judul }}</p>
                                            @if($r->deskripsi)
                                                <p class="text-xs text-slate-500 mt-1 line-clamp-1">{{ $r->deskripsi }}</p>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            @php
                                                $badgeColor = match($r->kategori) {
                                                    'Undang-Undang' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                    'Pedoman' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                    'Pengumuman' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                    default => 'bg-slate-100 text-slate-800 border-slate-200',
                                                };
                                            @endphp
                                            <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                                {{ $r->kategori }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-slate-600">
                                            <span class="font-medium text-slate-800">{{ $r->user->name }}</span>
                                            <span class="block text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y') }}</span>
                                        </td>
                                        <td class="py-4 px-4 text-center whitespace-nowrap">
                                            <a href="{{ route('informasi.regulasi.unduh', $r) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-lg transition-colors mr-2">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                <span>Unduh PDF</span>
                                            </a>
                                            
                                            @hasrole('bpm')
                                            @if($r->user_id === Auth::id())
                                            <form action="{{ route('informasi.regulasi.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('Hapus regulasi ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-semibold hover:underline">Hapus</button>
                                            </form>
                                            @endif
                                            @endhasrole
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="py-10 text-center text-slate-500">
                                            <p class="font-medium">Belum ada dokumen regulasi yang diunggah.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Tambah Pengumuman -->
        @hasanyrole('bem|bkhm|ormawa|admin')
        <div x-show="showPengumumanModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" @click="showPengumumanModal = false"></div>
                <div class="relative bg-white w-full max-w-lg p-6 rounded-2xl shadow-2xl border border-slate-200">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">
                                @role('bkhm') Terbitkan Pengumuman Resmi Kampus
                                @elserole('bem') Terbitkan Agenda / Berita BEM
                                @elserole('ormawa') Ajukan Publikasi Berita / Acara Ormawa
                                @else Buat Pengumuman Baru
                                @endrole
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                @role('ormawa')
                                    Berita yang Anda ajukan akan dikurasi oleh BEM terlebih dahulu sebelum tayang ke publik.
                                @else
                                    Pengumuman akan langsung dipublikasikan dan dapat diakses mahasiswa.
                                @endrole
                            </p>
                        </div>
                        <button @click="showPengumumanModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                    </div>

                    @role('ormawa')
                    <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-2.5 text-xs text-amber-800">
                        <svg class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Draf publikasi Anda akan melalui proses kurasi oleh BEM. Pastikan pamflet atau informasi jelas dan tidak melanggar etika kemahasiswaan.</span>
                    </div>
                    @endrole

                    <form action="{{ route('informasi.pengumuman.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="judul" value="Judul Pengumuman / Agenda" />
                                <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" placeholder="Contoh: Open Recruitment Anggota Baru / Lomba Nasional" required />
                            </div>
                            <div>
                                <x-input-label for="tanggal_kegiatan" value="Tanggal Kegiatan (Opsional untuk Agenda Acara)" />
                                <x-text-input id="tanggal_kegiatan" name="tanggal_kegiatan" type="date" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="isi" value="Isi / Deskripsi Lengkap" />
                                <textarea id="isi" name="isi" rows="4" class="border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-2xs block mt-1 w-full text-sm" placeholder="Rincian isi pengumuman, deskripsi agenda, persyaratan lomba, narasi..." required></textarea>
                            </div>
                            <div>
                                <x-input-label for="file_lampiran" value="File Lampiran / Pamflet (Opsional, PDF/JPG/PNG/JPEG maks 5MB)" />
                                <input id="file_lampiran" name="file_lampiran" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full border border-slate-300 rounded-lg p-2 text-sm text-slate-600 bg-slate-50" />
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-slate-200">
                            <button type="button" @click="showPengumumanModal = false" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 font-medium text-sm">Batal</button>
                            <x-primary-button>
                                @role('ormawa') Ajukan ke BEM @else Terbitkan Sekarang @endrole
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endhasanyrole

        <!-- Modal Tambah Regulasi -->
        @hasrole('bpm')
        <div x-show="showRegulasiModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" @click="showRegulasiModal = false"></div>
                <div class="relative bg-white w-full max-w-lg p-6 rounded-2xl shadow-2xl border border-slate-200">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                        <h3 class="text-lg font-bold text-slate-900">Tambah Regulasi / Pedoman Baru</h3>
                        <button @click="showRegulasiModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                    </div>
                    <form action="{{ route('informasi.regulasi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="judul_reg" value="Judul Dokumen" />
                                <x-text-input id="judul_reg" name="judul" type="text" class="mt-1 block w-full" placeholder="Contoh: UU DEMA No. 2 Tahun 2026" required />
                            </div>
                            <div>
                                <x-input-label for="kategori_reg" value="Kategori Regulasi" />
                                <select name="kategori" id="kategori_reg" class="border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-2xs block mt-1 w-full text-sm" required>
                                    <option value="Undang-Undang">Undang-Undang</option>
                                    <option value="Pedoman">Pedoman</option>
                                    <option value="Pengumuman">Pengumuman</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="deskripsi_reg" value="Deskripsi Singkat" />
                                <textarea id="deskripsi_reg" name="deskripsi" rows="3" class="border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-2xs block mt-1 w-full text-sm" placeholder="Penjelasan singkat isi regulasi..."></textarea>
                            </div>
                            <div>
                                <x-input-label for="file_dokumen" value="Dokumen PDF (Maks. 10MB)" />
                                <input id="file_dokumen" name="file" type="file" accept=".pdf" class="mt-1 block w-full border border-slate-300 rounded-lg p-2 text-sm text-slate-600 bg-slate-50" required />
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-slate-200">
                            <button type="button" @click="showRegulasiModal = false" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 font-medium text-sm">Batal</button>
                            <x-primary-button>Unggah Dokumen</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endhasrole
    </div>
</x-app-layout>