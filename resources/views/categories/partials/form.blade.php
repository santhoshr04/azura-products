@php
    $category = $category ?? null;
    $selectedStatus = old('status', $category?->status?->value ?? \App\Enums\Status::Active->value);
@endphp

<form
    method="POST"
    action="{{ $action }}"
    novalidate
    data-validate
    data-track-dirty
    data-confirm-submit="{{ $confirmSubmit }}"
    class="space-y-6"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    @if ($errors->any())
        <div class="flex gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm text-rose-800" role="alert">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-4a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 6Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
            </svg>
            <div>
                <p class="font-medium">
                    Please fix {{ $errors->count() === 1 ? 'this issue' : 'these '.$errors->count().' issues' }} before continuing:
                </p>
                <ul class="mt-1 list-disc space-y-0.5 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <x-form.input
                name="name"
                label="Category name"
                :value="$category?->name"
                placeholder="e.g. Electronics"
                hint="Category names must be unique."
                data-rules="required"
                required
            />
        </div>

        <x-form.select name="status" label="Status" data-rules="required" required>
            @foreach (\App\Enums\Status::cases() as $status)
                <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>{{ $status->value }}</option>
            @endforeach
        </x-form.select>
    </div>

    <div class="flex flex-col-reverse items-stretch gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-end">
        <x-button variant="secondary" :href="route('categories.index')" data-confirm-leave>Cancel</x-button>
        <x-button type="submit">{{ $submitLabel }}</x-button>
    </div>
</form>
