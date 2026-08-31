<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Kelola Aspirasi & Suara Mahasiswa</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded text-sm">{{ session('success') }}</div>
                @endif
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold">Daftar Aspirasi Masuk</h3>
                    <div class="flex gap-2">
                        <select onchange="window.location.href='?status='+this.value" class="text-sm border-gray-300 rounded-md">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="diproses">Diproses</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3 text-left border">Pengirim</th>
                            <th class="p-3 text-left border">Kategori</th>
                            <th class="p-3 text-left border">Judul & Isi</th>
                            <th class="p-3 text-center border">Status</th>
                            <th class="p-3 text-center border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aspirasis as $a)
                        <tr class="border-b">
                            <td class="p-3 border">{{ $a->user->name ?? 'Anonim' }}</td>
                            <td class="p-3 border">{{ $a->kategori }}</td>
                            <td class="p-3 border">
                                <div class="font-bold">{{ $a->judul }}</div>
                                <div class="text-xs text-gray-600">{{ Str::limit($a->isi, 100) }}</div>
                            </td>
                            <td class="p-3 border text-center">
                                <span class="px-2 py-1 rounded-full text-xs {{ $a->status==='pending'?'bg-yellow-100 text-yellow-700':($a->status==='diproses'?'bg-blue-100 text-blue-700':'bg-green-100 text-green-700') }}">
                                    {{ ucfirst($a->status) }}
                                </span>
                            </td>
                            <td class="p-3 border text-center">
                                <button onclick="openModal('{{ $a->id }}', '{{ $a->status }}', '{{ addslashes($a->catatan_bpm) }}')" class="text-indigo-600 hover:underline">Kelola</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="p-8 text-center text-gray-500 italic">Belum ada aspirasi atau keluhan yang masuk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                {{ $aspirasis->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Kelola Aspirasi -->
    <div id="modal-aspirasi" x-data="{ open: false, status: 'pending', catatan: '' }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <div class="bg-white rounded-lg shadow-xl z-10 max-w-lg w-full p-6 relative">
                <h3 class="text-lg font-bold mb-4">Kelola Aspirasi</h3>
                <form method="POST" :action="'/bpm/aspirasi/update/'+id">
                    @csrf @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="pending">Pending</option>
                                <option value="diproses">Diproses</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="catatan_bpm" :value="__('Catatan / Tanggapan BPM')" />
                            <textarea name="catatan_bpm" id="catatan_bpm" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Berikan tanggapan resmi..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end mt-6 gap-2">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600">Batal</button>
                        <x-primary-button>Simpan Perubahan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(id, status, catatan) {
            document.getElementById('modal-aspirasi').style.display = 'block';
            document.getElementById('modal-aspirasi').querySelector('form').action = '/bpm/aspirasi/update/' + id;
            document.getElementById('status').value = status;
            document.getElementById('catatan_bpm').value = catatan;
        }
        function closeModal() {
            document.getElementById('modal-aspirasi').style.display = 'none';
        }
    </script>
</x-app-layout>
