<x-layouts.app title="New category">
    <div class="mx-auto max-w-3xl">
        <nav class="mb-4 flex items-center gap-1.5 text-sm">
            <a href="{{ route('categories.index') }}" data-confirm-leave class="font-medium text-slate-500 transition hover:text-slate-700">Categories</a>
            <svg class="h-3.5 w-3.5 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
            </svg>
            <span class="font-medium text-slate-700">New category</span>
        </nav>

        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Add a category</h1>
            <p class="mt-1 text-sm text-slate-500">Categories group your products, so they can be filtered and reported on.</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @include('categories.partials.form', [
                'category' => null,
                'action' => route('categories.store'),
                'method' => 'POST',
                'submitLabel' => 'Create category',
                'confirmSubmit' => 'Create this category?',
            ])
        </div>
    </div>
</x-layouts.app>
