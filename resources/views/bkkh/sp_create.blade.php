<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Buat Surat Peringatan (SP)</h2></x-slot>
    <div class="py-6"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <p class="text-sm text-gray-600 mb-4">Gunakan form ini untuk menerbitkan surat peringatan resmi kepada organisasi mahasiswa.</p>
            <form method="POST" action="{{ route('bkkh.sp.store') }}">
                @csrf
                <div class="space-y-4">
                    <div><label class="text-sm font-semibold">Target Organisasi (ORMAWA)</label><select name="target_user_id" class="w-full border rounded p-2">@foreach($ormawas as $o)<option value="{{ $o->id }}">{{ $o->name }} ({{ $o->username }})</option>@endforeach</select>@error('target_user_id')<div class="text-xs text-red-600">{{ $message }}</div>@enderror</div>
                    <div><label class="text-sm font-semibold">Nomor Surat</label><input name="nomor_surat" placeholder="Contoh: 015/SP/BPM/V/2024" class="w-full border rounded p-2" required value="{{ old('nomor_surat') }}">@error('nomor_surat')<div class="text-xs text-red-600">{{ $message }}</div>@enderror</div>
                    <div><label class="text-sm font-semibold">Tingkat Peringatan</label><select name="tingkat" class="w-full border rounded p-2"><option>SP-1</option><option>SP-2</option><option>SP-3</option></select></div>
                    <div><label class="text-sm font-semibold">Perihal</label><input name="perihal" value="{{ old('perihal','Surat Peringatan Pelanggaran Peraturan Organisasi') }}" class="w-full border rounded p-2" required></div>
                    <div><label class="text-sm font-semibold">Alasan Utama (Singkat)</label><input name="alasan_singkat" placeholder="Contoh: Keterlambatan Pengumpulan LPJ Kegiatan" class="w-full border rounded p-2" required></div>
                    <div><label class="text-sm font-semibold">Deskripsi Pelanggaran</label><textarea name="deskripsi" rows="4" class="w-full border rounded p-2" required>{{ old('deskripsi') }}</textarea></div>
                    <div><label class="text-sm font-semibold">Sanksi yang Diberikan</label><textarea name="sanksi" rows="3" class="w-full border rounded p-2" required>{{ old('sanksi') }}</textarea></div>
                    <div><label class="text-sm font-semibold">Tanggal Surat</label><input type="date" name="tanggal_surat" value="{{ old('tanggal_surat', date('Y-m-d')) }}" class="w-full border rounded p-2" required></div>
                    <div><label class="text-sm font-semibold">Nama Penandatangan</label><input name="penandatangan" placeholder="Contoh: Ketua BPM ITG / Kepala BKKH ITG" class="w-full border rounded p-2" required></div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 p-3 rounded mt-4 text-xs text-yellow-800">Perhatian: Penerbitan surat peringatan adalah langkah formal. Pastikan data pelanggaran sudah tervalidasi dengan benar sebelum diterbitkan.</div>
                <div class="flex justify-end gap-2 mt-4">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded">Batal</a>
                    <button class="bg-indigo-600 text-white px-6 py-2 rounded">Terbitkan SP</button>
                </div>
            </form>
        </div>
    </div></div>
</x-app-layout>
