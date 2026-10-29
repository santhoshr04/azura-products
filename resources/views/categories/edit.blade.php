<x-layouts.app title="Edit category">
    <div class="mx-auto max-w-3xl">
        <nav class="mb-4 flex items-center gap-1.5 text-sm">
            <a href="{{ route('categories.index') }}" data-confirm-leave class="font-medium text-slate-500 transition hover:text-slate-700">Categories</a>
            <svg class="h-3.5 w-3.5 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
            </svg>
            <span class="font-medium text-slate-700">Edit category</span>
        </nav>

        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Edit category</h1>
            <p class="mt-1 text-sm text-slate-500">Update the details for <span class="font-medium text-slate-700">{{ $category->name }}</span>.</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @include('categories.partials.form', [
                'category' => $category,
                'action' => route('categories.update', $category),
                'method' => 'PUT',
                'submitLabel' => 'Save changes',
                'confirmSubmit' => 'Save changes to this category?',
            ])
        </div>

        <div class="mt-6 rounded-xl border border-rose-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between sm:gap-8">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Delete this category</h2>

                    @if ($category->products_count > 0)
                        <p class="mt-1 text-sm text-slate-500">
                            This category still has
                            <span class="font-medium text-slate-700">{{ $category->products_count }}</span>
                            {{ \Illuminate\Support\Str::plural('product', $category->products_count) }} assigned to it.
                            Move or delete {{ $category->products_count === 1 ? 'it' : 'them' }} before you can delete this category.
                        </p>
                    @else
                        <p class="mt-1 text-sm text-slate-500">
                            Permanently remove <span class="font-medium text-slate-700">{{ $category->name }}</span>. This action cannot be undone.
                        </p>
                    @endif
                </div>

                @if ($category->products_count > 0)
                    <x-button
                        variant="danger"
                        class="shrink-0"
                        disabled
                        title="Move or delete this category's products before deleting it"
                    >
                        Delete category
                    </x-button>
                @else
                    <form
                        method="POST"
                        action="{{ route('categories.destroy', $category) }}"
                        data-confirm-title="Delete category?"
                        data-confirm="This will permanently remove the &quot;{{ $category->name }}&quot; category. This action cannot be undone."
                        class="shrink-0"
                    >
                        @csrf
                        @method('DELETE')
                        <x-button variant="danger" type="submit">Delete category</x-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
