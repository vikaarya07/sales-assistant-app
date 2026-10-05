<?php

use App\Enums\MusicGenre;
use App\Models\Music;
use App\Models\MusicRequest;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public string $genre = '';

    public bool $showRequestModal = false;

    public bool $showRequestListModal = false;

    public string $requestNote = '';

    // Lifecycle

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);
    }

    // Search

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // Genre

    public function updatedGenre(): void
    {
        $this->resetPage();
    }

    // Music Request

    public function openRequest(): void
    {
        $this->resetRequestForm();

        $this->showRequestModal = true;
    }

    public function openRequestList(): void
    {
        $this->showRequestListModal = true;
    }

    private function resetRequestForm(): void
    {
        $this->resetValidation();

        $this->reset('requestNote');
    }

    public function submitRequest(): void
    {
        $this->validate(
            [
                'requestNote' => ['required', 'string', 'max:1000'],
            ],
            [
                'requestNote.required' => 'Permintaan music wajib diisi.',
                'requestNote.max' => 'Permintaan maksimal 1000 karakter.',
            ],
        );

        MusicRequest::create([
            'user_id' => auth()->id(),
            'note' => $this->requestNote,
            'status' => 'pending',
        ]);

        $this->resetRequestForm();

        $this->showRequestModal = false;

        $this->dispatch('swal', type: 'success', title: 'Request Terkirim', message: 'Request music berhasil dikirim kepada admin.');
    }

    // Render

    public function render()
    {
        $music = Music::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query->where('title', 'like', "%{$this->search}%")->orWhere('filename', 'like', "%{$this->search}%");
                });
            })
            ->when($this->genre !== '', function ($query) {
                $query->where('genre', $this->genre);
            })
            ->latest()
            ->paginate(10);

        $requests = MusicRequest::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return $this->view([
            'music' => $music,
            'requests' => $requests,
            'genres' => MusicGenre::cases(),
        ]);
    }
};
?>

<div class="space-y-6">

    {{-- Header --}}
    <div class="m-5 rounded-xl bg-white md:my-5 md:ms-2 md:me-5 dark:bg-zinc-800">
        <div class="flex flex-col items-center justify-between gap-4 p-6 sm:flex-row">
            <div class="flex items-center gap-3">
                <div
                    class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-indigo-400 to-violet-500 text-white">
                    <flux:icon name="musical-note" class="size-6" />
                </div>

                <div>
                    <flux:heading size="xl">
                        Music
                    </flux:heading>

                    <flux:text class="mt-1">
                        Dengarkan music dan request lagu favorit Anda.
                    </flux:text>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <flux:card class="m-5 border-none! md:my-5 md:ms-2 md:me-5">
        <div class="flex flex-col gap-3 lg:flex-row items-center">

            {{-- Search --}}
            <div class="min-w-0 flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari music..."
                    icon="magnifying-glass" />
            </div>

            {{-- Genre --}}
            <div class="w-full lg:w-52">
                <flux:select wire:model.live="genre">
                    <flux:select.option value="">
                        Semua Genre
                    </flux:select.option>

                    @foreach ($genres as $item)
                        <flux:select.option value="{{ $item->value }}">
                            {{ $item->label() }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            {{-- Actions --}}
            <div class="flex shrink-0 gap-2">
                <flux:button wire:click="openRequest" variant="primary" color="emerald" icon="paper-airplane"
                    size="sm">
                    Request Lagu
                </flux:button>

                <flux:button wire:click="openRequestList" variant="ghost" icon="clipboard-document-list" size="sm">
                    Daftar Request
                </flux:button>
            </div>
        </div>
    </flux:card>

    {{-- Music List --}}
    <div class="m-5 grid grid-cols-1 gap-4 rounded-xl md:my-5 md:ms-2 md:me-5 md:grid-cols-2">

        @forelse ($music as $item)
            <flux:card wire:key="music-{{ $item->id }}"
                class="m-0! rounded-2xl border-none! bg-white p-3 dark:bg-zinc-800">
                <div class="flex items-center gap-3">

                    {{-- Play / Pause --}}
                    <button type="button" data-music-play data-id="{{ $item->id }}" data-url="{{ $item->url }}"
                        data-title="{{ $item->title }}"
                        class="group relative flex size-10 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white transition-all duration-200 hover:scale-105 hover:bg-indigo-700 active:scale-95"
                        aria-label="Play {{ $item->title }}">
                        <flux:icon name="play" data-music-icon="play"
                            class="size-4.5 translate-x-0.5 transition-all duration-200" />

                        <flux:icon name="pause" data-music-icon="pause"
                            class="absolute size-4.5 scale-0 opacity-0 transition-all duration-200" />
                    </button>

                    {{-- Content --}}
                    <div class="min-w-0 flex-1">

                        {{-- Title + Genre --}}
                        <div class="flex items-center gap-2">
                            <flux:heading size="sm" class="min-w-0 flex-1 truncate">
                                {{ $item->title }}
                            </flux:heading>

                            <flux:badge :color="$item->genre->color()" size="sm" class="shrink-0">
                                {{ $item->genre->label() }}
                            </flux:badge>

                            @if ($item->duration)
                                <flux:text class="shrink-0 text-xs">
                                    {{ $item->formatted_duration }}
                                </flux:text>
                            @endif
                        </div>

                        {{-- Waveform --}}
                        <div class="mt-1.5 w-full min-w-0">

                            <div id="waveform-{{ $item->id }}" data-waveform data-url="{{ $item->url }}"
                                data-id="{{ $item->id }}" class="h-8 w-full cursor-pointer overflow-hidden"
                                title="Klik waveform untuk mengatur posisi audio"></div>

                            {{-- Time --}}
                            <div
                                class="mt-0.5 flex items-center justify-between text-[11px] leading-none text-zinc-500">
                                <span data-music-current-time="{{ $item->id }}">
                                    00:00
                                </span>

                                <span data-music-duration="{{ $item->id }}">
                                    00:00
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
            </flux:card>

        @empty
            <div
                class="col-span-full rounded-2xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-700">
                <flux:icon name="musical-note" class="mx-auto size-10 text-zinc-400" />

                <flux:heading size="sm" class="mt-3">
                    Belum ada music
                </flux:heading>

                <flux:text class="mt-1">
                    Belum ada music yang tersedia.
                </flux:text>
            </div>
        @endforelse

    </div>

    {{ $music->links() }}

    {{-- Request List Modal --}}
    <flux:modal wire:model="showRequestListModal" class="w-full max-w-2xl" :dismissible="true">
        <div class="space-y-6">

            {{-- Header --}}
            <div>
                <flux:heading size="lg">
                    Daftar Request Saya
                </flux:heading>

                <flux:text class="mt-1">
                    Riwayat permintaan music yang pernah Anda kirim.
                </flux:text>
            </div>

            {{-- Request List --}}
            <div class="max-h-[60vh] space-y-3 overflow-y-auto">

                @forelse ($requests as $request)
                    <div wire:key="my-music-request-{{ $request->id }}"
                        class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="flex items-start justify-between gap-4">

                            <div class="min-w-0 flex-1">
                                <flux:text class="text-sm font-medium">
                                    {{ $request->note }}
                                </flux:text>

                                <flux:text class="mt-1 text-xs text-zinc-500">
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

                                @default
                                    <flux:badge>
                                        {{ ucfirst($request->status) }}
                                    </flux:badge>
                            @endswitch

                        </div>
                    </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-700">
                            <flux:icon name="paper-airplane" class="mx-auto size-10 text-zinc-400" />

                            <flux:heading size="sm" class="mt-3">
                                Belum ada request
                            </flux:heading>

                            <flux:text class="mt-1">
                                Anda belum pernah mengirim request music.
                            </flux:text>
                        </div>
                    @endforelse

                </div>

                {{-- Footer --}}
                <div class="flex justify-end">
                    <flux:button type="button" wire:click="$set('showRequestListModal', false)">
                        Tutup
                    </flux:button>
                </div>

            </div>
        </flux:modal>

        {{-- Request Modal --}}
        <flux:modal wire:model="showRequestModal" class="w-full max-w-2xl" :dismissible="false">
            <form wire:submit="submitRequest" class="space-y-6">

                <div>
                    <flux:heading size="lg">
                        Request Music
                    </flux:heading>

                    <flux:text class="mt-1">
                        Sampaikan permintaan lagu kepada admin.
                    </flux:text>
                </div>

                <flux:textarea wire:model="requestNote" label="Catatan" placeholder="Contoh: Mohon tambahkan lagu ini..."
                    rows="4" />

                <div class="flex justify-end gap-2">
                    <flux:button type="button" wire:click="$set('showRequestModal', false)">
                        Batal
                    </flux:button>

                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                        wire:target="submitRequest">
                        Kirim Request
                    </flux:button>
                </div>

            </form>
        </flux:modal>

    </div>
