<?php

use App\Models\Update;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public ?int $editingId = null;
    public string $title = '';
    public string $version = '';
    public string $summary = '';
    public string $releaseDate = '';
    public array $items = [];

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'category' => 'feature',
            'title' => '',
            'description' => '',
        ];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) > 1 && isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function createUpdate(): void
    {
        $this->resetForm();

        $this->modal('update-editor')->show();
    }

    public function editUpdate(int $id): void
    {
        $update = Update::with('items')->findOrFail($id);

        $this->editingId = $update->id;
        $this->title = $update->title;
        $this->version = $update->version;
        $this->summary = $update->summary ?? '';
        $this->releaseDate = $update->release_date->format('Y-m-d');

        $this->items = $update->items
            ->map(
                fn($item) => [
                    'category' => $item->category,
                    'title' => $item->title ?? '',
                    'description' => $item->description,
                ],
            )
            ->values()
            ->all();

        if (empty($this->items)) {
            $this->items = [
                [
                    'category' => 'feature',
                    'title' => '',
                    'description' => '',
                ],
            ];
        }

        $this->resetValidation();
        $this->modal('update-editor')->show();
    }

    public function saveUpdate(string $action = 'draft'): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->validate(
            [
                'title' => ['required', 'string', 'max:255'],
                'version' => ['required', 'string', 'max:30', Rule::unique('updates', 'version')->ignore($this->editingId)],
                'summary' => ['required', 'string', 'max:2000'],
                'releaseDate' => ['required', 'date'],
                'items' => ['required', 'array', 'min:1', 'max:50'],
                'items.*.category' => ['required', Rule::in(['feature', 'improvement', 'fix', 'security'])],
                'items.*.title' => ['nullable', 'string', 'max:255'],
                'items.*.description' => ['nullable', 'string', 'max:5000'],
            ],
            [
                'title.required' => 'Judul rilis wajib diisi.',
                'version.required' => 'Nomor versi wajib diisi.',
                'version.unique' => 'Nomor versi sudah digunakan.',
                'summary' => 'Ringkasan wajib diisi',
                'releaseDate.required' => 'Tanggal rilis wajib diisi.',
                'items.required' => 'Tambahkan minimal satu catatan perubahan.',
                'items.min' => 'Tambahkan minimal satu catatan perubahan.',
            ],
        );

        if (!in_array($action, ['draft', 'publish'], true)) {
            abort(422);
        }

        DB::transaction(function () use ($action) {
            $update = $this->editingId ? Update::findOrFail($this->editingId) : new Update();

            $update->fill([
                'title' => $this->title,
                'version' => $this->version,
                'summary' => $this->summary ?: null,
                'release_date' => $this->releaseDate,
                'status' => $action === 'publish' ? 'published' : 'draft',
                'published_at' => $action === 'publish' ? $update->published_at ?? now() : null,
                'user_id' => $update->user_id ?? auth()->id(),
            ]);

            $update->save();

            $update->items()->delete();

            foreach ($this->items as $index => $item) {
                $update->items()->create([
                    'category' => $item['category'],
                    'title' => filled($item['title'] ?? '') ? $item['title'] : null,
                    'description' => $item['description'],
                    'sort_order' => $index,
                ]);
            }
        });

        $this->closeModal();
        $this->resetPage();

        $this->dispatch('swal', type: 'success', message: $action === 'publish' ? 'Update berhasil dipublikasikan.' : 'Update berhasil disimpan sebagai draft.');
    }

    public function deleteUpdate(int $id): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        Update::findOrFail($id)->delete();

        $this->dispatch('swal', type: 'success', message: 'Update berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->modal('update-editor')->close();

        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->resetValidation();

        $this->editingId = null;
        $this->title = '';
        $this->version = '';
        $this->summary = '';
        $this->releaseDate = '';

        $this->items = [
            [
                'category' => 'feature',
                'title' => '',
                'description' => '',
            ],
        ];
    }

    public function render()
    {
        $updates = Update::query()->withCount('items')->when($this->search, fn($query) => $query->where(fn($query) => $query->where('title', 'like', "%{$this->search}%")->orWhere('version', 'like', "%{$this->search}%")))->when($this->statusFilter, fn($query) => $query->where('status', $this->statusFilter))->latest('release_date')->latest('id')->paginate(10);

        return $this->view(compact('updates'));
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
                    <flux:icon name="users" class="size-6" />
                </div>

                <div>
                    <flux:heading size="xl">
                        Manajemen Update
                    </flux:heading>

                    <flux:text class="mt-1">
                        Kelola versi dan catatan perubahan Sales Assistant.
                    </flux:text>
                </div>
            </div>

            <flux:button variant="primary" color="violet" icon="plus" wire:click="createUpdate">
                Tambah Update
            </flux:button>
        </div>
    </div>

    {{-- Update Table --}}
    <flux:card class="m-5 overflow-hidden border-none! md:me-5 md:ms-2 md:my-5">

        {{-- Search + Status --}}
        <div class="flex flex-wrap gap-3">
            <div class="min-w-56 flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari judul atau versi..."
                    icon="magnifying-glass" />
            </div>

            <div class="w-44">
                <flux:select wire:model.live="statusFilter">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="published">Dipublikasikan</option>
                </flux:select>
            </div>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Rilis</flux:table.column>
                <flux:table.column>Tanggal</flux:table.column>
                <flux:table.column>Catatan</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column align="center">Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($updates as $update)
                    <flux:table.row :key="$update->id">
                        <flux:table.cell>
                            <div class="font-medium">{{ $update->title }}</div>
                            <flux:text size="sm">v{{ $update->version }}</flux:text>
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $update->release_date->locale('id')->translatedFormat('l, d F Y') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $update->items_count }} catatan
                        </flux:table.cell>

                        <flux:table.cell>
                            @if ($update->status === 'published')
                                <flux:badge color="green">Published</flux:badge>
                            @else
                                <flux:badge color="amber">Draft</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <div class="flex justify-end gap-2">
                                <flux:button size="sm" variant="ghost" icon="pencil" color="indigo"
                                    wire:click="editUpdate({{ $update->id }})" tooltip="Edit">
                                </flux:button>

                                <flux:button size="sm" variant="ghost" icon="trash" color="red"
                                    wire:click="deleteUpdate({{ $update->id }})"
                                    wire:confirm="Hapus rilis ini beserta seluruh catatannya?" tooltip="Hapus">
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5">
                            <div class="py-8 text-center">
                                Belum ada data update.
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

    </flux:card>

    {{ $updates->links() }}

    <flux:modal name="update-editor" class="md:w-150" :dismissible="false">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingId ? 'Edit Update' : 'Tambah Update' }}
                </flux:heading>
                <flux:text class="mt-1">
                    Satu rilis dapat memiliki beberapa catatan perubahan.
                </flux:text>
            </div>

            <form wire:submit="saveUpdate('draft')" class="space-y-4">
                <flux:input wire:model="title" label="Judul Rilis" placeholder="Contoh: WhatsApp Composer" />

                <flux:input wire:model="version" label="Nomor Versi" placeholder="1.3.0" />

                <flux:textarea wire:model="summary" label="Ringkasan"
                    placeholder="Ringkasan singkat perubahan pada rilis ini." />

                <flux:input type="date" wire:model="releaseDate" label="Tanggal Rilis" />

                <flux:separator />

                <div class="flex items-center justify-between gap-3">
                    <flux:heading>Catatan Perubahan</flux:heading>
                    <flux:button type="button" size="sm" variant="ghost" icon="plus" wire:click="addItem">
                        Tambah Catatan
                    </flux:button>
                </div>

                @foreach ($items as $index => $item)
                    <div wire:key="update-item-{{ $index }}"
                        class="space-y-3 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <flux:text class="font-medium">
                                Catatan {{ $index + 1 }}
                            </flux:text>

                            @if (count($items) > 1)
                                <flux:button type="button" size="sm" variant="ghost" icon="x-mark"
                                    wire:click="removeItem({{ $index }})" />
                            @endif
                        </div>

                        <flux:select wire:model="items.{{ $index }}.category" label="Kategori">
                            <option value="feature">Fitur Baru</option>
                            <option value="improvement">Peningkatan</option>
                            <option value="fix">Perbaikan Bug</option>
                            <option value="security">Keamanan</option>
                        </flux:select>

                        <flux:input wire:model="items.{{ $index }}.title" label="Judul Catatan (Opsional)"
                            placeholder="Contoh: Pencarian customer" />

                        <flux:textarea wire:model="items.{{ $index }}.description" label="Deskripsi"
                            placeholder="Jelaskan perubahan yang dilakukan." />
                    </div>
                @endforeach

                <div class="flex flex-wrap justify-end gap-2 pt-2">
                    <flux:button type="button" variant="ghost" wire:click="closeModal">
                        Batal
                    </flux:button>

                    <flux:button type="submit" variant="filled" wire:loading.attr="disabled" wire:target="saveUpdate">
                        Simpan Draft
                    </flux:button>

                    <flux:button type="button" variant="primary" wire:click="saveUpdate('publish')"
                        wire:loading.attr="disabled" wire:target="saveUpdate">
                        Publikasikan
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
