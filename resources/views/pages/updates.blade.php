<x-layouts::app.app :title="$title ?? 'Updates & Changelog'">
    @php
        $updates = \App\Models\Update::published()
            ->with('items')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        $latestUpdates = $updates->take(3);

        $categoryInfo = static fn(string $category): array => match ($category) {
            'feature' => [
                'label' => 'New',
                'color' => 'green',
                'icon' => 'sparkles',
            ],
            'improvement' => [
                'label' => 'Improved',
                'color' => 'blue',
                'icon' => 'arrow-path',
            ],
            'fix' => [
                'label' => 'Fixed',
                'color' => 'amber',
                'icon' => 'wrench-screwdriver',
            ],
            'security' => [
                'label' => 'Security',
                'color' => 'red',
                'icon' => 'shield-check',
            ],
            default => [
                'label' => 'Update',
                'color' => 'zinc',
                'icon' => 'information-circle',
            ],
        };

        $plannedUpdates = \App\Models\PlannedUpdate::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

    @endphp

    <div class="min-h-screen overflow-hidden bg-zinc-50 dark:bg-zinc-950">

        {{-- HERO --}}
        <section class="relative isolate overflow-hidden py-18">
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

            </div>
        </section>

        {{-- CONTENT --}}
        <main class="mx-auto max-w-5xl px-6 py-20 lg:px-8">

            {{-- LATEST UPDATES --}}
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
                    @forelse ($latestUpdates as $update)
                        @php
                            $firstItem = $update->items->first();
                            $info = $categoryInfo($firstItem?->category ?? 'feature');
                        @endphp

                        <flux:card
                            class="group relative overflow-hidden border-emerald-200/70 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-500/5 sm:p-8 dark:border-emerald-500/20 dark:bg-zinc-900">
                            <div
                                class="absolute left-0 top-0 h-full w-1 bg-linear-to-b from-emerald-400 via-green-500 to-teal-500">
                            </div>

                            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <flux:badge :color="$info['color']" :icon="$info['icon']">
                                            {{ $info['label'] }}
                                        </flux:badge>

                                        <flux:badge variant="outline">
                                            {{ $update->version }}
                                        </flux:badge>
                                    </div>

                                    <flux:heading size="lg" class="mt-4">
                                        {{ $update->title }}
                                    </flux:heading>

                                    <flux:text class="mt-1">
                                        {{ ($update->published_at ?? $update->release_date)->translatedFormat('d F Y') }}
                                    </flux:text>

                                    @if ($update->summary)
                                        <flux:text class="mt-3">
                                            {{ $update->summary }}
                                        </flux:text>
                                    @endif
                                </div>

                                <div
                                    class="hidden size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 sm:flex dark:bg-emerald-500/10 dark:text-emerald-400">
                                    <flux:icon :name="$info['icon']" class="size-5" />
                                </div>
                            </div>

                            <div class="mt-7 space-y-3">
                                @foreach ($update->items as $item)
                                    <div class="flex gap-3" wire:key="public-update-item-{{ $item->id }}">
                                        <div
                                            class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                            <flux:icon name="check" class="size-3.5" />
                                        </div>

                                        <div class="min-w-0">
                                            @if ($item->title)
                                                <flux:text class="font-medium">
                                                    {{ $item->title }}
                                                </flux:text>
                                            @endif

                                            <flux:text>
                                                {{ $item->description }}
                                            </flux:text>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </flux:card>
                    @empty
                        <flux:card class="p-8 text-center">
                            <flux:icon name="inbox" class="mx-auto size-10 text-zinc-400" />

                            <flux:heading size="lg" class="mt-3">
                                Belum ada update
                            </flux:heading>

                            <flux:text class="mt-1">
                                Update terbaru akan ditampilkan di sini setelah dipublikasikan.
                            </flux:text>
                        </flux:card>
                    @endforelse
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
                    @forelse ($updates as $index => $update)
                        <div class="relative pl-8" wire:key="public-changelog-{{ $update->id }}">
                            <div
                                class="absolute -left-2.25 top-1 flex size-4 items-center justify-center rounded-full bg-white ring-4 ring-indigo-100 dark:bg-zinc-950 dark:ring-indigo-500/10">
                                <div class="size-2 rounded-full bg-indigo-500"></div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <flux:heading size="lg">
                                    {{ $update->title }}
                                </flux:heading>

                                <flux:badge variant="outline">
                                    {{ $update->version }}
                                </flux:badge>

                                @if ($index === 0)
                                    <flux:badge color="green">
                                        Latest
                                    </flux:badge>
                                @endif
                            </div>

                            <flux:text class="mt-1">
                                {{ ($update->published_at ?? $update->release_date)->translatedFormat('F Y') }}
                            </flux:text>

                            @if ($update->summary)
                                <flux:text class="mt-2">
                                    {{ $update->summary }}
                                </flux:text>
                            @endif

                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($update->items as $item)
                                    @php
                                        $itemInfo = $categoryInfo($item->category);
                                    @endphp

                                    <flux:badge wire:key="public-changelog-item-{{ $item->id }}" variant="outline"
                                        :color="$itemInfo['color']">
                                        {{ $item->title ?: $itemInfo['label'] }}
                                    </flux:badge>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="pl-8">
                            <flux:text>
                                Belum ada riwayat versi yang dipublikasikan.
                            </flux:text>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- PLANNED UPDATES --}}
            <section class="mt-20">
                <flux:card
                    class="relative overflow-hidden border-amber-200/70 bg-white p-6 shadow-sm sm:p-8 dark:border-amber-500/20 dark:bg-zinc-900">
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
                                    Beberapa pengembangan yang sedang dipertimbangkan
                                    untuk versi berikutnya.
                                </flux:text>
                            </div>
                        </div>

                        <div class="mt-7 grid gap-3 sm:grid-cols-2">
                            @forelse ($plannedUpdates as $planned)
                                <div wire:key="planned-update-{{ $planned->id }}"
                                    class="group rounded-xl border border-zinc-200 bg-zinc-50 p-4 transition duration-300 hover:border-amber-200 hover:bg-amber-50/50 dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-amber-500/30">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                                            <flux:icon :name="$planned->icon" class="size-4" />
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <flux:text class="font-medium">
                                                {{ $planned->title }}
                                            </flux:text>

                                            @if ($planned->description)
                                                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                                    {{ $planned->description }}
                                                </flux:text>
                                            @endif
                                        </div>

                                        <flux:badge color="amber" size="sm">
                                            Soon
                                        </flux:badge>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="col-span-full rounded-xl border border-dashed border-zinc-300 p-6 text-center dark:border-zinc-700">
                                    <flux:icon name="calendar-clock" class="mx-auto size-8 text-zinc-400" />

                                    <flux:heading size="sm" class="mt-3">
                                        Belum ada rencana pembaruan
                                    </flux:heading>

                                    <flux:text class="mt-1">
                                        Nantikan fitur-fitur baru berikutnya.
                                    </flux:text>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </flux:card>
            </section>

        </main>

    </div>

</x-layouts::app.app>
