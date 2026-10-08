<div x-data="{ open: false, activeTab: 'alur', activeFaq: null }"
     @open-panduan-modal.window="open = true"
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
         aria-labelledby="panduan-title">

        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col pointer-events-auto overflow-hidden">
            
            {{-- Header Modal Si Ujang --}}
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-5 sm:p-6 border-b border-slate-800 relative shrink-0">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-14 h-14 min-w-[56px] max-w-[56px] rounded-2xl bg-[#1E3A8A] border-2 border-amber-400 p-1 flex items-center justify-center shrink-0 shadow-lg shadow-amber-500/20">
                            <img src="{{ asset('images/maskot-itg-head.png') }}" class="w-full h-full object-contain rounded-xl max-w-full max-h-full block" alt="Si Ujang">
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Asisten Panduan Mahasiswa</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-400/20 text-amber-300 border border-amber-400/30">SI UJANG ITG</span>
                            </div>
                            <h2 id="panduan-title" class="text-lg sm:text-xl font-extrabold text-white tracking-tight mt-0.5">
                                Panduan Interaktif Sistem Ormawa
                            </h2>
                            <p class="text-xs text-slate-300 mt-1 max-w-xl leading-relaxed">
                                @role('ormawa')
                                    Halo Pengurus Ormawa! Si Ujang siap memandu alur pengajuan proposal kegiatan, peminjaman sarpras, serta pelaporan LPJ.
                                @elserole('bem')
                                    Halo Presidium BEM! Pelajari tata cara penelaahan awal proposal dan koordinasi agenda kegiatan ormawa.
                                @elserole('bpm')
                                    Halo Legislator BPM! Pelajari mekanisme verifikasi legislatif, pengawasan anggaran, dan penerbitan surat peringatan.
                                @elserole('bkhm')
                                    Halo Pembina Kemahasiswaan! Panduan verifikasi tingkat institusi, manajemen saldo kas, dan kurasi berita kampus.
                                @elserole('wr3')
                                    Halo Wakil Rektor III! Panduan pengesahan tingkat pimpinan kampus dan validasi tanda tangan digital QR institusi.
                                @elserole('bendahara')
                                    Halo Bendahara Kampus! Panduan otorisasi transfer pendanaan termin, pemotongan saldo kas, dan bukti pembayaran.
                                @elserole('sarpras')
                                    Halo Pengelola Sarpras! Panduan pengelolaan izin penggunaan ruangan, inventaris alat, dan jadwal kegiatan.
                                @else
                                    Selamat datang di sistem manajemen kemahasiswaan Institut Teknologi Garut. Pelajari tata kelola alur kerja digital di sini.
                                @endrole
                            </p>
                        </div>
                    </div>

                    {{-- Tombol Tutup Modal --}}
                    <button @click="open = false"
                            class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400"
                            aria-label="Tutup panduan">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Tab Navigasi Segmented --}}
                <div class="flex items-center gap-1.5 mt-5 bg-slate-800/80 p-1 rounded-xl border border-slate-700/60 max-w-md">
                    <button @click="activeTab = 'alur'"
                            :class="activeTab === 'alur' ? 'bg-amber-400 text-slate-900 font-bold shadow-xs' : 'text-slate-300 hover:text-white font-medium'"
                            class="flex-1 py-1.5 px-3 rounded-lg text-xs transition text-center focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400">
                        Peta Alur 7 Tahap
                    </button>
                    <button @click="activeTab = 'peran'"
                            :class="activeTab === 'peran' ? 'bg-amber-400 text-slate-900 font-bold shadow-xs' : 'text-slate-300 hover:text-white font-medium'"
                            class="flex-1 py-1.5 px-3 rounded-lg text-xs transition text-center focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400">
                        Panduan Peran Saya
                    </button>
                    <button @click="activeTab = 'faq'"
                            :class="activeTab === 'faq' ? 'bg-amber-400 text-slate-900 font-bold shadow-xs' : 'text-slate-300 hover:text-white font-medium'"
                            class="flex-1 py-1.5 px-3 rounded-lg text-xs transition text-center focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400">
                        FAQ &amp; Kendala
                    </button>
                </div>
            </div>

            {{-- Body Modal --}}
            <div class="p-6 overflow-y-auto space-y-6 flex-1 text-slate-700 text-xs sm:text-sm">
                
                {{-- TAB 1: PETA ALUR 7 TAHAP --}}
                <div x-show="activeTab === 'alur'" class="space-y-4">
                    <div class="bg-blue-50/70 border border-blue-200/80 rounded-2xl p-4 text-xs text-blue-900 flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <span class="font-bold block text-blue-950">Alur Standar Pengajuan Kegiatan &amp; Anggaran ITG</span>
                            <span>Setiap proposal melewati 7 tahapan resmi untuk menjamin akuntabilitas naskah, kesesuaian anggaran, dan legalitas izin kegiatan.</span>
                        </div>
                    </div>

                    <div class="relative pl-6 space-y-4 before:absolute before:left-2.5 before:top-3 before:bottom-3 before:w-0.5 before:bg-slate-200">
                        
                        {{-- Tahap 1 --}}
                        <div class="relative group">
                            <span class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-blue-600 text-white font-bold text-[10px] flex items-center justify-center ring-4 ring-white">1</span>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 hover:bg-slate-100/70 transition">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Pengajuan Proposal &amp; RAB (Ormawa)</h4>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800">Ormawa</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1">Ormawa mengisi formulir atau menggunakan <strong>Generator Proposal Otomatis</strong>, melampirkan rincian anggaran, dan mengklik tombol "Ajukan".</p>
                            </div>
                        </div>

                        {{-- Tahap 2 --}}
                        <div class="relative group">
                            <span class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-indigo-600 text-white font-bold text-[10px] flex items-center justify-center ring-4 ring-white">2</span>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 hover:bg-slate-100/70 transition">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Telaah Awal &amp; Sinkronisasi Proker (BEM)</h4>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-100 text-indigo-800">BEM</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1">Kementerian Terkait BEM menelaah relevansi kegiatan terhadap kalender proker ormawa sebelum diteruskan ke legislatif.</p>
                            </div>
                        </div>

                        {{-- Tahap 3 --}}
                        <div class="relative group">
                            <span class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-indigo-700 text-white font-bold text-[10px] flex items-center justify-center ring-4 ring-white">3</span>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 hover:bg-slate-100/70 transition">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Verifikasi Legislatif &amp; Plafon Anggaran (BPM)</h4>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-100 text-indigo-800">BPM</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1">Komisi Anggaran BPM memeriksa kewajaran alokasi biaya dan kepatuhan terhadap undang-undang ormawa ITG.</p>
                            </div>
                        </div>

                        {{-- Tahap 4 --}}
                        <div class="relative group">
                            <span class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-amber-600 text-white font-bold text-[10px] flex items-center justify-center ring-4 ring-white">4</span>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 hover:bg-slate-100/70 transition">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Peninjauan Administrasi Institusi (BKHM)</h4>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800">BKHM</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1">Biro Kemahasiswaan memverifikasi kesiapan sarana, perizinan institusi, dan sisa pagu saldo kas tahunan ormawa.</p>
                            </div>
                        </div>

                        {{-- Tahap 5 --}}
                        <div class="relative group">
                            <span class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-emerald-600 text-white font-bold text-[10px] flex items-center justify-center ring-4 ring-white">5</span>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 hover:bg-slate-100/70 transition">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Pengesahan Pimpinan &amp; TTD Digital (WR3)</h4>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">Wakil Rektor III</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1">Wakil Rektor III menandatangani lembar persetujuan secara elektronik yang dilengkapi kode verifikasi QR resmi.</p>
                            </div>
                        </div>

                        {{-- Tahap 6 --}}
                        <div class="relative group">
                            <span class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-teal-600 text-white font-bold text-[10px] flex items-center justify-center ring-4 ring-white">6</span>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 hover:bg-slate-100/70 transition">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Pencairan Dana Bertahap / Termin (Bendahara)</h4>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-teal-100 text-teal-800">Bendahara</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1">Bendahara mentransfer dana termin ke rekening ormawa, mengunggah bukti transfer, dan sistem otomatis memotong saldo kas ormawa.</p>
                            </div>
                        </div>

                        {{-- Tahap 7 --}}
                        <div class="relative group">
                            <span class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-rose-600 text-white font-bold text-[10px] flex items-center justify-center ring-4 ring-white">7</span>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 hover:bg-slate-100/70 transition">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Pelaporan Pertanggungjawaban (LPJ)</h4>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-100 text-rose-800">Ormawa &amp; Verifikator</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1">Setelah kegiatan selesai, ormawa mengunggah berkas LPJ paling lambat 14 hari kerja untuk diverifikasi oleh BKHM dan WR3.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: PANDUAN PERAN SAYA --}}
                <div x-show="activeTab === 'peran'" class="space-y-4">
                    
                    @role('ormawa')
                    <div class="space-y-3">
                        <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/50">
                            <h4 class="font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center text-xs font-bold">1</span>
                                Pengajuan Proposal Cepat &amp; Generator Naskah
                            </h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Anda memiliki dua opsi: mengunggah naskah proposal PDF mandiri di menu <strong>Daftar Pengajuan</strong>, atau menggunakan menu <strong>Generator Dokumen Proposal</strong> untuk menghasilkan naskah dinas resmi ber-kop dan lembar penandatangan otomatis.
                            </p>
                        </div>

                        <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/50">
                            <h4 class="font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center text-xs font-bold">2</span>
                                Peminjaman Ruangan &amp; Sarana Sarpras
                            </h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Cek jadwal okupansi ruangan sebelum mengajukan kegiatan melalui menu <strong>Peminjaman Tempat &amp; Barang</strong>. Pengajuan ruangan akan diverifikasi secara paralel oleh BKHM dan Bagian Sarpras.
                            </p>
                        </div>

                        <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/50">
                            <h4 class="font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center text-xs font-bold">3</span>
                                Monitoring Pencairan &amp; Disiplin LPJ
                            </h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Perhatikan status tahapan termin dana Anda. Unggah berkas LPJ tepat waktu di menu <strong>Monitoring LPJ</strong> untuk menghindari sanksi penangguhan hak pengajuan atau Surat Peringatan (SP) dari BPM/BKHM.
                            </p>
                        </div>
                    </div>
                    @endrole

                    @hasanyrole('bem|bpm|bkhm|wr3')
                    <div class="space-y-3">
                        <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/50">
                            <h4 class="font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-800 flex items-center justify-center text-xs font-bold">1</span>
                                Penelaahan Berkas di Meja Verifikasi
                            </h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Seluruh berkas yang menunggu tindakan Anda terdaftar di menu <strong>Verifikasi Pengajuan</strong>. Anda dapat melihat pratinjau PDF proposal, rincian anggaran yang diajukan, serta riwayat catatan pemeriksa sebelumnya.
                            </p>
                        </div>

                        <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/50">
                            <h4 class="font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-800 flex items-center justify-center text-xs font-bold">2</span>
                                Tindakan Persetujuan &amp; Catatan Revisi
                            </h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Jika berkas memerlukan perbaikan, gunakan opsi <strong>Kembalikan / Tolak</strong> dengan memberikan catatan yang konstruktif. Ormawa akan mendapatkan notifikasi real-time untuk segera memperbaikinya.
                            </p>
                        </div>

                        @role('wr3')
                        <div class="border border-slate-200 rounded-2xl p-4 bg-emerald-50/60 border-emerald-200">
                            <h4 class="font-bold text-emerald-950 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-emerald-200 text-emerald-900 flex items-center justify-center text-xs font-bold">3</span>
                                Pengesahan Elektronik &amp; QR Code
                            </h4>
                            <p class="text-xs text-emerald-900 mt-1 leading-relaxed">
                                Saat Wakil Rektor III menyetujui proposal atau surat peringatan, sistem secara otomatis menerbitkan stempel QR digital terenkripsi yang dapat dipindai oleh publik untuk validasi keaslian dokumen.
                            </p>
                        </div>
                        @endrole

                        @role('bpm')
                        <div class="border border-slate-200 rounded-2xl p-4 bg-amber-50/60 border-amber-200">
                            <h4 class="font-bold text-amber-950 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-amber-200 text-amber-900 flex items-center justify-center text-xs font-bold">3</span>
                                Penerbitan Surat Peringatan (SP)
                            </h4>
                            <p class="text-xs text-amber-900 mt-1 leading-relaxed">
                                BPM memiliki wewenang menerbitkan SP internal parlemen (langsung berlaku) atau SP reguler institusi (melalui tinjauan BKHM dan pengesahan WR3) bagi ormawa yang melanggar tata tertib.
                            </p>
                        </div>
                        @endrole
                    </div>
                    @endhasanyrole

                    @role('bendahara')
                    <div class="space-y-3">
                        <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/50">
                            <h4 class="font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center text-xs font-bold">1</span>
                                Antrean Siap Transfer Dana
                            </h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Proposal yang telah disetujui secara resmi oleh WR3 akan otomatis muncul di <strong>Dashboard Bendahara</strong> pada tabel <em>Antrean Siap Dicairkan</em>.
                            </p>
                        </div>

                        <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/50">
                            <h4 class="font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center text-xs font-bold">2</span>
                                Pencairan Per-Termin &amp; Pemotongan Kas Otomatis
                            </h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Masukkan nominal yang dicairkan dan lampirkan bukti slip transfer. Saldo kas ormawa akan terpotong secara instan dan riwayat audit mutasi keuangan akan tercatat rapi.
                            </p>
                        </div>
                    </div>
                    @endrole

                </div>

                {{-- TAB 3: FAQ & KENDALA --}}
                <div x-show="activeTab === 'faq'" class="space-y-3">
                    
                    {{-- FAQ 1 --}}
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition">
                        <button type="button"
                                @click="activeFaq = (activeFaq === 1 ? null : 1)"
                                class="w-full p-4 text-left font-bold text-slate-900 text-xs sm:text-sm flex items-center justify-between hover:bg-slate-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 cursor-pointer">
                            <span>Berapa lama proses verifikasi proposal dari pengajuan hingga pencairan?</span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="activeFaq === 1 ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeFaq === 1" class="p-4 pt-0 text-xs text-slate-600 border-t border-slate-100 bg-slate-50/50 leading-relaxed">
                            Rata-rata alur verifikasi lengkap memerlukan waktu 3 hingga 5 hari kerja jika seluruh berkas dan rincian anggaran telah memenuhi syarat. Anda dapat memantau status alur secara transparan langsung pada tabel pengajuan.
                        </div>
                    </div>

                    {{-- FAQ 2 --}}
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition">
                        <button type="button"
                                @click="activeFaq = (activeFaq === 2 ? null : 2)"
                                class="w-full p-4 text-left font-bold text-slate-900 text-xs sm:text-sm flex items-center justify-between hover:bg-slate-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 cursor-pointer">
                            <span>Bagaimana jika tanggal pelaksanaan kegiatan sudah sangat mendesak?</span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="activeFaq === 2 ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeFaq === 2" class="p-4 pt-0 text-xs text-slate-600 border-t border-slate-100 bg-slate-50/50 leading-relaxed">
                            Sistem secara cerdas memberikan badge peringatan <em>Mendesak (H-3 atau H-7)</em> yang menonjol pada meja kerja verifikator. Anda juga dapat menggunakan fitur pengingat (*nudge*) setelah masa jeda 12 jam sejak pengajuan terakhir.
                        </div>
                    </div>

                    {{-- FAQ 3 --}}
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition">
                        <button type="button"
                                @click="activeFaq = (activeFaq === 3 ? null : 3)"
                                class="w-full p-4 text-left font-bold text-slate-900 text-xs sm:text-sm flex items-center justify-between hover:bg-slate-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 cursor-pointer">
                            <span>Kapan pencairan termin berikutnya (termin 2) dapat diproses?</span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="activeFaq === 3 ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeFaq === 3" class="p-4 pt-0 text-xs text-slate-600 border-t border-slate-100 bg-slate-50/50 leading-relaxed">
                            Pencairan termin berikutnya dapat dilakukan setelah termin sebelumnya berstatus dicairkan dan pengurus ormawa telah mengonfirmasi pelaksanaan kegiatan tahap berjalan kepada unit keuangan/Bendahara.
                        </div>
                    </div>

                    {{-- FAQ 4 --}}
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition">
                        <button type="button"
                                @click="activeFaq = (activeFaq === 4 ? null : 4)"
                                class="w-full p-4 text-left font-bold text-slate-900 text-xs sm:text-sm flex items-center justify-between hover:bg-slate-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 cursor-pointer">
                            <span>Apa yang harus dilakukan jika proposal dikembalikan atau ditolak?</span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="activeFaq === 4 ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeFaq === 4" class="p-4 pt-0 text-xs text-slate-600 border-t border-slate-100 bg-slate-50/50 leading-relaxed">
                            Buka halaman detail pengajuan, baca catatan revisi dari verifikator yang bersangkutan, klik tombol <strong>Edit</strong>, perbaiki rincian data atau berkas lampiran yang diminta, lalu ajukan kembali.
                        </div>
                    </div>

                </div>

            </div>

            {{-- Footer Aksi Cepat Modal --}}
            <div class="bg-slate-50 p-4 sm:p-5 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Sistem Terintegrasi ITG &bull; T.A. 2026/2027</span>
                </div>

                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-end">
                    <a href="{{ route('informasi.index') }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition shadow-2xs min-h-[36px]">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Pusat Regulasi &amp; Berita</span>
                    </a>
                    <button @click="open = false" 
                            type="button"
                            class="inline-flex items-center justify-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-xs min-h-[36px]">
                        Tutup Panduan
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
