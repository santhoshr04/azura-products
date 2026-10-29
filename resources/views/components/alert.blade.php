@props(['type' => 'success', 'message' => null])

@php
    $themes = [
        'success' => [
            'wrapper' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
            'icon' => 'text-emerald-500',
        ],
        'error' => [
            'wrapper' => 'border-rose-200 bg-rose-50 text-rose-800',
            'icon' => 'text-rose-500',
        ],
    ];

    $theme = $themes[$type] ?? $themes['success'];
    $message ??= (string) $slot;
@endphp

<div
    data-flash-notice
    data-type="{{ $type }}"
    data-message="{{ $message }}"
    {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-xl border px-4 py-3 text-sm '.$theme['wrapper']]) }}
    role="alert"
>
    @if ($type === 'success')
        <svg class="mt-0.5 h-5 w-5 shrink-0 {{ $theme['icon'] }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
        </svg>
    @else
        <svg class="mt-0.5 h-5 w-5 shrink-0 {{ $theme['icon'] }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-4a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 6Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
        </svg>
    @endif

    <p class="font-medium">{{ $message }}</p>
</div>
