<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Surat Peringatan - {{ $sp->nomor_surat }}</h2></x-slot>
    <div class="py-6"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <div class="border-b pb-2 mb-4 text-center"><div class="font-bold">SURAT PERINGATAN ({{ $sp->tingkat }})</div><div class="text-xs">No: {{ $sp->nomor_surat }}</div></div>
            <div class="text-sm space-y-2">
                <p>Target: <b>{{ $sp->target->name }}</b></p>
                <p>Perihal: {{ $sp->perihal }}</p>
                <p>Alasan: {{ $sp->alasan_singkat }}</p>
                <p>Deskripsi: {{ $sp->deskripsi }}</p>
                <p>Sanksi: {{ $sp->sanksi }}</p>
                <p>Tanggal: {{ $sp->tanggal_surat->format('d/m/Y') }}</p>
            </div>
            <div class="text-right mt-6">Penandatangan: <b>{{ $sp->penandatangan }}</b></div>
            <div class="mt-4"><button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded">Cetak</button> <a href="{{ route('bkkh.arsip.index') }}" class="border px-4 py-2 rounded">Kembali ke Arsip</a></div>
        </div>
    </div></div>
</x-app-layout>
