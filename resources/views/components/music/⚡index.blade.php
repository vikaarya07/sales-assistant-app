<?php

use App\Models\Music;
use App\Models\MusicRequest;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new class extends Component {
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public bool $showUploadModal = false;

    public string $title = '';

    public $audio;

    public ?int $editingMusicId = null;

    public string $editTitle = '';

    public bool $showRequestModal = false;

    public string $requestNote = '';

    public bool $showRequestsModal = false;

    public function mount(): void
    {
        $this->authorizeAccess();
    }

    private function authorizeAccess(): void
    {
        abort_unless(auth()->check(), 403);
    }

    private function isAdmin(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openUpload(): void
    {
        abort_unless($this->isAdmin(), 403);

        $this->resetValidation();

        $this->title = '';
        $this->audio = null;
        $this->showUploadModal = true;
    }

    public function saveMusic(): void
    {
        abort_unless($this->isAdmin(), 403);

        $this->validate(
            [
                'title' => ['required', 'string', 'max:255'],
                'audio' => ['required', 'file', 'mimes:ogg', 'max:20480'],
            ],
            [
                'title.required' => 'Judul music wajib diisi.',
                'title.max' => 'Judul music maksimal 255 karakter.',
                'audio.required' => 'File .ogg wajib dipilih.',
                'audio.file' => 'File music tidak valid.',
                'audio.mimes' => 'File music harus berformat .ogg.',
                'audio.max' => 'Ukuran file maksimal 20 MB.',
            ],
        );

        $filename = $this->audio->hashName();

        $path = $this->audio->storeAs('music', $filename, 'public');

        Music::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'filename' => $this->audio->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $this->audio->getMimeType() ?: 'audio/ogg',
            'size' => $this->audio->getSize(),
        ]);

        $this->reset(['title', 'audio']);

        $this->showUploadModal = false;

        session()->flash('success', 'Music berhasil ditambahkan.');
    }

    public function openEdit(int $musicId): void
    {
        abort_unless($this->isAdmin(), 403);

        $music = Music::findOrFail($musicId);

        $this->editingMusicId = $music->id;
        $this->editTitle = $music->title;

        $this->resetValidation();

        $this->dispatch('open-edit-music-modal');
    }

    public function updateMusic(): void
    {
        abort_unless($this->isAdmin(), 403);

        $this->validate(
            [
                'editTitle' => ['required', 'string', 'max:255'],
            ],
            [
                'editTitle.required' => 'Judul music wajib diisi.',
                'editTitle.max' => 'Judul music maksimal 255 karakter.',
            ],
        );

        $music = Music::findOrFail($this->editingMusicId);

        $music->update([
            'title' => $this->editTitle,
        ]);

        $this->dispatch('close-edit-music-modal');

        session()->flash('success', 'Music berhasil diperbarui.');
    }

    public function deleteMusic(int $musicId): void
    {
        abort_unless($this->isAdmin(), 403);

        $music = Music::findOrFail($musicId);

        if ($music->path) {
            Storage::disk('public')->delete($music->path);
        }

        $music->delete();

        session()->flash('success', 'Music berhasil dihapus.');
    }

    public function openRequest(): void
    {
        abort_unless(auth()->check(), 403);

        $this->requestNote = '';
        $this->resetValidation();
        $this->showRequestModal = true;
    }

    public function submitRequest(): void
    {
        abort_unless(auth()->check(), 403);

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

        $this->reset('requestNote');
        $this->showRequestModal = false;

        session()->flash('success', 'Request music berhasil dikirim.');
    }

    public function approveRequest(int $requestId): void
    {
        abort_unless($this->isAdmin(), 403);

        $request = MusicRequest::findOrFail($requestId);

        $request->update([
            'status' => 'approved',
        ]);

        session()->flash('success', 'Request disetujui.');
    }

    public function rejectRequest(int $requestId): void
    {
        abort_unless($this->isAdmin(), 403);

        $request = MusicRequest::findOrFail($requestId);

        $request->update([
            'status' => 'rejected',
        ]);

        session()->flash('success', 'Request ditolak.');
    }

    public function deleteRequest(int $requestId): void
    {
        abort_unless($this->isAdmin(), 403);

        $request = MusicRequest::findOrFail($requestId);

        $request->delete();

        session()->flash('success', 'Request berhasil dihapus.');
    }

    public function render()
    {
        $music = Music::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')->orWhere('filename', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        $requests = $this->isAdmin()
            ? MusicRequest::query()
                ->with(['user'])
                ->where('status', 'pending')
                ->latest()
                ->get()
            : collect();

        return $this->view([
            'music' => $music,
            'requests' => $requests,
            'isAdmin' => $this->isAdmin(),
        ]);
    }
};
?>

<div x-data x-on:open-edit-music-modal.window="$flux.modal('edit-music').show()"
    x-on:close-edit-music-modal.window="$flux.modal('edit-music').close()" class="space-y-6">
    {{-- HEADER --}}
    <div class="m-5 md:my-5 md:ms-2 md:me-5 rounded-xl bg-white dark:bg-zinc-800">
        <div class="mx-auto flex flex-col items-center justify-between gap-4 p-6 sm:flex-row">
            <div class="flex items-center gap-3">
                <div
                    class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-indigo-400 to-violet-500 text-white">
                    <flux:icon name="users" class="size-6" />
                </div>

                <div>
                    <flux:heading size="xl">
                        Music
                    </flux:heading>

                    <flux:text class="mt-1">
                        Dengarkan music dan kelola koleksi audio.
                    </flux:text>
                </div>
            </div>

            @if ($isAdmin)
                <flux:button wire:click="openUpload" variant="primary" icon="plus">
                    Upload Music
                </flux:button>
            @endif
        </div>
    </div>

    {{-- FLASH --}}
    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('success') }}
        </flux:callout>
    @endif

    {{-- SEARCH --}}
    <div class="m-5 md:my-5 md:ms-2 md:me-5 flex items-center gap-5">
        <div class="min-w-0 flex-1">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari music..." icon="magnifying-glass" />
        </div>

        @if (!$isAdmin)
            <flux:button wire:click="openRequest" variant="primary" icon="paper-airplane" size="sm">
                Request Lagu
            </flux:button>
        @endif
    </div>

    {{-- MUSIC LIST --}}
    <div class="m-5 md:my-5 md:ms-2 md:me-5 grid grid-cols-1 gap-2 space-y-3 rounded-xl md:grid-cols-2">
        @forelse ($music as $item)
            <div wire:key="music-{{ $item->id }}"
                class="m-0! rounded-2xl border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center gap-3">

                    {{-- PLAY / PAUSE --}}
                    <button type="button" data-music-play data-id="{{ $item->id }}" data-url="{{ $item->url }}"
                        data-title="{{ $item->title }}"
                        class="group relative flex size-10 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white transition-all duration-200 hover:scale-105 hover:bg-indigo-700 active:scale-95"
                        aria-label="Play {{ $item->title }}">
                        <flux:icon name="play" data-music-icon="play"
                            class="size-4.5 translate-x-0.5 transition-all duration-200" />

                        <flux:icon name="pause" data-music-icon="pause"
                            class="absolute size-4.5 scale-0 opacity-0 transition-all duration-200" />
                    </button>

                    {{-- CONTENT --}}
                    <div class="min-w-0 flex-1">

                        {{-- TITLE --}}
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <flux:heading size="sm" class="truncate">
                                    {{ $item->title }}
                                </flux:heading>
                            </div>

                            @if ($item->duration)
                                <flux:text class="shrink-0 text-xs">
                                    {{ $item->formatted_duration }}
                                </flux:text>
                            @endif
                        </div>

                        {{-- WAVEFORM --}}
                        <div class="mt-1.5 min-w-0 w-full">
                            <div id="waveform-{{ $item->id }}" data-waveform data-url="{{ $item->url }}"
                                data-id="{{ $item->id }}" class="h-8 w-full cursor-pointer overflow-hidden"
                                title="Klik waveform untuk mengatur posisi audio"></div>

                            {{-- TIME --}}
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

                    {{-- ACTIONS --}}
                    @if ($isAdmin)
                        <div class="flex shrink-0 items-center gap-0.5">
                            <flux:button wire:click="openEdit({{ $item->id }})" variant="ghost"
                                icon="pencil-square" size="sm" />

                            <flux:button wire:click="deleteMusic({{ $item->id }})"
                                wire:confirm="Hapus music {{ $item->title }}?" variant="ghost" icon="trash"
                                size="sm" />
                        </div>
                    @endif

                </div>
            </div>
        @empty
            <div
                class="col-span-full rounded-2xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-700">
                <flux:icon name="musical-note" class="mx-auto size-10 text-zinc-400" />

                <flux:heading size="sm" class="mt-3">
                    Belum ada music
                </flux:heading>

                <flux:text class="mt-1">
                    @if ($isAdmin)
                        Upload file .ogg pertama Anda.
                    @else
                        Belum ada music yang tersedia.
                    @endif
                </flux:text>
            </div>
        @endforelse
    </div>

    {{ $music->links() }}

    {{-- ADMIN REQUESTS --}}
    @if ($isAdmin && $requests->isNotEmpty())
        <div class="rounded-xl bg-white dark:bg-zinc-800 md:my-5 md:ms-2 md:me-5">

            {{-- HEADER --}}
            <div class="flex flex-col gap-2 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <flux:heading size="lg">
                        Request Music
                    </flux:heading>

                    <flux:text class="mt-1">
                        Request dari user yang menunggu persetujuan.
                    </flux:text>
                </div>
            </div>

            {{-- REQUEST LIST --}}
            <div class="grid grid-cols-1 gap-3 p-5 pt-0 md:grid-cols-2">
                @foreach ($requests as $request)
                    <div wire:key="music-request-{{ $request->id }}"
                        class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="space-y-3">

                            <div class="flex items-start justify-between">
                                {{-- USER --}}
                                <div>
                                    <flux:text class="text-sm">
                                        Diminta oleh
                                        <span class="font-semibold">
                                            {{ $request->user->name }}
                                        </span>
                                    </flux:text>

                                    {{-- REQUEST --}}
                                    <flux:text class="mt-2 text-sm font-bold text-zinc-700 dark:text-zinc-400">
                                        "{{ $request->note }}"
                                    </flux:text>
                                </div>

                                {{-- STATUS --}}
                                <div>
                                    @if ($request->status === 'pending')
                                        <flux:badge color="amber">
                                            Menunggu
                                        </flux:badge>
                                    @elseif ($request->status === 'approved')
                                        <flux:badge color="green">
                                            Disetujui
                                        </flux:badge>
                                    @elseif ($request->status === 'rejected')
                                        <flux:badge color="red">
                                            Ditolak
                                        </flux:badge>
                                    @endif
                                </div>

                            </div>

                            {{-- ACTIONS --}}
                            <div class="flex flex-wrap gap-2">
                                @if ($request->status === 'pending')
                                    <flux:button wire:click="approveRequest({{ $request->id }})" variant="primary"
                                        color="blue" size="sm">
                                        Setujui
                                    </flux:button>

                                    <flux:button wire:click="rejectRequest({{ $request->id }})" variant="primary"
                                        color="red" size="sm">
                                        Tolak
                                    </flux:button>
                                @endif

                                <flux:button wire:click="deleteRequest({{ $request->id }})"
                                    wire:confirm="Hapus request ini secara permanen?" variant="ghost" color="red"
                                    size="sm">
                                    Hapus
                                </flux:button>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ADMIN MODALS --}}
    @if ($isAdmin)
        {{-- UPLOAD MODAL --}}
        <flux:modal wire:model="showUploadModal" class="w-full max-w-2xl" :dismissible="false">
            <form wire:submit="saveMusic" class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        Upload Music
                    </flux:heading>

                    <flux:text class="mt-1">
                        Upload file audio dalam format .ogg.
                    </flux:text>
                </div>

                <flux:input wire:model="title" label="Judul Music" placeholder="Artist - Song" />

                <flux:input type="file" wire:model="audio" label="File Audio" accept=".ogg,audio/ogg" />

                <flux:text class="text-xs">
                    Format .ogg · Maksimal 20 MB
                </flux:text>

                <div wire:loading wire:target="audio" class="text-sm">
                    Mengupload file...
                </div>

                <div class="flex justify-end gap-2">
                    <flux:button type="button" wire:click="$set('showUploadModal', false)">
                        Batal
                    </flux:button>

                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                        wire:target="saveMusic,audio">
                        Simpan
                    </flux:button>
                </div>
            </form>
        </flux:modal>

        {{-- EDIT MODAL --}}
        <flux:modal name="edit-music" class="w-full max-w-2xl" :dismissible="false">
            <form wire:submit="updateMusic" class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        Edit Music
                    </flux:heading>
                </div>

                <flux:input wire:model="editTitle" label="Judul Music" />

                <div class="flex justify-end gap-2">
                    <flux:button type="button" x-on:click="$flux.modal('edit-music').close()">
                        Batal
                    </flux:button>

                    <flux:button type="submit" variant="primary">
                        Simpan
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    @endif

    {{-- REQUEST MODAL --}}
    <flux:modal wire:model="showRequestModal" class="w-full max-w-2xl" :dismissible="false">
        <form wire:submit="submitRequest" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    Request Music
                </flux:heading>

                <flux:text class="mt-1">
                    Sampaikan permintaan kepada admin.
                </flux:text>
            </div>

            <flux:textarea wire:model="requestNote" label="Catatan" placeholder="Contoh: Mohon tambahkan lagu ini..."
                rows="4" />

            <div class="flex justify-end gap-2">
                <flux:button type="button" wire:click="$set('showRequestModal', false)">
                    Batal
                </flux:button>

                <flux:button type="submit" variant="primary">
                    Kirim Request
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
