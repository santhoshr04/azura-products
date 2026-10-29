<x-layouts.app title="Products">
    @php
        $sortParam = request('sort', 'created_at');
        $directionParam = request('direction', 'desc');

        $sortLink = function (string $column) use ($sortParam, $directionParam): string {
            $next = ($sortParam === $column && $directionParam === 'asc') ? 'desc' : 'asc';

            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $next, 'page' => null]);
        };

        $searchClass = 'block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3.5 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100';
    @endphp

    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Products</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $products->total() }} {{ \Illuminate\Support\Str::plural('product', $products->total()) }} in your catalog.
                </p>
            </div>

            <x-button :href="route('products.create')">
                <svg class="-ml-0.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 3a1 1 0 0 1 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 1 1 0-2h5V4a1 1 0 0 1 1-1Z" />
                </svg>
                Add product
            </x-button>
        </div>

        <form method="GET" action="{{ route('products.index') }}" data-auto-filter class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-12">
                <div class="sm:col-span-2 lg:col-span-4">
                    <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700">Search</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.4 9.82l3.14 3.14a.75.75 0 1 0 1.06-1.06l-3.14-3.14A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                        </svg>
                        <input type="search" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name or SKU" class="{{ $searchClass }}">
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <x-form.select name="category" label="Category">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </x-form.select>
                </div>

                <div class="lg:col-span-2">
                    <x-form.select name="status" label="Status">
                        <option value="">All statuses</option>
                        @foreach (\App\Enums\Status::cases() as $status)
                            <option
                                value="{{ $status->value }}"
                                @selected(request('status', \App\Enums\Status::Active->value) === $status->value)
                            >
                                {{ $status->value }}
                            </option>
                        @endforeach
                    </x-form.select>
                </div>

                <div class="lg:col-span-2">
                    <x-form.select name="sort" label="Sort by">
                        <option value="created_at" @selected($sortParam === 'created_at')>Created date</option>
                        <option value="price" @selected($sortParam === 'price')>Price</option>
                        <option value="name" @selected($sortParam === 'name')>Name</option>
                    </x-form.select>
                </div>

                <div class="lg:col-span-2">
                    <x-form.select name="direction" label="Order">
                        <option value="desc" @selected($directionParam === 'desc')>Descending</option>
                        <option value="asc" @selected($directionParam === 'asc')>Ascending</option>
                    </x-form.select>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                <p class="text-xs text-slate-400">Results update automatically as you search and change filters.</p>
                <x-button variant="ghost" size="sm" :href="route('products.index')">Reset filters</x-button>
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th scope="col" class="px-5 py-3">Product</th>
                            <th scope="col" class="px-5 py-3">SKU</th>
                            <th scope="col" class="px-5 py-3">Category</th>
                            <th scope="col" class="px-5 py-3" @if ($sortParam === 'price') aria-sort="{{ $directionParam === 'asc' ? 'ascending' : 'descending' }}" @endif>
                                <a href="{{ $sortLink('price') }}" class="group inline-flex items-center gap-1 transition hover:text-slate-700">
                                    Price (₹)
                                    <x-sort-icon :active="$sortParam === 'price'" :direction="$directionParam" />
                                </a>
                            </th>
                            <th scope="col" class="px-5 py-3">Status</th>
                            <th scope="col" class="px-5 py-3" @if ($sortParam === 'created_at') aria-sort="{{ $directionParam === 'asc' ? 'ascending' : 'descending' }}" @endif>
                                <a href="{{ $sortLink('created_at') }}" class="group inline-flex items-center gap-1 transition hover:text-slate-700">
                                    Created
                                    <x-sort-icon :active="$sortParam === 'created_at'" :direction="$directionParam" />
                                </a>
                            </th>
                            <th scope="col" class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($products as $product)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-5 py-3.5">
                                    <span class="font-medium text-slate-900">{{ $product->name }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="font-mono text-xs text-slate-500">{{ $product->sku }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">{{ $product->category->name }}</td>
                                <td class="px-5 py-3.5 font-medium tabular-nums text-slate-900">₹{{ number_format((float) $product->price, 2) }}</td>
                                <td class="px-5 py-3.5">
                                    <x-status-badge :status="$product->status" />
                                </td>
                                <td class="px-5 py-3.5 text-slate-500">{{ $product->created_at->format('M j, Y') }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-button variant="ghost" size="sm" :href="route('products.edit', $product)">Edit</x-button>

                                        <form method="POST" action="{{ route('products.destroy', $product) }}" data-confirm-title="Delete product?" data-confirm="This will permanently remove &quot;{{ $product->name }}&quot; from your catalog. This action cannot be undone.">
                                            @csrf
                                            @method('DELETE')
                                            <x-button variant="danger" size="sm" type="submit">Delete</x-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <svg class="mx-auto h-10 w-10 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.4 9.82l3.14 3.14a.75.75 0 1 0 1.06-1.06l-3.14-3.14A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                                    </svg>
                                    <p class="mt-3 text-sm font-medium text-slate-700">No products found</p>
                                    <p class="mt-1 text-sm text-slate-500">Try adjusting your search or filters.</p>
                                    <x-button variant="secondary" size="sm" :href="route('products.index')" class="mt-4">Reset filters</x-button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-3.5 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">
                        Showing
                        <span class="font-medium text-slate-700">{{ $products->firstItem() }}</span>
                        to
                        <span class="font-medium text-slate-700">{{ $products->lastItem() }}</span>
                        of
                        <span class="font-medium text-slate-700">{{ $products->total() }}</span>
                        products
                    </p>

                    {{ $products->links('pagination.app') }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
