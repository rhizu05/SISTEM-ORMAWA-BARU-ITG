<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Kirim Aspirasi & Suara Mahasiswa') }}
            </h2>
            <span class="text-xs text-slate-500">Kanal aspirasi resmi dikelola Dewan Perwakilan Mahasiswa / BPM ITG</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80 p-8">
                <div class="mb-8 text-center max-w-lg mx-auto">
                    <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Sampaikan Aspirasimu</h3>
                    <p class="text-sm text-slate-500 mt-1 leading-relaxed">
                        Suara Anda adalah kontribusi penting bagi perbaikan fasilitas, birokrasi, dan mutu kegiatan kemahasiswaan di Institut Teknologi Garut.
                    </p>
                </div>

                <form action="{{ route('aspirasi.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    {{-- Alert Opsi Anonim --}}
                    <div class="flex items-start gap-3 p-4 bg-indigo-50/70 border border-indigo-100 rounded-xl">
                        <div class="flex items-center h-5 mt-0.5">
                            <input type="checkbox" name="anonim" id="anonim" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        </div>
                        <label for="anonim" class="text-sm text-slate-700 cursor-pointer select-none">
                            <span class="font-semibold block text-slate-900">Kirim sebagai Anonim</span>
                            <span class="text-xs text-slate-500">Identitas akun/nama Anda tidak akan ditampilkan ke publik maupun pengurus, namun aspirasi tetap ditindaklanjuti secara resmi.</span>
                        </label>
                    </div>

                    <div>
                        <x-input-label for="judul" :value="__('Judul Aspirasi')" />
                        <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full text-sm" required placeholder="Contoh: Perbaikan Ventilasi Udara di Aula Gedung Rektorat" />
                    </div>

                    <div>
                        <x-input-label for="kategori" :value="__('Kategori')" />
                        <select name="kategori" id="kategori" class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-2xs text-sm text-slate-800" required>
                            <option value="Fasilitas">Fasilitas Kampus & Sarana</option>
                            <option value="Pelayanan">Pelayanan Akademik & Kemahasiswaan</option>
                            <option value="Kebijakan">Kebijakan / Regulasi Kampus</option>
                            <option value="Lainnya">Lainnya / Masukan Umum</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="isi" :value="__('Isi Aspirasi / Keluhan')" />
                        <textarea id="isi" name="isi" rows="6" class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-2xs text-sm text-slate-800" required placeholder="Jelaskan aspirasi, kendala, atau saran Anda secara rinci, sopan, dan jelas..."></textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <a href="{{ route('aspirasi.mine') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium hover:underline order-2 sm:order-1">
                            &larr; Cek Riwayat Aspirasi Saya
                        </a>
                        <x-primary-button class="w-full sm:w-auto justify-center px-6 py-2.5 rounded-lg order-1 sm:order-2 shadow-sm">
                            {{ __('Kirim Aspirasi') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
