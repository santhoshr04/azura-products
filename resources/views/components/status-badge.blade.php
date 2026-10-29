@props(['status'])

@php
    $active = $status instanceof \App\Enums\Status ? $status->isActive() : $status === 'Active';
    $label = $status instanceof \App\Enums\Status ? $status->value : $status;
@endphp

<span @class([
    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset',
    'bg-emerald-50 text-emerald-700 ring-emerald-600/20' => $active,
    'bg-slate-100 text-slate-600 ring-slate-500/20' => ! $active,
])>
    <span @class([
        'h-1.5 w-1.5 rounded-full',
        'bg-emerald-500' => $active,
        'bg-slate-400' => ! $active,
    ])></span>
    {{ $label }}
</span>
