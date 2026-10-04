@props(['containerClass' => ''])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden ' . $containerClass]) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 text-left border-collapse">
            {{ $slot }}
        </table>
    </div>
</div>
