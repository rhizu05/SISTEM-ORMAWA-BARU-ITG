<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Buat Surat Peringatan (SP)</h2></x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-6">Gunakan form ini untuk menerbitkan surat peringatan resmi kepada organisasi mahasiswa.</p>
                
                <form action="{{ route('bpm.sp.store') }}" method="POST">
                    @csrf
                    <h3 class="font-bold text-indigo-600 border-b-2 border-indigo-500 pb-2 mb-4">Detail Pelanggaran & Peringatan</h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-input-label for="target_user_id" :value="__('Target Organisasi')" />
                            <select id="target_user_id" name="target_user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Pilih Organisasi yang akan diberi peringatan</option>
                                @foreach($ormawas as $o)
                                    <option value="{{ $o->id }}">{{ $o->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="nomor_surat" :value="__('Nomor Surat')" />
                                <x-text-input id="nomor_surat" name="nomor_surat" type="text" class="mt-1 block w-full" placeholder="Contoh: 015/SP/BPM/V/2024" required />
                            </div>
                            <div>
                                <x-input-label for="tingkat" :value="__('Tingkat Peringatan')" />
                                <select id="tingkat" name="tingkat" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="SP-1">Surat Peringatan 1 (SP-1)</option>
                                    <option value="SP-2">Surat Peringatan 2 (SP-2)</option>
                                    <option value="SP-3">Surat Peringatan 3 (SP-3)</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="perihal" :value="__('Perihal')" />
                            <x-text-input id="perihal" name="perihal" type="text" class="mt-1 block w-full" placeholder="Surat Peringatan Pelanggaran Peraturan Organisasi" required />
                        </div>
                        <div>
                            <x-input-label for="alasan_singkat" :value="__('Alasan Utama (Singkat)')" />
                            <x-text-input id="alasan_singkat" name="alasan_singkat" type="text" class="mt-1 block w-full" placeholder="Contoh: Keterlambatan Pengumpulan LPJ Kegiatan" required />
                        </div>
                        <div>
                            <x-input-label for="deskripsi" :value="__('Deskripsi Pelanggaran')" />
                            <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Jelaskan secara detail pelanggaran yang dilakukan..." required></textarea>
                        </div>
                        <div>
                            <x-input-label for="sanksi" :value="__('Sanksi yang Diberikan')" />
                            <textarea id="sanksi" name="sanksi" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Penangguhan dana kegiatan selama 1 bulan ke depan..." required></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="tanggal_surat" :value="__('Tanggal Surat')" />
                                <x-text-input id="tanggal_surat" name="tanggal_surat" type="date" class="mt-1 block w-full" required value="{{ date('Y-m-d') }}" />
                            </div>
                        <div>
                            <x-input-label for="penandatangan" :value="__('Nama Penandatangan')" />
                            <p class="text-xs text-gray-500 mb-1">Nama Lengkap & Jabatan</p>
                            <x-text-input id="penandatangan" name="penandatangan" type="text" class="mt-1 block w-full" placeholder="Contoh: Ketua BPM ITG / Kepala BKKH ITG" required />
                        </div>
                        </div>
                    </div>
                    <div class="mt-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs italic">
                        <strong>Perhatian:</strong> Penerbitan surat peringatan adalah langkah formal. Pastikan data pelanggaran sudah tervalidasi dengan benar sebelum diterbitkan.
                    </div>
                    <div class="flex justify-end mt-6">
                        <a href="{{ route('bpm.dashboard') }}" class="mr-4 text-sm text-gray-600 underline">Batal</a>
                        <x-primary-button>Terbitkan SP</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
