@props(['align' => 'left'])

@php
    $alignment = match($align) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
@endphp

<td {{ $attributes->merge(['class' => 'py-4 px-5 align-middle text-sm text-slate-800 ' . $alignment]) }}>
    {{ $slot }}
</td>
