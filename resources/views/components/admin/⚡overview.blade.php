<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

new class extends Component {
    public function refreshOnlineStatus(): void
    {
        // Sengaja kosong.
        // Polling Livewire akan menjalankan request ulang
        // sehingga data member dan last_activity_at diperbarui.
    }

    public function isOnline(User $member): bool
    {
        return $member->last_activity_at?->greaterThan(now()->subSeconds(90)) ?? false;
    }

    public function getStatsProperty(): array
    {
        return [
            'members' => User::where('role', '!=', UserRole::ADMIN->value)->count(),

            'active_members' => User::query()->where('role', '!=', UserRole::ADMIN->value)->where('status', UserStatus::ACTIVE->value)->count(),

            'inactive_members' => User::query()->where('role', '!=', UserRole::ADMIN->value)->where('status', UserStatus::INACTIVE->value)->count(),

            'customers' => DB::table('customers')->count(),

            'templates' => DB::table('message_templates')->count(),
        ];
    }

    public function getMembersProperty()
    {
        return User::query()
            ->where('role', '!=', UserRole::ADMIN->value)
            ->withCount(['customers', 'messageTemplates'])
            ->orderBy('name')
            ->get();
    }

    public function roleLabel(UserRole|string $role): string
    {
        $value = $role instanceof UserRole ? $role->value : $role;

        return match ($value) {
            UserRole::ADMIN->value => 'Admin',
            UserRole::PRIORITAS_DANA->value => 'Prioritas Dana',
            UserRole::LANDING_PAGE->value => 'Landing Page',
            UserRole::MULTIGUNA->value => 'Multiguna',
            default => ucfirst(str_replace('_', ' ', $value)),
        };
    }
};
?>

<div class="space-y-6" wire:poll.10s="refreshOnlineStatus">

    {{-- Header --}}
    <div class="m-5 md:my-5 md:ms-2 md:me-5 overflow-hidden rounded-2xl bg-white dark:bg-zinc-800">

        <div class="relative overflow-hidden p-6">

            {{-- Decorative background --}}
            <div class="pointer-events-none absolute -right-12 -top-16 size-48 rounded-full bg-indigo-500/10 blur-3xl">
            </div>
            <div class="pointer-events-none absolute -bottom-20 right-20 size-40 rounded-full bg-violet-500/10 blur-3xl">
            </div>

            <div class="relative flex items-center gap-4">

                <div
                    class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-500 to-violet-600 text-white shadow-sm">
                    <flux:icon name="chart-bar" class="size-6" />
                </div>

                <div>
                    <flux:heading size="xl">
                        Overview
                    </flux:heading>

                    <flux:text class="mt-1">
                        Pantau aktivitas dan perkembangan member Sales Assistant.
                    </flux:text>
                </div>

            </div>

        </div>

    </div>

    {{-- Summary --}}
    <div class="m-5 md:my-5 md:ms-2 md:me-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">

        {{-- Total Member --}}
        <flux:card class="group relative overflow-hidden border-none! p-5">

            <div class="flex items-start justify-between">

                <div>
                    <flux:subheading>
                        Total Member
                    </flux:subheading>

                    <flux:heading size="2xl" class="mt-2">
                        {{ number_format($this->stats['members']) }}
                    </flux:heading>
                </div>

                <div
                    class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                    <flux:icon name="users" class="size-5" />
                </div>

            </div>

            <div class="mt-4 flex items-center gap-2 text-xs text-zinc-500">
                <span>
                    Semua member
                </span>
            </div>

        </flux:card>

        {{-- Member Aktif --}}
        <flux:card class="group relative overflow-hidden border-none! p-5">

            <div class="flex items-start justify-between">

                <div>
                    <flux:subheading>
                        Member Aktif
                    </flux:subheading>

                    <flux:heading size="2xl" class="mt-2">
                        {{ number_format($this->stats['active_members']) }}
                    </flux:heading>
                </div>

                <div
                    class="flex size-10 items-center justify-center rounded-xl bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400">
                    <flux:icon name="check-circle" class="size-5" />
                </div>

            </div>

            <div class="mt-4 flex items-center gap-2 text-xs text-green-600 dark:text-green-400">
                <span class="size-2 rounded-full bg-green-500"></span>
                <span>Akun dapat digunakan</span>
            </div>

        </flux:card>

        {{-- Member Nonaktif --}}
        <flux:card class="group relative overflow-hidden border-none! p-5">

            <div class="flex items-start justify-between">

                <div>
                    <flux:subheading>
                        Nonaktif
                    </flux:subheading>

                    <flux:heading size="2xl" class="mt-2">
                        {{ number_format($this->stats['inactive_members']) }}
                    </flux:heading>
                </div>

                <div
                    class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400">
                    <flux:icon name="user-minus" class="size-5" />
                </div>

            </div>

            <div class="mt-4 flex items-center gap-2 text-xs text-zinc-500">
                <span>
                    Akun tidak dapat digunakan
                </span>
            </div>

        </flux:card>

        {{-- Customer --}}
        <flux:card class="group relative overflow-hidden border-none! p-5">

            <div class="flex items-start justify-between">

                <div>
                    <flux:subheading>
                        Total Customer
                    </flux:subheading>

                    <flux:heading size="2xl" class="mt-2">
                        {{ number_format($this->stats['customers']) }}
                    </flux:heading>
                </div>

                <div
                    class="flex size-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <flux:icon name="user-group" class="size-5" />
                </div>

            </div>

            <div class="mt-4 flex items-center gap-2 text-xs text-zinc-500">
                <span>
                    Data customer tersimpan
                </span>
            </div>

        </flux:card>

        {{-- Template --}}
        <flux:card class="group relative overflow-hidden border-none! p-5">

            <div class="flex items-start justify-between">

                <div>
                    <flux:subheading>
                        Total Template
                    </flux:subheading>

                    <flux:heading size="2xl" class="mt-2">
                        {{ number_format($this->stats['templates']) }}
                    </flux:heading>
                </div>

                <div
                    class="flex size-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                    <flux:icon name="chat-bubble-left-right" class="size-5" />
                </div>

            </div>

            <div class="mt-4 flex items-center gap-2 text-xs text-zinc-500">
                <span>
                    Template pesan tersedia
                </span>
            </div>

        </flux:card>

    </div>

    {{-- Member Statistics --}}
    <flux:card class="m-5 md:my-5 md:ms-2 md:me-5 overflow-hidden rounded-2xl bg-white dark:bg-zinc-800 border-none!">

        {{-- Table Header --}}
        <div class="flex flex-col gap-4 border-b pb-5 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <flux:heading size="lg">
                    Data Member
                </flux:heading>

                <flux:subheading>
                    Pantau status dan aktivitas masing-masing member.
                </flux:subheading>
            </div>

            <div class="flex items-center gap-2 text-sm text-zinc-500">
                <flux:icon name="users" class="size-4" />

                {{ number_format($this->stats['members']) }} member
            </div>

        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead>
                    <tr class="border-b bg-zinc-50/80 dark:bg-zinc-800/50">

                        <th class="px-5 py-3 text-left font-medium">
                            Member
                        </th>

                        <th class="px-5 py-3 text-left font-medium">
                            Role
                        </th>

                        <th class="px-5 py-3 text-left font-medium">
                            Status
                        </th>

                        <th class="px-5 py-3 text-right font-medium">
                            Customer
                        </th>

                        <th class="px-5 py-3 text-right font-medium">
                            Template
                        </th>

                    </tr>
                </thead>

                <tbody>

                    @forelse ($this->members as $member)
                        <tr
                            class="border-b transition-colors last:border-0 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50">

                            {{-- Member --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    @if ($this->isOnline($member))
                                        <flux:avatar :name="$member->name" :initials="$member->initials()"
                                            color="auto" circle badge badge:circle badge:color="green" />
                                    @else
                                        <flux:avatar :name="$member->name" :initials="$member->initials()"
                                            color="auto" circle badge badge:circle badge:color="zinc" />
                                    @endif

                                    <div class="min-w-0">

                                        <div class="truncate font-medium">
                                            {{ $member->name }}
                                        </div>

                                        <span class="truncate text-zinc-500">
                                            {{ $member->email }}
                                        </span>

                                    </div>

                                </div>

                            </td>

                            {{-- Role --}}
                            <td class="px-5 py-4">

                                <flux:badge :color="$member->role->color()">
                                    {{ $member->role->label() }}
                                </flux:badge>

                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-4">
                                <flux:badge :color="$member->status->color()">
                                    {{ $member->status->label() }}
                                </flux:badge>
                            </td>

                            </td>

                            {{-- Customer --}}
                            <td class="px-5 py-4 text-right">

                                <div class="inline-flex items-center gap-2 font-medium">
                                    <flux:icon name="users" class="size-4 text-zinc-400" />

                                    {{ number_format($member->customers_count) }}
                                </div>

                            </td>

                            {{-- Template --}}
                            <td class="px-5 py-4 text-right">

                                <div class="inline-flex items-center gap-2 font-medium">
                                    <flux:icon name="chat-bubble-left-right" class="size-4 text-zinc-400" />

                                    {{ number_format($member->message_templates_count) }}
                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="px-5 py-12 text-center">

                                <div class="flex flex-col items-center">

                                    <div
                                        class="flex size-12 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-400 dark:bg-zinc-700">
                                        <flux:icon name="users" class="size-6" />
                                    </div>

                                    <flux:heading size="sm" class="mt-4">
                                        Belum ada member
                                    </flux:heading>

                                    <flux:subheading class="mt-1">
                                        Data member akan tampil di sini.
                                    </flux:subheading>

                                </div>

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </flux:card>

</div>
