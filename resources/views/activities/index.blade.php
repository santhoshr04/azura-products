@php
    $sortParam = request('sort', 'created_at');
    $directionParam = request('direction', 'desc');

    $sortLink = function (string $column) use ($sortParam, $directionParam): string {
        $next = ($sortParam === $column && $directionParam === 'asc') ? 'desc' : 'asc';

        return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $next, 'page' => null]);
    };

    $searchClass = 'block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3.5 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100';
@endphp

<x-layouts.app title="Activity Log">
    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Activity Log</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $activities->total() }} {{ \Illuminate\Support\Str::plural('activity', $activities->total()) }} recorded across your catalog.
                </p>
            </div>

            <x-button variant="secondary" :href="route('products.index')" data-confirm-leave>
                <svg class="-ml-0.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.53 2.47a.75.75 0 0 1 0 1.06L4.81 8.25H17a.75.75 0 0 1 0 1.5H4.81l4.72 4.72a.75.75 0 1 1-1.06 1.06l-6-6a.75.75 0 0 1 0-1.06l6-6a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                </svg>
                Back to products
            </x-button>
        </div>

        <form method="GET" action="{{ route('activities.index') }}" data-auto-filter class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-12">
                <div class="sm:col-span-2 lg:col-span-4">
                    <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700">Search</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.4 9.82l3.14 3.14a.75.75 0 1 0 1.06-1.06l-3.14-3.14A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                        </svg>
                        <input type="search" id="search" name="search" value="{{ request('search') }}" placeholder="Search activity descriptions" class="{{ $searchClass }}">
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <x-form.select name="event" label="Event">
                        <option value="">All events</option>
                        @foreach ($events as $event)
                            <option value="{{ $event }}" @selected(request('event') === $event)>{{ ucfirst($event) }}</option>
                        @endforeach
                    </x-form.select>
                </div>

                <div class="lg:col-span-2">
                    <x-form.select name="type" label="Type">
                        <option value="">All types</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}" @selected(request('type') === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </x-form.select>
                </div>

                <div class="lg:col-span-2">
                    <x-form.select name="sort" label="Sort by">
                        <option value="created_at" @selected($sortParam === 'created_at')>Logged date</option>
                        <option value="event" @selected($sortParam === 'event')>Event</option>
                        <option value="log_name" @selected($sortParam === 'log_name')>Type</option>
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
                <x-button variant="ghost" size="sm" :href="route('activities.index')">Reset filters</x-button>
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th scope="col" class="px-5 py-3" @if ($sortParam === 'created_at') aria-sort="{{ $directionParam === 'asc' ? 'ascending' : 'descending' }}" @endif>
                                <a href="{{ $sortLink('created_at') }}" class="group inline-flex items-center gap-1 transition hover:text-slate-700">
                                    When
                                    <x-sort-icon :active="$sortParam === 'created_at'" :direction="$directionParam" />
                                </a>
                            </th>
                            <th scope="col" class="px-5 py-3" @if ($sortParam === 'event') aria-sort="{{ $directionParam === 'asc' ? 'ascending' : 'descending' }}" @endif>
                                <a href="{{ $sortLink('event') }}" class="group inline-flex items-center gap-1 transition hover:text-slate-700">
                                    Event
                                    <x-sort-icon :active="$sortParam === 'event'" :direction="$directionParam" />
                                </a>
                            </th>
                            <th scope="col" class="px-5 py-3" @if ($sortParam === 'log_name') aria-sort="{{ $directionParam === 'asc' ? 'ascending' : 'descending' }}" @endif>
                                <a href="{{ $sortLink('log_name') }}" class="group inline-flex items-center gap-1 transition hover:text-slate-700">
                                    Type
                                    <x-sort-icon :active="$sortParam === 'log_name'" :direction="$directionParam" />
                                </a>
                            </th>
                            <th scope="col" class="px-5 py-3">Description</th>
                            <th scope="col" class="px-5 py-3">Changes</th>
                            <th scope="col" class="px-5 py-3">By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($activities as $activity)
                            <tr class="align-top transition hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-5 py-3.5 text-slate-500">
                                    <span class="block font-medium text-slate-700">{{ $activity->created_at->format('M j, Y') }}</span>
                                    <span class="block text-xs text-slate-400">{{ $activity->created_at->format('g:i A') }}</span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5">
                                    <x-event-badge :event="$activity->event" />
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ ucfirst($activity->log_name) }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-700">{{ $activity->description }}</td>
                                <td class="px-5 py-3.5">
                                    <x-activity-changes :activity="$activity" />
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-slate-500">
                                    {{ $activity->causer?->name ?? 'System' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <svg class="mx-auto h-10 w-10 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-12.5a.75.75 0 0 0-1.5 0v4c0 .2.08.39.22.53l2.5 2.5a.75.75 0 1 0 1.06-1.06l-2.28-2.28V5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <p class="mt-3 text-sm font-medium text-slate-700">No activity found</p>
                                    <p class="mt-1 text-sm text-slate-500">Try adjusting your search or filters.</p>
                                    <x-button variant="secondary" size="sm" :href="route('activities.index')" class="mt-4">Reset filters</x-button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($activities->hasPages())
                <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-3.5 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">
                        Showing
                        <span class="font-medium text-slate-700">{{ $activities->firstItem() }}</span>
                        to
                        <span class="font-medium text-slate-700">{{ $activities->lastItem() }}</span>
                        of
                        <span class="font-medium text-slate-700">{{ $activities->total() }}</span>
                        activities
                    </p>

                    {{ $activities->links('pagination.app') }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
