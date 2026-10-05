<?php

use App\Enums\MusicGenre;
use App\Models\Music;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new class extends Component {
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public bool $showUploadModal = false;

    public string $title = '';
    public string $genre = MusicGenre::OTHER->value;
    public $audio;

    public ?int $editingMusicId = null;
    public string $editTitle = '';
    public string $editGenre = MusicGenre::OTHER->value;

    public ?int $deletingMusicId = null;

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

    // Helpers

    private function resetUploadForm(): void
    {
        $this->resetValidation();

        $this->reset(['title', 'audio']);

        $this->genre = MusicGenre::OTHER->value;
    }

    private function resetEditForm(): void
    {
        $this->resetValidation();

        $this->reset(['editingMusicId', 'editTitle']);

        $this->editGenre = MusicGenre::OTHER->value;
    }

    // Search

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // Upload Music

    public function openUpload(): void
    {
        $this->adminOnly();

        $this->resetUploadForm();

        $this->showUploadModal = true;
    }

    public function saveMusic(): void
    {
        $this->adminOnly();

        $this->validate(
            [
                'title' => ['required', 'string', 'max:255'],
                'genre' => ['required', 'string', 'in:' . implode(',', array_column(MusicGenre::cases(), 'value'))],
                'audio' => ['required', 'file', 'mimes:ogg', 'max:20480'],
            ],
            [
                'title.required' => 'Judul music wajib diisi.',
                'title.max' => 'Judul music maksimal 255 karakter.',
                'genre.required' => 'Genre music wajib dipilih.',
                'genre.in' => 'Genre music tidak valid.',
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
            'genre' => $this->genre,
            'filename' => $this->audio->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $this->audio->getMimeType() ?: 'audio/ogg',
            'size' => $this->audio->getSize(),
        ]);

        $this->resetUploadForm();
        $this->showUploadModal = false;

        $this->dispatch('swal', type: 'success', title: 'Berhasil', message: 'Music berhasil ditambahkan.');
    }

    // Edit Music

    public function openEdit(int $musicId): void
    {
        $this->adminOnly();

        $music = Music::findOrFail($musicId);

        $this->editingMusicId = $music->id;
        $this->editTitle = $music->title;
        $this->editGenre = $music->genre instanceof MusicGenre ? $music->genre->value : ($music->genre ?: MusicGenre::OTHER->value);

        $this->resetValidation();

        $this->dispatch('open-edit-music-modal');
    }

    public function updateMusic(): void
    {
        $this->adminOnly();

        $this->validate(
            [
                'editTitle' => ['required', 'string', 'max:255'],
                'editGenre' => ['required', 'string', 'in:' . implode(',', array_column(MusicGenre::cases(), 'value'))],
            ],
            [
                'editTitle.required' => 'Judul music wajib diisi.',
                'editTitle.max' => 'Judul music maksimal 255 karakter.',
                'editGenre.required' => 'Genre music wajib dipilih.',
                'editGenre.in' => 'Genre music tidak valid.',
            ],
        );

        $music = Music::findOrFail($this->editingMusicId);

        $music->update([
            'title' => $this->editTitle,
            'genre' => $this->editGenre,
        ]);

        $this->resetEditForm();

        $this->dispatch('close-edit-music-modal');

        $this->dispatch('swal', type: 'success', title: 'Berhasil', message: 'Music berhasil diperbarui.');
    }

    // Delete Music

    public function confirmDeleteMusic(int $musicId): void
    {
        $this->adminOnly();

        $music = Music::findOrFail($musicId);

        $this->deletingMusicId = $music->id;

        $this->dispatch('confirm', title: 'Hapus Music?', message: "\"{$music->title}\" akan dihapus secara permanen.", action: 'delete-music-confirmed');
    }

    #[On('delete-music-confirmed')]
    public function deleteMusic(): void
    {
        $this->adminOnly();

        if (!$this->deletingMusicId) {
            return;
        }

        $music = Music::findOrFail($this->deletingMusicId);

        if ($music->path) {
            Storage::disk('public')->delete($music->path);
        }

        $music->delete();

        $this->deletingMusicId = null;

        $this->dispatch('swal', type: 'success', title: 'Berhasil', message: 'Music berhasil dihapus.');
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
            ->latest()
            ->paginate(10);

        return $this->view([
            'music' => $music,
            'genres' => MusicGenre::cases(),
        ]);
    }
};
?>

<div x-data x-on:open-edit-music-modal.window="$flux.modal('edit-music').show()"
    x-on:close-edit-music-modal.window="$flux.modal('edit-music').close()" class="space-y-6">
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
                        List Music
                    </flux:heading>

                    <flux:text class="mt-1">
                        Dengarkan dan kelola koleksi music.
                    </flux:text>
                </div>
            </div>

            <flux:button wire:click="openUpload" variant="primary" color="violet" icon="plus">
                Upload Music
            </flux:button>
        </div>
    </div>

    {{-- Search --}}
    <flux:card class="m-5 border-none! md:my-5 md:ms-2 md:me-5">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari music..." icon="magnifying-glass" />
    </flux:card>

    {{-- Music List --}}
    <div class="m-5 grid grid-cols-1 gap-4 rounded-xl md:my-5 md:ms-2 md:me-5 md:grid-cols-2">
        @forelse ($music as $item)
            <flux:card wire:key="music-{{ $item->id }}"
                class="m-0! rounded-2xl border-none! bg-white p-3 dark:bg-zinc-800">
                <div class="flex items-center gap-3">

                    {{-- Play --}}
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

                    {{-- Actions --}}
                    <div class="flex shrink-0 items-center gap-0.5">
                        <flux:button wire:click="openEdit({{ $item->id }})" variant="ghost" icon="pencil"
                            size="sm" tooltip="Edit musik" />

                        <flux:button wire:click="confirmDeleteMusic({{ $item->id }})" variant="ghost"
                            color="red" icon="trash" size="sm" tooltip="Hapus musik" />
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
                    Upload file .ogg pertama Anda.
                </flux:text>
            </div>
        @endforelse
    </div>

    {{ $music->links() }}

    {{-- Upload Modal --}}
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

            <div class="flex flex-col gap-4 sm:flex-row">
                <div class="min-w-0 flex-1">
                    <flux:input wire:model="title" label="Judul Music" placeholder="Artist - Song" />
                </div>

                <div class="w-full sm:w-48">
                    <flux:select wire:model="genre" label="Genre">
                        @foreach ($genres as $item)
                            <flux:select.option value="{{ $item->value }}">
                                {{ $item->label() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <div class="space-y-2">
                <flux:input type="file" wire:model="audio" label="File Audio" accept=".ogg,audio/ogg" />

                @if ($audio)
                    <div class="rounded-lg bg-zinc-50 px-3 py-2 dark:bg-zinc-800">
                        <flux:text class="break-all text-xs">
                            {{ $audio->getClientOriginalName() }}
                        </flux:text>
                    </div>
                @endif

                <flux:text class="text-xs">
                    Format .ogg · Maksimal 20 MB
                </flux:text>
            </div>

            <div wire:loading wire:target="audio" class="text-sm text-zinc-500">
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

    {{-- Edit Modal --}}
    <flux:modal name="edit-music" class="w-full max-w-2xl" :dismissible="false">
        <form wire:submit="updateMusic" class="space-y-6">

            <div>
                <flux:heading size="lg">
                    Edit Music
                </flux:heading>

                <flux:text class="mt-1">
                    Perbarui informasi music.
                </flux:text>
            </div>

            <flux:input wire:model="editTitle" label="Judul Music" />

            <flux:select wire:model="editGenre" label="Genre">
                @foreach ($genres as $item)
                    <flux:select.option value="{{ $item->value }}">
                        {{ $item->label() }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex justify-end gap-2">
                <flux:button type="button" x-on:click="$flux.modal('edit-music').close()">
                    Batal
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                    wire:target="updateMusic">
                    Simpan
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
