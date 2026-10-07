<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sales WhatsApp Assistant</title>

    <meta name="description"
        content="Sales WhatsApp Assistant adalah aplikasi produktivitas untuk membantu salesperson mengelola customer dan mempersiapkan komunikasi WhatsApp.">

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    {{-- Laravel Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Flux UI --}}
    @fluxAppearance

</head>

<body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-white">

    {{-- NAVBAR --}}
    <header
        class="sticky top-0 z-50 border-b border-zinc-200/70 bg-white/85 backdrop-blur-xl dark:border-zinc-800/70 dark:bg-zinc-950/85">

        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-5 sm:px-6 lg:px-8">

            {{-- LOGO --}}
            {{-- <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3"> --}}
            <a href="" wire:navigate class="flex items-center gap-3">

                <div class="flex size-9 items-center justify-center rounded-xl">
                    <div class="flex size-full items-center justify-center rounded-[10px] bg-white dark:bg-zinc-950">
                        <img src="{{ asset('favicon.svg') }}" alt="Sales WhatsApp Assistant"
                            class="size-7 object-contain">
                    </div>
                </div>

                <div class="hidden sm:block">

                    <div class="text-sm font-semibold tracking-tight text-zinc-950 dark:text-white">
                        Sales WhatsApp Assistant
                    </div>

                    <div class="text-[10px] text-zinc-500">
                        Sales Productivity Assistant
                    </div>

                </div>

            </a>

            {{-- NAVIGASI --}}
            <nav class="hidden items-center gap-1 md:flex">

                <a href="#fitur"
                    class="rounded-lg px-3 py-2 text-sm text-zinc-600 transition hover:bg-indigo-50 hover:text-indigo-700 dark:text-zinc-400 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-300">
                    Fitur
                </a>

                <a href="#cara-kerja"
                    class="rounded-lg px-3 py-2 text-sm text-zinc-600 transition hover:bg-violet-50 hover:text-violet-700 dark:text-zinc-400 dark:hover:bg-violet-500/10 dark:hover:text-violet-300">
                    Cara Kerja
                </a>

                <a href="#tentang" wire:navigate
                    class="rounded-lg px-3 py-2 text-sm text-zinc-600 transition hover:bg-fuchsia-50 hover:text-fuchsia-700 dark:text-zinc-400 dark:hover:bg-fuchsia-500/10 dark:hover:text-fuchsia-300">
                    Tentang
                </a>

                <a href="#pembaruan" wire:navigate
                    class="rounded-lg px-3 py-2 text-sm text-zinc-600 transition hover:bg-indigo-50 hover:text-indigo-700 dark:text-zinc-400 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-300">
                    Pembaruan
                </a>

            </nav>

            {{-- AUTH --}}
            <div class="flex items-center gap-2">

                @auth

                    <flux:button href="{{ route('dashboard') }}" wire:navigate variant="primary" icon="arrow-right">
                        Dashboard
                    </flux:button>
                @else
                    <flux:button href="{{ route('login') }}" wire:navigate variant="ghost">
                        Masuk
                    </flux:button>

                    <flux:button href="{{ route('register') }}" wire:navigate variant="primary">
                        Daftar
                    </flux:button>

                @endauth

            </div>

        </div>

    </header>

    {{-- KONTEN --}}
    <main>

        {{-- HERO --}}
        <section class="relative overflow-hidden">

            {{-- Background dekorasi --}}
            <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">

                <div
                    class="absolute left-1/2 -top-75 h-150 w-200 -translate-x-1/2 rounded-full bg-linear-to-r from-indigo-500/15 via-violet-500/15 to-fuchsia-500/15 blur-3xl">
                </div>

                <div class="absolute -left-40 top-60 size-80 rounded-full bg-indigo-500/10 blur-3xl"></div>

                <div class="absolute -right-40 top-72 size-80 rounded-full bg-fuchsia-500/10 blur-3xl"></div>

            </div>

            <div class="mx-auto max-w-7xl px-5 pb-20 pt-20 sm:px-6 lg:px-8 lg:pb-28 lg:pt-28">

                <div class="mx-auto max-w-4xl text-center">

                    {{-- Badge --}}
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700 dark:border-indigo-500/20 dark:bg-indigo-500/10 dark:text-indigo-300">

                        <span class="relative flex size-1.5">

                            <span
                                class="absolute inline-flex size-full animate-ping rounded-full bg-fuchsia-400 opacity-75"></span>

                            <span class="relative inline-flex size-1.5 rounded-full bg-fuchsia-500"></span>

                        </span>
                        Sales Productivity Application
                    </div>

                    {{-- Judul --}}
                    <h1
                        class="mt-7 text-4xl font-bold tracking-tight text-zinc-950 dark:text-white sm:text-6xl lg:text-7xl">

                        Kelola Customer.

                        <span class="block">
                            Persiapkan Komunikasi.
                        </span>

                        <span
                            class="block bg-linear-to-r from-indigo-600 via-violet-600 to-fuchsia-600 bg-clip-text text-transparent">
                            Lebih Sederhana.
                        </span>

                    </h1>

                    {{-- Deskripsi --}}
                    <p class="mx-auto mt-7 max-w-2xl text-base leading-7 text-zinc-600 dark:text-zinc-400 sm:text-lg">

                        <strong class="font-medium text-zinc-950 dark:text-white">
                            Sales WhatsApp Assistant
                        </strong>

                        membantu salesperson mengelola data customer,
                        membuat template pesan, dan mempersiapkan
                        komunikasi melalui WhatsApp dalam satu alur
                        kerja yang lebih terorganisir.

                    </p>

                    {{-- Tombol --}}
                    <div class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">

                        @auth

                            <flux:button href="{{ route('dashboard') }}" wire:navigate variant="primary" icon="arrow-right">
                                Buka Dashboard
                            </flux:button>
                        @else
                            <flux:button href="{{ route('register') }}" wire:navigate variant="primary" icon="arrow-right">
                                Mulai Sekarang
                            </flux:button>

                            <flux:button href="{{ route('login') }}" wire:navigate variant="ghost">
                                Masuk
                            </flux:button>

                        @endauth

                    </div>

                    <p class="mt-5 text-xs text-zinc-500 dark:text-zinc-500">
                        Aplikasi pihak ketiga · Tidak berafiliasi dengan
                        Meta atau WhatsApp
                    </p>

                </div>

                {{-- PREVIEW APLIKASI --}}
                <div class="mx-auto mt-16 max-w-6xl">

                    <div
                        class="relative rounded-2xl bg-linear-to-br from-indigo-500 via-violet-500 to-fuchsia-500 p-px shadow-2xl shadow-violet-500/20">

                        <div class="overflow-hidden rounded-2xl bg-white p-2 dark:bg-zinc-950">

                            <div
                                class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-900">

                                {{-- Browser header --}}
                                <div class="flex items-center gap-2">

                                    <div class="size-2.5 rounded-full bg-zinc-300 dark:bg-zinc-700"></div>
                                    <div class="size-2.5 rounded-full bg-zinc-300 dark:bg-zinc-700"></div>
                                    <div class="size-2.5 rounded-full bg-zinc-300 dark:bg-zinc-700"></div>

                                    <div class="ml-3 text-xs text-zinc-400">
                                        Sales WhatsApp Assistant
                                    </div>

                                </div>

                                <div class="mt-5 grid gap-4 lg:grid-cols-[210px_1fr]">

                                    {{-- Sidebar --}}
                                    <div
                                        class="hidden rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-950 lg:block">

                                        <div class="flex items-center gap-2">

                                            <div class="flex size-7 items-center justify-center rounded-lg">

                                                <div
                                                    class="flex size-full items-center justify-center rounded-[7px] bg-white dark:bg-zinc-950">

                                                    <img src="{{ asset('favicon.svg') }}" class="size-5"
                                                        alt="">

                                                </div>

                                            </div>


                                            <span class="text-xs font-semibold">
                                                Sales Assistant
                                            </span>

                                        </div>

                                        <div class="mt-6 space-y-1.5">

                                            <div
                                                class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs text-zinc-500">

                                                <flux:icon name="home" class="size-4" />
                                                Dashboard
                                            </div>

                                            <div
                                                class="flex items-center gap-2 rounded-lg bg-linear-to-r from-indigo-50 to-violet-50 px-3 py-2 text-xs font-medium text-indigo-700 dark:from-indigo-500/10 dark:to-violet-500/10 dark:text-indigo-300">

                                                <flux:icon name="users" class="size-4" />
                                                Customer
                                            </div>

                                            <div
                                                class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs text-zinc-500">

                                                <flux:icon name="document-text" class="size-4" />
                                                Template Pesan
                                            </div>

                                        </div>

                                    </div>

                                    {{-- Dashboard --}}
                                    <div>

                                        <div class="flex items-center justify-between">

                                            <div>

                                                <div class="text-sm font-semibold">
                                                    Customer
                                                </div>

                                                <div class="mt-1 text-xs text-zinc-500">
                                                    Kelola data customer Sales.
                                                </div>

                                            </div>

                                            <div
                                                class="hidden rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-medium text-green-700 sm:block dark:bg-green-500/10 dark:text-green-400">
                                                Aktif
                                            </div>

                                        </div>

                                        {{-- Statistik --}}
                                        <div class="mt-5 grid gap-3 sm:grid-cols-3">

                                            <div
                                                class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-950">

                                                <div class="text-xs text-zinc-500">
                                                    Customer
                                                </div>

                                                <div class="mt-2 text-lg font-semibold">
                                                    Kelola
                                                </div>

                                            </div>

                                            <div
                                                class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-950">

                                                <div class="text-xs text-zinc-500">
                                                    Template
                                                </div>

                                                <div class="mt-2 text-lg font-semibold">
                                                    Pesan
                                                </div>

                                            </div>

                                            <div
                                                class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-950">

                                                <div class="text-xs text-zinc-500">
                                                    Komunikasi
                                                </div>

                                                <div class="mt-2 text-lg font-semibold">
                                                    WhatsApp
                                                </div>

                                            </div>

                                        </div>

                                        {{-- Customer --}}
                                        <div
                                            class="mt-4 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-950">

                                            <div class="flex items-center justify-between">

                                                <div class="flex items-center gap-3">

                                                    <flux:avatar src="{{ asset('storage/profile-full.png') }}" />

                                                    <div>

                                                        <div class="text-xs font-medium">
                                                            Vika Arya
                                                        </div>

                                                        <div class="mt-1 text-[11px] text-zinc-500">
                                                            +62 857-9992-8828
                                                        </div>

                                                    </div>

                                                </div>


                                                <div
                                                    class="rounded-full bg-indigo-50 px-2 py-1 text-[10px] font-medium text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                                                    Baru
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        {{-- TENTANG APLIKASI --}}
        <section class="border-y border-zinc-200 bg-zinc-50/60 dark:border-zinc-800 dark:bg-zinc-900/30">

            <div class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">

                <div class="grid gap-12 lg:grid-cols-2 lg:items-center">

                    <div>

                        <flux:badge color="indigo">
                            Tentang Aplikasi
                        </flux:badge>


                        <h2 class="mt-5 text-3xl font-semibold tracking-tight sm:text-4xl">
                            Dibuat untuk membantu workflow Sales.
                        </h2>

                        <div class="mt-5 space-y-4 text-base leading-7 text-zinc-600 dark:text-zinc-400">

                            <p>
                                Sales WhatsApp Assistant adalah aplikasi web
                                yang dirancang untuk membantu salesperson
                                mengelola data customer dan mempersiapkan
                                komunikasi secara lebih terorganisir.
                            </p>

                            <p>
                                Data customer dapat dimasukkan dengan cara
                                copy-paste, kemudian dicari, difilter,
                                dan dikelola berdasarkan status komunikasi.
                            </p>

                            <p>
                                Pengguna juga dapat membuat template pesan
                                dan menggunakan informasi customer sebagai
                                bagian dari pesan yang dipersiapkan.
                            </p>

                        </div>

                        <div class="mt-7">

                            <flux:button href="{{ route('about') }}" wire:navigate variant="ghost"
                                icon="arrow-right">
                                Selengkapnya
                            </flux:button>

                        </div>

                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">

                        <flux:card
                            class="p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/10">

                            <div
                                class="flex size-11 items-center justify-center rounded-xl bg-linear-to-br from-indigo-500 to-violet-600 text-white shadow-lg shadow-indigo-500/20">

                                <flux:icon name="users" class="size-5" />

                            </div>

                            <flux:heading class="mt-5">
                                Kelola Customer
                            </flux:heading>


                            <flux:text class="mt-2">
                                Kelola data customer dalam satu tempat.
                            </flux:text>

                        </flux:card>

                        <flux:card
                            class="p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-violet-500/10">

                            <div
                                class="flex size-11 items-center justify-center rounded-xl bg-linear-to-br from-violet-500 to-fuchsia-600 text-white shadow-lg shadow-violet-500/20">

                                <flux:icon name="document-text" class="size-5" />

                            </div>


                            <flux:heading class="mt-5">
                                Template Pesan
                            </flux:heading>


                            <flux:text class="mt-2">
                                Simpan template pesan untuk digunakan kembali.
                            </flux:text>

                        </flux:card>

                        <flux:card
                            class="p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/10">

                            <div
                                class="flex size-11 items-center justify-center rounded-xl bg-linear-to-br from-indigo-500 to-violet-600 text-white shadow-lg shadow-indigo-500/20">

                                <flux:icon name="magnifying-glass" class="size-5" />

                            </div>

                            <flux:heading class="mt-5">
                                Pencarian
                            </flux:heading>

                            <flux:text class="mt-2">
                                Temukan customer dengan lebih cepat.
                            </flux:text>

                        </flux:card>

                        <flux:card
                            class="p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-fuchsia-500/10">

                            <div
                                class="flex size-11 items-center justify-center rounded-xl bg-linear-to-br from-fuchsia-500 to-violet-600 text-white shadow-lg shadow-fuchsia-500/20">

                                <flux:icon name="chat-bubble-left-right" class="size-5" />

                            </div>

                            <flux:heading class="mt-5">
                                Komunikasi
                            </flux:heading>

                            <flux:text class="mt-2">
                                Persiapkan pesan sebelum melanjutkan ke WhatsApp.
                            </flux:text>

                        </flux:card>

                    </div>

                </div>

            </div>

        </section>

        {{-- FITUR --}}
        <section id="fitur" class="scroll-mt-20">

            <div class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">

                <div class="max-w-2xl">

                    <flux:badge color="violet">
                        Fitur
                    </flux:badge>

                    <h2 class="mt-5 text-3xl font-semibold tracking-tight sm:text-4xl">
                        Semua yang dibutuhkan untuk workflow Sales.
                    </h2>


                    <p class="mt-4 text-zinc-600 dark:text-zinc-400">
                        Dirancang untuk membantu pekerjaan Sales menjadi
                        lebih terorganisir dan praktis.
                    </p>

                </div>

                @php
                    $fitur = [
                        [
                            'icon' => 'clipboard-document-list',
                            'judul' => 'Import Cepat',
                            'deskripsi' =>
                                'Masukkan data customer dengan cara copy-paste tanpa proses import file yang rumit.',
                            'linear' => 'from-indigo-500 to-violet-600',
                            'shadow' => 'shadow-indigo-500/20',
                        ],
                        [
                            'icon' => 'users',
                            'judul' => 'Manajemen Customer',
                            'deskripsi' => 'Simpan dan kelola data customer secara lebih terorganisir.',
                            'linear' => 'from-violet-500 to-fuchsia-600',
                            'shadow' => 'shadow-violet-500/20',
                        ],
                        [
                            'icon' => 'magnifying-glass',
                            'judul' => 'Pencarian & Filter',
                            'deskripsi' => 'Cari customer berdasarkan nama, nomor, kontrak, cabang, atau status.',
                            'linear' => 'from-indigo-500 to-violet-600',
                            'shadow' => 'shadow-indigo-500/20',
                        ],
                        [
                            'icon' => 'document-text',
                            'judul' => 'Template Pesan',
                            'deskripsi' => 'Buat dan simpan template pesan yang dapat digunakan kembali.',
                            'linear' => 'from-violet-500 to-fuchsia-600',
                            'shadow' => 'shadow-violet-500/20',
                        ],
                        [
                            'icon' => 'chat-bubble-left-right',
                            'judul' => 'Penyusun Pesan WhatsApp',
                            'deskripsi' =>
                                'Persiapkan pesan menggunakan data customer sebelum melanjutkan ke WhatsApp.',
                            'linear' => 'from-indigo-500 to-fuchsia-600',
                            'shadow' => 'shadow-fuchsia-500/20',
                        ],
                        [
                            'icon' => 'chart-bar',
                            'judul' => 'Status Customer',
                            'deskripsi' => 'Catat perkembangan komunikasi customer melalui status yang tersedia.',
                            'linear' => 'from-fuchsia-500 to-violet-600',
                            'shadow' => 'shadow-fuchsia-500/20',
                        ],
                    ];
                @endphp

                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                    @foreach ($fitur as $item)
                        <flux:card
                            class="group p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl {{ $item['shadow'] }}">

                            <div
                                class="flex size-11 items-center justify-center rounded-xl bg-linear-to-br {{ $item['linear'] }} text-white shadow-lg">

                                <flux:icon :name="$item['icon']" class="size-5" />

                            </div>

                            <flux:heading class="mt-5">
                                {{ $item['judul'] }}
                            </flux:heading>

                            <flux:text class="mt-2">
                                {{ $item['deskripsi'] }}
                            </flux:text>

                        </flux:card>
                    @endforeach

                </div>

            </div>

        </section>

        {{-- CARA KERJA --}}
        <section id="cara-kerja"
            class="scroll-mt-20 border-y border-zinc-200 bg-zinc-50/60 dark:border-zinc-800 dark:bg-zinc-900/30">

            <div class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">

                <div class="mx-auto max-w-2xl text-center">

                    <flux:badge color="fuchsia">
                        Cara Kerja
                    </flux:badge>

                    <h2 class="mt-5 text-3xl font-semibold tracking-tight sm:text-4xl">
                        Dari data customer sampai siap berkomunikasi.
                    </h2>

                    <p class="mt-4 text-zinc-600 dark:text-zinc-400">
                        Alur kerja dibuat sederhana agar pekerjaan
                        berulang menjadi lebih praktis.
                    </p>

                </div>

                @php
                    $langkah = [
                        [
                            'nomor' => '01',
                            'judul' => 'Import',
                            'deskripsi' => 'Copy data customer kemudian paste langsung ke aplikasi.',
                        ],
                        [
                            'nomor' => '02',
                            'judul' => 'Kelola',
                            'deskripsi' => 'Cari, filter, dan kelola customer berdasarkan informasi yang tersedia.',
                        ],
                        [
                            'nomor' => '03',
                            'judul' => 'Persiapkan',
                            'deskripsi' => 'Pilih template dan persiapkan pesan berdasarkan data customer.',
                        ],
                        [
                            'nomor' => '04',
                            'judul' => 'Lanjutkan',
                            'deskripsi' => 'Lanjutkan komunikasi melalui WhatsApp sesuai workflow kamu.',
                        ],
                    ];
                @endphp

                <div class="mt-12 grid gap-5 md:grid-cols-4">

                    @foreach ($langkah as $item)
                        <flux:card
                            class="p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-violet-500/10">

                            <div
                                class="flex size-10 items-center justify-center rounded-full bg-linear-to-br from-indigo-600 via-violet-600 to-fuchsia-600 text-xs font-semibold text-white shadow-lg shadow-violet-500/20">
                                {{ $item['nomor'] }}
                            </div>

                            <flux:heading class="mt-5">
                                {{ $item['judul'] }}
                            </flux:heading>

                            <flux:text class="mt-2">
                                {{ $item['deskripsi'] }}
                            </flux:text>

                        </flux:card>
                    @endforeach

                </div>

            </div>

        </section>

        {{-- PERINGATAN WHATSAPP --}}
        <section class="border-b border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-950/20">

            <div class="mx-auto max-w-5xl px-5 py-14 sm:px-6 lg:px-8">

                <flux:card class="border-amber-200 bg-transparent shadow-none dark:border-amber-900/50">

                    <div class="flex gap-5">

                        <div
                            class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400">

                            <flux:icon name="exclamation-triangle" class="size-6" />

                        </div>

                        <div>

                            <flux:badge color="amber">
                                Penting
                            </flux:badge>

                            <flux:heading class="mt-4">
                                Peringatan Penggunaan WhatsApp
                            </flux:heading>

                            <div class="mt-5 space-y-4 text-sm leading-7 text-zinc-600 dark:text-zinc-400">

                                <p>
                                    <strong class="text-zinc-950 dark:text-white">
                                        Sales WhatsApp Assistant bukan merupakan
                                        produk, layanan, aplikasi, atau layanan
                                        resmi dari Meta maupun WhatsApp.
                                    </strong>
                                </p>

                                <p>
                                    Aplikasi ini merupakan
                                    <strong class="text-zinc-950 dark:text-white">
                                        aplikasi pihak ketiga
                                    </strong>
                                    yang dikembangkan secara independen untuk
                                    membantu workflow pengguna.
                                </p>

                                <p>
                                    Penggunaan WhatsApp tetap mengikuti
                                    ketentuan dan kebijakan layanan WhatsApp.
                                </p>

                                <p>
                                    Aktivitas seperti spam, penyalahgunaan
                                    layanan, atau aktivitas yang melanggar
                                    kebijakan dapat menyebabkan pesan dibatasi
                                    atau akun mendapatkan pembatasan.
                                </p>

                                <div
                                    class="rounded-xl border border-amber-200 bg-white/60 p-4 dark:border-amber-900/50 dark:bg-amber-950/20">

                                    <p class="font-medium text-zinc-900 dark:text-white">
                                        ⚠ Tidak ada jaminan bahwa akun WhatsApp
                                        akan selalu bebas dari pembatasan
                                        atau pemblokiran.
                                    </p>

                                </div>

                                <p>
                                    Pengguna bertanggung jawab atas cara
                                    aplikasi digunakan, isi pesan, penerima
                                    komunikasi, serta kepatuhan terhadap
                                    kebijakan layanan yang digunakan.
                                </p>

                            </div>

                        </div>

                    </div>

                </flux:card>

            </div>

        </section>

        {{-- PENGGUNAAN BERTANGGUNG JAWAB --}}
        <section>

            <div class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">

                <div class="max-w-2xl">

                    <flux:badge color="indigo">
                        Penggunaan Bertanggung Jawab
                    </flux:badge>

                    <h2 class="mt-5 text-3xl font-semibold tracking-tight sm:text-4xl">
                        Gunakan aplikasi secara bertanggung jawab.
                    </h2>

                    <p class="mt-4 text-zinc-600 dark:text-zinc-400">
                        Beberapa hal yang perlu diperhatikan ketika
                        menggunakan aplikasi.
                    </p>

                </div>

                <div class="mt-10 grid gap-5 md:grid-cols-3">

                    <flux:card class="p-7">

                        <div
                            class="flex size-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">

                            <flux:icon name="shield-check" class="size-6" />

                        </div>

                        <flux:heading class="mt-5">
                            Hindari Spam
                        </flux:heading>

                        <flux:text class="mt-2">
                            Gunakan komunikasi secara wajar dan hindari
                            aktivitas yang dapat dianggap sebagai spam.
                        </flux:text>

                    </flux:card>

                    <flux:card class="p-7">

                        <div
                            class="flex size-11 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">

                            <flux:icon name="lock-closed" class="size-6" />

                        </div>

                        <flux:heading class="mt-5">
                            Jaga Data Customer
                        </flux:heading>

                        <flux:text class="mt-2">
                            Pastikan data customer dikelola secara bertanggung
                            jawab dan sesuai kebutuhan.
                        </flux:text>

                    </flux:card>

                    <flux:card class="p-7">

                        <div
                            class="flex size-11 items-center justify-center rounded-xl bg-fuchsia-50 text-fuchsia-600 dark:bg-fuchsia-500/10 dark:text-fuchsia-400">

                            <flux:icon name="document-check" class="size-6" />

                        </div>

                        <flux:heading class="mt-5">
                            Pahami Kebijakan
                        </flux:heading>


                        <flux:text class="mt-2">
                            Pengguna tetap bertanggung jawab memahami
                            kebijakan layanan yang digunakan.
                        </flux:text>

                    </flux:card>

                </div>

            </div>

        </section>

        {{-- CREATOR --}}
        <section id="tentang"
            class="border-y border-zinc-200 bg-zinc-50/60 dark:border-zinc-800 dark:bg-zinc-900/30">

            <div class="mx-auto max-w-5xl px-5 py-20 sm:px-6 lg:px-8">

                <flux:card class="overflow-hidden">

                    <div class="grid lg:grid-cols-3">

                        {{-- Profile --}}
                        <div
                            class="flex flex-col items-center justify-center border-b border-zinc-200 p-10 text-center dark:border-zinc-800 lg:border-b-0 lg:border-r">
                            <div class="relative">

                                {{-- Animated gradient glow --}}
                                <div
                                    class="absolute -inset-3 rounded-full bg-linear-to-r from-violet-500 via-fuchsia-500 to-cyan-500 opacity-60 blur-xl animate-gradient">
                                </div>

                                {{-- Animated gradient ring --}}
                                <div class="avatar-ring relative rounded-full p-0.75">
                                    {{-- Avatar --}}
                                    <div
                                        class="size-28 overflow-hidden rounded-full border-4 border-white bg-zinc-100 dark:border-zinc-900 dark:bg-zinc-800">
                                        <img src="{{ asset('storage/profile-full.png') }}" alt="Vika Arya"
                                            class="h-full w-full object-cover">
                                    </div>
                                </div>

                            </div>

                            <flux:heading size="xl" class="mt-6">
                                Vika Arya
                            </flux:heading>

                            <flux:text class="mt-1">
                                Owner & Programmer
                            </flux:text>

                            <div class="mt-4 flex flex-wrap justify-center gap-2">
                                <flux:badge color="indigo">
                                    Web Developer
                                </flux:badge>

                                <flux:badge color="violet">
                                    Creator
                                </flux:badge>
                            </div>
                        </div>

                        {{-- INFORMASI --}}
                        <div class="p-8 lg:col-span-2">

                            <flux:badge color="violet">
                                Tentang Pembuat
                            </flux:badge>

                            <flux:heading class="mt-5">
                                Dibuat dari kebutuhan nyata.
                            </flux:heading>

                            <div class="mt-5 space-y-4 text-sm leading-7 text-zinc-600 dark:text-zinc-400">

                                <p>
                                    <strong class="text-zinc-950 dark:text-white">
                                        Sales WhatsApp Assistant
                                    </strong>
                                    dikembangkan secara mandiri oleh
                                    <strong class="text-zinc-950 dark:text-white">
                                        Vika Arya
                                    </strong>
                                    sebagai alat produktivitas pribadi
                                    untuk membantu aktivitas Sales.
                                </p>

                                <p>
                                    Proyek ini berawal dari kebutuhan sederhana
                                    untuk mengurangi pekerjaan manual ketika
                                    mengelola data customer dan mempersiapkan
                                    komunikasi.
                                </p>

                                <p>
                                    Aplikasi terus dikembangkan berdasarkan
                                    kebutuhan penggunaan, ide baru, serta
                                    masukan yang diterima.
                                </p>

                            </div>

                            {{-- TEKNOLOGI --}}
                            <div class="mt-7">

                                <div class="text-xs font-medium uppercase tracking-wider text-zinc-500">
                                    Teknologi yang Digunakan
                                </div>

                                <div class="mt-3 flex flex-wrap gap-2">

                                    <flux:badge variant="outline">
                                        Laravel 13.32.0
                                    </flux:badge>

                                    <flux:badge variant="outline">
                                        Livewire 4.4.5
                                    </flux:badge>

                                    <flux:badge variant="outline">
                                        Flux UI 2.20.0
                                    </flux:badge>

                                    <flux:badge variant="outline">
                                        Tailwind CSS 4.3.3
                                    </flux:badge>

                                </div>

                            </div>

                            <div class="mt-7">

                                <flux:button href="https://vikaarya07.my.id" wire:navigate variant="ghost"
                                    target="_blank" icon="arrow-right">
                                    Tentang Pembuat
                                </flux:button>

                            </div>

                        </div>

                    </div>

                </flux:card>

            </div>

        </section>

        {{-- PEMBARUAN --}}
        <section id="pembaruan">

            <div class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">

                <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">

                    <div>

                        <flux:badge color="fuchsia">
                            Pembaruan
                        </flux:badge>

                        <h2 class="mt-5 text-3xl font-semibold tracking-tight sm:text-4xl">
                            Aplikasi terus berkembang.
                        </h2>

                        <p class="mt-4 max-w-2xl text-zinc-600 dark:text-zinc-400">
                            Lihat fitur baru, perbaikan, dan perubahan
                            yang ditambahkan ke Sales WhatsApp Assistant.
                        </p>

                    </div>

                    <flux:button href="{{ route('updates') }}" wire:navigate variant="ghost" icon="arrow-right">
                        Lihat Semua Pembaruan
                    </flux:button>

                </div>

                <div class="mt-10 grid gap-5 md:grid-cols-3">

                    <flux:card class="p-6">

                        <flux:badge color="green">
                            Baru
                        </flux:badge>

                        <flux:heading class="mt-5">
                            Template Pesan
                        </flux:heading>

                        <flux:text class="mt-2">
                            Buat dan simpan template pesan untuk
                            digunakan kembali.
                        </flux:text>

                    </flux:card>

                    <flux:card class="p-6">

                        <flux:badge color="indigo">
                            Diperbarui
                        </flux:badge>

                        <flux:heading class="mt-5">
                            Manajemen Customer
                        </flux:heading>

                        <flux:text class="mt-2">
                            Pencarian, filter, dan status customer
                            terus dikembangkan.
                        </flux:text>

                    </flux:card>

                    <flux:card class="p-6">

                        <flux:badge color="amber">
                            Direncanakan
                        </flux:badge>

                        <flux:heading class="mt-5">
                            Fitur Berikutnya
                        </flux:heading>

                        <flux:text class="mt-2">
                            Fitur baru akan terus dikembangkan berdasarkan
                            kebutuhan dan masukan.
                        </flux:text>

                    </flux:card>

                </div>

            </div>

        </section>

        {{-- CTA --}}
        <section class="relative overflow-hidden">

            <div class="absolute inset-0 bg-linear-to-br from-indigo-600 via-violet-600 to-fuchsia-600"></div>

            <div class="absolute inset-0 opacity-20">

                <div class="absolute left-1/4 top-0 size-72 rounded-full bg-white blur-3xl"></div>

                <div class="absolute bottom-0 right-1/4 size-72 rounded-full bg-fuchsia-300 blur-3xl"></div>

            </div>

            <div class="relative mx-auto max-w-3xl px-5 py-20 text-center sm:px-6 lg:px-8 lg:py-24">

                <div
                    class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20 backdrop-blur">

                    <flux:icon name="sparkles" class="size-6 text-white" />

                </div>

                <h2 class="mt-6 text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                    Siap menggunakan Sales WhatsApp Assistant?
                </h2>

                <p class="mx-auto mt-4 max-w-xl text-sm leading-6 text-white/75 sm:text-base">
                    Buat akun dan mulai kelola workflow Sales
                    dengan lebih terorganisir.
                </p>

                @guest

                    <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">

                        <flux:button href="{{ route('register') }}" wire:navigate variant="primary" icon="arrow-right">
                            Buat Akun
                        </flux:button>


                        <a href="{{ route('login') }}" wire:navigate
                            class="inline-flex h-10 items-center justify-center rounded-lg border border-white/25 bg-white/10 px-5 text-sm font-medium text-white backdrop-blur transition hover:bg-white/20">
                            Masuk
                        </a>

                    </div>
                @else
                    <div class="mt-8">

                        <flux:button href="{{ route('dashboard') }}" wire:navigate variant="primary" icon="arrow-right">
                            Buka Dashboard
                        </flux:button>

                    </div>

                @endguest

            </div>

        </section>

    </main>

    {{-- FOOTER --}}
    <footer class="border-t border-zinc-800 bg-zinc-900 text-white">

        <div class="mx-auto max-w-7xl px-5 py-10 sm:px-6 lg:px-8">

            <div class="flex flex-col gap-8 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <div class="flex items-center gap-2">

                        <div class="flex size-8 items-center justify-center rounded-lg bg-zinc-200">

                            <div class="flex size-full items-center justify-center rounded-[7px]">

                                <img src="{{ asset('favicon.svg') }}" alt="" class="size-6">

                            </div>

                        </div>

                        <span
                            class="bg-linear-to-r from-indigo-400 via-violet-400 to-fuchsia-400 bg-clip-text text-sm font-semibold text-transparent">
                            Sales WhatsApp Assistant
                        </span>

                    </div>

                    <p class="mt-2 text-xs text-zinc-400">
                        Dibuat untuk Sales. Dirancang untuk kesederhanaan.
                    </p>

                </div>

                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">

                    <a href="{{ route('about') }}" wire:navigate class="text-zinc-400 transition hover:text-white">
                        Tentang
                    </a>

                    <a href="{{ route('updates') }}" wire:navigate class="text-zinc-400 transition hover:text-white">
                        Pembaruan
                    </a>

                    @guest

                        <a href="{{ route('login') }}" wire:navigate class="text-zinc-400 transition hover:text-white">
                            Masuk
                        </a>

                        <a href="{{ route('register') }}" wire:navigate
                            class="text-zinc-400 transition hover:text-white">
                            Daftar
                        </a>

                    @endguest

                </div>

            </div>

            <div
                class="mt-8 flex flex-col gap-3 border-t border-zinc-800 pt-6 text-xs text-zinc-400 sm:flex-row sm:items-center sm:justify-between">

                <span>
                    © {{ date('Y') }} Vika Arya. Hak cipta dilindungi.
                </span>

                <flux:text size="sm" class="text-xs text-zinc-400">

                    Created with
                    <span class="mx-1 text-red-500">
                        ❤️
                    </span>
                    by

                    <a href="https://vikaarya07.my.id/" target="_blank" rel="noopener noreferrer"
                        class="ml-1 font-medium text-sky-600 transition hover:text-blue-600 dark:text-sky-400 dark:hover:text-blue-400">
                        @vikaarya07
                    </a>

                </flux:text>

            </div>

            <div class="mt-5 text-center text-[11px] leading-5 text-zinc-400">
                Sales WhatsApp Assistant adalah aplikasi pihak ketiga
                yang dikembangkan secara independen dan tidak berafiliasi,
                didukung, atau disponsori oleh Meta Platforms, Inc.
                maupun WhatsApp.
            </div>

        </div>

    </footer>

    @fluxScripts

    @if (session('swal'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const swal = @js(session('swal'));

                if (swal.type === 'toast') {
                    window.sweetAlert.toast(
                        swal.message,
                        swal.icon ?? 'success',
                    );
                }
            });
        </script>
    @endif

</body>

</html>
