<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jadwal Rapat & Koordinasi') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @hasanyrole('bem|bpm')
            <div class="flex justify-end">
                <button @click="showModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    + Buat Agenda Rapat
                </button>
            </div>
            @endhasanyrole

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($rapats as $rapat)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500 relative">
                    
                    @hasanyrole('bem|bpm')
                    @if($rapat->user_id === Auth::id())
                    <div class="absolute top-2 right-2">
                        <form action="{{ route('rapat.destroy', $rapat) }}" method="POST" onsubmit="return confirm('Batalkan jadwal rapat ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Batalkan</button>
                        </form>
                    </div>
                    @endif
                    @endhasanyrole

                    <div class="p-5">
                        <h3 class="text-lg font-bold text-gray-900 mb-1 pr-6">{{ $rapat->judul_rapat }}</h3>
                        <p class="text-sm text-gray-500 mb-4">Oleh: {{ $rapat->penyelenggara->name }}</p>
                        
                        <div class="space-y-2 text-sm text-gray-700">
                            <div class="flex items-start">
                                <span class="w-20 font-semibold text-gray-500">Tanggal:</span>
                                <span>{{ \Carbon\Carbon::parse($rapat->tanggal_rapat)->format('l, d F Y') }}</span>
                            </div>
                            <div class="flex items-start">
                                <span class="w-20 font-semibold text-gray-500">Waktu:</span>
                                <span>{{ \Carbon\Carbon::parse($rapat->jam_rapat)->format('H:i') }} WIB</span>
                            </div>
                            <div class="flex items-start">
                                <span class="w-20 font-semibold text-gray-500">Lokasi:</span>
                                <span>{{ $rapat->lokasi }}</span>
                            </div>
                            
                            @if($rapat->link_meeting)
                            <div class="flex items-start">
                                <span class="w-20 font-semibold text-gray-500">Link:</span>
                                <a href="{{ $rapat->link_meeting }}" target="_blank" class="text-indigo-600 hover:underline break-all">{{ $rapat->link_meeting }}</a>
                            </div>
                            @endif

                            <div class="mt-4 pt-3 border-t text-xs">
                                <span class="font-semibold text-gray-500">Target Peserta:</span>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach($rapat->target_peserta as $peserta)
                                        <span class="bg-gray-100 px-2 py-1 rounded">{{ strtoupper($peserta) }}</span>
                                    @endforeach
                                </div>
                            </div>

                            @if($rapat->deskripsi)
                            <div class="mt-3 text-xs bg-gray-50 p-2 rounded">
                                {{ $rapat->deskripsi }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-10 bg-white shadow-sm sm:rounded-lg text-gray-500">
                    Tidak ada agenda rapat dalam waktu dekat.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Modal Tambah Jadwal -->
        @hasanyrole('bem|bpm')
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showModal = false"></div>
                <div class="relative bg-white w-full max-w-lg p-6 rounded-lg shadow-xl">
                    <h3 class="text-lg font-bold mb-4">Buat Agenda Rapat Baru</h3>
                    <form action="{{ route('rapat.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="judul_rapat" value="Agenda / Judul Rapat" />
                                <x-text-input id="judul_rapat" name="judul_rapat" type="text" class="mt-1 block w-full" required />
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="tanggal_rapat" value="Tanggal" />
                                    <x-text-input id="tanggal_rapat" name="tanggal_rapat" type="date" class="mt-1 block w-full" :value="date('Y-m-d')" required />
                                </div>
                                <div>
                                    <x-input-label for="jam_rapat" value="Waktu (WIB)" />
                                    <x-text-input id="jam_rapat" name="jam_rapat" type="time" class="mt-1 block w-full" required />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="lokasi" value="Tempat / Lokasi Rapat" />
                                <x-text-input id="lokasi" name="lokasi" type="text" class="mt-1 block w-full" required />
                            </div>

                            <div>
                                <x-input-label for="link_meeting" value="Link Virtual Meeting (Opsional)" />
                                <x-text-input id="link_meeting" name="link_meeting" type="url" class="mt-1 block w-full" placeholder="https://zoom.us/j/..." />
                            </div>

                            <div>
                                <x-input-label value="Target Peserta Undangan" />
                                <div class="mt-2 space-y-2 bg-gray-50 p-3 rounded border">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="peserta[]" value="all" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                        <span class="ml-2 text-sm">Semua Organisasi (BEM, BPM, Ormawa)</span>
                                    </label>
                                    <label class="inline-flex items-center block">
                                        <input type="checkbox" name="peserta[]" value="ormawa" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                        <span class="ml-2 text-sm">Ketua Ormawa (HIMA/UKM)</span>
                                    </label>
                                    <label class="inline-flex items-center block">
                                        <input type="checkbox" name="peserta[]" value="bem" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                        <span class="ml-2 text-sm">Pengurus BEM</span>
                                    </label>
                                    <label class="inline-flex items-center block">
                                        <input type="checkbox" name="peserta[]" value="bpm" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                        <span class="ml-2 text-sm">Anggota BPM</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="deskripsi" value="Keterangan / Catatan Tambahan (Opsional)" />
                                <textarea id="deskripsi" name="deskripsi" rows="2" class="border-gray-300 rounded-md shadow-sm block mt-1 w-full"></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showModal = false" class="px-4 py-2 border rounded text-gray-600">Batal</button>
                            <x-primary-button>Buat Agenda</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endhasanyrole

    </div>
</x-app-layout>