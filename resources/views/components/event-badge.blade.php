@props(['event'])

@php
    $themes = [
        'created' => ['bg-emerald-50 text-emerald-700 ring-emerald-600/20', 'bg-emerald-500'],
        'updated' => ['bg-amber-50 text-amber-700 ring-amber-600/20', 'bg-amber-500'],
        'deleted' => ['bg-rose-50 text-rose-700 ring-rose-600/20', 'bg-rose-500'],
        'restored' => ['bg-sky-50 text-sky-700 ring-sky-600/20', 'bg-sky-500'],
    ];

    [$wrapper, $dot] = $themes[$event] ?? ['bg-slate-100 text-slate-600 ring-slate-500/20', 'bg-slate-400'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium capitalize ring-1 ring-inset '.$wrapper]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
    {{ $event }}
</span>
