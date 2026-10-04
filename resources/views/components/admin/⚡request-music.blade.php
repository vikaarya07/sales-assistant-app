<?php

use App\Models\MusicRequest;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public ?int $approvingRequestId = null;

    public ?int $rejectingRequestId = null;

    public ?int $deletingRequestId = null;

    // Lifecycle

    public function mount(): void
    {
        $this->adminOnly();
    }

    // Authorization

    private function adminOnly(): void
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);
    }

    // Approve Request

    public function confirmApproveRequest(int $requestId): void
    {
        $this->adminOnly();

        $request = MusicRequest::with('user')->where('status', 'pending')->findOrFail($requestId);

        $this->approvingRequestId = $request->id;

        $this->dispatch('confirm', title: 'Setujui Request?', message: "Request dari {$request->user->name} akan disetujui.", action: 'approve-request-confirmed');
    }

    #[On('approve-request-confirmed')]
    public function approveRequest(): void
    {
        $this->adminOnly();

        if (!$this->approvingRequestId) {
            return;
        }

        $request = MusicRequest::findOrFail($this->approvingRequestId);

        if ($request->status !== 'pending') {
            $this->approvingRequestId = null;

            return;
        }

        $request->update([
            'status' => 'approved',
        ]);

        $this->approvingRequestId = null;

        $this->dispatch('swal', type: 'success', title: 'Request Disetujui', message: 'Request music berhasil disetujui.');
    }

    // Reject Request

    public function confirmRejectRequest(int $requestId): void
    {
        $this->adminOnly();

        $request = MusicRequest::with('user')->where('status', 'pending')->findOrFail($requestId);

        $this->rejectingRequestId = $request->id;

        $this->dispatch('confirm', title: 'Tolak Request?', message: "Request dari {$request->user->name} akan ditolak.", action: 'reject-request-confirmed');
    }

    #[On('reject-request-confirmed')]
    public function rejectRequest(): void
    {
        $this->adminOnly();

        if (!$this->rejectingRequestId) {
            return;
        }

        $request = MusicRequest::findOrFail($this->rejectingRequestId);

        if ($request->status !== 'pending') {
            $this->rejectingRequestId = null;

            return;
        }

        $request->update([
            'status' => 'rejected',
        ]);

        $this->rejectingRequestId = null;

        $this->dispatch('swal', type: 'success', title: 'Request Ditolak', message: 'Request music berhasil ditolak.');
    }

    // Delete Request

    public function confirmDeleteRequest(int $requestId): void
    {
        $this->adminOnly();

        $request = MusicRequest::findOrFail($requestId);

        $this->deletingRequestId = $request->id;

        $this->dispatch('confirm', title: 'Hapus Request?', message: 'Request ini akan dihapus secara permanen.', action: 'delete-request-confirmed');
    }

    #[On('delete-request-confirmed')]
    public function deleteRequest(): void
    {
        $this->adminOnly();

        if (!$this->deletingRequestId) {
            return;
        }

        $request = MusicRequest::findOrFail($this->deletingRequestId);

        $request->delete();

        $this->deletingRequestId = null;

        $this->dispatch('swal', type: 'success', title: 'Berhasil', message: 'Request berhasil dihapus.');
    }

    // Render

    public function render()
    {
        $requests = MusicRequest::query()->with('user')->latest()->paginate(10);

        $pendingCount = MusicRequest::where('status', 'pending')->count();

        $approvedCount = MusicRequest::where('status', 'approved')->count();

        $rejectedCount = MusicRequest::where('status', 'rejected')->count();

        return $this->view([
            'requests' => $requests,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
        ]);
    }
};
?>

<div class="space-y-6">

    {{-- Header --}}
    <div class="m-5 rounded-xl bg-white md:my-5 md:ms-2 md:me-5 dark:bg-zinc-800">
        <div class="flex flex-col items-start justify-between gap-4 p-6 sm:flex-row sm:items-center">
            <div class="flex items-center gap-3">
                <div
                    class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-indigo-400 to-violet-500 text-white">
                    <flux:icon name="paper-airplane" class="size-6" />
                </div>

                <div>
                    <flux:heading size="xl">
                        Request Music
                    </flux:heading>

                    <flux:text class="mt-1">
                        Kelola permintaan music dari user.
                    </flux:text>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary --}}
    @php
        $summary = [
            [
                'label' => 'Total Request',
                'value' => $requests->total(),
                'icon' => 'inbox',
                'color' => 'indigo',
            ],
            [
                'label' => 'Menunggu',
                'value' => $pendingCount,
                'icon' => 'clock',
                'color' => 'amber',
            ],
            [
                'label' => 'Disetujui',
                'value' => $approvedCount,
                'icon' => 'check-circle',
                'color' => 'green',
            ],
            [
                'label' => 'Ditolak',
                'value' => $rejectedCount,
                'icon' => 'x-circle',
                'color' => 'red',
            ],
        ];
    @endphp

    <div class="mx-5 grid gap-4 md:ms-2 md:me-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($summary as $item)
            <flux:card class="border-none!">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-10 items-center justify-center rounded-xl bg-{{ $item['color'] }}-100 text-{{ $item['color'] }}-600 dark:bg-{{ $item['color'] }}-500/10 dark:text-{{ $item['color'] }}-400'">
                        <flux:icon :name="$item['icon']" class="size-5" />
                    </div>

                    <div>
                        <flux:text class="text-sm">
                            {{ $item['label'] }}
                        </flux:text>

                        <flux:heading size="lg">
                            {{ $item['value'] }}
                        </flux:heading>
                    </div>
                </div>
            </flux:card>
        @endforeach
    </div>

    {{-- Request List --}}
    <div class="m-5 rounded-xl bg-white md:my-5 md:ms-2 md:me-5 dark:bg-zinc-800">

        <div class="border-b border-zinc-200 p-5 dark:border-zinc-700">
            <flux:heading size="lg">
                Semua Request
            </flux:heading>

            <flux:text class="mt-1">
                Kelola seluruh permintaan music dari user.
            </flux:text>
        </div>

        <div class="grid grid-cols-1 gap-3 p-5 md:grid-cols-2">

            @forelse ($requests as $request)
                <div wire:key="music-request-{{ $request->id }}"
                    class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                    <div class="space-y-4">

                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-4">
                            <div class="w-full">
                                <flux:text class="text-sm">
                                    Diminta oleh :
                                    <span class="text-slate-700 font-bold">
                                        {{ $request->user->name }}
                                    </span>
                                </flux:text>

                                <flux:text class="text-xs text-zinc-500">
                                    {{ $request->created_at->translatedFormat('d F Y, H:i') }}
                                </flux:text>
                            </div>

                            @switch($request->status)
                                @case('pending')
                                    <flux:badge color="amber">
                                        Menunggu
                                    </flux:badge>
                                @break

                                @case('approved')
                                    <flux:badge color="green">
                                        Disetujui
                                    </flux:badge>
                                @break

                                @case('rejected')
                                    <flux:badge color="red">
                                        Ditolak
                                    </flux:badge>
                                @break
                            @endswitch
                        </div>

                        {{-- Request --}}
                        <div class="rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800">
                            <flux:text class="text-sm">
                                {{ $request->note }}
                            </flux:text>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-between gap-2">

                            {{-- Status Actions --}}
                            <div class="flex items-center gap-2">
                                @if ($request->status === 'pending')
                                    <flux:button wire:click="confirmApproveRequest({{ $request->id }})"
                                        variant="primary" color="indigo" size="sm" wire:loading.attr="disabled">
                                        Setujui
                                    </flux:button>

                                    <flux:button wire:click="confirmRejectRequest({{ $request->id }})"
                                        variant="primary" color="red" size="sm" wire:loading.attr="disabled">
                                        Tolak
                                    </flux:button>
                                @endif
                            </div>

                            {{-- Delete --}}
                            <flux:button wire:click="confirmDeleteRequest({{ $request->id }})" variant="ghost"
                                icon="trash" color="red" size="sm" wire:loading.attr="disabled"
                                tooltip="Hapus request" />
                        </div>

                    </div>
                </div>

                @empty

                    <div
                        class="col-span-full rounded-2xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-700">
                        <flux:icon name="paper-airplane" class="mx-auto size-10 text-zinc-400" />

                        <flux:heading size="sm" class="mt-3">
                            Belum ada request
                        </flux:heading>

                        <flux:text class="mt-1">
                            Belum ada request music dari user.
                        </flux:text>
                    </div>
                @endforelse

            </div>

            <div class="p-5 pt-0">
                {{ $requests->links() }}
            </div>

        </div>

    </div>
