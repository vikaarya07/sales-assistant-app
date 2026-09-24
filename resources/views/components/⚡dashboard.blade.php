<?php

use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Models\MessageTemplate;
use Livewire\Component;

new class extends Component {
    public function render()
    {
        $user = auth()->user();

        $customers = $user->customers();

        $totalCustomers = (clone $customers)->count();
        $newCustomers = (clone $customers)->where('status', CustomerStatus::NEW->value)->count();
        $contactedCustomers = (clone $customers)->where('status', CustomerStatus::CONTACTED->value)->count();
        $repliedCustomers = (clone $customers)->where('status', CustomerStatus::REPLIED->value)->count();
        $interestedCustomers = (clone $customers)->where('status', CustomerStatus::INTERESTED->value)->count();
        $followUpCustomers = (clone $customers)->where('status', CustomerStatus::FOLLOW_UP->value)->count();
        $convertedCustomers = (clone $customers)->where('status', CustomerStatus::CONVERTED->value)->count();
        $notInterestedCustomers = (clone $customers)->where('status', CustomerStatus::NOT_INTERESTED->value)->count();
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
                'status' => CustomerStatus::CONTACTED,
                'count' => $contactedCustomers,
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
                'status' => CustomerStatus::CONVERTED,
                'count' => $convertedCustomers,
            ],
            [
                'status' => CustomerStatus::NOT_INTERESTED,
                'count' => $notInterestedCustomers,
            ],
            [
                'status' => CustomerStatus::INVALID,
                'count' => $invalidCustomers,
            ],
        ];

        return $this->view([
            'totalCustomers' => $totalCustomers,
            'newCustomers' => $newCustomers,
            'contactedCustomers' => $contactedCustomers,
            'repliedCustomers' => $repliedCustomers,
            'interestedCustomers' => $interestedCustomers,
            'followUpCustomers' => $followUpCustomers,
            'convertedCustomers' => $convertedCustomers,
            'notInterestedCustomers' => $notInterestedCustomers,
            'invalidCustomers' => $invalidCustomers,
            'contactedToday' => $contactedToday,
            'contactedThisWeek' => $contactedThisWeek,
            'recentCustomers' => $recentCustomers,
            'activeTemplates' => $activeTemplates,
            'statusOverview' => $statusOverview,
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
                            Sudah Dihubungi
                        </flux:text>

                        <flux:heading size="xl" class="mt-1">
                            {{ number_format($contactedCustomers) }}
                        </flux:heading>

                        <flux:text class="mt-1 text-xs text-zinc-500">
                            Total customer contacted
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
                            Interested
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
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

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
                        'label' => 'Converted',
                        'value' => $convertedCustomers,
                        'icon' => 'check-circle',
                        'color' => 'emerald',
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
                class="relative overflow-hidden border-violet-200/70 bg-linear-to-br from-indigo-50/80 via-white to-violet-50/80 shadow-sm dark:border-violet-500/20 dark:from-indigo-500/10 dark:via-zinc-900 dark:to-violet-500/10">

                <div
                    class="pointer-events-none absolute -right-16 -top-16 size-40 rounded-full bg-violet-500/10 blur-3xl">
                </div>

                <div class="relative">

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