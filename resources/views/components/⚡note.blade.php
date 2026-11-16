<?php

use App\Models\Note;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Notepad')] class extends Component {
    public string $search = '';

    public ?int $activeNoteId = null;

    public string $title = '';

    public string $content = '';

    public string $saveState = 'Tersimpan';

    #[Computed]
    public function notes()
    {
        return Note::query()
            ->where('user_id', Auth::id())
            ->when(trim($this->search) !== '', fn(Builder $query) => $query->where('title', 'like', '%' . trim($this->search) . '%'))
            ->orderByDesc('updated_at')
            ->get();
    }

    public function updatedSearch(): void
    {
        unset($this->notes);
    }

    public function createNote(): void
    {
        $note = Note::query()->create([
            'user_id' => Auth::id(),
            'title' => 'Halaman baru',
            'content' => '',
        ]);

        $this->activeNoteId = $note->id;
        $this->title = $note->title;
        $this->content = '';
        $this->saveState = 'Tersimpan';

        unset($this->notes);

        $this->dispatch('toast', type: 'success', message: 'Catatan berhasil dibuat.');
    }

    public function selectNote(int $id): void
    {
        $note = Note::query()->where('user_id', Auth::id())->findOrFail($id);

        $this->activeNoteId = $note->id;
        $this->title = $note->title;
        $this->content = $note->content ?? '';
        $this->saveState = 'Tersimpan';
    }

    public function updatedTitle(): void
    {
        if ($this->activeNoteId === null) {
            return;
        }

        $title = trim($this->title);

        if ($title === '') {
            $this->saveState = 'Judul tidak boleh kosong';

            $this->dispatch('swal', type: 'warning', title: 'Judul tidak valid', message: 'Judul catatan tidak boleh kosong.');

            return;
        }

        if (mb_strlen($title) > 150) {
            $this->saveState = 'Judul maksimal 150 karakter';

            $this->dispatch('swal', type: 'warning', title: 'Judul tidak valid', message: 'Judul catatan maksimal 150 karakter.');

            return;
        }

        $this->activeNote()->update([
            'title' => $title,
        ]);

        $this->title = $title;
        $this->saveState = 'Tersimpan';

        unset($this->notes);
    }

    public function updatedContent(): void
    {
        if ($this->activeNoteId === null) {
            return;
        }

        $this->activeNote()->update([
            'content' => $this->content,
        ]);

        $this->saveState = 'Tersimpan';

        unset($this->notes);
    }

    public function askDeleteNote(): void
    {
        if ($this->activeNoteId === null) {
            return;
        }

        $this->dispatch('confirm', title: 'Hapus catatan?', message: 'Catatan yang dihapus tidak dapat dipulihkan.', action: 'notepad-delete-confirmed');
    }

    #[On('notepad-delete-confirmed')]
    public function deleteNote(): void
    {
        if ($this->activeNoteId === null) {
            return;
        }

        $this->activeNote()->delete();

        $this->activeNoteId = null;
        $this->title = '';
        $this->content = '';
        $this->saveState = 'Tersimpan';

        unset($this->notes);

        $this->dispatch('toast', type: 'success', message: 'Catatan berhasil dihapus.');
    }

    private function activeNote(): Note
    {
        abort_if($this->activeNoteId === null, 404);

        return Note::query()->where('user_id', Auth::id())->findOrFail($this->activeNoteId);
    }
};
?>

<div class="max-h-screen overflow-hidden space-y-6">
    {{-- Header --}}
    <div class="m-5 rounded-xl bg-white md:my-5 md:ms-2 md:me-5 dark:bg-zinc-800">
        <div class="flex flex-col items-center justify-between gap-4 p-6 sm:flex-row">
            <div class="flex items-center gap-3">
                <div
                    class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-indigo-400 to-violet-500 text-white">
                    <flux:icon name="document-text" class="size-6" />
                </div>

                <div>
                    <flux:heading size="xl">
                        Notepad
                    </flux:heading>

                    <flux:text class="mt-1">
                        Simpan catatan pribadi Anda.
                    </flux:text>
                </div>
            </div>
            <flux:button variant="primary" wire:click="createNote" icon="plus">
                Catatan Baru
            </flux:button>
        </div>
    </div>

    {{-- Main content --}}
    <div
        class="m-5 grid max-h-screen min-h-0 grid-cols-1 gap-5 overflow-hidden md:my-5 md:ms-2 md:me-5 lg:grid-cols-12">
        {{-- Sidebar --}}
        <section
            class="flex min-h-0 flex-col overflow-hidden rounded-xl bg-white dark:bg-zinc-900 lg:col-span-4 xl:col-span-3">
            <div class="border-b border-zinc-200 p-4 dark:border-zinc-700">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h2 class="font-semibold text-zinc-900 dark:text-white">
                        Catatan Saya
                    </h2>

                    <span
                        class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                        {{ $this->notes->count() }}
                    </span>
                </div>

                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari judul catatan..."
                    icon="magnifying-glass" clearable />
            </div>

            <div class="max-h-130 min-h-110 overflow-y-auto">
                @forelse ($this->notes as $note)
                    <button type="button" wire:key="note-{{ $note->id }}"
                        wire:click="selectNote({{ $note->id }})" @class([
                            'block w-full border-b border-zinc-100 p-4 text-left transition-colors dark:border-zinc-800',
                            'bg-zinc-100 dark:bg-zinc-800/80' => $activeNoteId === $note->id,
                            'hover:bg-zinc-50 dark:hover:bg-zinc-800/50' => $activeNoteId !== $note->id,
                        ])>
                        <div class="mb-1 flex items-start justify-between gap-2">
                            <span class="truncate text-sm font-semibold text-zinc-900 dark:text-white">
                                {{ $note->title }}
                            </span>

                            @if ($activeNoteId === $note->id)
                                <flux:icon name="pencil-square" class="size-4 shrink-0 text-zinc-500" />
                            @endif
                        </div>

                        <p class="line-clamp-2 text-xs leading-5 text-zinc-500 dark:text-zinc-400">
                            {{ \Illuminate\Support\Str::limit(trim($note->content ?? ''), 100) ?: 'Belum ada isi catatan' }}
                        </p>

                        <p class="mt-2 text-xs text-zinc-400 dark:text-zinc-500">
                            {{ $note->updated_at?->format('d M Y, H:i') }}
                        </p>
                    </button>
                @empty
                    <div class="flex min-h-60 flex-col items-center justify-center px-5 py-10 text-center">
                        <div
                            class="mb-3 flex size-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <flux:icon name="document-text" class="size-6 text-zinc-400" />
                        </div>

                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            {{ trim($search) !== '' ? 'Catatan tidak ditemukan' : 'Belum ada catatan' }}
                        </p>

                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            {{ trim($search) !== '' ? 'Coba gunakan kata kunci lain.' : 'Buat catatan baru untuk mulai menulis.' }}
                        </p>

                        @if (trim($search) === '')
                            <flux:button class="mt-4" variant="outline" size="sm" wire:click="createNote"
                                icon="plus">
                                Buat Catatan
                            </flux:button>
                        @endif
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Editor --}}
        <section
            class="flex max-h-screen min-h-0 flex-col overflow-hidden rounded-xl bg-white dark:bg-zinc-900 lg:col-span-8 xl:col-span-9">
            @if ($activeNoteId !== null)
                <div
                    class="flex flex-col gap-4 border-b border-zinc-200 p-4 dark:border-zinc-700 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                        <flux:icon name="pencil-square" class="size-4 shrink-0" />

                        <span>Editor catatan</span>

                        <span class="text-zinc-300 dark:text-zinc-600">/</span>

                        <span class="truncate">{{ $saveState }}</span>
                    </div>

                    <flux:button variant="danger" wire:click="askDeleteNote" icon="trash" aria-label="Hapus catatan">
                        Hapus
                    </flux:button>
                </div>

                <div class="space-y-4 p-4 sm:p-6">
                    <div>
                        <flux:input wire:model.live.debounce.700ms="title" placeholder="Judul catatan..."
                            maxlength="150" class="text-lg" />
                    </div>

                    <div>
                        <textarea id="note-content" wire:model.live.debounce.700ms="content" placeholder="Mulai tulis catatan Anda di sini..."
                            class="max-h-screen min-h-0 w-full resize-y rounded-lg border border-zinc-200 bg-white p-4 text-sm leading-7 text-zinc-800 outline-none transition focus:border-zinc-400 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-800"></textarea>
                    </div>

                    <div class="flex items-center justify-between gap-3 text-xs text-zinc-400 dark:text-zinc-500">
                        <span>Catatan tersimpan otomatis saat Anda berhenti mengetik.</span>

                        <span>{{ mb_strlen($content) }} karakter</span>
                    </div>
                </div>
            @else
                <div class="flex min-h-155 flex-col items-center justify-center px-6 py-12 text-center">
                    <div class="mb-4 flex size-16 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800">
                        <flux:icon name="document-text" class="size-8 text-zinc-400" />
                    </div>

                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        Pilih catatan untuk mulai
                    </h2>

                    <p class="mt-2 max-w-sm text-sm leading-6 text-zinc-500 dark:text-zinc-400">
                        Pilih catatan dari daftar di sebelah kiri atau buat catatan baru untuk mulai menulis.
                    </p>

                    <flux:button class="mt-5" variant="primary" wire:click="createNote" icon="plus">
                        Buat Catatan Baru
                    </flux:button>
                </div>
            @endif
        </section>
    </div>
</div>
