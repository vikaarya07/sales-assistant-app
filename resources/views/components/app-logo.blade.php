@props([
    'sidebar' => false,
])

@if ($sidebar)
    <flux:sidebar.brand :name="config('app.name', 'Laravel')" {{ $attributes }}>
        <x-slot name="logo"
            class="flex aspect-square size-8 items-center justify-center rounded-md border border-slate-300">
            <img src="{{ asset('storage/favicon.svg') }}" alt="Favicon">
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="config('app.name', 'Laravel')" {{ $attributes }}>
        <x-slot name="logo"
            class="flex aspect-square size-8 items-center justify-center rounded-md border border-slate-300">
            <img src="{{ asset('storage/favicon.svg') }}" alt="Favicon">
        </x-slot>
    </flux:brand>
@endif
