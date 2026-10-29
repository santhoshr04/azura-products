@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'required' => false, 'prefix' => null, 'hint' => null])

@php
    $hasError = $errors->has($name);
@endphp

<div data-field="{{ $name }}">
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-slate-700">
            {{ $label }}@if ($required)<span class="ml-0.5 text-rose-500">*</span>@endif
        </label>
    @endif

    <div class="relative">
        @if ($prefix)
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-sm font-medium text-slate-500">{{ $prefix }}</span>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            @if ($required) required @endif
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            {{ $attributes->merge(['class' => 'block w-full rounded-lg border bg-white py-2.5 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:outline-none focus:ring-4 '.($prefix ? 'pl-8 pr-3.5 ' : 'px-3.5 ').($hasError ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100')]) }}
        >
    </div>

    @error($name)
        <p id="{{ $name }}-error" class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
    @enderror

    @if ($hint)
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
