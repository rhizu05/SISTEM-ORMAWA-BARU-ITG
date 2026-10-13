<div x-data="{ open: false }"
     @open-about-modal.window="open = true"
     @keydown.escape.window="open = false"
     x-cloak
     class="relative z-50">

    {{-- Backdrop Overlay --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
         @click="open = false"
         aria-hidden="true"></div>

    {{-- Modal Dialog --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 scale-95 translate-y-3"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-3"
         class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 lg:p-8 flex items-center justify-center pointer-events-none"
         role="dialog"
         aria-modal="true"
         aria-labelledby="about-modal-title">

        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col pointer-events-auto overflow-hidden">
            
            {{-- Header Modal --}}
            <div class="bg-[#0B1528] text-white p-5 sm:p-6 border-b border-slate-800 relative shrink-0">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-12 h-12 rounded-2xl bg-[#1E3A8A] p-1 flex items-center justify-center shrink-0 shadow-md">
                            <img src="{{ asset('images/logo-skin.png') }}" class="w-full h-full object-contain" alt="Logo SKIN ITG">
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Tentang Sistem &amp; Pengembang</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-500/20 text-blue-300 border border-blue-400/30">v1.0.0</span>
                            </div>
                            <h2 id="about-modal-title" class="text-lg sm:text-xl font-extrabold text-white tracking-tight mt-0.5 truncate">
                                SKIN ITG : Sistem Ormawa
                            </h2>
                            <p class="text-xs text-slate-300 mt-0.5 truncate">
                                Institut Teknologi Garut · Biro Kemahasiswaan &amp; Hubungan Masyarakat
                            </p>
                        </div>
                    </div>

                    {{-- Tombol Tutup Modal --}}
                    <button type="button"
                            @click="open = false"
                            class="min-h-[44px] min-w-[44px] rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 cursor-pointer"
                            aria-label="Tutup jendela tentang sistem">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-5 sm:p-6 overflow-y-auto space-y-6">

                {{-- Section: Ringkasan Sistem --}}
                <div class="p-4 rounded-2xl bg-slate-50 text-xs text-slate-700 leading-relaxed space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span class="font-extrabold text-slate-900 text-sm">Sistem Informasi Keuangan &amp; Administrasi Ormawa</span>
                    </div>
                    <p>
                        Platform digital resmi untuk tata kelola kegiatan kemahasiswaan di lingkungan <span class="font-semibold text-slate-900">Institut Teknologi Garut</span>. Mengintegrasikan verifikasi proposal berjenjang (BEM &rarr; BPM &rarr; BKHM &rarr; WR3 &rarr; Bendahara), peminjaman sarana prasarana kampus, laporan pertanggungjawaban (LPJ), kas ormawa, dan persuratan digital terverifikasi QR Code.
                    </p>
                </div>

                {{-- Section: Pencetus Awal Sistem --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.516 0c.85.493 1.508 1.333 1.508 2.316V18" />
                            </svg>
                            <span>Pencetus Awal Sistem</span>
                        </h3>
                        <span class="text-[11px] text-slate-500 font-medium">Inisiator Proyek</span>
                    </div>

                    {{-- Kartu Pencetus Awal --}}
                    <div class="rounded-2xl bg-slate-50 p-4 sm:p-4.5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                            <div class="flex items-start sm:items-center gap-3.5">
                                <div class="w-14 h-14 min-w-[56px] min-h-[56px] max-w-[56px] max-h-[56px] rounded-full overflow-hidden bg-slate-200 shrink-0">
                                    <img src="{{ file_exists(public_path('images/developers/reikhan.jpg')) ? asset('images/developers/reikhan.jpg') : (file_exists(public_path('images/developers/reikhan.png')) ? asset('images/developers/reikhan.png') : 'https://github.com/rdreikhan-commits.png') }}"
                                         alt="Rd Moch Reikhan Ibnu"
                                         class="w-full h-full object-cover"
                                         width="56"
                                         height="56"
                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Rd+Moch+Reikhan&background=D97706&color=fff&size=128';">
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm font-extrabold text-slate-900 leading-tight">
                                        Rd Moch Reikhan Ibnu
                                    </h4>
                                    <p class="text-xs text-amber-700 font-semibold mt-0.5">
                                        NIM: 2407013
                                    </p>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Pencetus Awal &amp; Inisiator Proyek
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200/80">
                                <a href="https://github.com/rdreikhan-commits"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="min-h-[40px] px-3.5 py-2 rounded-xl bg-slate-900 text-white text-xs font-semibold flex items-center justify-center gap-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900"
                                   aria-label="Profil GitHub Rd Moch Reikhan Ibnu">
                                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                                    </svg>
                                    <span>GitHub</span>
                                </a>
                                <a href="https://www.linkedin.com/in/rdhanss"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="min-h-[40px] px-3.5 py-2 rounded-xl bg-[#0A66C2] text-white text-xs font-semibold flex items-center justify-center gap-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600"
                                   aria-label="Profil LinkedIn Rd Moch Reikhan Ibnu">
                                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
                                    </svg>
                                    <span>LinkedIn</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section: Pengembang Sistem (Alih Kelola Proyek) --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Pengembang Sistem (Alih Kelola)</span>
                        </h3>
                        <span class="text-[11px] text-slate-500 font-medium">S1 Teknik Informatika · ITG</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        {{-- Kartu Pengembang 1: Rasyid --}}
                        <div class="rounded-2xl bg-slate-50 p-4.5 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start gap-3.5 mb-3">
                                    <div class="w-14 h-14 min-w-[56px] min-h-[56px] max-w-[56px] max-h-[56px] rounded-full overflow-hidden bg-slate-200 shrink-0">
                                        <img src="{{ file_exists(public_path('images/developers/rasyid.jpg')) ? asset('images/developers/rasyid.jpg') : (file_exists(public_path('images/developers/rasyid.png')) ? asset('images/developers/rasyid.png') : 'https://github.com/rhizu05.png') }}"
                                             alt="Muhamad Ar Rasyid Rizki Oktavian"
                                             class="w-full h-full object-cover"
                                             width="56"
                                             height="56"
                                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Muhamad+Ar+Rasyid&background=1E3A8A&color=fff&size=128';">
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-extrabold text-slate-900 leading-tight">
                                            Muhamad Ar Rasyid Rizki Oktavian
                                        </h4>
                                        <p class="text-xs text-blue-700 font-semibold mt-0.5">
                                            NIM: 2306045
                                        </p>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                            Pengembang Sistem
                                        </span>
                                    </div>
                                </div>
                                <p class="text-[11px] text-slate-600 leading-relaxed mb-4">
                                    Mahasiswa S1 Teknik Informatika ITG. Mengambil alih dan melanjutkan rancang bangun logika sistem, modul administrasi persuratan, dan penataan alur kerja ormawa.
                                </p>
                            </div>

                            {{-- Tautan Sosial Media --}}
                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200/80">
                                <a href="https://github.com/rhizu05"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="min-h-[44px] px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-semibold flex items-center justify-center gap-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900"
                                   aria-label="Profil GitHub Muhamad Ar Rasyid">
                                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                                    </svg>
                                    <span>rhizu05</span>
                                </a>
                                <a href="https://www.linkedin.com/in/rasyidoktavian"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="min-h-[44px] px-3 py-2 rounded-xl bg-[#0A66C2] text-white text-xs font-semibold flex items-center justify-center gap-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600"
                                   aria-label="Profil LinkedIn Muhamad Ar Rasyid">
                                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
                                    </svg>
                                    <span>LinkedIn</span>
                                </a>
                            </div>
                        </div>

                        {{-- Kartu Pengembang 2: Naufal --}}
                        <div class="rounded-2xl bg-slate-50 p-4.5 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start gap-3.5 mb-3">
                                    <div class="w-14 h-14 min-w-[56px] min-h-[56px] max-w-[56px] max-h-[56px] rounded-full overflow-hidden bg-slate-200 shrink-0">
                                        <img src="{{ file_exists(public_path('images/developers/naufal.jpg')) ? asset('images/developers/naufal.jpg') : (file_exists(public_path('images/developers/naufal.png')) ? asset('images/developers/naufal.png') : 'https://github.com/SepNaufal.png') }}"
                                             alt="Sep Naufal Dzimar Sadli"
                                             class="w-full h-full object-cover"
                                             width="56"
                                             height="56"
                                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Sep+Naufal&background=4F46E5&color=fff&size=128';">
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-extrabold text-slate-900 leading-tight">
                                            Sep Naufal Dzimar Sadli
                                        </h4>
                                        <p class="text-xs text-indigo-700 font-semibold mt-0.5">
                                            NIM: 2306078
                                        </p>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800">
                                            Pengembang Sistem
                                        </span>
                                    </div>
                                </div>
                                <p class="text-[11px] text-slate-600 leading-relaxed mb-4">
                                    Mahasiswa S1 Teknik Informatika ITG. Mengambil alih dan melanjutkan pengembangan antarmuka pengguna, standarisasi aksesibilitas, dan tata letak aplikasi.
                                </p>
                            </div>

                            {{-- Tautan Sosial Media --}}
                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200/80">
                                <a href="https://github.com/SepNaufal"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="min-h-[44px] px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-semibold flex items-center justify-center gap-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900"
                                   aria-label="Profil GitHub Sep Naufal">
                                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                                    </svg>
                                    <span>SepNaufal</span>
                                </a>
                                <a href="https://www.linkedin.com/in/sep-naufal-dzimar-sadli-616035348"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="min-h-[44px] px-3 py-2 rounded-xl bg-[#0A66C2] text-white text-xs font-semibold flex items-center justify-center gap-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600"
                                   aria-label="Profil LinkedIn Sep Naufal">
                                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
                                    </svg>
                                    <span>LinkedIn</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Stack & Institusi Info --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 text-center">
                    <div class="p-2.5 rounded-xl bg-slate-50">
                        <span class="block text-[10px] text-slate-500 font-semibold uppercase">Institusi</span>
                        <span class="block text-xs font-bold text-slate-800 mt-0.5">Institut Teknologi Garut</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50">
                        <span class="block text-[10px] text-slate-500 font-semibold uppercase">Program Studi</span>
                        <span class="block text-xs font-bold text-slate-800 mt-0.5">Teknik Informatika</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50">
                        <span class="block text-[10px] text-slate-500 font-semibold uppercase">Pengampu</span>
                        <span class="block text-xs font-bold text-slate-800 mt-0.5">BKHM ITG</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50">
                        <span class="block text-[10px] text-slate-500 font-semibold uppercase">Teknologi</span>
                        <span class="block text-xs font-bold text-slate-800 mt-0.5">Laravel &middot; Tailwind</span>
                    </div>
                </div>

            </div>

            {{-- Footer Modal --}}
            <div class="bg-slate-50 px-5 sm:px-6 py-3.5 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                <span class="text-xs text-slate-500 text-center sm:text-left">
                    &copy; {{ date('Y') }} Institut Teknologi Garut · Dikembangkan oleh Mahasiswa Informatika ITG
                </span>
                <button type="button"
                        @click="open = false"
                        class="min-h-[44px] w-full sm:w-auto px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
