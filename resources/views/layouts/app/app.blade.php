<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Sales WhatsApp Assistant' }}</title>

    <meta name="description"
        content="Sales WhatsApp Assistant adalah aplikasi produktivitas untuk membantu salesperson mengelola customer dan mempersiapkan komunikasi WhatsApp.">

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-white">

    {{-- NAVBAR --}}
    <header
        class="sticky top-0 z-50 border-b border-zinc-200/70 bg-white/85 backdrop-blur-xl dark:border-zinc-800/70 dark:bg-zinc-950/85">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-5 sm:px-6 lg:px-8">

            {{-- LOGO --}}
            <a href="{{ route('home') }}" wire:navigate class="flex min-w-0 shrink-0 items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-900">
                    <img src="{{ asset('favicon.svg') }}" alt="Sales WhatsApp Assistant" class="size-7 object-contain">
                </div>

                <div class="hidden sm:block">
                    <flux:heading size="sm" class="tracking-tight">
                        Sales WhatsApp Assistant
                    </flux:heading>
                    <flux:text size="xs" class="text-zinc-500">
                        Sales Productivity Assistant
                    </flux:text>
                </div>
            </a>

            {{-- NAVIGASI DESKTOP --}}
            <nav class="hidden md:block">
                <flux:navbar>
                    <flux:navbar.item href="{{ route('home') }}#fitur" wire:navigate icon="squares-2x2">
                        Fitur
                    </flux:navbar.item>

                    <flux:navbar.item href="{{ route('home') }}#cara-kerja" wire:navigate icon="arrow-path">
                        Cara Kerja
                    </flux:navbar.item>

                    <flux:navbar.item href="{{ route('about') }}" wire:navigate icon="information-circle">
                        Tentang
                    </flux:navbar.item>

                    <flux:navbar.item href="{{ route('updates') }}" wire:navigate icon="megaphone">
                        Pembaruan
                    </flux:navbar.item>
                </flux:navbar>
            </nav>

            {{-- AKSI --}}
            <div class="flex shrink-0 items-center gap-2">
                @auth
                    <flux:button
                        href="{{ auth()->user()->role->value === 'admin' ? route('admin.overview') : route('user.dashboard') }}"
                        wire:navigate variant="primary" size="sm">
                        Dashboard
                    </flux:button>
                @else
                    <flux:button href="{{ route('login') }}" wire:navigate variant="ghost" size="sm">
                        Masuk
                    </flux:button>

                    <flux:button href="{{ route('register') }}" wire:navigate variant="primary" size="sm">
                        Daftar
                    </flux:button>
                @endauth

                {{-- TOGGLE LIGHT / DARK --}}
                <button type="button" x-data @click="$flux.appearance = $flux.appearance === 'dark' ? 'light' : 'dark'"
                    :aria-label="$flux.appearance === 'dark' ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'"
                    class="flex size-10 shrink-0 items-center justify-center rounded-full border border-indigo-200 bg-indigo-100 text-indigo-700 transition hover:bg-indigo-200 dark:border-violet-800 dark:bg-violet-950/50 dark:text-violet-300 dark:hover:bg-violet-900">
                    <flux:icon.sun x-show="$flux.appearance !== 'dark'" variant="mini" class="size-5" />
                    <flux:icon.moon x-show="$flux.appearance === 'dark'" variant="mini" class="size-5" />
                </button>

                {{-- NAVIGASI MOBILE FLUX --}}
                <div class="md:hidden">
                    <flux:dropdown position="bottom" align="end">
                        <flux:button variant="ghost" square aria-label="Buka navigasi" icon="bars-3" />

                        <flux:menu class="min-w-52">
                            <flux:menu.item href="{{ route('home') }}#fitur" wire:navigate icon="squares-2x2">
                                Fitur
                            </flux:menu.item>

                            <flux:menu.item href="{{ route('home') }}#cara-kerja" wire:navigate icon="arrow-path">
                                Cara Kerja
                            </flux:menu.item>

                            <flux:menu.item href="{{ route('about') }}" wire:navigate icon="information-circle">
                                Tentang
                            </flux:menu.item>

                            <flux:menu.item href="{{ route('updates') }}" wire:navigate icon="megaphone">
                                Pembaruan
                            </flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </div>
            </div>
        </div>
    </header>

    {{-- KONTEN --}}
    <main>
        {{ $slot }}
    </main>

    {{-- FOOTER --}}
    <footer class="border-t border-zinc-800 bg-zinc-900 text-white">
        <div class="mx-auto max-w-7xl px-5 py-10 sm:px-6 lg:px-8">

            <div class="flex flex-col gap-8 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="flex size-9 items-center justify-center rounded-lg bg-white">
                            <img src="{{ asset('favicon.svg') }}" alt="" class="size-6">
                        </div>

                        <span
                            class="bg-linear-to-r from-indigo-400 via-violet-400 to-fuchsia-400 bg-clip-text text-sm font-semibold text-transparent">
                            Sales WhatsApp Assistant
                        </span>
                    </div>

                    <flux:text size="sm" class="mt-2 text-zinc-400">
                        Dibuat untuk Sales. Dirancang untuk kesederhanaan.
                    </flux:text>
                </div>

                <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                    <flux:link href="{{ route('about') }}" wire:navigate class="text-zinc-400 hover:text-white">
                        Tentang
                    </flux:link>

                    <flux:link href="{{ route('updates') }}" wire:navigate class="text-zinc-400 hover:text-white">
                        Pembaruan
                    </flux:link>

                    @guest
                        <flux:link href="{{ route('login') }}" wire:navigate class="text-zinc-400 hover:text-white">
                            Masuk
                        </flux:link>

                        <flux:link href="{{ route('register') }}" wire:navigate class="text-zinc-400 hover:text-white">
                            Daftar
                        </flux:link>
                    @endguest
                </div>
            </div>

            <div
                class="mt-8 flex flex-col gap-3 border-t border-zinc-800 pt-6 sm:flex-row sm:items-center sm:justify-between">
                <flux:text size="sm" class="text-zinc-400">
                    © {{ date('Y') }} Vika Arya. Hak cipta dilindungi.
                </flux:text>

                <flux:text size="sm" class="text-zinc-400">
                    Created with
                    <span class="mx-1 text-red-500">❤️</span>
                    by
                    <a href="https://vikaarya07.my.id/" target="_blank" rel="noopener noreferrer"
                        class="ml-1 font-medium text-sky-400 transition hover:text-blue-400">
                        @vikaarya07
                    </a>
                </flux:text>
            </div>

            <div class="mt-5 text-center">
                <flux:text size="xs" class="leading-5 text-zinc-400">
                    Sales WhatsApp Assistant adalah aplikasi pihak ketiga
                    yang dikembangkan secara independen dan tidak berafiliasi,
                    didukung, atau disponsori oleh Meta Platforms, Inc.
                    maupun WhatsApp.
                </flux:text>
            </div>
        </div>
    </footer>

    @fluxScripts

    {{-- SWEETALERT2 TOAST --}}
    @if (session('swal'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const swal = @js(session('swal'));

                if (swal.type === 'toast') {
                    window.sweetAlert?.toast(
                        swal.message,
                        swal.icon ?? 'success',
                    );
                }
            });
        </script>
    @endif

</body>

</html>
