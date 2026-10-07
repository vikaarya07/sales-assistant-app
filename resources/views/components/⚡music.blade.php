<?php

use App\Enums\MusicGenre;
use App\Models\Music;
use App\Models\MusicRequest;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public string $genre = '';

    public string $playlist = 'all';

    public bool $showRequestModal = false;

    public bool $showRequestListModal = false;

    public string $requestNote = '';

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Genre
    |--------------------------------------------------------------------------
    */

    public function updatedGenre(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Playlist
    |--------------------------------------------------------------------------
    */

    public function setPlaylist(string $playlist): void
    {
        $this->playlist = in_array($playlist, ['all', 'favorites'], true) ? $playlist : 'all';

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Favorite
    |--------------------------------------------------------------------------
    */

    public function toggleFavorite(int $musicId): void
    {
        $music = Music::findOrFail($musicId);

        $userId = auth()->id();

        $favorite = DB::table('favorite_music')->where('user_id', $userId)->where('music_id', $music->id)->first();

        if ($favorite) {
            DB::table('favorite_music')->where('id', $favorite->id)->delete();

            $this->normalizeFavoritePositions();

            $this->dispatch('swal', type: 'success', title: 'Dihapus dari Favorit', message: 'Music telah dihapus dari favorit.');

            return;
        }

        $position = (DB::table('favorite_music')->where('user_id', $userId)->max('position') ?? 0) + 1;

        DB::table('favorite_music')->insert([
            'user_id' => $userId,
            'music_id' => $music->id,
            'position' => $position,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->dispatch('swal', type: 'success', title: 'Ditambahkan ke Favorit', message: 'Music telah ditambahkan ke favorit.');
    }

    private function normalizeFavoritePositions(): void
    {
        $favorites = DB::table('favorite_music')
            ->where('user_id', auth()->id())
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        foreach ($favorites as $index => $favorite) {
            DB::table('favorite_music')
                ->where('id', $favorite->id)
                ->update([
                    'position' => $index + 1,
                    'updated_at' => now(),
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder Favorite
    |--------------------------------------------------------------------------
    */

    public function reorderFavorites(array $ids): void
    {
        $userId = auth()->id();

        /*
         * Ambil seluruh lagu favorit milik user.
         */
        $favoriteIds = DB::table('favorite_music')->where('user_id', $userId)->pluck('music_id')->map(fn($id) => (int) $id)->all();

        /*
         * Pastikan semua ID berupa integer.
         */
        $ids = array_map('intval', $ids);

        /*
         * Hanya izinkan ID yang memang merupakan
         * favorit milik user.
         */
        $ids = array_values(array_intersect($ids, $favoriteIds));

        /*
         * Jika ada ID yang tidak dikirim browser,
         * tambahkan kembali di bagian akhir.
         */
        $missingIds = array_values(array_diff($favoriteIds, $ids));

        $ids = array_merge($ids, $missingIds);

        DB::transaction(function () use ($ids, $userId) {
            foreach ($ids as $index => $musicId) {
                DB::table('favorite_music')
                    ->where('user_id', $userId)
                    ->where('music_id', $musicId)
                    ->update([
                        'position' => $index + 1,
                        'updated_at' => now(),
                    ]);
            }
        });

        $this->dispatch('favorite-updated');
    }

    /*
    |--------------------------------------------------------------------------
    | Music Request
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        /*
        |--------------------------------------------------------------------------
        | Favorite Playlist
        |--------------------------------------------------------------------------
        |
        | Tidak menggunakan pagination agar seluruh lagu favorit
        | dapat di-drag & drop dalam satu playlist.
        |
        */

        if ($this->playlist === 'favorites') {
            $music = Music::query()
                ->when($this->search !== '', function ($query) {
                    $query->where('title', 'like', "%{$this->search}%");
                })
                ->when($this->genre !== '', function ($query) {
                    $query->where('genre', $this->genre);
                })
                ->join('favorite_music', 'music.id', '=', 'favorite_music.music_id')
                ->where('favorite_music.user_id', auth()->id())
                ->orderBy('favorite_music.position')
                ->orderBy('favorite_music.id')
                ->select('music.*')
                ->get();
        } else {
            /*
            |--------------------------------------------------------------------------
            | All Music
            |--------------------------------------------------------------------------
            */

            $music = Music::query()
                ->when($this->search !== '', function ($query) {
                    $query->where(function ($query) {
                        $query->where('title', 'like', "%{$this->search}%")->orWhere('filename', 'like', "%{$this->search}%");
                    });
                })
                ->when($this->genre !== '', function ($query) {
                    $query->where('genre', $this->genre);
                })
                ->latest('music.created_at')
                ->paginate(10);
        }

        /*
        |--------------------------------------------------------------------------
        | Request List
        |--------------------------------------------------------------------------
        */

        $requests = MusicRequest::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Favorite IDs
        |--------------------------------------------------------------------------
        */

        $favoriteIds = DB::table('favorite_music')
            ->where('user_id', auth()->id())
            ->pluck('music_id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        return $this->view([
            'music' => $music,
            'requests' => $requests,
            'genres' => MusicGenre::cases(),
            'favoriteIds' => $favoriteIds,
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

    {{-- Music Controls --}}
    <div class="overflow-hidden m-5 rounded-xl bg-white md:my-5 md:ms-2 md:me-5 dark:bg-zinc-800">
        {{-- Playlist --}}
        <div class="border-b border-zinc-200 p-4 sm:p-6 dark:border-zinc-700">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <flux:heading size="sm">
                        Koleksi Music
                    </flux:heading>

                    <flux:text class="mt-1 text-sm">
                        Pilih playlist yang ingin Anda dengarkan.
                    </flux:text>
                </div>

                {{-- Playlist Selector --}}
                <div class="w-full sm:w-auto">
                    <flux:button.group>
                        <flux:button wire:click="setPlaylist('all')"
                            :variant="$playlist === 'all' ? 'primary' : 'ghost'" icon="musical-note">
                            Semua Lagu
                        </flux:button>

                        <flux:button wire:click="setPlaylist('favorites')"
                            :variant="$playlist === 'favorites' ? 'primary' : 'ghost'" icon="heart">
                            Favorit Saya
                        </flux:button>
                    </flux:button.group>
                </div>
            </div>

            {{-- Active Playlist --}}
            <div class="mt-4 flex items-center gap-2">
                @if ($playlist === 'favorites')
                    <flux:badge color="rose" size="sm" icon="heart">
                        Favorit Saya
                    </flux:badge>

                    <flux:text class="text-xs">
                        Geser lagu untuk mengubah urutan playlist.
                    </flux:text>
                @else
                    <flux:badge color="indigo" size="sm" icon="musical-note">
                        Semua Lagu
                    </flux:badge>

                    <flux:text class="text-xs">
                        Seluruh koleksi music tersedia di sini.
                    </flux:text>
                @endif
            </div>
        </div>

        {{-- Search & Filter --}}
        <div class="p-4 sm:p-6">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_13rem_auto] lg:items-end">
                {{-- Search --}}
                <flux:field>
                    <flux:label>
                        Cari Music
                    </flux:label>

                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari judul..."
                        icon="magnifying-glass" clearable />
                </flux:field>

                {{-- Genre --}}
                <flux:field>
                    <flux:label>
                        Genre
                    </flux:label>

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
                </flux:field>

                {{-- Request Actions --}}
                <div class="flex gap-2 lg:pb-0.5">
                    <flux:button wire:click="openRequest" variant="primary" color="emerald" icon="paper-airplane"
                        class="flex-1 lg:flex-none">
                        Request Lagu
                    </flux:button>

                    <flux:button wire:click="openRequestList" variant="ghost" icon="clipboard-document-list"
                        class="flex-1 lg:flex-none">
                        <span class="hidden xl:inline">
                            Daftar Request
                        </span>

                        <span class="xl:hidden">
                            Request
                        </span>
                    </flux:button>
                </div>
            </div>

            {{-- Active Filters --}}
            @if ($search !== '' || $genre !== '')
                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <flux:text class="text-xs">
                        Filter aktif:
                    </flux:text>

                    @if ($search !== '')
                        <flux:badge color="indigo" size="sm">
                            <div class="flex items-center gap-1.5">
                                <flux:icon name="magnifying-glass" class="size-3.5" />

                                <span class="max-w-40 truncate">
                                    {{ $search }}
                                </span>

                                <button type="button" wire:click="$set('search', '')"
                                    class="ms-0.5 rounded-full p-0.5 transition hover:bg-indigo-200 dark:hover:bg-indigo-800"
                                    aria-label="Hapus pencarian">
                                    <flux:icon name="x-mark" class="size-3" />
                                </button>
                            </div>
                        </flux:badge>
                    @endif

                    @if ($genre !== '')
                        @php
                            $selectedGenre = collect($genres)->first(fn($item) => $item->value === $genre);
                        @endphp

                        @if ($selectedGenre)
                            <flux:badge color="violet" size="sm">
                                <div class="flex items-center gap-1.5">
                                    <flux:icon name="musical-note" class="size-3.5" />

                                    {{ $selectedGenre->label() }}

                                    <button type="button" wire:click="$set('genre', '')"
                                        class="ms-0.5 rounded-full p-0.5 transition hover:bg-violet-200 dark:hover:bg-violet-800"
                                        aria-label="Hapus genre">
                                        <flux:icon name="x-mark" class="size-3" />
                                    </button>
                                </div>
                            </flux:badge>
                        @endif
                    @endif

                    <flux:button type="button" wire:click="$set('search', ''); $set('genre', '')" variant="ghost"
                        size="sm">
                        Bersihkan
                    </flux:button>
                </div>
            @endif
        </div>
    </div>

    {{-- Music List --}}
    @if ($playlist === 'favorites')
        {{-- Favorite Playlist --}}
        <div data-favorite-list class="m-5 space-y-3 rounded-xl md:my-5 md:ms-2 md:me-5">
            @forelse ($music as $item)
                {{-- Sortable Item --}}
                <div data-favorite-id="{{ $item->id }}" wire:key="favorite-music-{{ $item->id }}">
                    <flux:card class="m-0! rounded-2xl border-none! bg-white p-3 dark:bg-zinc-800">
                        <div class="flex items-center gap-3">
                            {{-- Drag Handle --}}
                            <button type="button" data-drag-handle
                                class="shrink-0 cursor-grab text-zinc-400 transition hover:text-zinc-600 active:cursor-grabbing dark:hover:text-zinc-200"
                                title="Geser untuk mengubah urutan">
                                <flux:icon name="bars-3" class="size-5" />
                            </button>

                            {{-- Play / Pause --}}
                            <button type="button" data-music-play data-id="{{ $item->id }}"
                                data-url="{{ $item->url }}" data-title="{{ $item->title }}"
                                class="group relative flex size-10 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white transition-all duration-200 hover:scale-105 hover:bg-indigo-700 active:scale-95"
                                aria-label="Play {{ $item->title }}">
                                <flux:icon name="play" data-music-icon="play"
                                    class="size-4.5 translate-x-0.5 transition-all duration-200" />

                                <flux:icon name="pause" data-music-icon="pause"
                                    class="absolute size-4.5 scale-0 opacity-0 transition-all duration-200" />
                            </button>

                            {{-- Content --}}
                            <div class="min-w-0 flex-1">
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
                                    <div id="waveform-{{ $item->id }}" data-waveform
                                        data-url="{{ $item->url }}" data-id="{{ $item->id }}"
                                        class="h-8 w-full cursor-pointer overflow-hidden"
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

                            {{-- Favorite --}}
                            <button type="button" wire:click="toggleFavorite({{ $item->id }})"
                                wire:loading.attr="disabled"
                                class="flex size-9 shrink-0 items-center justify-center rounded-full transition hover:bg-zinc-100 dark:hover:bg-zinc-700"
                                title="Hapus dari favorit">
                                <flux:icon name="heart" variant="solid" class="size-5 text-rose-500" />
                            </button>
                        </div>
                    </flux:card>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-700">
                    <flux:icon name="heart" class="mx-auto size-10 text-zinc-400" />

                    <flux:heading size="sm" class="mt-3">
                        Belum ada favorit
                    </flux:heading>

                    <flux:text class="mt-1">
                        Tambahkan music ke favorit untuk membuat playlist Anda.
                    </flux:text>
                </div>
            @endforelse
        </div>
    @else
        {{-- All Music --}}
        <div class="m-5 grid grid-cols-1 gap-4 rounded-xl md:my-5 md:ms-2 md:me-5 md:grid-cols-2">
            @forelse ($music as $item)
                <flux:card wire:key="music-{{ $item->id }}"
                    class="m-0! rounded-2xl border-none! bg-white p-3 dark:bg-zinc-800">
                    <div class="flex items-center gap-3">
                        {{-- Play / Pause --}}
                        <button type="button" data-music-play data-id="{{ $item->id }}"
                            data-url="{{ $item->url }}" data-title="{{ $item->title }}"
                            class="group relative flex size-10 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white transition-all duration-200 hover:scale-105 hover:bg-indigo-700 active:scale-95"
                            aria-label="Play {{ $item->title }}">
                            <flux:icon name="play" data-music-icon="play"
                                class="size-4.5 translate-x-0.5 transition-all duration-200" />

                            <flux:icon name="pause" data-music-icon="pause"
                                class="absolute size-4.5 scale-0 opacity-0 transition-all duration-200" />
                        </button>

                        {{-- Content --}}
                        <div class="min-w-0 flex-1">
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

                        {{-- Favorite --}}
                        <button type="button" wire:click="toggleFavorite({{ $item->id }})"
                            wire:loading.attr="disabled"
                            class="flex size-9 shrink-0 items-center justify-center rounded-full transition hover:bg-zinc-100 dark:hover:bg-zinc-700"
                            title="{{ in_array($item->id, $favoriteIds, true) ? 'Hapus dari favorit' : 'Tambahkan ke favorit' }}">
                            @if (in_array($item->id, $favoriteIds, true))
                                <flux:icon name="heart" variant="solid" class="size-5 text-rose-500" />
                            @else
                                <flux:icon name="heart"
                                    class="size-5 text-zinc-400 transition hover:text-rose-500" />
                            @endif
                        </button>
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
    @endif

    {{-- Pagination hanya untuk Semua Lagu --}}
    @if ($playlist === 'all')
        {{ $music->links() }}
    @endif

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
                {{-- Header --}}
                <div>
                    <flux:heading size="lg">
                        Request Music
                    </flux:heading>

                    <flux:text class="mt-1">
                        Sampaikan permintaan lagu kepada admin.
                    </flux:text>
                </div>

                {{-- Request Note --}}
                <flux:textarea wire:model="requestNote" label="Catatan" placeholder="Contoh: Mohon tambahkan lagu ini..."
                    rows="4" />

                {{-- Footer --}}
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
