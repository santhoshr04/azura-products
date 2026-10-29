<x-layouts.app title="Categories">
    @php
        $searchClass = 'block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3.5 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100';
    @endphp

    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Categories</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $categories->total() }} {{ \Illuminate\Support\Str::plural('category', $categories->total()) }} organising your catalog.
                </p>
            </div>

            <x-button :href="route('categories.create')">
                <svg class="-ml-0.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 3a1 1 0 0 1 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 1 1 0-2h5V4a1 1 0 0 1 1-1Z" />
                </svg>
                Add category
            </x-button>
        </div>

        <form method="GET" action="{{ route('categories.index') }}" data-auto-filter class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-12">
                <div class="sm:col-span-2 lg:col-span-4">
                    <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700">Search</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.4 9.82l3.14 3.14a.75.75 0 1 0 1.06-1.06l-3.14-3.14A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                        </svg>
                        <input type="search" id="search" name="search" value="{{ request('search') }}" placeholder="Search by category name" class="{{ $searchClass }}">
                    </div>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                <p class="text-xs text-slate-400">Results update automatically as you search.</p>
                <x-button variant="ghost" size="sm" :href="route('categories.index')">Reset filters</x-button>
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th scope="col" class="px-5 py-3">Category</th>
                            <th scope="col" class="px-5 py-3">Status</th>
                            <th scope="col" class="px-5 py-3 text-center">Products</th>
                            <th scope="col" class="px-5 py-3">Created</th>
                            <th scope="col" class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($categories as $category)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-5 py-3.5 font-medium text-slate-900">{{ $category->name }}</td>
                                <td class="px-5 py-3.5">
                                    <x-status-badge :status="$category->status" />
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex min-w-8 items-center justify-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium tabular-nums text-slate-600">{{ $category->products_count }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-500">{{ $category->created_at->format('M j, Y') }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-button variant="ghost" size="sm" :href="route('categories.edit', $category)">Edit</x-button>

                                        @if ($category->products_count > 0)
                                            <span
                                                class="inline-flex cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-medium text-slate-400"
                                                title="Move or delete this category's products before deleting it"
                                            >
                                                Delete
                                            </span>
                                        @else
                                            <form method="POST" action="{{ route('categories.destroy', $category) }}" data-confirm-title="Delete category?" data-confirm="This will permanently remove the &quot;{{ $category->name }}&quot; category. This action cannot be undone.">
                                                @csrf
                                                @method('DELETE')
                                                <x-button variant="danger" size="sm" type="submit">Delete</x-button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <svg class="mx-auto h-10 w-10 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M2 5.5A2.5 2.5 0 0 1 4.5 3h3.086a1 1 0 0 1 .707.293L9.586 4.5H15.5A2.5 2.5 0 0 1 18 7v7.5a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 2 14.5v-9Z" clip-rule="evenodd" />
                                    </svg>
                                    <p class="mt-3 text-sm font-medium text-slate-700">No categories found</p>
                                    <p class="mt-1 text-sm text-slate-500">Try a different search, or add a new category.</p>
                                    <div class="mt-4 flex items-center justify-center gap-3">
                                        <x-button variant="secondary" size="sm" :href="route('categories.index')">Reset filters</x-button>
                                        <x-button size="sm" :href="route('categories.create')">Add category</x-button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-3.5 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">
                        Showing
                        <span class="font-medium text-slate-700">{{ $categories->firstItem() }}</span>
                        to
                        <span class="font-medium text-slate-700">{{ $categories->lastItem() }}</span>
                        of
                        <span class="font-medium text-slate-700">{{ $categories->total() }}</span>
                        categories
                    </p>

                    {{ $categories->links('pagination.app') }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
