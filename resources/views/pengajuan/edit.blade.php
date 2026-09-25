<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit / Revisi Pengajuan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 max-w-2xl mx-auto">
                    
                    @if($pengajuan->state->name === 'rejected')
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
                            <h3 class="font-bold text-red-700">Catatan Revisi/Penolakan:</h3>
                            <p class="text-red-600 mt-1">
                                @php
                                    $rejectHistory = $pengajuan->histori()->whereHas('state', function($q) {
                                        $q->where('name', 'rejected');
                                    })->latest()->first();
                                @endphp
                                {{ $rejectHistory ? $rejectHistory->catatan : 'Silakan perbaiki proposal Anda dan upload ulang.' }}
                            </p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pengajuan.update', $pengajuan) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Nama Kegiatan -->
                        <div class="mb-4">
                            <x-input-label for="nama_kegiatan" :value="__('Nama Kegiatan')" />
                            <x-text-input id="nama_kegiatan" class="block mt-1 w-full" type="text" name="nama_kegiatan" :value="old('nama_kegiatan', $pengajuan->nama_kegiatan)" required />
                            <x-input-error :messages="$errors->get('nama_kegiatan')" class="mt-2" />
                        </div>

                        <!-- Program Kerja Terkait -->
                        <div class="mb-4">
                            <x-input-label for="program_kerja_id" :value="__('Program Kerja Terkait (Opsional)')" />
                            <select id="program_kerja_id" name="program_kerja_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">-- Tidak Terkait / Di Luar Program Kerja Tahunan --</option>
                                @foreach($prokers ?? [] as $proker)
                                    <option value="{{ $proker->id }}" {{ (string)old('program_kerja_id', $pengajuan->program_kerja_id) === (string)$proker->id ? 'selected' : '' }}>
                                        {{ $proker->nama_proker }} (Rencana: {{ \Carbon\Carbon::parse($proker->rencana_pelaksanaan)->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @if(count($prokers ?? []) === 0)
                                <p class="text-xs text-indigo-600 mt-1">💡 Belum memiliki program kerja tahunan? Daftarkan proker Anda di <a href="{{ route('proker.create') }}" class="underline font-semibold" target="_blank">Menu Program Kerja</a>.</p>
                            @else
                                <p class="text-xs text-gray-500 mt-1">Pilih proker tahunan ormawa Anda untuk memudahkan monitoring realisasi oleh BPM dan BKHM.</p>
                            @endif
                            <x-input-error :messages="$errors->get('program_kerja_id')" class="mt-2" />
                        </div>

                        <!-- Tanggal Pelaksanaan Kegiatan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="tanggal_mulai_kegiatan" :value="__('Tanggal Mulai Kegiatan (Opsional)')" />
                                <x-text-input id="tanggal_mulai_kegiatan" class="block mt-1 w-full" type="date" name="tanggal_mulai_kegiatan" :value="old('tanggal_mulai_kegiatan', $pengajuan->tanggal_mulai_kegiatan ? \Carbon\Carbon::parse($pengajuan->tanggal_mulai_kegiatan)->format('Y-m-d') : '')" onchange="document.getElementById('tanggal_selesai_kegiatan').min = this.value" />
                                <x-input-error :messages="$errors->get('tanggal_mulai_kegiatan')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="tanggal_selesai_kegiatan" :value="__('Tanggal Selesai Kegiatan (Opsional)')" />
                                <x-text-input id="tanggal_selesai_kegiatan" class="block mt-1 w-full" type="date" name="tanggal_selesai_kegiatan" :value="old('tanggal_selesai_kegiatan', $pengajuan->tanggal_selesai_kegiatan ? \Carbon\Carbon::parse($pengajuan->tanggal_selesai_kegiatan)->format('Y-m-d') : '')" :min="old('tanggal_mulai_kegiatan', $pengajuan->tanggal_mulai_kegiatan ? \Carbon\Carbon::parse($pengajuan->tanggal_mulai_kegiatan)->format('Y-m-d') : '')" />
                                <x-input-error :messages="$errors->get('tanggal_selesai_kegiatan')" class="mt-2" />
                            </div>
                            <div class="col-span-1 md:col-span-2 -mt-2">
                                <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded p-2">
                                    💡 <strong>Urgensi Pencairan:</strong> Mengisi tanggal pelaksanaan kegiatan akan menampilkan indikator hitung mundur (H-X) pada antrean verifikator sehingga proposal dapat diprioritaskan sebelum acara berlangsung.
                                </p>
                            </div>
                        </div>

                        <!-- Dana Diajukan -->
                        <div class="mb-4">
                            <x-input-label for="dana_diajukan" :value="__('Dana Diajukan (Rp)')" />
                            <x-text-input id="dana_diajukan" class="block mt-1 w-full" type="number" name="dana_diajukan" :value="old('dana_diajukan', $pengajuan->dana_diajukan)" required min="0" />
                            <x-input-error :messages="$errors->get('dana_diajukan')" class="mt-2" />
                        </div>

                        <!-- Tanggal Pengajuan -->
                        <div class="mb-4">
                            <x-input-label for="tanggal_pengajuan" :value="__('Tanggal Pengajuan Proposal')" />
                            <x-text-input id="tanggal_pengajuan" class="block mt-1 w-full" type="date" name="tanggal_pengajuan" :value="old('tanggal_pengajuan', \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('tanggal_pengajuan')" class="mt-2" />
                        </div>

                        <!-- File Proposal -->
                        <div class="mb-6">
                            <x-input-label for="file_proposal" :value="__('File Proposal Baru (PDF, maks 5MB)')" />
                            <p class="text-sm text-gray-500 mb-1">Kosongkan jika tidak ingin mengganti file proposal yang sudah ada.</p>
                            <input id="file_proposal" class="block mt-1 w-full border border-gray-300 rounded p-2" type="file" name="file_proposal" accept=".pdf" />
                            <x-input-error :messages="$errors->get('file_proposal')" class="mt-2" />
                            
                            <div class="mt-2 text-sm">
                                File saat ini: <a href="{{ route('dokumen.proposal', $pengajuan) }}" target="_blank" class="text-indigo-600 hover:underline">Lihat PDF</a>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4" href="{{ route('pengajuan.show', $pengajuan) }}">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>