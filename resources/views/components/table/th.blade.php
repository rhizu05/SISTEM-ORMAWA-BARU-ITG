@props(['align' => 'left'])

@php
    $alignment = match($align) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
@endphp

<th {{ $attributes->merge(['class' => 'py-3.5 px-5 text-[11px] font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap ' . $alignment]) }}>
    {{ $slot }}
</th>
