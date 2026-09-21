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
        $this->resetForm();

        $this->modal('template-form')->show();
    }

    public function editTemplate(int $id): void
    {
        $template = MessageTemplate::query()->findOrFail($id);

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

            $template->update([
                'name' => $this->name,
                'content' => $this->content,
                'is_active' => $this->isActive,
            ]);
        } else {
            MessageTemplate::create([
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
        MessageTemplate::query()->findOrFail($id)->delete();

        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $template = MessageTemplate::query()->findOrFail($id);

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
        $templates = MessageTemplate::query()->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))->latest()->paginate(20);

        return $this->view([
            'templates' => $templates,
        ]);
    }
};
?>

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between gap-4">

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

        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari template..." icon="magnifying-glass" />

    </flux:card>

    {{-- TEMPLATE LIST --}}
    <flux:card>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead>
                    <tr class="border-b text-left">

                        <th class="px-4 py-3">
                            Template
                        </th>

                        <th class="px-4 py-3">
                            Preview
                        </th>

                        <th class="px-4 py-3">
                            Status
                        </th>

                        <th class="px-4 py-3 text-right">
                            Action
                        </th>

                    </tr>
                </thead>

                <tbody>

                    @forelse ($templates as $template)
                        <tr wire:key="template-{{ $template->id }}" class="border-b last:border-0">

                            <td class="px-4 py-4">

                                <div class="font-medium">
                                    {{ $template->name }}
                                </div>

                                <div class="mt-1 text-xs text-zinc-500">
                                    {{ $template->created_at->format('d M Y H:i') }}
                                </div>

                            </td>


                            <td class="px-4 py-4">

                                <div class="max-w-xl whitespace-pre-line text-sm text-zinc-600">
                                    {{ \Illuminate\Support\Str::limit($template->content, 150) }}
                                </div>

                            </td>


                            <td class="px-4 py-4">

                                @if ($template->is_active)
                                    <flux:badge color="green">
                                        Aktif
                                    </flux:badge>
                                @else
                                    <flux:badge>
                                        Nonaktif
                                    </flux:badge>
                                @endif

                            </td>


                            <td class="px-4 py-4">

                                <div class="flex justify-end gap-2">

                                    <flux:button size="sm" variant="ghost"
                                        wire:click="toggleActive({{ $template->id }})">
                                        {{ $template->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </flux:button>

                                    <flux:button size="sm" wire:click="editTemplate({{ $template->id }})">
                                        Edit
                                    </flux:button>

                                    <flux:button size="sm" variant="ghost"
                                        wire:click="deleteTemplate({{ $template->id }})"
                                        wire:confirm="Hapus template ini?">
                                        Hapus
                                    </flux:button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="px-4 py-12 text-center">
                                <flux:text>
                                    Belum ada template.
                                </flux:text>
                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($templates->hasPages())
            <div class="border-t p-4">
                {{ $templates->links() }}
            </div>
        @endif

    </flux:card>

    {{-- FORM MODAL --}}
    <flux:modal name="template-form" class="md:w-162">

        <form wire:submit="saveTemplate" class="space-y-6">

            <div>
                <flux:heading size="lg">
                    {{ $editingId ? 'Edit Template' : 'Add Template' }}
                </flux:heading>
                <flux:text class="mt-1">
                    Buat template pesan yang dapat digunakan
                    saat menghubungi customer.
                </flux:text>
            </div>

            {{-- NAME --}}
            <flux:input wire:model="name" label="Nama Template" placeholder="Contoh: Penawaran Kontrak" />

            @error('name')
                <flux:text class="text-red-600">
                    {{ $message }}
                </flux:text>
            @enderror

            {{-- CONTENT --}}
            <div x-data="{
                insertVariable(variable) {
                    const textarea = $refs.content;
            
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
            }" class="space-y-3">
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
                    <flux:text class="text-red-600">
                        {{ $message }}
                    </flux:text>
                @enderror

                <div class="space-y-3">

                    <flux:text class="text-xs text-zinc-500">
                        Klik variable untuk memasukkannya ke posisi cursor.
                    </flux:text>

                    {{-- DATA CUSTOMER --}}
                    <div class="space-y-2">
                        <flux:text class="text-xs font-medium text-zinc-700 dark:text-zinc-300">
                            Data Customer
                        </flux:text>

                        <div class="flex flex-wrap gap-2">

                            <flux:button type="button" size="sm" variant="ghost"
                                x-on:click="insertVariable('&#123;&#123;nama&#125;&#125;')">
                                Nama
                            </flux:button>

                            <flux:button type="button" size="sm" variant="ghost"
                                x-on:click="insertVariable('&#123;&#123;nomor_kontrak&#125;&#125;')">
                                Nomor Kontrak
                            </flux:button>

                            <flux:button type="button" size="sm" variant="ghost"
                                x-on:click="insertVariable('&#123;&#123;nominal&#125;&#125;')">
                                Nominal
                            </flux:button>

                            <flux:button type="button" size="sm" variant="ghost"
                                x-on:click="insertVariable('&#123;&#123;cabang&#125;&#125;')">
                                Cabang
                            </flux:button>

                        </div>
                    </div>

                    {{-- PILIHAN SALES --}}
                    <div class="space-y-2">
                        <flux:text class="text-xs font-medium text-zinc-700 dark:text-zinc-300">
                            Pilihan Saat Kirim
                        </flux:text>

                        <div class="flex flex-wrap gap-2">

                            <flux:button type="button" size="sm" variant="ghost"
                                x-on:click="insertVariable('&#123;&#123;sapaan_waktu&#125;&#125;')">
                                Sapaan Waktu
                            </flux:button>

                            <flux:button type="button" size="sm" variant="ghost"
                                x-on:click="insertVariable('&#123;&#123;panggilan&#125;&#125;')">
                                Panggilan
                            </flux:button>

                            <flux:button type="button" size="sm" variant="ghost"
                                x-on:click="insertVariable('&#123;&#123;panggilan_singkat&#125;&#125;')">
                                Panggilan Singkat
                            </flux:button>

                        </div>
                    </div>

                </div>
            </div>

            <div class="flex justify-end gap-2 border-t pt-4">

                <flux:button type="button" variant="ghost" wire:click="cancelForm">
                    Batal
                </flux:button>

                <flux:button type="submit" variant="primary">
                    {{ $editingId ? 'Update Template' : 'Simpan Template' }}
                </flux:button>

            </div>

        </form>

    </flux:modal>

</div>
