<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajukan Peminjaman Ruangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 max-w-2xl mx-auto">
                    

                    <form method="POST" action="{{ route('peminjaman.tempat.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Ruangan -->
                        <div class="mb-4">
                            <x-input-label for="ruangan_id" :value="__('Pilih Ruangan')" />
                            <select id="ruangan_id" name="ruangan_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($ruangans as $ruang)
                                    <option value="{{ $ruang->id }}" {{ old('ruangan_id') == $ruang->id ? 'selected' : '' }}>
                                        {{ $ruang->nama_ruangan }} (Kapasitas: {{ $ruang->kapasitas }} orang)
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('ruangan_id')" class="mt-2" />
                        </div>

                        <!-- Nama Kegiatan -->
                        <div class="mb-4">
                            <x-input-label for="nama_kegiatan" :value="__('Nama Kegiatan')" />
                            <x-text-input id="nama_kegiatan" class="block mt-1 w-full" type="text" name="nama_kegiatan" :value="old('nama_kegiatan')" required />
                            <x-input-error :messages="$errors->get('nama_kegiatan')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <!-- Tanggal Mulai -->
                            <div>
                                <x-input-label for="tgl_mulai" :value="__('Tanggal Mulai')" />
                                <x-text-input id="tgl_mulai" class="block mt-1 w-full" type="date" name="tgl_mulai" :value="old('tgl_mulai', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('tgl_mulai')" class="mt-2" />
                            </div>
                            
                            <!-- Tanggal Selesai -->
                            <div>
                                <x-input-label for="tgl_selesai" :value="__('Tanggal Selesai')" />
                                <x-text-input id="tgl_selesai" class="block mt-1 w-full" type="date" name="tgl_selesai" :value="old('tgl_selesai', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('tgl_selesai')" class="mt-2" />
                            </div>

                            <!-- Jam Mulai -->
                            <div>
                                <x-input-label for="jam_mulai" :value="__('Jam Mulai')" />
                                <x-text-input id="jam_mulai" class="block mt-1 w-full" type="time" name="jam_mulai" :value="old('jam_mulai')" required />
                                <x-input-error :messages="$errors->get('jam_mulai')" class="mt-2" />
                            </div>
                            
                            <!-- Jam Selesai -->
                            <div>
                                <x-input-label for="jam_selesai" :value="__('Jam Selesai')" />
                                <x-text-input id="jam_selesai" class="block mt-1 w-full" type="time" name="jam_selesai" :value="old('jam_selesai')" required />
                                <x-input-error :messages="$errors->get('jam_selesai')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-6">
                            <x-input-label for="deskripsi_kegiatan" :value="__('Deskripsi Kegiatan Singkat')" />
                            <textarea id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('deskripsi_kegiatan') }}</textarea>
                            <x-input-error :messages="$errors->get('deskripsi_kegiatan')" class="mt-2" />
                        </div>

                        <!-- Q-SAR-04: Dokumen Persetujuan Prodi (wajib untuk HIMA) -->
                        <div class="mb-6">
                            <x-input-label for="file_persetujuan_prodi" :value="Auth::user()->isHima() ? __('Dokumen Persetujuan Prodi (PDF, wajib untuk HIMA)') : __('Dokumen Persetujuan Prodi (PDF, opsional)')" />
                            <input id="file_persetujuan_prodi" type="file" name="file_persetujuan_prodi" accept=".pdf" class="block mt-1 w-full border border-gray-300 rounded p-2" @if(Auth::user()->isHima()) required @endif />
                            <p class="text-xs text-gray-500 mt-1">Persetujuan Prodi dilakukan di luar sistem; unggah dokumen buktinya di sini.</p>
                            <x-input-error :messages="$errors->get('file_persetujuan_prodi')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 mr-4" href="{{ route('peminjaman.index') }}">Batal</a>
                            <x-primary-button>Ajukan Peminjaman</x-primary-button>
                        </div>
                    </form>

                    {{-- FR-018 / UI-013: kalender ketersediaan ruangan --}}
                    <div class="mt-8">
                        <h3 class="text-lg font-bold mb-3 border-b pb-2">Ketersediaan Ruangan</h3>
                        <p class="text-xs text-gray-500 mb-3">Cek jadwal terpakai sebelum mengajukan. Oranye = peminjaman disetujui/proses.</p>
                        <div id="calendar" class="p-2 border rounded"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('calendar');
            if (!el || typeof FullCalendar === 'undefined') return;
            const cal = new FullCalendar.Calendar(el, {
                locale: 'id',
                initialView: 'dayGridMonth',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
                height: 'auto',
                events: [
                    @foreach($peminjamanTempat as $p)
                    { title: '{{ $p->nama_kegiatan }} ({{ $p->ruangan->nama_ruangan ?? '-' }})', start: '{{ $p->tgl_mulai }}T{{ substr($p->jam_mulai, 0, 5) }}', end: '{{ $p->tgl_selesai }}T{{ substr($p->jam_selesai, 0, 5) }}', color: '#f59e0b' },
                    @endforeach
                ]
            });
            cal.render();
        });
    </script>
</x-app-layout>