<?php

use App\Models\PlannedUpdate;
use Livewire\Component;

new class extends Component {
    public ?int $editingId = null;

    public string $title = '';
    public string $description = '';
    public string $icon = 'sparkles';
    public int $sort_order = 0;
    public bool $is_active = true;
    public bool $showModal = false;
    public string $iconPreview = '';
    public bool $iconChecked = false;
    public bool $iconAvailable = false;

    public function createPlan(): void
    {
        $this->cancelEdit();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $item = PlannedUpdate::findOrFail($id);

        $this->editingId = $item->id;
        $this->title = $item->title;
        $this->description = $item->description ?? '';
        $this->icon = $item->icon;
        $this->sort_order = $item->sort_order;
        $this->is_active = $item->is_active;

        $this->resetValidation();
        $this->showModal = true;
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingId', 'title', 'description', 'sort_order']);

        $this->icon = 'sparkles';
        $this->is_active = true;
        $this->resetValidation();
        $this->showModal = false;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'icon' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9-]*$/'],
            'sort_order' => ['required', 'integer', 'min:1', 'max:9999'],
            'is_active' => ['boolean'],
        ]);

        $this->checkIcon();

        if (!$this->iconAvailable) {
            return;
        }

        if ($this->editingId) {
            PlannedUpdate::findOrFail($this->editingId)->update($validated);
        } else {
            PlannedUpdate::create($validated);
        }

        $this->cancelEdit();

        $this->dispatch('swal', type: 'toast', icon: 'success', message: 'Rencana pembaruan berhasil disimpan.');
    }

    public function checkIcon(): void
    {
        $this->resetValidation('icon');

        $icon = trim($this->icon);

        // Nama ikon harus menggunakan format kebab-case.
        if (!preg_match('/^[a-z][a-z0-9-]*$/', $icon)) {
            $this->addError('icon', 'Format nama ikon tidak valid.');
            $this->iconChecked = true;
            $this->iconAvailable = false;
            $this->iconPreview = '';

            return;
        }

        // Periksa ikon bawaan Heroicons milik Flux.
        $iconPath = base_path('vendor/livewire/flux/stubs/resources/views/flux/icon/' . $icon . '.blade.php');

        // Periksa juga ikon tambahan yang diimpor ke resources/views/flux/icon.
        $customIconPath = resource_path('views/flux/icon/' . $icon . '.blade.php');

        $this->iconChecked = true;
        $this->iconAvailable = is_file($iconPath) || is_file($customIconPath);
        $this->iconPreview = $this->iconAvailable ? $icon : '';

        if (!$this->iconAvailable) {
            $this->addError('icon', 'Ikon tidak ditemukan. Periksa nama ikon di Heroicons.');

            return;
        }
    }

    public function toggleActive(int $id): void
    {
        $item = PlannedUpdate::findOrFail($id);

        $item->update([
            'is_active' => !$item->is_active,
        ]);

        $this->dispatch('swal', type: 'toast', icon: 'success', message: 'Status rencana pembaruan berhasil diubah.');
    }

    public function delete(int $id): void
    {
        PlannedUpdate::findOrFail($id)->delete();

        if ($this->editingId === $id) {
            $this->cancelEdit();
        }

        $this->dispatch('swal', type: 'toast', icon: 'success', message: 'Rencana pembaruan berhasil dihapus.');
    }

    public function with(): array
    {
        return [
            'plannedUpdates' => PlannedUpdate::query()->orderBy('sort_order')->orderByDesc('id')->get(),
        ];
    }
};
?>

<div class="space-y-6">

    {{-- Header --}}
    <div class="m-5 rounded-xl bg-white md:me-5 md:ms-2 md:my-5 dark:bg-zinc-800">
        <div class="mx-auto flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div
                    class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-indigo-400 to-violet-500 text-white">
                    <flux:icon name="squares-plus" class="size-6" />
                </div>

                <div>
                    <flux:heading size="xl">
                        Coming Soon
                    </flux:heading>

                    <flux:text class="mt-1">
                        Kelola rencana fitur yang ditampilkan di halaman publik.
                    </flux:text>
                </div>
            </div>

            <flux:button variant="primary" color="violet" icon="plus" wire:click="createPlan">
                Tambah Rencana
            </flux:button>
        </div>
    </div>

    {{-- Daftar Rencana Fitur --}}
    <flux:card class="m-5 overflow-hidden border-none! md:me-5 md:ms-2 md:my-5">

        <div class="mb-4">
            <flux:heading size="lg">
                Daftar Rencana Fitur
            </flux:heading>

            <flux:text size="sm" class="mt-1">
                {{ $plannedUpdates->count() }} rencana terdaftar.
            </flux:text>
        </div>

        <div class="space-y-3">
            @forelse ($plannedUpdates as $item)
                <div wire:key="planned-update-{{ $item->id }}"
                    class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-4 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-start gap-3">
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                            <flux:icon :name="$item->icon" class="size-5" />
                        </div>

                        <div class="min-w-0">
                            <flux:heading>
                                {{ $item->title }}
                            </flux:heading>

                            @if ($item->description)
                                <flux:text class="mt-1">
                                    {{ $item->description }}
                                </flux:text>
                            @endif

                            <div class="mt-2 flex flex-wrap gap-2">
                                <flux:badge variant="outline">
                                    Urutan: {{ $item->sort_order }}
                                </flux:badge>

                                <flux:badge :color="$item->is_active ? 'green' : 'zinc'">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </flux:badge>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-wrap gap-2">
                        <flux:button size="sm" variant="ghost" :icon="$item->is_active ? 'eye-slash' : 'eye'"
                            wire:click="toggleActive({{ $item->id }})">
                            {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </flux:button>

                        <flux:button size="sm" variant="ghost" icon="pencil" color="indigo"
                            wire:click="edit({{ $item->id }})" tooltip="Hapus">
                        </flux:button>

                        <flux:button size="sm" variant="danger" icon="trash" color="red"
                            wire:click="delete({{ $item->id }})"
                            wire:confirm="Yakin ingin menghapus rencana fitur ini?" tooltip="Hapus">
                        </flux:button>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center">
                    <flux:icon name="inbox" class="mx-auto size-10 text-zinc-400" />

                    <flux:heading class="mt-3">
                        Belum ada rencana fitur
                    </flux:heading>

                    <flux:text class="mt-1">
                        Tambahkan rencana fitur menggunakan tombol di atas.
                    </flux:text>
                </div>
            @endforelse
        </div>
    </flux:card>

    {{-- Modal Tambah / Edit --}}
    <flux:modal name="planned-update-modal" class="md:w-150" :dismissible="false" wire:model="showModal">
        <div class="space-y-5">
            <div>
                <flux:heading size="lg">
                    {{ $editingId ? 'Edit Rencana Fitur' : 'Tambah Rencana Fitur' }}
                </flux:heading>

                <flux:text class="mt-1">
                    Atur informasi fitur yang akan ditampilkan di halaman publik.
                </flux:text>
            </div>

            <form wire:submit="save" class="space-y-4">

                <div class="flex flex-col gap-4 sm:flex-row items-start">
                    <flux:field class="min-w-0 flex-1">
                        <flux:label>Judul fitur</flux:label>

                        <flux:input wire:model="title" placeholder="Contoh: Dashboard Statistics" />

                        <flux:error name="title" />
                    </flux:field>

                    <flux:field class="w-full sm:w-32 sm:shrink-0">
                        <flux:label>Urutan tampil</flux:label>

                        <flux:input type="number" min="1" max="9999" wire:model="sort_order" />

                        <flux:error name="sort_order" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Deskripsi</flux:label>

                    <flux:textarea wire:model="description" rows="3"
                        placeholder="Jelaskan rencana fitur ini..." />

                    <flux:error name="description" />
                </flux:field>

                <flux:field>
                    <flux:label>Nama Ikon Heroicons</flux:label>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        @if ($iconChecked && $iconAvailable)
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-emerald-300 bg-emerald-100 text-emerald-700 dark:border-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400">
                                <flux:icon :name="$iconPreview" class="size-5" />
                            </div>
                        @elseif ($iconChecked && !$iconAvailable)
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-red-300 text-red-600 dark:border-red-800 dark:text-red-400">
                                <flux:icon name="exclamation-circle" class="size-5" />
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <flux:input wire:model="icon" placeholder="academic-cap" />
                        </div>

                        <flux:button type="button" wire:click="checkIcon" variant="primary" class="shrink-0">
                            Periksa Ikon
                        </flux:button>
                    </div>

                    <div class="flex items-center gap-2 ">

                        @if ($iconChecked)
                            <flux:text size="sm"
                                class="{{ $iconAvailable ? 'text-emerald-600' : 'text-red-600' }} mt-1">
                                @if ($iconAvailable)
                                    Ikon tersedia: {{ $iconPreview }}
                                @else
                                    Ikon tidak tersedia. Periksa kembali nama ikon.
                                @endif
                            </flux:text>
                        @endif

                        <div>
                            <flux:badge size="sm" color="fuchsia">
                                <a href="https://heroicons.com/" target="_blank" rel="noopener noreferrer"
                                    class="underline underline-offset-2">
                                    Cari ikon di Heroicons
                                </a>
                            </flux:badge>
                        </div>

                    </div>

                </flux:field>

                <flux:checkbox wire:model="is_active" label="Tampilkan di halaman publik" />

                <div class="flex flex-wrap justify-end gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                    <flux:button type="button" variant="ghost" wire:click="cancelEdit">
                        Batal
                    </flux:button>

                    <flux:button type="submit" variant="primary" icon="check">
                        {{ $editingId ? 'Simpan Perubahan' : 'Tambah Rencana' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

</div>
