{{-- UI-019: pesan flash dengan peran ARIA yang tepat (status/alert). --}}
@if (session('success'))
    <div role="status" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div role="alert" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        {{ session('error') }}
    </div>
@endif