<x-layouts::app.app :title="$title ?? 'Sales WhatsApp Assistant | @vikaarya07'">

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

        <div class="mx-auto max-w-7xl px-5 py-20 md:px-8">
            <div class="mx-auto max-w-4xl text-center">

                {{-- Badge --}}
                <flux:badge color="indigo" icon="sparkles">
                    Sales Productivity Application
                </flux:badge>

                {{-- Judul --}}
                <flux:heading level="1" size="2xl"
                    class="mt-7 text-zinc-950 dark:text-white sm:text-6xl lg:text-7xl">
                    Kelola Customer.
                    <span class="block">Persiapkan Komunikasi.</span>
                    <span
                        class="block bg-linear-to-r from-indigo-600 via-violet-600 to-fuchsia-600 bg-clip-text text-transparent">
                        Lebih Sederhana.
                    </span>
                </flux:heading>

                {{-- Deskripsi --}}
                <flux:text
                    class="mx-auto mt-7 max-w-2xl text-base leading-7 text-zinc-600 dark:text-zinc-400 sm:text-lg">
                    <strong class="font-medium text-zinc-950 dark:text-white">
                        Sales WhatsApp Assistant
                    </strong>
                    membantu salesperson mengelola data customer,
                    membuat template pesan, dan mempersiapkan komunikasi
                    melalui WhatsApp dalam satu alur kerja yang lebih terorganisir.
                </flux:text>

                {{-- Tombol --}}
                <div class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    @auth
                        <flux:button
                            href="{{ auth()->user()->role->value === 'admin' ? route('admin.overview') : route('user.dashboard') }}"
                            wire:navigate variant="primary" icon="arrow-right">
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

                <flux:text size="xs" class="mt-5 text-zinc-500">
                    Aplikasi pihak ketiga · Tidak berafiliasi dengan Meta atau WhatsApp
                </flux:text>
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

                                <flux:text size="xs" class="ml-3 text-zinc-400">
                                    Sales WhatsApp Assistant
                                </flux:text>
                            </div>

                            <div class="mt-5 grid gap-4 lg:grid-cols-[210px_1fr]">

                                {{-- Sidebar --}}
                                <div
                                    class="hidden rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-950 lg:block">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="flex size-7 items-center justify-center rounded-lg bg-white dark:bg-zinc-950">
                                            <img src="{{ asset('favicon.svg') }}" class="size-5" alt="">
                                        </div>

                                        <flux:text size="sm" class="font-semibold">
                                            Sales Assistant
                                        </flux:text>
                                    </div>

                                    <div class="mt-6 space-y-1.5">
                                        <div class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs text-zinc-500">
                                            <flux:icon name="home" class="size-4" />
                                            Dashboard
                                        </div>

                                        <div
                                            class="flex items-center gap-2 rounded-lg bg-linear-to-r from-indigo-50 to-violet-50 px-3 py-2 text-xs font-medium text-indigo-700 dark:from-indigo-500/10 dark:to-violet-500/10 dark:text-indigo-300">
                                            <flux:icon name="users" class="size-4" />
                                            Customer
                                        </div>

                                        <div class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs text-zinc-500">
                                            <flux:icon name="document-text" class="size-4" />
                                            Template Pesan
                                        </div>
                                    </div>
                                </div>

                                {{-- Dashboard --}}
                                <div>
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <flux:heading size="lg">Customer</flux:heading>
                                            <flux:text size="xs" class="mt-1 text-zinc-500">
                                                Kelola data customer Sales.
                                            </flux:text>
                                        </div>

                                        <flux:badge color="green" class="hidden sm:inline-flex">
                                            Aktif
                                        </flux:badge>
                                    </div>

                                    {{-- Statistik --}}
                                    <div class="mt-5 grid gap-3 sm:grid-cols-3">
                                        <flux:card class="p-4">
                                            <flux:text size="xs" class="text-zinc-500">Customer</flux:text>
                                            <flux:heading size="lg" class="mt-2">Kelola</flux:heading>
                                        </flux:card>

                                        <flux:card class="p-4">
                                            <flux:text size="xs" class="text-zinc-500">Template</flux:text>
                                            <flux:heading size="lg" class="mt-2">Pesan</flux:heading>
                                        </flux:card>

                                        <flux:card class="p-4">
                                            <flux:text size="xs" class="text-zinc-500">Komunikasi</flux:text>
                                            <flux:heading size="lg" class="mt-2">WhatsApp</flux:heading>
                                        </flux:card>
                                    </div>

                                    {{-- Customer --}}
                                    <flux:card class="mt-4 p-4">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex min-w-0 items-center gap-3">
                                                <flux:avatar src="{{ asset('storage/profile-full.png') }}" />

                                                <div class="min-w-0">
                                                    <flux:text size="sm" class="font-medium">
                                                        Vika Arya
                                                    </flux:text>
                                                    <flux:text size="xs" class="mt-1 text-zinc-500">
                                                        +62 857-9992-8828
                                                    </flux:text>
                                                </div>
                                            </div>

                                            <flux:badge color="indigo">Baru</flux:badge>
                                        </div>
                                    </flux:card>
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
                    <flux:badge color="indigo">Tentang Aplikasi</flux:badge>

                    <flux:heading level="2" size="xl" class="mt-5 tracking-tight sm:text-4xl">
                        Dibuat untuk membantu workflow Sales.
                    </flux:heading>

                    <div class="mt-5 space-y-4">
                        <flux:text class="leading-7 text-zinc-600 dark:text-zinc-400">
                            Sales WhatsApp Assistant adalah aplikasi web yang dirancang
                            untuk membantu salesperson mengelola data customer dan
                            mempersiapkan komunikasi secara lebih terorganisir.
                        </flux:text>

                        <flux:text class="leading-7 text-zinc-600 dark:text-zinc-400">
                            Data customer dapat dimasukkan dengan cara copy-paste,
                            kemudian dicari, difilter, dan dikelola berdasarkan
                            status komunikasi.
                        </flux:text>

                        <flux:text class="leading-7 text-zinc-600 dark:text-zinc-400">
                            Pengguna juga dapat membuat template pesan dan menggunakan
                            informasi customer sebagai bagian dari pesan yang dipersiapkan.
                        </flux:text>
                    </div>

                    <div class="mt-7">
                        <flux:button href="{{ route('about') }}" wire:navigate variant="ghost" icon="arrow-right">
                            Selengkapnya
                        </flux:button>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    @php
                        $tentangFitur = [
                            [
                                'icon' => 'users',
                                'judul' => 'Kelola Customer',
                                'deskripsi' => 'Kelola data customer dalam satu tempat.',
                                'gradient' => 'from-indigo-500 to-violet-600',
                                'shadow' => 'shadow-indigo-500/20',
                            ],
                            [
                                'icon' => 'document-text',
                                'judul' => 'Template Pesan',
                                'deskripsi' => 'Simpan template pesan untuk digunakan kembali.',
                                'gradient' => 'from-violet-500 to-fuchsia-600',
                                'shadow' => 'shadow-violet-500/20',
                            ],
                            [
                                'icon' => 'magnifying-glass',
                                'judul' => 'Pencarian',
                                'deskripsi' => 'Temukan customer dengan lebih cepat.',
                                'gradient' => 'from-indigo-500 to-violet-600',
                                'shadow' => 'shadow-indigo-500/20',
                            ],
                            [
                                'icon' => 'chat-bubble-left-right',
                                'judul' => 'Komunikasi',
                                'deskripsi' => 'Persiapkan pesan sebelum melanjutkan ke WhatsApp.',
                                'gradient' => 'from-fuchsia-500 to-violet-600',
                                'shadow' => 'shadow-fuchsia-500/20',
                            ],
                        ];
                    @endphp

                    @foreach ($tentangFitur as $item)
                        <flux:card
                            class="p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl {{ $item['shadow'] }}">
                            <div
                                class="flex size-11 items-center justify-center rounded-xl bg-linear-to-br {{ $item['gradient'] }} text-white shadow-lg">
                                <flux:icon :name="$item['icon']" class="size-5" />
                            </div>

                            <flux:heading size="lg" class="mt-5">
                                {{ $item['judul'] }}
                            </flux:heading>

                            <flux:text class="mt-2">
                                {{ $item['deskripsi'] }}
                            </flux:text>
                        </flux:card>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- FITUR --}}
    <section id="fitur" class="scroll-mt-20">
        <div class="mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <flux:badge color="violet">Fitur</flux:badge>

                <flux:heading level="2" size="xl" class="mt-5 tracking-tight sm:text-4xl">
                    Semua yang dibutuhkan untuk workflow Sales.
                </flux:heading>

                <flux:text class="mt-4 text-zinc-600 dark:text-zinc-400">
                    Dirancang untuk membantu pekerjaan Sales menjadi lebih terorganisir dan praktis.
                </flux:text>
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
                        'deskripsi' => 'Persiapkan pesan menggunakan data customer sebelum melanjutkan ke WhatsApp.',
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

                        <flux:heading size="lg" class="mt-5">
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
                <flux:badge color="fuchsia">Cara Kerja</flux:badge>

                <flux:heading level="2" size="xl" class="mt-5 tracking-tight sm:text-4xl">
                    Dari data customer sampai siap berkomunikasi.
                </flux:heading>

                <flux:text class="mt-4 text-zinc-600 dark:text-zinc-400">
                    Alur kerja dibuat sederhana agar pekerjaan berulang menjadi lebih praktis.
                </flux:text>
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

                        <flux:heading size="lg" class="mt-5">
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
                <div class="flex items-start gap-5">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400">
                        <flux:icon name="exclamation-triangle" class="size-6" />
                    </div>

                    <div class="min-w-0 flex-1">
                        <flux:badge color="amber">Penting</flux:badge>

                        <flux:heading size="lg" class="mt-4">
                            Peringatan Penggunaan WhatsApp
                        </flux:heading>

                        <div class="mt-5 space-y-4">
                            <flux:text class="leading-7 text-zinc-600 dark:text-zinc-400">
                                <strong class="text-zinc-950 dark:text-white">
                                    Sales WhatsApp Assistant bukan merupakan produk, layanan, aplikasi,
                                    atau layanan resmi dari Meta maupun WhatsApp.
                                </strong>
                            </flux:text>

                            <flux:text class="leading-7 text-zinc-600 dark:text-zinc-400">
                                Aplikasi ini merupakan <strong class="text-zinc-950 dark:text-white">aplikasi pihak
                                    ketiga</strong>
                                yang dikembangkan secara independen untuk membantu workflow pengguna.
                            </flux:text>

                            <flux:text class="leading-7 text-zinc-600 dark:text-zinc-400">
                                Penggunaan WhatsApp tetap mengikuti ketentuan dan kebijakan layanan WhatsApp.
                            </flux:text>

                            <flux:text class="leading-7 text-zinc-600 dark:text-zinc-400">
                                Aktivitas seperti spam, penyalahgunaan layanan, atau aktivitas yang melanggar
                                kebijakan dapat menyebabkan pesan dibatasi atau akun mendapatkan pembatasan.
                            </flux:text>

                            <div
                                class="rounded-xl border border-amber-200 bg-white/60 p-4 dark:border-amber-900/50 dark:bg-amber-950/20">
                                <flux:text class="font-medium text-zinc-900 dark:text-white">
                                    <flux:icon name="exclamation-triangle"
                                        class="mr-1 inline size-4 text-amber-600" />
                                    Tidak ada jaminan bahwa akun WhatsApp akan selalu bebas dari pembatasan atau
                                    pemblokiran.
                                </flux:text>
                            </div>

                            <flux:text class="leading-7 text-zinc-600 dark:text-zinc-400">
                                Pengguna bertanggung jawab atas cara aplikasi digunakan, isi pesan, penerima komunikasi,
                                serta kepatuhan terhadap kebijakan layanan yang digunakan.
                            </flux:text>
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
                <flux:badge color="indigo">Penggunaan Bertanggung Jawab</flux:badge>

                <flux:heading level="2" size="xl" class="mt-5 tracking-tight sm:text-4xl">
                    Gunakan aplikasi secara bertanggung jawab.
                </flux:heading>

                <flux:text class="mt-4 text-zinc-600 dark:text-zinc-400">
                    Beberapa hal yang perlu diperhatikan ketika menggunakan aplikasi.
                </flux:text>
            </div>

            <div class="mt-10 grid gap-5 md:grid-cols-3">
                <flux:card class="p-7">
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                        <flux:icon name="shield-check" class="size-6" />
                    </div>

                    <flux:heading size="lg" class="mt-5">Hindari Spam</flux:heading>
                    <flux:text class="mt-2">
                        Gunakan komunikasi secara wajar dan hindari aktivitas yang dapat dianggap sebagai spam.
                    </flux:text>
                </flux:card>

                <flux:card class="p-7">
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                        <flux:icon name="lock-closed" class="size-6" />
                    </div>

                    <flux:heading size="lg" class="mt-5">Jaga Data Customer</flux:heading>
                    <flux:text class="mt-2">
                        Pastikan data customer dikelola secara bertanggung jawab dan sesuai kebutuhan.
                    </flux:text>
                </flux:card>

                <flux:card class="p-7">
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-fuchsia-50 text-fuchsia-600 dark:bg-fuchsia-500/10 dark:text-fuchsia-400">
                        <flux:icon name="document-check" class="size-6" />
                    </div>

                    <flux:heading size="lg" class="mt-5">Pahami Kebijakan</flux:heading>
                    <flux:text class="mt-2">
                        Pengguna tetap bertanggung jawab memahami kebijakan layanan yang digunakan.
                    </flux:text>
                </flux:card>
            </div>
        </div>
    </section>

    {{-- CREATOR --}}
    <section id="tentang" class="border-y border-zinc-200 bg-zinc-50/60 dark:border-zinc-800 dark:bg-zinc-900/30">
        <div class="mx-auto max-w-5xl px-5 py-20 sm:px-6 lg:px-8">
            <flux:card class="overflow-hidden p-0">
                <div class="grid lg:grid-cols-3">

                    {{-- Profile --}}
                    <div
                        class="flex flex-col items-center justify-center border-b border-zinc-200 p-10 text-center dark:border-zinc-800 lg:border-b-0 lg:border-r">
                        <div class="relative">
                            <div
                                class="absolute -inset-3 animate-gradient rounded-full bg-linear-to-r from-violet-500 via-fuchsia-500 to-cyan-500 opacity-60 blur-xl">
                            </div>

                            <div class="avatar-ring relative rounded-full p-0.75">
                                <div
                                    class="size-28 overflow-hidden rounded-full border-4 border-white bg-zinc-100 dark:border-zinc-900 dark:bg-zinc-800">
                                    <img src="{{ asset('storage/profile-full.png') }}" alt="Vika Arya"
                                        class="size-full object-cover">
                                </div>
                            </div>
                        </div>

                        <flux:heading size="xl" class="mt-6">Vika Arya</flux:heading>
                        <flux:text class="mt-1">Owner &amp; Programmer</flux:text>

                        <div class="mt-4 flex flex-wrap justify-center gap-2">
                            <flux:badge color="indigo">Web Developer</flux:badge>
                            <flux:badge color="violet">Creator</flux:badge>
                        </div>
                    </div>

                    {{-- Informasi --}}
                    <div class="p-8 lg:col-span-2">
                        <flux:badge color="violet">Tentang Pembuat</flux:badge>

                        <flux:heading level="2" size="xl" class="mt-5">
                            Dibuat dari kebutuhan nyata.
                        </flux:heading>

                        <div class="mt-5 space-y-4">
                            <flux:text class="text-sm leading-7 text-zinc-600 dark:text-zinc-400">
                                <strong class="text-zinc-950 dark:text-white">Sales WhatsApp Assistant</strong>
                                dikembangkan secara mandiri oleh
                                <strong class="text-zinc-950 dark:text-white">Vika Arya</strong>
                                sebagai alat produktivitas pribadi untuk membantu aktivitas Sales.
                            </flux:text>

                            <flux:text class="text-sm leading-7 text-zinc-600 dark:text-zinc-400">
                                Proyek ini berawal dari kebutuhan sederhana untuk mengurangi pekerjaan manual
                                ketika mengelola data customer dan mempersiapkan komunikasi.
                            </flux:text>

                            <flux:text class="text-sm leading-7 text-zinc-600 dark:text-zinc-400">
                                Aplikasi terus dikembangkan berdasarkan kebutuhan penggunaan, ide baru,
                                serta masukan yang diterima.
                            </flux:text>
                        </div>

                        {{-- Teknologi --}}
                        <div class="mt-7">
                            <flux:text size="xs" class="font-medium uppercase tracking-wider text-zinc-500">
                                Teknologi yang Digunakan
                            </flux:text>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <flux:badge variant="outline">Laravel 13.32.0</flux:badge>
                                <flux:badge variant="outline">Livewire 4.4.5</flux:badge>
                                <flux:badge variant="outline">Flux UI 2.20.0</flux:badge>
                                <flux:badge variant="outline">Tailwind CSS 4.3.3</flux:badge>
                            </div>
                        </div>

                        <div class="mt-7">
                            <flux:button href="https://vikaarya07.my.id" variant="ghost" target="_blank"
                                rel="noopener noreferrer" icon="arrow-up-right">
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
                    <flux:badge color="fuchsia">Pembaruan</flux:badge>

                    <flux:heading level="2" size="xl" class="mt-5 tracking-tight sm:text-4xl">
                        Aplikasi terus berkembang.
                    </flux:heading>

                    <flux:text class="mt-4 max-w-2xl text-zinc-600 dark:text-zinc-400">
                        Lihat fitur baru, perbaikan, dan perubahan yang ditambahkan ke Sales WhatsApp Assistant.
                    </flux:text>
                </div>

                <flux:button href="{{ route('updates') }}" wire:navigate variant="ghost" icon="arrow-right">
                    Lihat Semua Pembaruan
                </flux:button>
            </div>

            <div class="mt-10 grid gap-5 md:grid-cols-3">
                <flux:card class="p-6">
                    <flux:badge color="green">Baru</flux:badge>
                    <flux:heading size="lg" class="mt-5">Template Pesan</flux:heading>
                    <flux:text class="mt-2">
                        Buat dan simpan template pesan untuk digunakan kembali.
                    </flux:text>
                </flux:card>

                <flux:card class="p-6">
                    <flux:badge color="indigo">Diperbarui</flux:badge>
                    <flux:heading size="lg" class="mt-5">Manajemen Customer</flux:heading>
                    <flux:text class="mt-2">
                        Pencarian, filter, dan status customer terus dikembangkan.
                    </flux:text>
                </flux:card>

                <flux:card class="p-6">
                    <flux:badge color="amber">Direncanakan</flux:badge>
                    <flux:heading size="lg" class="mt-5">Fitur Berikutnya</flux:heading>
                    <flux:text class="mt-2">
                        Fitur baru akan terus dikembangkan berdasarkan kebutuhan dan masukan.
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

            <flux:heading level="2" size="xl" class="mt-6 text-white sm:text-4xl">
                Siap menggunakan Sales WhatsApp Assistant?
            </flux:heading>

            <flux:text class="mx-auto mt-4 max-w-xl text-sm leading-6 text-white/75 sm:text-base">
                Buat akun dan mulai kelola workflow Sales dengan lebih terorganisir.
            </flux:text>

            @guest
                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <flux:button href="{{ route('register') }}" wire:navigate variant="primary" icon="arrow-right">
                        Buat Akun
                    </flux:button>

                    <flux:button href="{{ route('login') }}" wire:navigate variant="ghost"
                        class="border border-white/25 text-white hover:bg-white/20">
                        Masuk
                    </flux:button>
                </div>
            @else
                <div class="mt-8">
                    <flux:button
                        href="{{ auth()->user()->role->value === 'admin' ? route('admin.overview') : route('user.dashboard') }}"
                        wire:navigate variant="primary" icon="arrow-right">
                        Buka Dashboard
                    </flux:button>
                </div>
            @endguest
        </div>
    </section>

</x-layouts::app.app>
