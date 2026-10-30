@props(['activity'])

@php
    $changes = $activity->attribute_changes ?? collect();
    $attributes = (array) ($changes['attributes'] ?? []);
    $old = (array) ($changes['old'] ?? []);
    $keys = array_values(array_unique(array_merge(array_keys($old), array_keys($attributes))));

    $format = fn ($value): string => is_array($value)
        ? (string) json_encode($value)
        : \Illuminate\Support\Str::limit((string) $value, 40);
@endphp

@if ($keys !== [])
    <details class="group">
        <summary class="inline-flex cursor-pointer list-none items-center gap-1 text-xs font-medium text-indigo-600 transition hover:text-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300">
            View changes
            <span class="rounded-full bg-slate-100 px-1.5 text-[11px] tabular-nums text-slate-500">{{ count($keys) }}</span>
            <svg class="h-3.5 w-3.5 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
            </svg>
        </summary>

        <dl class="mt-2 space-y-1.5">
            @foreach ($keys as $key)
                <div class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5 text-xs">
                    <dt class="font-medium text-slate-700">{{ $key }}</dt>
                    @if (array_key_exists($key, $old) && array_key_exists($key, $attributes))
                        <span class="text-slate-400 line-through">{{ $format($old[$key]) }}</span>
                        <span class="text-slate-300" aria-hidden="true">&rarr;</span>
                        <dd class="font-medium text-slate-900">{{ $format($attributes[$key]) }}</dd>
                    @elseif (array_key_exists($key, $attributes))
                        <dd class="font-medium text-emerald-600">{{ $format($attributes[$key]) }}</dd>
                    @else
                        <dd class="font-medium text-rose-600">{{ $format($old[$key]) }}</dd>
                    @endif
                </div>
            @endforeach
        </dl>
    </details>
@else
    <span class="text-xs text-slate-400">&mdash;</span>
@endif
