<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Pusat Notifikasi') }}</h2>
            <form action="{{ route('notifikasi.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-indigo-600 hover:underline">Tandai semua sudah dibaca</button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg divide-y">
                @forelse($notifikasis as $n)
                <div class="p-4 flex items-start justify-between {{ $n->status_baca === 'belum' ? 'bg-indigo-50' : '' }}">
                    <div>
                        <p class="text-sm text-gray-800">{{ $n->pesan }}</p>
                        <span class="text-xs text-gray-400">{{ $n->created_at->diffForHumans() }}</span>
                    </div>
                    @if($n->status_baca === 'belum')
                    <form action="{{ route('notifikasi.read', $n) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs text-indigo-600 hover:underline whitespace-nowrap ml-4">Tandai dibaca</button>
                    </form>
                    @else
                        <span class="text-xs text-gray-400 ml-4">Dibaca</span>
                    @endif
                </div>
                @empty
                <div class="p-8 text-center text-gray-500 italic">Belum ada notifikasi.</div>
                @endforelse
            </div>
            <div class="mt-4">{{ $notifikasis->links() }}</div>
        </div>
    </div>
</x-app-layout>
