@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title.' · '.config('app.name') : config('app.name') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full bg-slate-100 text-slate-800 antialiased">
        <div class="flex min-h-screen flex-col">
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/85 backdrop-blur">
                <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <a href="{{ route('products.index') }}" class="flex items-center gap-2.5 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-sm font-semibold text-white shadow-sm">A</span>
                        <span class="text-[15px] font-semibold tracking-tight text-slate-900">
                            Product <span class="font-normal text-slate-500">Management</span>
                        </span>
                    </a>

                    <nav class="flex items-center gap-1 text-sm font-medium">
                        <a
                            href="{{ route('products.index') }}"
                            data-confirm-leave
                            @class([
                                'rounded-md px-3 py-2 transition',
                                'bg-slate-100 text-slate-900' => request()->routeIs('products.*'),
                                'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! request()->routeIs('products.*'),
                            ])
                        >
                            Products
                        </a>

                        <a
                            href="{{ route('categories.index') }}"
                            data-confirm-leave
                            @class([
                                'rounded-md px-3 py-2 transition',
                                'bg-slate-100 text-slate-900' => request()->routeIs('categories.*'),
                                'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! request()->routeIs('categories.*'),
                            ])
                        >
                            Categories
                        </a>

                        <a
                            href="{{ route('activities.index') }}"
                            data-confirm-leave
                            @class([
                                'rounded-md px-3 py-2 transition',
                                'bg-slate-100 text-slate-900' => request()->routeIs('activities.*'),
                                'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! request()->routeIs('activities.*'),
                            ])
                        >
                            Activity
                        </a>
                    </nav>
                </div>
            </header>

            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">
                @foreach (['success', 'error'] as $type)
                    @if (session($type))
                        <x-alert :type="$type" :message="session($type)" class="mb-6" />
                    @endif
                @endforeach

                {{ $slot }}
            </main>

            <footer class="border-t border-slate-200 bg-white">
                <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <p class="text-xs text-slate-400">&copy; {{ date('Y') }} {{ config('app.name') }} · Product Management Demo</p>
                </div>
            </footer>
        </div>
    </body>
</html>
