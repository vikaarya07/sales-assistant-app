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
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <flux:heading size="xl">
                Overview
            </flux:heading>

            <flux:text class="mt-1">
                Ringkasan aktivitas Sales WhatsApp Anda.
            </flux:text>
        </div>

        <div class="flex gap-2">
            <flux:button href="{{ route('customers') }}" wire:navigate variant="ghost" icon="users">
                Customers
            </flux:button>

            <flux:button href="{{ route('message-templates') }}" wire:navigate variant="primary" icon="document-text">
                Templates
            </flux:button>
        </div>
    </div>


    {{-- MAIN STATS --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- TOTAL CUSTOMER --}}
        <flux:card>
            <div class="flex items-start justify-between gap-4">
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

                <div class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon name="users" class="size-5 text-zinc-600 dark:text-zinc-300" />
                </div>
            </div>
        </flux:card>


        {{-- NEW --}}
        <flux:card>
            <div class="flex items-start justify-between gap-4">
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

                <div class="flex size-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-950/40">
                    <flux:icon name="user-plus" class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </flux:card>


        {{-- CONTACTED --}}
        <flux:card>
            <div class="flex items-start justify-between gap-4">
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

                <div class="flex size-10 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-950/40">
                    <flux:icon name="chat-bubble-left-right" class="size-5 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
        </flux:card>


        {{-- INTERESTED --}}
        <flux:card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <flux:text>
                        Interested
                    </flux:text>

                    <flux:heading size="xl" class="mt-1">
                        {{ number_format($interestedCustomers) }}
                    </flux:heading>

                    <flux:text class="mt-1 text-xs text-zinc-500">
                        Customer tertarik
                    </flux:text>
                </div>

                <div class="flex size-10 items-center justify-center rounded-xl bg-green-50 dark:bg-green-950/40">
                    <flux:icon name="sparkles" class="size-5 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </flux:card>

    </div>


    {{-- ACTIVITY --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <flux:card>
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon name="paper-airplane" class="size-5 text-zinc-600 dark:text-zinc-300" />
                </div>

                <div>
                    <flux:text class="text-xs">
                        Kontak Hari Ini
                    </flux:text>

                    <flux:heading size="lg">
                        {{ number_format($contactedToday) }}
                    </flux:heading>
                </div>
            </div>
        </flux:card>


        <flux:card>
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon name="calendar-days" class="size-5 text-zinc-600 dark:text-zinc-300" />
                </div>

                <div>
                    <flux:text class="text-xs">
                        Kontak Minggu Ini
                    </flux:text>

                    <flux:heading size="lg">
                        {{ number_format($contactedThisWeek) }}
                    </flux:heading>
                </div>
            </div>
        </flux:card>


        <flux:card>
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon name="arrow-path" class="size-5 text-zinc-600 dark:text-zinc-300" />
                </div>

                <div>
                    <flux:text class="text-xs">
                        Follow Up
                    </flux:text>

                    <flux:heading size="lg">
                        {{ number_format($followUpCustomers) }}
                    </flux:heading>
                </div>
            </div>
        </flux:card>


        <flux:card>
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon name="check-circle" class="size-5 text-zinc-600 dark:text-zinc-300" />
                </div>

                <div>
                    <flux:text class="text-xs">
                        Converted
                    </flux:text>

                    <flux:heading size="lg">
                        {{ number_format($convertedCustomers) }}
                    </flux:heading>
                </div>
            </div>
        </flux:card>

    </div>


    {{-- STATUS + QUICK ACTION --}}
    <div class="grid gap-6 lg:grid-cols-3">

        {{-- STATUS OVERVIEW --}}
        <flux:card class="lg:col-span-2">

            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="lg">
                        Customer Status
                    </flux:heading>

                    <flux:text class="mt-1">
                        Distribusi status customer Anda.
                    </flux:text>
                </div>

                <flux:button href="{{ route('customers') }}" wire:navigate variant="ghost" size="sm">
                    Lihat Semua
                </flux:button>
            </div>


            <div class="mt-6 space-y-4">

                @foreach ($statusOverview as $item)
                    @php
                        $status = $item['status'];
                        $count = $item['count'];

                        $percentage = $totalCustomers > 0 ? round(($count / $totalCustomers) * 100) : 0;
                    @endphp

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">

                            <div class="flex items-center gap-2">
                                <flux:badge :color="$status->color()" size="sm">
                                    {{ $status->label() }}
                                </flux:badge>
                            </div>

                            <div class="text-xs text-zinc-500">
                                {{ number_format($count) }}
                                <span class="mx-1">·</span>
                                {{ $percentage }}%
                            </div>

                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-full rounded-full bg-zinc-900 transition-all dark:bg-zinc-100"
                                style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach

            </div>
        </flux:card>


        {{-- QUICK ACTION --}}
        <flux:card>

            <flux:heading size="lg">
                Quick Actions
            </flux:heading>

            <flux:text class="mt-1">
                Akses fitur yang sering digunakan.
            </flux:text>


            <div class="mt-6 space-y-3">

                <flux:button href="{{ route('customers') }}" wire:navigate variant="outline"
                    class="w-full justify-start" icon="user-plus">
                    Import Customer
                </flux:button>

                <flux:button href="{{ route('customers') }}" wire:navigate variant="outline"
                    class="w-full justify-start" icon="chat-bubble-left-right">
                    Hubungi Customer
                </flux:button>

                <flux:button href="{{ route('message-templates') }}" wire:navigate variant="outline"
                    class="w-full justify-start" icon="document-plus">
                    Buat Template
                </flux:button>

            </div>

        </flux:card>

    </div>


    {{-- RECENT CUSTOMERS + ACTIVE TEMPLATES --}}
    <div class="grid gap-6 lg:grid-cols-2">

        {{-- RECENT CUSTOMERS --}}
        <flux:card class="overflow-hidden">

            <div class="flex items-center justify-between">

                <div>
                    <flux:heading size="lg">
                        Customer Terbaru
                    </flux:heading>

                    <flux:text class="mt-1">
                        Customer yang terakhir ditambahkan.
                    </flux:text>
                </div>

                <flux:button href="{{ route('customers') }}" wire:navigate variant="ghost" size="sm">
                    Lihat Semua
                </flux:button>

            </div>


            <div class="mt-5 divide-y divide-zinc-100 dark:divide-zinc-800">

                @forelse ($recentCustomers as $customer)
                    <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">

                        <flux:avatar :name="$customer->name" size="sm" />

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
        <flux:card class="overflow-hidden">

            <div class="flex items-center justify-between">

                <div>
                    <flux:heading size="lg">
                        Template Aktif
                    </flux:heading>

                    <flux:text class="mt-1">
                        Template yang tersedia untuk digunakan.
                    </flux:text>
                </div>

                <flux:button href="{{ route('message-templates') }}" wire:navigate variant="ghost" size="sm">
                    Kelola
                </flux:button>

            </div>


            <div class="mt-5 divide-y divide-zinc-100 dark:divide-zinc-800">

                @forelse ($activeTemplates as $template)
                    <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">

                        <div
                            class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">

                            <flux:icon name="document-text" class="size-4 text-zinc-600 dark:text-zinc-300" />

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
