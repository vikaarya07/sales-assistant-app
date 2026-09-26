<?php

use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Models\MessageTemplate;
use Livewire\Component;

new class extends Component {
    public string $selectedMonth;

    public array $monthOptions = [];

    public function mount(): void
    {
        $currentMonth = now()->startOfMonth();
        $this->selectedMonth = $currentMonth->format('Y-m');

        $this->monthOptions = collect(range(-6, 6))
            ->map(function ($offset) use ($currentMonth) {
                $date = $currentMonth->copy()->addMonths($offset);

                return [
                    'value' => $date->format('Y-m'),
                    'label' => $date->translatedFormat('F Y'),
                ];
            })
            ->sortBy('value')
            ->values()
            ->all();
    }

    public function updatedSelectedMonth(): void
    {
        $this->dispatch('overview-chart-updated', data: $this->getChartData());
    }

    private function getChartData(): array
    {
        $user = auth()->user();

        $selectedDate = \Carbon\Carbon::createFromFormat('Y-m', $this->selectedMonth);

        $previousDate = $selectedDate->copy()->subMonth();

        $statuses = [CustomerStatus::NEW, CustomerStatus::BLAST, CustomerStatus::REPLIED, CustomerStatus::FOLLOW_UP, CustomerStatus::INTERESTED, CustomerStatus::NOT_INTERESTED, CustomerStatus::APP_IN, CustomerStatus::VALID, CustomerStatus::INVALID];

        $currentCustomers = $user
            ->customers()
            ->whereBetween('created_at', [$selectedDate->copy()->startOfMonth(), $selectedDate->copy()->endOfMonth()])
            ->get()
            ->groupBy('status');

        $previousCustomers = $user
            ->customers()
            ->whereBetween('created_at', [$previousDate->copy()->startOfMonth(), $previousDate->copy()->endOfMonth()])
            ->get()
            ->groupBy('status');

        return [
            'labels' => collect($statuses)->map(fn($status) => $status->label())->values()->all(),

            'current' => collect($statuses)->map(fn($status) => $currentCustomers->get($status->value, collect())->count())->values()->all(),

            'previous' => collect($statuses)->map(fn($status) => $previousCustomers->get($status->value, collect())->count())->values()->all(),
        ];
    }

    public function render()
    {
        $user = auth()->user();

        $customers = $user->customers();

        $totalCustomers = (clone $customers)->count();
        $newCustomers = (clone $customers)->where('status', CustomerStatus::NEW->value)->count();
        $blastedCustomers = (clone $customers)->where('status', CustomerStatus::BLAST->value)->count();
        $repliedCustomers = (clone $customers)->where('status', CustomerStatus::REPLIED->value)->count();
        $followUpCustomers = (clone $customers)->where('status', CustomerStatus::FOLLOW_UP->value)->count();
        $interestedCustomers = (clone $customers)->where('status', CustomerStatus::INTERESTED->value)->count();
        $notInterestedCustomers = (clone $customers)->where('status', CustomerStatus::NOT_INTERESTED->value)->count();
        $appInCustomers = (clone $customers)->where('status', CustomerStatus::APP_IN->value)->count();
        $validCustomers = (clone $customers)->where('status', CustomerStatus::VALID->value)->count();
        $invalidCustomers = (clone $customers)->where('status', CustomerStatus::INVALID->value)->count();

        $contactedToday = (clone $customers)->whereDate('last_contacted_at', today())->count();

        $contactedThisWeek = (clone $customers)->where('last_contacted_at', '>=', now()->startOfWeek())->count();

        $recentCustomers = $user->customers()->latest()->limit(6)->get();

        $activeTemplates = $user->messageTemplates()->where('is_active', true)->latest()->limit(5)->get();

        $statusOverview = [
            [
                'status' => CustomerStatus::NEW,
                'count' => $newCustomers,
            ],
            [
                'status' => CustomerStatus::BLAST,
                'count' => $blastedCustomers,
            ],
            [
                'status' => CustomerStatus::REPLIED,
                'count' => $repliedCustomers,
            ],
            [
                'status' => CustomerStatus::FOLLOW_UP,
                'count' => $followUpCustomers,
            ],
            [
                'status' => CustomerStatus::INTERESTED,
                'count' => $interestedCustomers,
            ],
            [
                'status' => CustomerStatus::NOT_INTERESTED,
                'count' => $notInterestedCustomers,
            ],
            [
                'status' => CustomerStatus::APP_IN,
                'count' => $appInCustomers,
            ],
            [
                'status' => CustomerStatus::VALID,
                'count' => $validCustomers,
            ],
            [
                'status' => CustomerStatus::INVALID,
                'count' => $invalidCustomers,
            ],
        ];

        $chartData = $this->getChartData();

        return $this->view([
            'totalCustomers' => $totalCustomers,
            'newCustomers' => $newCustomers,
            'blastedCustomers' => $blastedCustomers,
            'repliedCustomers' => $repliedCustomers,
            'followUpCustomers' => $followUpCustomers,
            'interestedCustomers' => $interestedCustomers,
            'notInterestedCustomers' => $notInterestedCustomers,
            'appInCustomers' => $appInCustomers,
            'validCustomers' => $validCustomers,
            'invalidCustomers' => $invalidCustomers,
            'contactedToday' => $contactedToday,
            'contactedThisWeek' => $contactedThisWeek,
            'recentCustomers' => $recentCustomers,
            'activeTemplates' => $activeTemplates,
            'statusOverview' => $statusOverview,
            'chartData' => $chartData,
        ]);
    }
};
?>

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="relative overflow-hidden border-b border-zinc-200/70 bg-white dark:border-zinc-800 dark:bg-zinc-950">

        {{-- Glow --}}
        <div
            class="pointer-events-none absolute -top-32 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-indigo-500/10 blur-3xl dark:bg-indigo-500/15">
        </div>

        <div class="relative mx-auto max-w-7xl px-6 py-10 lg:px-8">

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-4">

                    <div
                        class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-500 to-violet-600 text-white shadow-lg shadow-indigo-500/20">
                        <flux:icon name="chart-bar-square" class="size-6" />
                    </div>

                    <div>
                        <flux:heading size="xl">
                            Overview
                        </flux:heading>

                        <flux:text class="mt-1">
                            Ringkasan aktivitas Sales WhatsApp Anda.
                        </flux:text>
                    </div>

                </div>


                <div class="flex flex-wrap gap-2">

                    <flux:button href="{{ route('customers') }}" wire:navigate variant="ghost" icon="users">
                        Customers
                    </flux:button>

                    <flux:button href="{{ route('message-templates') }}" wire:navigate variant="primary"
                        icon="document-text" class="shadow-lg shadow-indigo-500/20">
                        Templates
                    </flux:button>

                </div>

            </div>

        </div>
    </div>

    {{-- CONTENT --}}
    <div class="mx-auto max-w-7xl space-y-6 px-6 py-8 lg:px-8">

        {{-- MAIN STATS --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- TOTAL --}}
            <flux:card
                class="group relative overflow-hidden border-indigo-200/70 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-indigo-500/10 dark:border-indigo-500/20 dark:bg-zinc-900">

                <div
                    class="pointer-events-none absolute -right-10 -top-10 size-32 rounded-full bg-indigo-500/10 blur-3xl transition duration-500 group-hover:bg-indigo-500/20">
                </div>

                <div class="relative flex items-start justify-between gap-4">

                    <div>
                        <flux:text>
                            Total Customer
                        </flux:text>

                        <flux:heading size="xl" class="mt-1">
                            {{ number_format($totalCustomers) }}
                        </flux:heading>

                        <flux:text class="mt-1 text-xs text-zinc-500">
                            Semua customer
                        </flux:text>
                    </div>

                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100 transition duration-300 group-hover:scale-110 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20">
                        <flux:icon name="users" class="size-5" />
                    </div>

                </div>

            </flux:card>

            {{-- NEW --}}
            <flux:card
                class="group relative overflow-hidden border-blue-200/70 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-500/10 dark:border-blue-500/20 dark:bg-zinc-900">

                <div
                    class="pointer-events-none absolute -right-10 -top-10 size-32 rounded-full bg-blue-500/10 blur-3xl transition duration-500 group-hover:bg-blue-500/20">
                </div>

                <div class="relative flex items-start justify-between gap-4">

                    <div>
                        <flux:text>
                            Customer Baru
                        </flux:text>

                        <flux:heading size="xl" class="mt-1">
                            {{ number_format($newCustomers) }}
                        </flux:heading>

                        <flux:text class="mt-1 text-xs text-zinc-500">
                            Belum dihubungi
                        </flux:text>
                    </div>

                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 ring-1 ring-blue-100 transition duration-300 group-hover:scale-110 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20">
                        <flux:icon name="user-plus" class="size-5" />
                    </div>

                </div>

            </flux:card>

            {{-- CONTACTED --}}
            <flux:card
                class="group relative overflow-hidden border-amber-200/70 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-amber-500/10 dark:border-amber-500/20 dark:bg-zinc-900">

                <div
                    class="pointer-events-none absolute -right-10 -top-10 size-32 rounded-full bg-amber-500/10 blur-3xl transition duration-500 group-hover:bg-amber-500/20">
                </div>

                <div class="relative flex items-start justify-between gap-4">

                    <div>
                        <flux:text>
                            Total Customer Blast
                        </flux:text>

                        <flux:heading size="xl" class="mt-1">
                            {{ number_format($blastedCustomers) }}
                        </flux:heading>

                        <flux:text class="mt-1 text-xs text-zinc-500">
                            Sudah Diblast WA
                        </flux:text>
                    </div>

                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 transition duration-300 group-hover:scale-110 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20">
                        <flux:icon name="chat-bubble-left-right" class="size-5" />
                    </div>

                </div>

            </flux:card>


            {{-- INTERESTED --}}
            <flux:card
                class="group relative overflow-hidden border-emerald-200/70 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-emerald-500/10 dark:border-emerald-500/20 dark:bg-zinc-900">

                <div
                    class="pointer-events-none absolute -right-10 -top-10 size-32 rounded-full bg-emerald-500/10 blur-3xl transition duration-500 group-hover:bg-emerald-500/20">
                </div>

                <div class="relative flex items-start justify-between gap-4">

                    <div>
                        <flux:text>
                            Customer Minat
                        </flux:text>

                        <flux:heading size="xl" class="mt-1 text-emerald-600 dark:text-emerald-400">
                            {{ number_format($interestedCustomers) }}
                        </flux:heading>

                        <flux:text class="mt-1 text-xs text-zinc-500">
                            Customer tertarik
                        </flux:text>
                    </div>

                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100 transition duration-300 group-hover:scale-110 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">
                        <flux:icon name="sparkles" class="size-5" />
                    </div>

                </div>

            </flux:card>

        </div>

        {{-- ACTIVITY --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">

            @php
                $activities = [
                    [
                        'label' => 'Kontak Hari Ini',
                        'value' => $contactedToday,
                        'icon' => 'paper-airplane',
                        'color' => 'indigo',
                    ],
                    [
                        'label' => 'Kontak Minggu Ini',
                        'value' => $contactedThisWeek,
                        'icon' => 'calendar-days',
                        'color' => 'violet',
                    ],
                    [
                        'label' => 'Follow Up',
                        'value' => $followUpCustomers,
                        'icon' => 'arrow-path',
                        'color' => 'amber',
                    ],
                    [
                        'label' => 'Pengajuan',
                        'value' => $appInCustomers,
                        'icon' => 'arrow-left-end-on-rectangle',
                        'color' => 'teal',
                    ],
                    [
                        'label' => 'Valid',
                        'value' => $validCustomers,
                        'icon' => 'check-circle',
                        'color' => 'green',
                    ],
                ];
            @endphp

            @foreach ($activities as $activity)
                <flux:card
                    class="group border-zinc-200/80 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-800">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-{{ $activity['color'] }}-50 text-{{ $activity['color'] }}-600 transition duration-300 group-hover:scale-105 dark:bg-{{ $activity['color'] }}-500/10 dark:text-{{ $activity['color'] }}-400">
                            <flux:icon name="{{ $activity['icon'] }}" class="size-5" />
                        </div>

                        <div>
                            <flux:text class="text-xs">
                                {{ $activity['label'] }}
                            </flux:text>

                            <flux:heading size="lg" class="mt-0.5">
                                {{ number_format($activity['value']) }}
                            </flux:heading>
                        </div>

                    </div>

                </flux:card>
            @endforeach

        </div>

        {{-- CUSTOMER STATUS CHART --}}
        <flux:card
            class="overflow-hidden border-indigo-200/70 bg-white shadow-sm transition duration-300 hover:shadow-md dark:border-indigo-500/20 dark:bg-zinc-900">

            {{-- HEADER --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-3">

                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                        <flux:icon name="chart-bar" class="size-5" />
                    </div>

                    <div>
                        <flux:heading size="lg">
                            Customer Status
                        </flux:heading>

                        <flux:text class="mt-1">
                            Perbandingan customer berdasarkan status.
                        </flux:text>
                    </div>

                </div>

                {{-- MONTH FILTER --}}
                <div class="w-full sm:w-auto">
                    <flux:select wire:model.live="selectedMonth" class="w-full sm:w-52">
                        @foreach ($monthOptions as $month)
                            <flux:select.option value="{{ $month['value'] }}">
                                {{ $month['label'] }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

            </div>

            {{-- SUMMARY --}}
            <div class="mt-6 grid gap-3 sm:grid-cols-3">

                {{-- CURRENT MONTH --}}
                <div
                    class="rounded-xl border border-indigo-100 bg-indigo-50/70 p-4 dark:border-indigo-500/10 dark:bg-indigo-500/5">

                    <flux:text class="text-xs text-indigo-600 dark:text-indigo-400">
                        Bulan Dipilih
                    </flux:text>

                    <flux:heading size="lg" class="mt-1">
                        {{ number_format(collect($chartData['current'])->sum()) }}
                    </flux:heading>

                </div>

                {{-- PREVIOUS MONTH --}}
                <div
                    class="rounded-xl border border-zinc-200 bg-zinc-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-800/40">

                    <flux:text class="text-xs">
                        Bulan Sebelumnya
                    </flux:text>

                    <flux:heading size="lg" class="mt-1">
                        {{ number_format(collect($chartData['previous'])->sum()) }}
                    </flux:heading>

                </div>

                {{-- DIFFERENCE --}}
                @php
                    $currentTotal = collect($chartData['current'])->sum();
                    $previousTotal = collect($chartData['previous'])->sum();

                    $difference = $currentTotal - $previousTotal;

                    $percentageChange =
                        $previousTotal > 0
                            ? round(($difference / $previousTotal) * 100, 1)
                            : ($currentTotal > 0
                                ? 100
                                : 0);
                @endphp

                <div
                    class="rounded-xl border border-emerald-100 bg-emerald-50/70 p-4 dark:border-emerald-500/10 dark:bg-emerald-500/5">

                    <flux:text class="text-xs text-emerald-600 dark:text-emerald-400">
                        Perubahan
                    </flux:text>

                    <div class="mt-1 flex items-center gap-2">

                        <flux:heading size="lg"
                            class="{{ $difference >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">

                            {{ $difference >= 0 ? '+' : '' }}{{ number_format($difference) }}
                        </flux:heading>

                        <span
                            class="text-xs font-medium {{ $difference >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">

                            {{ $percentageChange >= 0 ? '+' : '' }}{{ $percentageChange }}%

                        </span>

                    </div>

                </div>

            </div>

            {{-- CHART --}}
            <div wire:ignore class="relative mt-6 h-90 w-full" x-data="overviewChart(@js($chartData))"
                x-on:overview-chart-updated.window="update($event.detail.data)">
                <canvas x-ref="canvas"></canvas>
            </div>

        </flux:card>

        {{-- STATUS + QUICK ACTION --}}
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- STATUS --}}
            <flux:card class="overflow-hidden border-indigo-200/60 shadow-sm dark:border-indigo-500/20 lg:col-span-2">

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                            <flux:icon name="chart-bar" class="size-5" />
                        </div>

                        <div>
                            <flux:heading size="lg">
                                Customer Status
                            </flux:heading>

                            <flux:text class="mt-1">
                                Distribusi status customer Anda.
                            </flux:text>
                        </div>

                    </div>

                    <flux:button href="{{ route('customers') }}" wire:navigate variant="ghost" size="sm">
                        Lihat Semua
                    </flux:button>

                </div>

                <div class="mt-6 space-y-5">

                    @foreach ($statusOverview as $item)
                        @php
                            $status = $item['status'];
                            $count = $item['count'];

                            $percentage = $totalCustomers > 0 ? round(($count / $totalCustomers) * 100) : 0;
                        @endphp

                        <div>

                            <div class="mb-2 flex items-center justify-between gap-3">

                                <flux:badge :color="$status->color()" size="sm">
                                    {{ $status->label() }}
                                </flux:badge>

                                <div class="text-xs font-medium text-zinc-500">
                                    {{ number_format($count) }}
                                    <span class="mx-1 text-zinc-300">·</span>
                                    {{ $percentage }}%
                                </div>

                            </div>

                            <div class="h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">

                                <div class="h-full rounded-full bg-linear-to-r from-indigo-500 to-violet-500 transition-all duration-700"
                                    style="width: {{ $percentage }}%">
                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>

            </flux:card>

            {{-- QUICK ACTION --}}
            <flux:card
                class="overflow-hidden border-violet-200/70 bg-linear-to-br from-indigo-100/80 via-white to-violet-100/80 shadow-sm dark:border-violet-500/20 dark:from-indigo-500/10 dark:via-zinc-900 dark:to-violet-500/10">

                <div class="flex items-center gap-3">

                    <div
                        class="flex size-10 items-center justify-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                        <flux:icon name="bolt" class="size-5" />
                    </div>

                    <div>
                        <flux:heading size="lg">
                            Quick Actions
                        </flux:heading>

                        <flux:text class="mt-1">
                            Akses fitur yang sering digunakan.
                        </flux:text>
                    </div>

                </div>

                <div class="mt-6 space-y-3">

                    <flux:button href="{{ route('customers') }}" wire:navigate variant="outline"
                        class="w-full justify-start border-indigo-200 bg-white/70 hover:bg-indigo-50 dark:border-indigo-500/20 dark:bg-zinc-900/50 dark:hover:bg-indigo-500/10"
                        icon="user-plus">
                        Import Customer
                    </flux:button>

                    <flux:button href="{{ route('customers') }}" wire:navigate variant="outline"
                        class="w-full justify-start border-emerald-200 bg-white/70 hover:bg-emerald-50 dark:border-emerald-500/20 dark:bg-zinc-900/50 dark:hover:bg-emerald-500/10"
                        icon="chat-bubble-left-right">
                        Hubungi Customer
                    </flux:button>

                    <flux:button href="{{ route('message-templates') }}" wire:navigate variant="outline"
                        class="w-full justify-start border-violet-200 bg-white/70 hover:bg-violet-50 dark:border-violet-500/20 dark:bg-zinc-900/50 dark:hover:bg-violet-500/10"
                        icon="document-plus">
                        Buat Template
                    </flux:button>

                </div>

            </flux:card>

        </div>

        {{-- RECENT + TEMPLATES --}}
        <div class="grid gap-6 lg:grid-cols-2">


            {{-- RECENT CUSTOMERS --}}
            <flux:card class="overflow-hidden border-zinc-200/80 shadow-sm dark:border-zinc-800">

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                            <flux:icon name="users" class="size-5" />
                        </div>

                        <div>
                            <flux:heading size="lg">
                                Customer Terbaru
                            </flux:heading>

                            <flux:text class="mt-1">
                                Customer yang terakhir ditambahkan.
                            </flux:text>
                        </div>

                    </div>

                    <flux:button href="{{ route('customers') }}" wire:navigate variant="ghost" size="sm">
                        Lihat Semua
                    </flux:button>

                </div>

                <div class="mt-5 divide-y divide-zinc-100 dark:divide-zinc-800">

                    @forelse ($recentCustomers as $customer)
                        <div
                            class="group flex items-center gap-3 rounded-xl py-3 transition duration-200 first:pt-0 last:pb-0 hover:bg-indigo-50/50 dark:hover:bg-indigo-500/5">

                            <flux:avatar :name="$customer->name" size="sm" color="auto" />

                            <div class="min-w-0 flex-1">

                                <div class="truncate text-sm font-medium">
                                    {{ $customer->name }}
                                </div>

                                <div class="mt-0.5 truncate text-xs text-zinc-500">
                                    {{ $customer->branch }}
                                    <span class="mx-1">·</span>
                                    {{ $customer->contract_number }}
                                </div>

                            </div>

                            <flux:badge :color="$customer->status->color()" size="sm">
                                {{ $customer->status->label() }}
                            </flux:badge>

                        </div>

                    @empty

                        <div class="py-10 text-center">
                            <flux:icon name="users" class="mx-auto size-8 text-zinc-400" />

                            <flux:text class="mt-2">
                                Belum ada customer.
                            </flux:text>
                        </div>
                    @endforelse

                </div>

            </flux:card>

            {{-- ACTIVE TEMPLATES --}}
            <flux:card class="overflow-hidden border-zinc-200/80 shadow-sm dark:border-zinc-800">

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                            <flux:icon name="document-text" class="size-5" />
                        </div>

                        <div>
                            <flux:heading size="lg">
                                Template Aktif
                            </flux:heading>

                            <flux:text class="mt-1">
                                Template yang tersedia untuk digunakan.
                            </flux:text>
                        </div>

                    </div>

                    <flux:button href="{{ route('message-templates') }}" wire:navigate variant="ghost"
                        size="sm">
                        Kelola
                    </flux:button>

                </div>


                <div class="mt-5 divide-y divide-zinc-100 dark:divide-zinc-800">

                    @forelse ($activeTemplates as $template)
                        <div
                            class="group flex items-center gap-3 rounded-xl py-3 transition duration-200 first:pt-0 last:pb-0 hover:bg-violet-50/50 dark:hover:bg-violet-500/5">

                            <div
                                class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600 transition duration-200 group-hover:scale-105 dark:bg-violet-500/10 dark:text-violet-400">

                                <flux:icon name="document-text" class="size-4" />

                            </div>

                            <div class="min-w-0 flex-1">

                                <div class="truncate text-sm font-medium">
                                    {{ $template->name }}
                                </div>

                                <div class="mt-0.5 truncate text-xs text-zinc-500">
                                    {{ \Illuminate\Support\Str::limit($template->content, 70) }}
                                </div>

                            </div>

                            <flux:badge color="green" size="sm" icon="check">
                                Aktif
                            </flux:badge>

                        </div>

                    @empty

                        <div class="py-10 text-center">

                            <flux:icon name="document-text" class="mx-auto size-8 text-zinc-400" />

                            <flux:text class="mt-2">
                                Belum ada template aktif.
                            </flux:text>

                        </div>
                    @endforelse

                </div>

            </flux:card>

        </div>

    </div>

</div>
