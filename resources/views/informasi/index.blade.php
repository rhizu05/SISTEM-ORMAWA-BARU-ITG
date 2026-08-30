<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pusat Informasi & Regulasi') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'pengumuman', showPengumumanModal: false, showRegulasiModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 mb-6 flex">
                <button @click="activeTab = 'pengumuman'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'pengumuman', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'pengumuman' }" class="py-4 px-6 border-b-2 font-medium text-sm">
                    Berita & Pengumuman
                </button>
                <button @click="activeTab = 'regulasi'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'regulasi', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'regulasi' }" class="py-4 px-6 border-b-2 font-medium text-sm">
                    Regulasi & Pedoman
                </button>
            </div>

            <!-- Tab Content: Pengumuman -->
            <div x-show="activeTab === 'pengumuman'" class="space-y-6">
                @hasrole('bem')
                <div class="flex justify-end">
                    <button @click="showPengumumanModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        + Tambah Pengumuman
                    </button>
                </div>
                @endhasrole

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($pengumuman as $p)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500 relative">
                        @hasrole('bem')
                        @if($p->user_id === Auth::id())
                        <div class="absolute top-4 right-4">
                            <form action="{{ route('informasi.pengumuman.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
                            </form>
                        </div>
                        @endif
                        @endhasrole

                        <div class="p-6">
                            <div class="text-xs text-gray-500 mb-1">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }} • {{ $p->user->name }}</div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $p->judul }}</h3>
                            <p class="text-gray-700 text-sm whitespace-pre-wrap mb-4">{{ $p->isi }}</p>
                            
                            @if($p->file_lampiran)
                            <a href="{{ Storage::url($p->file_lampiran) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-semibold rounded">
                                📎 Lihat Lampiran
                            </a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-span-2 text-center py-8 text-gray-500 bg-white shadow-sm sm:rounded-lg">
                        Belum ada pengumuman terbaru.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Tab Content: Regulasi -->
            <div x-show="activeTab === 'regulasi'" style="display: none;" class="space-y-6">
                @hasrole('bpm')
                <div class="flex justify-end">
                    <button @click="showRegulasiModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        + Tambah Regulasi / UU
                    </button>
                </div>
                @endhasrole

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4 text-left">Judul Dokumen</th>
                                    <th class="py-3 px-4 text-left">Kategori</th>
                                    <th class="py-3 px-4 text-left">Diterbitkan Oleh</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($regulasi as $r)
                                <tr>
                                    <td class="py-3 px-4">
                                        <p class="font-semibold">{{ $r->judul }}</p>
                                        <p class="text-xs text-gray-500">{{ $r->deskripsi }}</p>
                                    </td>
                                    <td class="py-3 px-4"><span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ $r->kategori }}</span></td>
                                    <td class="py-3 px-4 text-sm">{{ $r->user->name }}<br><span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y') }}</span></td>
                                    <td class="py-3 px-4 text-center">
                                        <a href="{{ Storage::url($r->file_path) }}" target="_blank" class="text-indigo-600 hover:underline text-sm mr-2">Unduh PDF</a>
                                        
                                        @hasrole('bpm')
                                        @if($r->user_id === Auth::id())
                                        <form action="{{ route('informasi.regulasi.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('Hapus regulasi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
                                        </form>
                                        @endif
                                        @endhasrole
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="py-6 text-center text-gray-500">Belum ada dokumen regulasi yang diunggah.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Tambah Pengumuman -->
        @hasrole('bem')
        <div x-show="showPengumumanModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showPengumumanModal = false"></div>
                <div class="relative bg-white w-full max-w-lg p-6 rounded-lg shadow-xl">
                    <h3 class="text-lg font-bold mb-4">Buat Pengumuman Baru</h3>
                    <form action="{{ route('informasi.pengumuman.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="judul" value="Judul Pengumuman" />
                                <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label for="isi" value="Isi / Deskripsi" />
                                <textarea id="isi" name="isi" rows="4" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required></textarea>
                            </div>
                            <div>
                                <x-input-label for="file_lampiran" value="File Lampiran (Opsional, PDF/JPG/PNG)" />
                                <input id="file_lampiran" name="file_lampiran" type="file" class="mt-1 block w-full border rounded p-1" />
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showPengumumanModal = false" class="px-4 py-2 border rounded text-gray-600">Batal</button>
                            <x-primary-button>Terbitkan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- Modal Tambah Regulasi -->
        @hasrole('bpm')
        <div x-show="showRegulasiModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showRegulasiModal = false"></div>
                <div class="relative bg-white w-full max-w-lg p-6 rounded-lg shadow-xl">
                    <h3 class="text-lg font-bold mb-4">Upload Regulasi / UU</h3>
                    <form action="{{ route('informasi.regulasi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="judul_reg" value="Judul Dokumen" />
                                <x-text-input id="judul_reg" name="judul" type="text" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label for="kategori" value="Kategori" />
                                <select id="kategori" name="kategori" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="Undang-Undang">Undang-Undang Dasar KM</option>
                                    <option value="Pedoman">Buku Pedoman Ormawa</option>
                                    <option value="Ketetapan BPM">Ketetapan BPM</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="deskripsi_reg" value="Deskripsi Singkat (Opsional)" />
                                <textarea id="deskripsi_reg" name="deskripsi" rows="2" class="border-gray-300 rounded-md shadow-sm block mt-1 w-full"></textarea>
                            </div>
                            <div>
                                <x-input-label for="file_path" value="File Dokumen (Wajib, PDF max 10MB)" />
                                <input id="file_path" name="file_path" type="file" accept=".pdf" class="mt-1 block w-full border rounded p-1" required />
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showRegulasiModal = false" class="px-4 py-2 border rounded text-gray-600">Batal</button>
                            <x-primary-button>Upload</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endhasrole

    </div>
</x-app-layout>