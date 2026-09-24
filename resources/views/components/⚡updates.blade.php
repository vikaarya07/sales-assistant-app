<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="min-h-screen overflow-hidden bg-zinc-50 dark:bg-zinc-950">

    {{-- HERO --}}
    <section class="relative isolate overflow-hidden">

        {{-- Background glow --}}
        <div class="absolute inset-0 -z-10">
            <div
                class="absolute left-1/2 top-0 h-112.5 w-200 -translate-x-1/2 rounded-full bg-indigo-500/10 blur-3xl dark:bg-indigo-500/15">
            </div>
            <div class="absolute -left-40 top-40 h-72 w-72 rounded-full bg-violet-500/10 blur-3xl"></div>
            <div class="absolute -right-40 top-56 h-72 w-72 rounded-full bg-cyan-500/10 blur-3xl"></div>
        </div>

        <div class="mx-auto max-w-7xl px-6 pb-16 pt-16 lg:px-8 lg:pb-20 lg:pt-24">

            <div class="mx-auto max-w-4xl text-center">

                <flux:badge color="indigo" icon="arrow-path"
                    class="border border-indigo-200 bg-indigo-50 px-4 py-2 text-indigo-700 dark:border-indigo-500/20 dark:bg-indigo-500/10 dark:text-indigo-300">
                    Product Updates
                </flux:badge>

                <h1 class="mt-7 text-4xl font-bold tracking-tight text-zinc-950 sm:text-6xl dark:text-white">
                    Updates &
                    <span
                        class="bg-linear-to-r from-indigo-600 via-violet-600 to-fuchsia-600 bg-clip-text text-transparent">
                        Changelog
                    </span>
                </h1>

                <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-zinc-600 dark:text-zinc-400">
                    Ikuti perkembangan Sales WhatsApp Assistant,
                    mulai dari fitur baru, peningkatan sistem, perbaikan,
                    hingga pengembangan yang sedang direncanakan.
                </p>

            </div>


            {{-- REQUEST UPDATE --}}
            <div class="mx-auto mt-12 max-w-4xl">

                <flux:card
                    class="group relative overflow-hidden border-indigo-200/70 bg-white shadow-lg shadow-indigo-500/5 transition duration-300 hover:shadow-xl hover:shadow-indigo-500/10 dark:border-indigo-500/20 dark:bg-zinc-900">

                    {{-- Glow --}}
                    <div
                        class="absolute -right-20 -top-20 size-56 rounded-full bg-indigo-500/10 blur-3xl transition duration-500 group-hover:bg-indigo-500/20">
                    </div>

                    <div class="relative p-6 sm:p-8">

                        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">

                            <div class="flex gap-4">

                                <div
                                    class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-500 to-violet-600 text-white shadow-lg shadow-indigo-500/20">
                                    <flux:icon name="light-bulb" class="size-6" />
                                </div>

                                <div>
                                    <flux:heading size="lg">
                                        Punya ide atau request fitur?
                                    </flux:heading>

                                    <flux:text class="mt-1 max-w-xl">
                                        Beri tahu fitur apa yang menurut kamu akan
                                        membuat Sales WhatsApp Assistant menjadi
                                        lebih berguna.
                                    </flux:text>
                                </div>

                            </div>

                            <flux:button variant="primary" icon="plus"
                                class="shrink-0 shadow-lg shadow-indigo-500/20">
                                Request Update
                            </flux:button>

                        </div>

                    </div>

                </flux:card>

            </div>

        </div>

    </section>


    {{-- CONTENT --}}
    <main class="mx-auto max-w-5xl px-6 pb-20 lg:px-8">


        {{-- LATEST UPDATE --}}
        <section>

            <div class="mb-7">

                <flux:badge color="green" icon="sparkles">
                    What's New
                </flux:badge>

                <flux:heading size="xl" class="mt-3">
                    Latest Updates
                </flux:heading>

                <flux:text class="mt-1">
                    Perubahan dan fitur terbaru.
                </flux:text>

            </div>


            <div class="space-y-5">

                {{-- UPDATE 1 --}}
                <flux:card
                    class="group relative overflow-hidden border-emerald-200/70 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-500/5 sm:p-8 dark:border-emerald-500/20 dark:bg-zinc-900">

                    {{-- Accent --}}
                    <div
                        class="absolute left-0 top-0 h-full w-1 bg-linear-to-b from-emerald-400 via-green-500 to-teal-500">
                    </div>

                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">

                        <div>

                            <div class="flex flex-wrap items-center gap-2">

                                <flux:badge color="green" icon="sparkles">
                                    New
                                </flux:badge>

                                <flux:badge variant="outline">
                                    v1.2.0
                                </flux:badge>

                            </div>

                            <flux:heading size="lg" class="mt-4">
                                Message Template & WhatsApp Composer
                            </flux:heading>

                            <flux:text class="mt-1">
                                24 September 2026
                            </flux:text>

                        </div>

                        <div
                            class="hidden size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 sm:flex dark:bg-emerald-500/10 dark:text-emerald-400">
                            <flux:icon name="chat-bubble-left-right" class="size-5" />
                        </div>

                    </div>


                    <div class="mt-7 space-y-3">

                        <div class="flex gap-3">
                            <div
                                class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <flux:icon name="check" class="size-3.5" />
                            </div>

                            <flux:text>
                                Menambahkan fitur pembuatan dan pengelolaan
                                message template.
                            </flux:text>
                        </div>

                        <div class="flex gap-3">
                            <div
                                class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <flux:icon name="check" class="size-3.5" />
                            </div>

                            <flux:text>
                                Menambahkan variable customer seperti
                                <code
                                    class="rounded-md bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-indigo-600 dark:bg-zinc-800 dark:text-indigo-400">
                                    @{{ nama }}
                                </code>
                                dan
                                <code
                                    class="rounded-md bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-indigo-600 dark:bg-zinc-800 dark:text-indigo-400">
                                    @{{ nomor_kontrak }}
                                </code>.
                            </flux:text>
                        </div>

                        <div class="flex gap-3">
                            <div
                                class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <flux:icon name="check" class="size-3.5" />
                            </div>

                            <flux:text>
                                Menambahkan preview pesan sebelum
                                digunakan untuk komunikasi WhatsApp.
                            </flux:text>
                        </div>

                    </div>

                </flux:card>


                {{-- UPDATE 2 --}}
                <flux:card
                    class="group relative overflow-hidden border-blue-200/70 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-500/5 sm:p-8 dark:border-blue-500/20 dark:bg-zinc-900">

                    <div class="absolute left-0 top-0 h-full w-1 bg-linear-to-b from-blue-400 to-cyan-500"></div>

                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">

                        <div>

                            <div class="flex flex-wrap items-center gap-2">

                                <flux:badge color="blue" icon="arrow-path">
                                    Improved
                                </flux:badge>

                                <flux:badge variant="outline">
                                    v1.1.0
                                </flux:badge>

                            </div>

                            <flux:heading size="lg" class="mt-4">
                                Customer Management
                            </flux:heading>

                            <flux:text class="mt-1">
                                22 September 2026
                            </flux:text>

                        </div>

                        <div
                            class="hidden size-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 sm:flex dark:bg-blue-500/10 dark:text-blue-400">
                            <flux:icon name="users" class="size-5" />
                        </div>

                    </div>


                    <div class="mt-7 space-y-3">

                        <div class="flex gap-3">
                            <div
                                class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                                <flux:icon name="check" class="size-3.5" />
                            </div>

                            <flux:text>
                                Menambahkan pencarian customer berdasarkan
                                nama, nomor telepon, kontrak, dan cabang.
                            </flux:text>
                        </div>

                        <div class="flex gap-3">
                            <div
                                class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                                <flux:icon name="check" class="size-3.5" />
                            </div>

                            <flux:text>
                                Menambahkan filter berdasarkan status customer.
                            </flux:text>
                        </div>

                        <div class="flex gap-3">
                            <div
                                class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                                <flux:icon name="check" class="size-3.5" />
                            </div>

                            <flux:text>
                                Meningkatkan tampilan tabel customer
                                agar lebih mudah digunakan.
                            </flux:text>
                        </div>

                    </div>

                </flux:card>


                {{-- UPDATE 3 --}}
                <flux:card
                    class="group relative overflow-hidden border-violet-200/70 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-violet-500/5 sm:p-8 dark:border-violet-500/20 dark:bg-zinc-900">

                    <div class="absolute left-0 top-0 h-full w-1 bg-linear-to-b from-violet-400 to-fuchsia-500"></div>

                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">

                        <div>

                            <div class="flex flex-wrap items-center gap-2">

                                <flux:badge color="violet" icon="rocket-launch">
                                    New
                                </flux:badge>

                                <flux:badge variant="outline">
                                    v1.0.0
                                </flux:badge>

                            </div>

                            <flux:heading size="lg" class="mt-4">
                                Customer Import
                            </flux:heading>

                            <flux:text class="mt-1">
                                20 September 2026
                            </flux:text>

                        </div>

                        <div
                            class="hidden size-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600 sm:flex dark:bg-violet-500/10 dark:text-violet-400">
                            <flux:icon name="arrow-up-tray" class="size-5" />
                        </div>

                    </div>


                    <div class="mt-7 space-y-3">

                        <div class="flex gap-3">
                            <div
                                class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-violet-100 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                                <flux:icon name="check" class="size-3.5" />
                            </div>

                            <flux:text>
                                Initial release Sales WhatsApp Assistant.
                            </flux:text>
                        </div>

                        <div class="flex gap-3">
                            <div
                                class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-violet-100 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                                <flux:icon name="check" class="size-3.5" />
                            </div>

                            <flux:text>
                                Menambahkan import customer menggunakan
                                metode copy-paste.
                            </flux:text>
                        </div>

                        <div class="flex gap-3">
                            <div
                                class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-violet-100 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                                <flux:icon name="check" class="size-3.5" />
                            </div>

                            <flux:text>
                                Menambahkan normalisasi nomor WhatsApp.
                            </flux:text>
                        </div>

                        <div class="flex gap-3">
                            <div
                                class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-violet-100 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                                <flux:icon name="check" class="size-3.5" />
                            </div>

                            <flux:text>
                                Menambahkan validasi duplicate customer.
                            </flux:text>
                        </div>

                    </div>

                </flux:card>

            </div>

        </section>


        {{-- CHANGELOG --}}
        <section class="mt-20">

            <div class="mb-8">

                <flux:badge color="violet" icon="clock">
                    Version History
                </flux:badge>

                <flux:heading size="xl" class="mt-3">
                    Changelog
                </flux:heading>

                <flux:text class="mt-1">
                    Ringkasan perkembangan aplikasi dari waktu ke waktu.
                </flux:text>

            </div>


            <div class="relative ml-2 space-y-10 border-l-2 border-zinc-200 dark:border-zinc-800">

                {{-- VERSION 1.2 --}}
                <div class="relative pl-8">

                    {{-- Timeline dot --}}
                    <div
                        class="absolute -left-2.25 top-1 flex size-4 items-center justify-center rounded-full bg-white ring-4 ring-emerald-100 dark:bg-zinc-950 dark:ring-emerald-500/10">
                        <div class="size-2 rounded-full bg-emerald-500"></div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">

                        <flux:heading size="lg">
                            Version 1.2.0
                        </flux:heading>

                        <flux:badge color="green">
                            Latest
                        </flux:badge>

                    </div>

                    <flux:text class="mt-1">
                        September 2026
                    </flux:text>

                    <div class="mt-4 flex flex-wrap gap-2">

                        <flux:badge variant="outline">
                            Message Template
                        </flux:badge>

                        <flux:badge variant="outline">
                            WhatsApp Composer
                        </flux:badge>

                        <flux:badge variant="outline">
                            Customer Variable
                        </flux:badge>

                        <flux:badge variant="outline">
                            Message Preview
                        </flux:badge>

                    </div>

                </div>


                {{-- VERSION 1.1 --}}
                <div class="relative pl-8">

                    <div
                        class="absolute -left-2.25 top-1 flex size-4 items-center justify-center rounded-full bg-white ring-4 ring-blue-100 dark:bg-zinc-950 dark:ring-blue-500/10">
                        <div class="size-2 rounded-full bg-blue-500"></div>
                    </div>

                    <flux:heading size="lg">
                        Version 1.1.0
                    </flux:heading>

                    <flux:text class="mt-1">
                        September 2026
                    </flux:text>

                    <div class="mt-4 flex flex-wrap gap-2">

                        <flux:badge variant="outline">
                            Customer Search
                        </flux:badge>

                        <flux:badge variant="outline">
                            Customer Filter
                        </flux:badge>

                        <flux:badge variant="outline">
                            Customer Status
                        </flux:badge>

                        <flux:badge variant="outline">
                            Improved Table
                        </flux:badge>

                    </div>

                </div>


                {{-- VERSION 1.0 --}}
                <div class="relative pl-8">

                    <div
                        class="absolute -left-2.25 top-1 flex size-4 items-center justify-center rounded-full bg-white ring-4 ring-violet-100 dark:bg-zinc-950 dark:ring-violet-500/10">
                        <div class="size-2 rounded-full bg-violet-500"></div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">

                        <flux:heading size="lg">
                            Version 1.0.0
                        </flux:heading>

                        <flux:badge variant="outline">
                            Initial Release
                        </flux:badge>

                    </div>

                    <flux:text class="mt-1">
                        September 2026
                    </flux:text>

                    <div class="mt-4 flex flex-wrap gap-2">

                        <flux:badge variant="outline">
                            Customer Management
                        </flux:badge>

                        <flux:badge variant="outline">
                            Copy-Paste Import
                        </flux:badge>

                        <flux:badge variant="outline">
                            Phone Normalization
                        </flux:badge>

                        <flux:badge variant="outline">
                            Duplicate Detection
                        </flux:badge>

                    </div>

                </div>

            </div>

        </section>


        {{-- UPCOMING --}}
        <section class="mt-20">

            <flux:card
                class="relative overflow-hidden border-amber-200/70 bg-white p-6 shadow-sm sm:p-8 dark:border-amber-500/20 dark:bg-zinc-900">

                {{-- Background glow --}}
                <div class="absolute -right-20 -top-20 size-56 rounded-full bg-amber-500/10 blur-3xl"></div>

                <div class="relative">

                    <div class="flex gap-4">

                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-amber-400 to-orange-500 text-white shadow-lg shadow-amber-500/20">
                            <flux:icon name="clock" class="size-6" />
                        </div>

                        <div>

                            <flux:badge color="amber">
                                Coming Soon
                            </flux:badge>

                            <flux:heading size="lg" class="mt-3">
                                Planned Updates
                            </flux:heading>

                            <flux:text class="mt-1">
                                Beberapa pengembangan yang sedang
                                dipertimbangkan untuk versi berikutnya.
                            </flux:text>

                        </div>

                    </div>


                    <div class="mt-7 grid gap-3 sm:grid-cols-2">

                        <div
                            class="group rounded-xl border border-zinc-200 bg-zinc-50 p-4 transition duration-300 hover:border-amber-200 hover:bg-amber-50/50 dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-amber-500/30">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex size-9 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                                    <flux:icon name="chart-bar" class="size-4" />
                                </div>

                                <flux:text class="font-medium">
                                    Dashboard Statistics
                                </flux:text>

                            </div>

                        </div>


                        <div
                            class="group rounded-xl border border-zinc-200 bg-zinc-50 p-4 transition duration-300 hover:border-amber-200 hover:bg-amber-50/50 dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-amber-500/30">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex size-9 items-center justify-center rounded-lg bg-violet-100 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                                    <flux:icon name="clock" class="size-4" />
                                </div>

                                <flux:text class="font-medium">
                                    Activity History
                                </flux:text>

                            </div>

                        </div>


                        <div
                            class="group rounded-xl border border-zinc-200 bg-zinc-50 p-4 transition duration-300 hover:border-amber-200 hover:bg-amber-50/50 dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-amber-500/30">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex size-9 items-center justify-center rounded-lg bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                                    <flux:icon name="bell" class="size-4" />
                                </div>

                                <flux:text class="font-medium">
                                    Follow-up Reminder
                                </flux:text>

                            </div>

                        </div>


                        <div
                            class="group rounded-xl border border-zinc-200 bg-zinc-50 p-4 transition duration-300 hover:border-amber-200 hover:bg-amber-50/50 dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-amber-500/30">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex size-9 items-center justify-center rounded-lg bg-cyan-100 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400">
                                    <flux:icon name="variable" class="size-4" />
                                </div>

                                <flux:text class="font-medium">
                                    More Customer Variables
                                </flux:text>

                            </div>

                        </div>

                    </div>

                </div>

            </flux:card>

        </section>


        {{-- FOOTER --}}
        <footer class="mt-20 border-t border-zinc-200 py-8 text-center dark:border-zinc-800">

            <flux:text>
                <span class="font-medium text-zinc-900 dark:text-white">
                    Sales WhatsApp Assistant
                </span>

                <span class="mx-2">
                    ·
                </span>

                © {{ date('Y') }} Vika Arya
            </flux:text>

            <flux:text size="sm" class="mt-1">

                Created with
                <span class="mx-1 text-red-500">
                    ♥
                </span>
                by

                <a href="https://vikaarya07.my.id/" target="_blank" rel="noopener noreferrer"
                    class="ml-1 font-medium text-indigo-600 transition hover:text-violet-600 dark:text-indigo-400 dark:hover:text-violet-400">
                    @vikaarya07
                </a>

            </flux:text>

        </footer>

    </main>

</div>
