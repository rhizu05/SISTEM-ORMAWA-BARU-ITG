<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Layanan Kemahasiswaan Terpadu - ITG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">
    <!-- Navbar -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo_itg.png') }}" alt="Logo ITG" class="h-12 w-auto object-contain">
                <div>
                    <span class="text-xs font-semibold tracking-wider text-blue-700 uppercase block">Institut Teknologi Garut</span>
                    <span class="text-lg font-black tracking-tight text-slate-900 block leading-tight">Portal Layanan Mahasiswa</span>
                </div>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('layanan.cek-status') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Lacak Tiket
                </a>
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
                    Login Pengurus
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Header -->
    <section class="relative overflow-hidden bg-gradient-to-b from-blue-900 to-indigo-950 text-white py-16 px-4 sm:px-6 lg:px-8 text-center">
        <div class="max-w-4xl mx-auto space-y-4">
            <span class="inline-block px-3.5 py-1 rounded-full text-xs font-semibold bg-blue-800/80 border border-blue-600/50 text-blue-200">
                Pusat Layanan Mahasiswa Tanpa Login
            </span>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">
                Layanan Kemahasiswaan Terpadu ITG
            </h1>
            <p class="text-base sm:text-lg text-blue-100 max-w-2xl mx-auto font-light leading-relaxed">
                Sampaikan aspirasi, jadwalkan konseling personal, atau daftarkan prestasi dan permohonan delegasi lomba Anda dengan aman, cepat, dan transparan melalui sistem tiket kami.
            </p>
            <div class="pt-4 flex flex-wrap justify-center gap-3">
                <a href="{{ route('layanan.cek-status') }}" class="px-6 py-3 rounded-xl font-bold text-sm bg-white text-blue-900 shadow-lg hover:bg-blue-50 transition">
                    🔍 Cek Status Pengajuan Tiket
                </a>
                <a href="{{ route('prestasi.showcase') }}" class="px-6 py-3 rounded-xl font-semibold text-sm bg-blue-800/60 text-white border border-blue-700 hover:bg-blue-800 transition">
                    🏆 Showcase Prestasi Mahasiswa
                </a>
            </div>
        </div>
    </section>

    <!-- 3 Layanan Utama -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="text-center mb-12">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">Pilih Kategori Layanan</h2>
            <p class="text-slate-600 mt-2 text-sm">Tidak memerlukan login akun. Cukup isi NIM dan Email aktif untuk menerima Kode Tiket pelacakan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1: Aspirasi & Kritik -->
            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between group hover:border-blue-400">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition">
                        💬
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Aspirasi & Kritik</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-6">
                        Sampaikan kritik konstruktif, saran, dan gagasan Anda untuk kemajuan kampus. Ditampung oleh BPM dan dikoordinasikan langsung ke BKHM ITG.
                    </p>
                </div>
                <a href="{{ route('layanan.aspirasi.create') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-semibold text-sm bg-blue-600 hover:bg-blue-700 text-white transition shadow-sm">
                    Kirim Aspirasi &rarr;
                </a>
            </div>

            <!-- Card 2: Konseling Mahasiswa (Rahasia) -->
            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between group hover:border-emerald-400 relative overflow-hidden">
                <div class="absolute top-4 right-4">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Privat & Rahasia
                    </span>
                </div>
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition">
                        🤝
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Konseling Personal</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-6">
                        Konsultasikan permasalahan akademik, finansial, maupun masalah pribadi Anda secara aman. Ditangani langsung secara tertutup oleh staf BKHM ITG.
                    </p>
                </div>
                <a href="{{ route('layanan.konseling.create') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-semibold text-sm bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-sm">
                    Ajukan Konseling &rarr;
                </a>
            </div>

            <!-- Card 3: Prestasi & Bantuan Lomba -->
            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between group hover:border-amber-400">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition">
                        🏆
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Prestasi & Delegasi</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-6">
                        Laporkan prestasi lomba yang telah Anda raih (untuk apresiasi & arsip), atau ajukan permohonan pendanaan delegasi lomba yang akan Anda ikuti ke BKHM.
                    </p>
                </div>
                <a href="{{ route('layanan.prestasi.create') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-semibold text-sm bg-amber-600 hover:bg-amber-700 text-white transition shadow-sm">
                    Lapor / Ajukan Lomba &rarr;
                </a>
            </div>
        </div>

        <!-- Section Cek Tiket Cepat -->
        <div class="mt-16 bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-8 sm:p-12 text-white shadow-xl">
            <div class="max-w-3xl mx-auto text-center space-y-4">
                <h3 class="text-2xl sm:text-3xl font-extrabold">Sudah Pernah Mengajukan?</h3>
                <p class="text-slate-300 text-sm">
                    Pantau perkembangan, jadwal temu konseling, atau status tindak lanjut pengajuan Anda menggunakan Kode Tiket unik dan Alamat Email Anda.
                </p>
                <form action="{{ route('layanan.cek-status') }}" method="GET" class="mt-6 flex flex-col sm:flex-row gap-3 max-w-xl mx-auto">
                    <input type="text" name="kode" placeholder="Kode Tiket (SKIN-TKT-2026-XXXX)" class="flex-1 px-4 py-3 rounded-xl text-slate-900 placeholder-slate-400 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                    <input type="email" name="email" placeholder="Alamat Email Anda" class="flex-1 px-4 py-3 rounded-xl text-slate-900 placeholder-slate-400 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 font-bold text-sm text-white transition shadow-md">
                        Lacak
                    </button>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} Institut Teknologi Garut. Biro Kemahasiswaan dan Hubungan Masyarakat (BKHM).</p>
    </footer>
</body>
</html>
