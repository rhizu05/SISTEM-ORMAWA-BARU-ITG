<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Kelola Aspirasi & Suara Mahasiswa</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 mb-6 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Section 1: Aspirasi Bertiket Mahasiswa (Portal Layanan Baru) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8 border border-slate-200">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Aspirasi Mahasiswa Bertiket (Portal Terpadu)</h3>
                        <p class="text-xs text-slate-500">Aspirasi publik mahasiswa melalui sistem kode tiket. BPM dapat menindaklanjuti atau meneruskan ke BKHM.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="p-3 text-left border">Kode Tiket</th>
                                <th class="p-3 text-left border">Mahasiswa</th>
                                <th class="p-3 text-left border">Judul & Aspirasi</th>
                                <th class="p-3 text-center border">Status</th>
                                <th class="p-3 text-center border">Aksi BPM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tiketAspirasis as $t)
                                <tr class="border-b hover:bg-slate-50/60 transition">
                                    <td class="p-3 border font-mono font-bold text-xs text-blue-700">
                                        {{ $t->kode_tiket }}
                                    </td>
                                    <td class="p-3 border">
                                        <div class="font-bold text-xs text-slate-800">{{ $t->nama_mahasiswa }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $t->nim }} &bull; {{ $t->prodi ?? 'ITG' }}</div>
                                    </td>
                                    <td class="p-3 border">
                                        <div class="font-bold text-slate-900 text-xs">{{ $t->judul }}</div>
                                        <div class="text-xs text-slate-600 mt-0.5">{{ Str::limit($t->isi, 90) }}</div>
                                        @if($t->catatan_bpm)
                                            <div class="mt-1 text-[11px] text-blue-700 bg-blue-50 p-1.5 rounded">
                                                <strong>Catatan BPM:</strong> {{ $t->catatan_bpm }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-3 border text-center">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold border {{ $t->status_color }}">
                                            {{ $t->status_label }}
                                        </span>
                                    </td>
                                    <td class="p-3 border text-center space-y-1">
                                        @if($t->status !== 'diteruskan_ke_bkhm' && $t->status !== 'selesai')
                                            <button type="button" onclick="openTeruskanModal('{{ $t->id }}', '{{ $t->kode_tiket }}')"
                                                class="px-2.5 py-1 text-xs font-bold rounded-lg text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 transition inline-block">
                                                Teruskan ke BKHM &rarr;
                                            </button>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Sudah diteruskan</span>
                                        @endif
                                        @if($t->lampiran)
                                            <div>
                                                <a href="{{ route('layanan.lampiran', $t) }}" target="_blank" class="text-[11px] text-indigo-600 hover:underline">
                                                    Lampiran
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400 italic">Belum ada aspirasi bertiket yang masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $tiketAspirasis->links() }}
                </div>
            </div>

            <!-- Section 2: Aspirasi Internal Akun Mahasiswa (Legacy) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-slate-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Aspirasi Akun Terdaftar (Internal)</h3>
                    <div class="flex gap-2">
                        <select onchange="window.location.href='?status='+this.value" class="text-sm border-gray-300 rounded-md">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="direkap">Direkap BPM</option>
                            <option value="diproses">Diproses</option>
                            <option value="ditindaklanjuti">Ditindaklanjuti</option>
                            <option value="selesai">Selesai</option>
                            <option value="ditolak">Ditolak</option>
                            <option value="tidak_terbukti">Tidak Terbukti</option>
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
                            <td class="p-3 border">
                                {{ $a->user->name ?? 'Anonim' }}
                                @if($a->anonim)
                                    <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-gray-200 text-gray-600">anonim ke publik</span>
                                @endif
                            </td>
                            <td class="p-3 border">{{ $a->kategori }}</td>
                            <td class="p-3 border">
                                <div class="font-bold">{{ $a->judul }}</div>
                                <div class="text-xs text-gray-600">{{ Str::limit($a->isi, 100) }}</div>
                            </td>
                            <td class="p-3 border text-center">
                                @php
                                    $statusClass = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'direkap' => 'bg-purple-100 text-purple-700',
                                        'diproses' => 'bg-blue-100 text-blue-700',
                                        'ditindaklanjuti' => 'bg-indigo-100 text-indigo-700',
                                        'selesai' => 'bg-green-100 text-green-700',
                                        'ditolak' => 'bg-red-100 text-red-700',
                                        'tidak_terbukti' => 'bg-gray-200 text-gray-700',
                                    ][$a->status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs {{ $statusClass }}">
                                    {{ \Illuminate\Support\Str::of($a->status)->replace('_', ' ')->title() }}
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
                                <option value="direkap">Direkap BPM</option>
                                <option value="diproses">Diproses</option>
                                <option value="ditindaklanjuti">Ditindaklanjuti</option>
                                <option value="selesai">Selesai</option>
                                <option value="ditolak">Ditolak</option>
                                <option value="tidak_terbukti">Tidak Terbukti</option>
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

    <!-- Modal Teruskan ke BKHM -->
    <div id="modal-teruskan" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" onclick="closeTeruskanModal()"></div>
            <div class="bg-white rounded-2xl shadow-xl z-10 max-w-lg w-full p-6 relative border border-slate-200">
                <h3 class="text-base font-bold text-slate-900 mb-1">Teruskan Aspirasi ke BKHM</h3>
                <p class="text-xs text-slate-500 mb-4" id="teruskan-kode-label">Kode Tiket: -</p>
                <form id="form-teruskan" method="POST" action="">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="catatan_bpm_teruskan" :value="__('Catatan Rekomendasi BPM untuk BKHM *')" />
                            <textarea name="catatan_bpm" id="catatan_bpm_teruskan" rows="4" required class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Tuliskan urgensi, rekomendasi tindak lanjut, atau dasar pertimbangan dari BPM..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end mt-6 gap-2">
                        <button type="button" onclick="closeTeruskanModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-slate-100 rounded-xl transition">Batal</button>
                        <x-primary-button class="rounded-xl">Teruskan ke BKHM</x-primary-button>
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

        function openTeruskanModal(id, kodeTiket) {
            document.getElementById('modal-teruskan').style.display = 'block';
            document.getElementById('form-teruskan').action = '/bpm/aspirasi/' + id + '/teruskan';
            document.getElementById('teruskan-kode-label').textContent = 'Tiket: ' + kodeTiket;
        }
        function closeTeruskanModal() {
            document.getElementById('modal-teruskan').style.display = 'none';
        }
    </script>
</x-app-layout>
