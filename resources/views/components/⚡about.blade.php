<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="min-h-screen overflow-hidden bg-zinc-50 dark:bg-zinc-950">

    {{-- HERO --}}
    <section class="relative isolate overflow-hidden">

        {{-- Background decoration --}}
        <div class="absolute inset-0 -z-10">
            <div
                class="absolute left-1/2 top-0 h-150 w-200 -translate-x-1/2 rounded-full bg-indigo-500/10 blur-3xl dark:bg-indigo-500/15">
            </div>
            <div class="absolute -left-32 top-32 h-72 w-72 rounded-full bg-violet-500/10 blur-3xl"></div>
            <div class="absolute -right-32 top-64 h-72 w-72 rounded-full bg-sky-500/10 blur-3xl"></div>
        </div>

        <div class="mx-auto max-w-7xl px-6 pb-20 pt-16 lg:px-8 lg:pb-28 lg:pt-24">

            <div class="mx-auto max-w-4xl text-center">

                {{-- Badge --}}
                <div class="flex justify-center">
                    <flux:badge color="violet" icon="computer-desktop">
                        About This Application
                    </flux:badge>
                </div>

                {{-- Heading --}}
                <h1
                    class="mt-7 text-4xl font-bold tracking-tight text-zinc-950 sm:text-6xl lg:text-7xl dark:text-white">
                    Sales
                    <span
                        class="bg-linear-to-r from-indigo-600 via-violet-600 to-fuchsia-600 bg-clip-text text-transparent">
                        WhatsApp
                    </span>
                    Assistant
                </h1>

                <p class="mx-auto mt-7 max-w-2xl text-lg leading-8 text-zinc-600 dark:text-zinc-400">
                    Aplikasi sederhana untuk membantu salesperson mengelola customer,
                    mempercepat komunikasi, dan membuat workflow sales menjadi
                    lebih terorganisir.
                </p>

                {{-- Tech badges --}}
                <div class="mt-8 flex flex-wrap items-center justify-center gap-2">
                    <flux:badge variant="outline">Laravel 13</flux:badge>
                    <flux:badge variant="outline">Livewire 4</flux:badge>
                    <flux:badge variant="outline">Flux UI 2</flux:badge>
                    <flux:badge variant="outline">Tailwind CSS 4</flux:badge>
                </div>

                {{-- Mini stats --}}
                <div
                    class="mx-auto mt-12 grid max-w-2xl grid-cols-3 overflow-hidden rounded-2xl border border-zinc-200 bg-white/70 shadow-sm backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/70">

                    <div class="border-r border-zinc-200 px-4 py-5 dark:border-zinc-800">
                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                            01
                        </div>
                        <div class="mt-1 text-xs text-zinc-500">
                            Centralized Tool
                        </div>
                    </div>

                    <div class="border-r border-zinc-200 px-4 py-5 dark:border-zinc-800">
                        <div class="text-2xl font-bold text-violet-600 dark:text-violet-400">
                            06+
                        </div>
                        <div class="mt-1 text-xs text-zinc-500">
                            Core Features
                        </div>
                    </div>

                    <div class="px-4 py-5">
                        <div class="text-2xl font-bold text-fuchsia-600 dark:text-fuchsia-400">
                            100%
                        </div>
                        <div class="mt-1 text-xs text-zinc-500">
                            Productivity Focus
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    {{-- ABOUT --}}
    <section class="mx-auto max-w-7xl px-6 py-10 lg:px-8 lg:py-16">

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Main description --}}
            <flux:card
                class="group relative overflow-hidden border-zinc-200 bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/5 lg:col-span-2 dark:border-zinc-800 dark:bg-zinc-900">

                <div
                    class="absolute right-0 top-0 h-40 w-40 rounded-full bg-indigo-500/5 blur-3xl transition duration-500 group-hover:bg-indigo-500/10">
                </div>

                <div class="relative">

                    <div class="flex items-center gap-4">

                        <div
                            class="flex size-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                            <flux:icon name="information-circle" class="size-6" />
                        </div>

                        <div>
                            <flux:heading size="lg">
                                Tentang Aplikasi
                            </flux:heading>

                            <flux:text class="mt-0.5">
                                Mengenal Sales WhatsApp Assistant
                            </flux:text>
                        </div>

                    </div>

                    <div class="mt-7 space-y-4 text-sm leading-7 text-zinc-600 dark:text-zinc-400">

                        <p>
                            <strong class="font-semibold text-zinc-900 dark:text-white">
                                Sales WhatsApp Assistant
                            </strong>
                            adalah aplikasi web yang dibuat untuk membantu salesperson
                            mengelola data customer dan mendukung proses komunikasi
                            melalui WhatsApp.
                        </p>

                        <p>
                            Aplikasi ini dibuat berdasarkan kebutuhan nyata dalam aktivitas
                            sales, terutama ketika harus menangani data customer dalam
                            jumlah banyak dan melakukan komunikasi secara berulang.
                        </p>

                        <p>
                            Salah satu fokus utama aplikasi ini adalah membuat proses
                            pengelolaan data menjadi sederhana. Data yang diperoleh dari
                            kantor dapat langsung
                            <strong class="font-semibold text-indigo-600 dark:text-indigo-400">
                                copy-paste
                            </strong>
                            ke dalam aplikasi tanpa harus melakukan proses import file
                            secara manual.
                        </p>

                        <p>
                            Data kemudian dapat dicari, difilter, dikelola, dan digunakan
                            untuk membantu membuat pesan WhatsApp berdasarkan template
                            yang telah disiapkan.
                        </p>

                    </div>

                </div>
            </flux:card>


            {{-- Purpose --}}
            <flux:card
                class="border-zinc-200 bg-linear-to-br from-indigo-600 to-violet-700 p-8 text-white shadow-lg shadow-indigo-500/10 dark:border-indigo-500/20">

                <div class="flex size-12 items-center justify-center rounded-2xl bg-white/15 backdrop-blur">
                    <flux:icon name="hand-thumb-up" class="size-6" />
                </div>

                <flux:heading size="lg" class="mt-6 text-white">
                    Tujuan Aplikasi
                </flux:heading>

                <p class="mt-2 text-sm leading-6 text-indigo-100">
                    Membuat pekerjaan sales menjadi lebih sederhana,
                    cepat, dan terorganisir.
                </p>

                <div class="mt-7 space-y-4">

                    <div class="flex items-center gap-3">
                        <div class="flex size-7 shrink-0 items-center justify-center rounded-full bg-white/15">
                            <flux:icon name="check" class="size-4" />
                        </div>
                        <span class="text-sm text-indigo-50">
                            Mengurangi pekerjaan manual
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex size-7 shrink-0 items-center justify-center rounded-full bg-white/15">
                            <flux:icon name="check" class="size-4" />
                        </div>
                        <span class="text-sm text-indigo-50">
                            Memusatkan data customer
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex size-7 shrink-0 items-center justify-center rounded-full bg-white/15">
                            <flux:icon name="check" class="size-4" />
                        </div>
                        <span class="text-sm text-indigo-50">
                            Mempermudah komunikasi
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex size-7 shrink-0 items-center justify-center rounded-full bg-white/15">
                            <flux:icon name="check" class="size-4" />
                        </div>
                        <span class="text-sm text-indigo-50">
                            Workflow lebih terorganisir
                        </span>
                    </div>

                </div>

            </flux:card>

        </div>
    </section>

    {{-- FEATURES --}}
    <section class="mx-auto max-w-7xl px-6 py-10 lg:px-8 lg:py-16">

        <div class="mb-8 max-w-2xl">

            <flux:badge color="violet" icon="bolt" class="mb-4">
                Core Features
            </flux:badge>

            <flux:heading size="xl">
                Semua yang dibutuhkan sales,
                <span class="text-indigo-600 dark:text-indigo-400">
                    dalam satu tempat.
                </span>
            </flux:heading>

            <flux:text class="mt-2">
                Fitur dirancang untuk mengurangi pekerjaan berulang
                dan membuat aktivitas sales lebih efisien.
            </flux:text>

        </div>


        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

            {{-- Customer --}}
            <flux:card
                class="group border-zinc-200 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl hover:shadow-blue-500/5 dark:border-zinc-800 dark:bg-zinc-900">

                <div
                    class="flex size-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 transition group-hover:scale-110 dark:bg-blue-500/10 dark:text-blue-400">
                    <flux:icon name="users" class="size-6" />
                </div>

                <flux:heading size="lg" class="mt-5">
                    Customer Management
                </flux:heading>

                <flux:text class="mt-2">
                    Mengelola data customer dalam satu tempat agar
                    lebih mudah dicari dan digunakan.
                </flux:text>

            </flux:card>


            {{-- Import --}}
            <flux:card
                class="group border-zinc-200 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:border-emerald-200 hover:shadow-xl hover:shadow-emerald-500/5 dark:border-zinc-800 dark:bg-zinc-900">

                <div
                    class="flex size-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 transition group-hover:scale-110 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <flux:icon name="clipboard-document-list" class="size-6" />
                </div>

                <flux:heading size="lg" class="mt-5">
                    Quick Import
                </flux:heading>

                <flux:text class="mt-2">
                    Masukkan data customer dengan metode copy-paste
                    langsung dari data kantor.
                </flux:text>

            </flux:card>


            {{-- WhatsApp --}}
            <flux:card
                class="group border-zinc-200 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:border-green-200 hover:shadow-xl hover:shadow-green-500/5 dark:border-zinc-800 dark:bg-zinc-900">

                <div
                    class="flex size-12 items-center justify-center rounded-2xl bg-green-100 text-green-600 transition group-hover:scale-110 dark:bg-green-500/10 dark:text-green-400">
                    <flux:icon name="chat-bubble-left-right" class="size-6" />
                </div>

                <flux:heading size="lg" class="mt-5">
                    WhatsApp Workflow
                </flux:heading>

                <flux:text class="mt-2">
                    Membantu mempersiapkan komunikasi customer
                    melalui WhatsApp dengan workflow yang praktis.
                </flux:text>

            </flux:card>


            {{-- Template --}}
            <flux:card
                class="group border-zinc-200 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:border-violet-200 hover:shadow-xl hover:shadow-violet-500/5 dark:border-zinc-800 dark:bg-zinc-900">

                <div
                    class="flex size-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-600 transition group-hover:scale-110 dark:bg-violet-500/10 dark:text-violet-400">
                    <flux:icon name="document-text" class="size-6" />
                </div>

                <flux:heading size="lg" class="mt-5">
                    Message Templates
                </flux:heading>

                <flux:text class="mt-2">
                    Membuat dan menyimpan template pesan yang
                    dapat digunakan kembali.
                </flux:text>

            </flux:card>


            {{-- Search --}}
            <flux:card
                class="group border-zinc-200 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:border-amber-200 hover:shadow-xl hover:shadow-amber-500/5 dark:border-zinc-800 dark:bg-zinc-900">

                <div
                    class="flex size-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 transition group-hover:scale-110 dark:bg-amber-500/10 dark:text-amber-400">
                    <flux:icon name="magnifying-glass" class="size-6" />
                </div>

                <flux:heading size="lg" class="mt-5">
                    Search & Filter
                </flux:heading>

                <flux:text class="mt-2">
                    Mencari customer berdasarkan nama, nomor,
                    kontrak, cabang, maupun status.
                </flux:text>

            </flux:card>


            {{-- Status --}}
            <flux:card
                class="group border-zinc-200 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:border-rose-200 hover:shadow-xl hover:shadow-rose-500/5 dark:border-zinc-800 dark:bg-zinc-900">

                <div
                    class="flex size-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 transition group-hover:scale-110 dark:bg-rose-500/10 dark:text-rose-400">
                    <flux:icon name="chart-bar" class="size-6" />
                </div>

                <flux:heading size="lg" class="mt-5">
                    Customer Status
                </flux:heading>

                <flux:text class="mt-2">
                    Membantu mencatat perkembangan komunikasi
                    customer melalui berbagai status.
                </flux:text>

            </flux:card>

        </div>
    </section>

    {{-- CREATOR --}}
    <section class="mx-auto max-w-7xl px-6 py-10 lg:px-8 lg:py-16">

        <flux:card
            class="relative overflow-hidden border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">

            {{-- Background --}}
            <div class="absolute right-0 top-0 h-80 w-80 rounded-full bg-indigo-500/5 blur-3xl"></div>

            <div class="relative grid lg:grid-cols-3">

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


                {{-- Description --}}
                <div class="p-8 lg:col-span-2 lg:p-12">

                    <flux:badge color="violet" icon="user">
                        About the Creator
                    </flux:badge>

                    <flux:heading size="xl" class="mt-5">
                        Dibuat dari kebutuhan nyata.
                    </flux:heading>

                    <div class="mt-6 space-y-4 text-sm leading-7 text-zinc-600 dark:text-zinc-400">

                        <p>
                            <strong class="font-semibold text-zinc-900 dark:text-white">
                                Vika Arya
                            </strong>
                            adalah web developer dan creator dari
                            Sales WhatsApp Assistant.
                        </p>

                        <p>
                            Aplikasi ini dikembangkan secara mandiri sebagai
                            personal productivity tool untuk membantu
                            aktivitas sales sehari-hari.
                        </p>

                        <p>
                            Ide pengembangannya berawal dari kebutuhan sederhana:
                            bagaimana membuat pekerjaan yang dilakukan berulang kali
                            menjadi lebih cepat, rapi, dan mudah digunakan.
                        </p>

                        <p>
                            Dengan pendekatan tersebut, aplikasi ini terus
                            dikembangkan dengan mengutamakan pengalaman pengguna,
                            workflow yang praktis, serta teknologi web modern.
                        </p>

                    </div>

                </div>

            </div>

        </flux:card>
    </section>

    {{-- TECHNOLOGY --}}
    <section class="mx-auto max-w-7xl px-6 py-10 lg:px-8 lg:py-16">

        <div class="text-center">

            <flux:badge color="violet" icon="code-bracket">
                Technology Stack
            </flux:badge>

            <flux:heading size="xl" class="mt-4">
                Built with modern technology.
            </flux:heading>

            <flux:text class="mx-auto mt-2 max-w-2xl">
                Sales WhatsApp Assistant dibangun menggunakan
                teknologi web modern untuk pengalaman yang cepat
                dan interaktif.
            </flux:text>

        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Laravel --}}
            <div
                class="group relative rounded-2xl border border-zinc-200 bg-white p-6 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-red-200 hover:shadow-xl hover:shadow-red-500/10 dark:border-zinc-800 dark:bg-zinc-900">

                <div
                    class="relative mx-auto flex size-14 items-center justify-center rounded-2xl bg-red-50 p-3 ring-1 ring-red-100 transition duration-300 group-hover:scale-110 group-hover:ring-red-200 dark:bg-red-500/10 dark:ring-red-500/10">
                    <img src="{{ asset('storage/icons/laravel.svg') }}" alt="Laravel" class="size-7 object-contain">
                </div>

                <flux:heading size="lg" class="relative mt-4">
                    Laravel 13
                </flux:heading>

                <flux:text class="relative mt-1">
                    Backend Framework
                </flux:text>
            </div>


            {{-- Livewire --}}
            <div
                class="group relative rounded-2xl border border-zinc-200 bg-white p-6 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-pink-200 hover:shadow-xl hover:shadow-pink-500/10 dark:border-zinc-800 dark:bg-zinc-900">

                <div
                    class="relative mx-auto flex size-14 items-center justify-center rounded-2xl bg-pink-50 p-3 ring-1 ring-pink-100 transition duration-300 group-hover:scale-110 group-hover:ring-pink-200 dark:bg-pink-500/10 dark:ring-pink-500/10">
                    <img src="{{ asset('storage/icons/livewire.svg') }}" alt="Livewire"
                        class="size-7 object-contain">
                </div>

                <flux:heading size="lg" class="relative mt-4">
                    Livewire 4
                </flux:heading>

                <flux:text class="relative mt-1">
                    Reactive Components
                </flux:text>
            </div>


            {{-- Flux --}}
            <div
                class="group relative rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-slate-300 hover:shadow-xl hover:shadow-slate-500/20 dark:border-slate-800 dark:bg-slate-900">

                <div
                    class="relative mx-auto flex size-14 items-center justify-center rounded-2xl bg-black text-white dark:text-black p-3 shadow-sm ring-1 ring-slate-800 transition duration-300 group-hover:scale-110 dark:bg-white dark:ring-slate-700">
                    <x-flux-icon />
                </div>

                <flux:heading size="lg" class="relative mt-4">
                    Flux UI 2
                </flux:heading>

                <flux:text class="relative mt-1">
                    UI Components
                </flux:text>
            </div>


            {{-- Tailwind --}}
            <div
                class="group relative rounded-2xl border border-zinc-200 bg-white p-6 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-cyan-200 hover:shadow-xl hover:shadow-cyan-500/10 dark:border-zinc-800 dark:bg-zinc-900">

                <div
                    class="relative mx-auto flex size-14 items-center justify-center rounded-2xl bg-cyan-50 p-3 ring-1 ring-cyan-100 transition duration-300 group-hover:scale-110 group-hover:ring-cyan-200 dark:bg-cyan-500/10 dark:ring-cyan-500/10">
                    <img src="{{ asset('storage/icons/tailwind.svg') }}" alt="Tailwind CSS"
                        class="size-7 object-contain">
                </div>

                <flux:heading size="lg" class="relative mt-4">
                    Tailwind CSS 4
                </flux:heading>

                <flux:text class="relative mt-1">
                    Styling & Design
                </flux:text>
            </div>

        </div>


    </section>


    {{-- QUOTE --}}
    <section class="mx-auto max-w-4xl px-6 py-16 text-center lg:px-8 lg:py-24">

        <div
            class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-600 to-violet-600 text-white shadow-lg shadow-indigo-500/20">
            <flux:icon name="chat-bubble-oval-left-ellipsis" class="size-7" />
        </div>

        <blockquote class="mt-7 text-2xl font-semibold tracking-tight text-zinc-900 sm:text-3xl dark:text-white">
            “Membangun tools bukan hanya tentang membuat aplikasi bekerja,
            tetapi tentang membuat pekerjaan menjadi lebih sederhana.”
        </blockquote>

        <div class="mt-5 flex items-center justify-center gap-2">
            <div class="h-px w-8 bg-zinc-300 dark:bg-zinc-700"></div>

            <flux:text>
                Vika Arya
            </flux:text>

            <div class="h-px w-8 bg-zinc-300 dark:bg-zinc-700"></div>
        </div>

    </section>


    {{-- FOOTER --}}
    <footer class="border-t border-zinc-200 bg-white/50 dark:border-zinc-800 dark:bg-zinc-950">

        <div
            class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-6 py-8 text-center sm:flex-row sm:text-left lg:px-8">

            <flux:text>
                <span class="font-medium text-zinc-900 dark:text-white">
                    Sales WhatsApp Assistant
                </span>

                <span class="mx-2 text-zinc-300 dark:text-zinc-700">
                    ·
                </span>

                © {{ date('Y') }} Vika Arya
            </flux:text>

            <flux:text size="sm">

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

        </div>

    </footer>

</div>
