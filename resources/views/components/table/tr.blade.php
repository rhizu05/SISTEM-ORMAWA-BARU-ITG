@props(['hover' => true])

<tr {{ $attributes->merge(['class' => ($hover ? 'hover:bg-slate-50/80 transition-colors' : '')]) }}>
    {{ $slot }}
</tr>
