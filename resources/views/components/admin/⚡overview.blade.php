<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

new class extends Component {
    public function getOnlineUserIdsProperty(): array
    {
        return DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->pluck('user_id')
            ->unique()
            ->map(fn($id) => (int) $id)
            ->all();
    }

    public function getStatsProperty(): array
    {
        $memberQuery = User::query()->where('role', '!=', UserRole::ADMIN->value);

        return [
            'members' => (clone $memberQuery)->count(),
            'active_members' => (clone $memberQuery)->where('status', UserStatus::ACTIVE->value)->count(),
            'inactive_members' => (clone $memberQuery)->where('status', UserStatus::INACTIVE->value)->count(),
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
};
?>

<div class="space-y-6" wire:poll.10s>

    {{-- Header --}}
    <div class="m-5 overflow-hidden rounded-xl bg-white md:my-5 md:ms-2 md:me-5 dark:bg-zinc-800">
        <div class="flex items-center gap-4 p-6">
            <div
                class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-indigo-400 to-violet-500 text-white shadow-sm">
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

    {{-- Summary --}}
    <div class="m-5 grid gap-4 sm:grid-cols-2 md:my-5 md:ms-2 md:me-5 lg:grid-cols-5">

        @php
            $mainStats = [
                [
                    'label' => 'Total Member',
                    'value' => $this->stats['members'],
                    'description' => 'Semua member',
                    'icon' => 'users',
                    'color' => 'indigo',
                ],
                [
                    'label' => 'Member Aktif',
                    'value' => $this->stats['active_members'],
                    'description' => 'Akun dapat digunakan',
                    'icon' => 'check-circle',
                    'color' => 'green',
                    'indicator' => true,
                ],
                [
                    'label' => 'Member Nonaktif',
                    'value' => $this->stats['inactive_members'],
                    'description' => 'Akun tidak dapat digunakan',
                    'icon' => 'user-minus',
                    'color' => 'zinc',
                ],
                [
                    'label' => 'Total Customer',
                    'value' => $this->stats['customers'],
                    'description' => 'Data customer tersimpan',
                    'icon' => 'user-group',
                    'color' => 'blue',
                ],
                [
                    'label' => 'Total Template',
                    'value' => $this->stats['templates'],
                    'description' => 'Template pesan tersedia',
                    'icon' => 'chat-bubble-left-right',
                    'color' => 'violet',
                ],
            ];
        @endphp

        @foreach ($mainStats as $stat)
            <flux:card variant="soft"
                class="overflow-hidden bg-white p-5! transition duration-300 hover:-translate-y-1 dark:bg-zinc-800">
                <div class="flex items-start justify-between">

                    {{-- Content --}}
                    <div>
                        <flux:subheading class="font-semibold text-slate-500">
                            {{ $stat['label'] }}
                        </flux:subheading>

                        <flux:heading size="2xl" class="mt-2 font-bold text-slate-800">
                            {{ number_format($stat['value']) }}
                        </flux:heading>
                    </div>

                    {{-- Icon --}}
                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-{{ $stat['color'] }}-100 text-{{ $stat['color'] }}-600 transition duration-300 group-hover:scale-110 dark:bg-{{ $stat['color'] }}-500/10 dark:text-{{ $stat['color'] }}-400">
                        <flux:icon name="{{ $stat['icon'] }}" class="size-5" />
                    </div>
                </div>

                {{-- Description --}}
                @if ($stat['indicator'] ?? false)
                    <div class="mt-4 flex items-center gap-2 text-xs text-green-600 dark:text-green-400">
                        <span class="size-2 rounded-full bg-green-500"></span>
                        <span>{{ $stat['description'] }}</span>
                    </div>
                @else
                    <flux:text class="mt-4 text-xs font-normal text-slate-400">
                        {{ $stat['description'] }}
                    </flux:text>
                @endif
            </flux:card>
        @endforeach

    </div>

    {{-- Member Statistics --}}
    <flux:card class="m-5 overflow-hidden rounded-2xl border-none! bg-white md:my-5 md:ms-2 md:me-5 dark:bg-zinc-800">

        {{-- Table Header --}}
        <div class="flex flex-col gap-3 border-b pb-5 sm:flex-row sm:items-center sm:justify-between">
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
                        <tr wire:key="member-{{ $member->id }}"
                            class="border-b transition-colors last:border-0 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50">
                            {{-- Member --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if (in_array($member->id, $this->onlineUserIds, true))
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
