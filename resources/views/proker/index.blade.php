<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Program Kerja Tahunan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold">Rencana & Monitoring Proker</h3>
                    @hasanyrole('ormawa|bem')
                    <a href="{{ route('proker.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">
                        + Tambah Proker
                    </a>
                    @endhasanyrole
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="py-3 px-4 border-b text-left">Ormawa</th>
                                <th class="py-3 px-4 border-b text-left">Nama Proker</th>
                                <th class="py-3 px-4 border-b text-left">Rencana Pelaksanaan</th>
                                <th class="py-3 px-4 border-b text-center">Status</th>
                                <th class="py-3 px-4 border-b text-left">Catatan BPM</th>
                                @hasanyrole('bpm|admin')
                                <th class="py-3 px-4 border-b text-center">Aksi (BPM)</th>
                                @endhasanyrole
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($prokers as $proker)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 border-b font-medium">{{ $proker->user->name }}</td>
                                <td class="py-3 px-4 border-b font-semibold text-indigo-600">{{ $proker->nama_proker }}<br><span class="text-xs font-normal text-gray-500">{{ Str::limit($proker->deskripsi, 40) }}</span></td>
                                <td class="py-3 px-4 border-b">{{ \Carbon\Carbon::parse($proker->rencana_pelaksanaan)->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 border-b text-center">
                                    @if($proker->status == 'rencana')
                                        <span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-full text-xs">Rencana</span>
                                    @elseif($proker->status == 'proses')
                                        <span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-xs">Proses</span>
                                    @elseif($proker->status == 'terlaksana')
                                        <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs">Terlaksana</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-200 text-red-800 rounded-full text-xs">Kendala</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 border-b text-xs text-gray-600">{{ $proker->catatan_bpm ?? '-' }}</td>
                                @hasanyrole('bpm|admin')
                                <td class="py-3 px-4 border-b text-center">
                                    <form action="{{ route('proker.update', $proker) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="text-xs border-gray-300 rounded">
                                            <option value="rencana" {{ $proker->status == 'rencana' ? 'selected' : '' }}>Rencana</option>
                                            <option value="proses" {{ $proker->status == 'proses' ? 'selected' : '' }}>Proses</option>
                                            <option value="terlaksana" {{ $proker->status == 'terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                                            <option value="kendala" {{ $proker->status == 'kendala' ? 'selected' : '' }}>Kendala</option>
                                        </select>
                                        <input type="text" name="catatan_bpm" placeholder="Catatan..." value="{{ $proker->catatan_bpm }}" class="text-xs border-gray-300 rounded w-28">
                                        <button type="submit" class="px-2 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700">Simpan</button>
                                    </form>
                                </td>
                                @endhasanyrole
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-500">Belum ada program kerja terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $prokers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
