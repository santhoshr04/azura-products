@props(['active' => false, 'direction' => 'asc'])

<svg
    @class([
        'h-3.5 w-3.5 shrink-0 transition',
        'text-indigo-600' => $active,
        'text-slate-400 group-hover:text-slate-500' => ! $active,
        'rotate-180' => $active && $direction === 'desc',
    ])
    viewBox="0 0 20 20"
    fill="currentColor"
    aria-hidden="true"
>
    <path fill-rule="evenodd" d="M10 5a.75.75 0 0 1 .53.22l4.25 4.25a.75.75 0 1 1-1.06 1.06L10 6.81l-3.72 3.72a.75.75 0 1 1-1.06-1.06l4.25-4.25A.75.75 0 0 1 10 5Z" clip-rule="evenodd" />
</svg>
