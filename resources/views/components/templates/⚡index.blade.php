<?php

use App\Models\MessageTemplate;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public ?int $editingId = null;

    public string $name = '';

    public string $content = '';

    public bool $isActive = true;

    public string $search = '';

    public function createTemplate(): void
    {
        $this->authorize('create', MessageTemplate::class);

        $this->resetForm();

        $this->modal('template-form')->show();
    }

    public function editTemplate(int $id): void
    {
        $template = auth()->user()->messageTemplates()->findOrFail($id);

        $this->authorize('update', $template);

        $this->editingId = $template->id;
        $this->name = $template->name;
        $this->content = $template->content;
        $this->isActive = $template->is_active;

        $this->resetValidation();

        $this->modal('template-form')->show();
    }

    public function saveTemplate(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'isActive' => ['boolean'],
        ]);

        if ($this->editingId) {
            $template = MessageTemplate::query()->findOrFail($this->editingId);

            $this->authorize('update', $template);

            $template->update([
                'name' => $this->name,
                'content' => $this->content,
                'is_active' => $this->isActive,
            ]);
        } else {
            $this->authorize('create', MessageTemplate::class);

            auth()
                ->user()
                ->messageTemplates()
                ->create([
                    'name' => $this->name,
                    'content' => $this->content,
                    'is_active' => $this->isActive,
                ]);
        }

        $this->resetForm();

        $this->modal('template-form')->close();
    }

    public function deleteTemplate(int $id): void
    {
        $template = auth()->user()->messageTemplates()->findOrFail($id);

        $this->authorize('delete', $template);

        $template->delete();

        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $template = auth()->user()->messageTemplates()->findOrFail($id);

        $this->authorize('update', $template);

        $template->update([
            'is_active' => !$template->is_active,
        ]);
    }

    public function cancelForm(): void
    {
        $this->resetForm();

        $this->modal('template-form')->close();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->content = '';
        $this->isActive = true;

        $this->resetValidation();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $templates = auth()->user()->messageTemplates()->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))->latest()->paginate(20);

        return $this->view([
            'templates' => $templates,
        ]);
    }
};
?>

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">
                Message Templates
            </flux:heading>

            <flux:text class="mt-1">
                Buat dan kelola template pesan WhatsApp.
            </flux:text>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="createTemplate">
            Add Template
        </flux:button>
    </div>


    {{-- SEARCH --}}
    <flux:card>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari template..." icon="magnifying-glass"
                class="sm:max-w-md" />

            @if ($search)
                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="$set('search', '')">
                    Clear
                </flux:button>
            @endif
        </div>
    </flux:card>


    {{-- TEMPLATE LIST --}}
    <flux:card class="overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                {{-- TABLE HEADER --}}
                <thead>
                    <tr class="border-b border-zinc-200 text-left dark:border-zinc-700">
                        <th class="px-4 py-3 font-medium">
                            Template
                        </th>

                        <th class="px-4 py-3 font-medium">
                            Preview
                        </th>

                        <th class="px-4 py-3 font-medium">
                            Status
                        </th>

                        <th class="px-4 py-3 text-right font-medium">
                            Action
                        </th>
                    </tr>
                </thead>


                {{-- TABLE BODY --}}
                <tbody>

                    @forelse ($templates as $template)
                        <tr wire:key="template-{{ $template->id }}"
                            class="border-b border-zinc-100 last:border-0 dark:border-zinc-800">

                            {{-- TEMPLATE --}}
                            <td class="px-4 py-4">
                                <div class="flex items-start gap-3">

                                    <flux:avatar icon="document-text" size="sm" />

                                    <div class="min-w-0">

                                        <div class="font-medium">
                                            {{ $template->name }}
                                        </div>

                                        <div class="mt-1 text-xs text-zinc-500">
                                            Dibuat
                                            {{ $template->created_at->format('d M Y H:i') }}
                                        </div>

                                    </div>

                                </div>
                            </td>


                            {{-- PREVIEW --}}
                            <td class="px-4 py-4">
                                <div class="max-w-xl">

                                    <div class="whitespace-pre-line text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ \Illuminate\Support\Str::limit($template->content, 150) }}
                                    </div>

                                </div>
                            </td>


                            {{-- STATUS --}}
                            <td class="px-4 py-4">

                                @if ($template->is_active)
                                    <flux:badge color="green" icon="check">
                                        Aktif
                                    </flux:badge>
                                @else
                                    <flux:badge color="zinc" icon="minus">
                                        Nonaktif
                                    </flux:badge>
                                @endif

                            </td>


                            {{-- ACTION --}}
                            <td class="px-4 py-4">

                                <div class="flex justify-end gap-2">

                                    <flux:button size="sm" variant="ghost"
                                        :icon="$template->is_active ? 'eye-slash' : 'eye'"
                                        wire:click="toggleActive({{ $template->id }})">
                                        {{ $template->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </flux:button>

                                    <flux:button size="sm" variant="ghost" icon="pencil"
                                        wire:click="editTemplate({{ $template->id }})">
                                        Edit
                                    </flux:button>

                                    <flux:button size="sm" variant="ghost" icon="trash"
                                        wire:click="deleteTemplate({{ $template->id }})"
                                        wire:confirm="Hapus template ini?">
                                        Hapus
                                    </flux:button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4">

                                <div class="flex flex-col items-center justify-center py-16 text-center">

                                    <div
                                        class="flex size-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                                        <flux:icon name="document-text" class="size-6 text-zinc-400" />
                                    </div>

                                    <flux:heading size="sm" class="mt-4">
                                        Belum ada template
                                    </flux:heading>

                                    <flux:text class="mt-1">
                                        Buat template pertama untuk digunakan saat menghubungi customer.
                                    </flux:text>

                                    <flux:button class="mt-4" size="sm" variant="primary" icon="plus"
                                        wire:click="createTemplate">
                                        Add Template
                                    </flux:button>

                                </div>

                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>


        {{-- PAGINATION --}}
        @if ($templates->hasPages())
            <flux:separator />

            <div class="p-4">
                {{ $templates->links() }}
            </div>
        @endif

    </flux:card>



    {{-- FORM MODAL --}}
    <flux:modal name="template-form" class="w-full max-w-2xl">

        <form wire:submit="saveTemplate" class="space-y-6">

            {{-- MODAL HEADER --}}
            <div>

                <flux:heading size="lg">
                    {{ $editingId ? 'Edit Template' : 'Add Template' }}
                </flux:heading>

                <flux:text class="mt-1">
                    Buat template pesan yang dapat digunakan
                    saat menghubungi customer.
                </flux:text>

            </div>


            {{-- TEMPLATE NAME --}}
            <flux:input wire:model="name" label="Nama Template" placeholder="Contoh: Penawaran Prioritas Dana" />


            {{-- MESSAGE CONTENT --}}
            <div x-data="{
                insertVariable(variable) {
                    const textarea = $refs.content;
            
                    textarea.focus();
            
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
            
                    textarea.setRangeText(
                        variable,
                        start,
                        end,
                        'end'
                    );
            
                    textarea.dispatchEvent(
                        new Event('input', { bubbles: true })
                    );
            
                    textarea.focus();
                }
            }" class="space-y-4">

                <flux:text class="text-sm font-medium">
                    Isi Pesan
                </flux:text>

                <textarea x-ref="content" wire:model="content" rows="12"
                    class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none transition focus:border-zinc-400 focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:focus:border-zinc-600 dark:focus:ring-zinc-800"
                    placeholder="&#123;&#123;sapaan_waktu&#125;&#125;, &#123;&#123;panggilan&#125;&#125; *&#123;&#123;nama&#125;&#125;*

Saya dari Astra Credit Companies (ACC) Prioritas Dana.

Saat ini &#123;&#123;panggilan&#125;&#125; mendapatkan kesempatan pencairan dana sebesar *Rp &#123;&#123;nominal&#125;&#125;* dengan nomor kontrak *&#123;&#123;nomor_kontrak&#125;&#125;*

Apakah berkenan untuk cek hitungan angsurannya dulu &#123;&#123;panggilan_singkat&#125;&#125;?

Info lebih lanjut:

085113292236 (SETYA)"></textarea>


                @error('content')
                    <flux:text class="text-sm text-red-600">
                        {{ $message }}
                    </flux:text>
                @enderror

                <div class="space-y-4">

                    <flux:text class="text-xs text-zinc-500">
                        Klik variable untuk memasukkannya ke posisi cursor.
                    </flux:text>

                    {{-- DATA CUSTOMER --}}
                    <div class="space-y-2">
                        <flux:text class="text-xs font-medium text-zinc-700 dark:text-zinc-300">
                            Data Customer
                        </flux:text>

                        <div class="flex flex-wrap gap-2">

                            <button type="button" x-on:click="insertVariable('&#123;&#123;nama&#125;&#125;')"
                                class="rounded-md border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50 active:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                                Nama
                            </button>

                            <button type="button" x-on:click="insertVariable('&#123;&#123;nomor_kontrak&#125;&#125;')"
                                class="rounded-md border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50 active:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                                Nomor Kontrak
                            </button>

                            <button type="button" x-on:click="insertVariable('&#123;&#123;nominal&#125;&#125;')"
                                class="rounded-md border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50 active:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                                Nominal
                            </button>

                            <button type="button" x-on:click="insertVariable('&#123;&#123;cabang&#125;&#125;')"
                                class="rounded-md border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50 active:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                                Cabang
                            </button>

                        </div>
                    </div>

                    {{-- PILIHAN SALES --}}
                    <div class="space-y-2">

                        <flux:text class="text-xs font-medium text-zinc-700 dark:text-zinc-300">
                            Pilihan Saat Kirim
                        </flux:text>

                        <div class="flex flex-wrap gap-2">

                            <button type="button" x-on:click="insertVariable('&#123;&#123;sapaan_waktu&#125;&#125;')"
                                class="rounded-md border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50 active:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                                Sapaan Waktu
                            </button>

                            <button type="button" x-on:click="insertVariable('&#123;&#123;panggilan&#125;&#125;')"
                                class="rounded-md border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50 active:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                                Panggilan
                            </button>

                            <button type="button"
                                x-on:click="insertVariable('&#123;&#123;panggilan_singkat&#125;&#125;')"
                                class="rounded-md border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50 active:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                                Panggilan Singkat
                            </button>

                        </div>
                    </div>

                </div>


            </div>

            {{-- INFO --}}
            <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800/50">

                <div class="flex gap-3">

                    <flux:icon name="information-circle" class="mt-0.5 size-5 shrink-0 text-zinc-500" />

                    <div>

                        <flux:text class="text-xs">
                            Variable akan otomatis diganti dengan data customer
                            ketika pesan dibuat di WhatsApp Composer.
                        </flux:text>

                    </div>

                </div>

            </div>


            {{-- FOOTER --}}
            <flux:separator />

            <div class="flex items-center justify-between gap-3">

                <flux:button type="button" variant="ghost" wire:click="cancelForm">
                    Batal
                </flux:button>

                <flux:button type="submit" variant="primary" icon="{{ $editingId ? 'check' : 'plus' }}">
                    {{ $editingId ? 'Update Template' : 'Simpan Template' }}
                </flux:button>

            </div>

        </form>

    </flux:modal>

</div>
