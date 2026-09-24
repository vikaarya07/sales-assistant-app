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
    <div
        class="relative overflow-hidden border-b border-indigo-100/60 bg-linear-to-r from-indigo-50/70 via-violet-50/50 to-fuchsia-50/40 dark:border-indigo-500/10 dark:from-indigo-950/30 dark:via-violet-950/20 dark:to-fuchsia-950/20">

        <div
            class="pointer-events-none absolute -top-32 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-linear-to-br from-indigo-400/10 via-violet-400/8 to-fuchsia-400/8 blur-3xl dark:from-indigo-500/10 dark:via-violet-500/8 dark:to-fuchsia-500/8">
        </div>

        <div class="relative mx-auto max-w-7xl px-6 py-10 lg:px-8">

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <div class="flex items-center gap-3">

                        <div
                            class="flex size-11 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-400 to-violet-500 text-white shadow-lg shadow-indigo-500/15">
                            <flux:icon name="document-text" class="size-5" />
                        </div>

                        <div>
                            <flux:heading size="xl">
                                Message Templates
                            </flux:heading>

                            <flux:text class="mt-1">
                                Buat dan kelola template pesan WhatsApp.
                            </flux:text>
                        </div>

                    </div>
                </div>

                <flux:button variant="primary" icon="plus" wire:click="createTemplate"
                    class="bg-linear-to-r from-indigo-500 via-violet-500 to-fuchsia-500 shadow-lg shadow-indigo-500/15 transition duration-300 hover:-translate-y-0.5 hover:from-indigo-600 hover:via-violet-600 hover:to-fuchsia-600 hover:shadow-indigo-500/20">
                    Add Template
                </flux:button>

            </div>

        </div>
    </div>


    {{-- CONTENT --}}
    <div class="mx-auto max-w-7xl space-y-6 px-6 py-8 lg:px-8">

        {{-- SEARCH --}}
        <flux:card
            class="border-indigo-100/70 bg-white/80 shadow-sm backdrop-blur transition duration-300 dark:border-indigo-500/10 dark:bg-zinc-900/80">

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari template..."
                    icon="magnifying-glass" class="sm:max-w-md" />

                @if ($search)
                    <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="$set('search', '')"
                        class="hover:bg-fuchsia-50 hover:text-fuchsia-600 dark:hover:bg-fuchsia-500/10 dark:hover:text-fuchsia-400">
                        Clear
                    </flux:button>
                @endif

            </div>

        </flux:card>

        {{-- TEMPLATE LIST --}}
        <flux:card
            class="overflow-hidden border-indigo-100/70 shadow-sm transition duration-300 dark:border-indigo-500/10">

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    {{-- TABLE HEADER --}}
                    <thead>
                        <tr
                            class="border-b border-indigo-100/70 bg-linear-to-r from-indigo-50/60 via-violet-50/40 to-fuchsia-50/30 text-left dark:border-indigo-500/10 dark:from-indigo-950/30 dark:via-violet-950/20 dark:to-fuchsia-950/10">

                            <th class="px-4 py-3 font-semibold text-indigo-700/80 dark:text-indigo-300/80">
                                Template
                            </th>

                            <th class="px-4 py-3 font-semibold text-violet-700/80 dark:text-violet-300/80">
                                Preview
                            </th>

                            <th class="px-4 py-3 font-semibold text-fuchsia-700/70 dark:text-fuchsia-300/70">
                                Status
                            </th>

                            <th class="px-4 py-3 text-right font-semibold text-violet-700/80 dark:text-violet-300/80">
                                Action
                            </th>

                        </tr>
                    </thead>


                    {{-- TABLE BODY --}}
                    <tbody>

                        @forelse ($templates as $template)
                            <tr wire:key="template-{{ $template->id }}"
                                class="group border-b border-indigo-50 transition duration-200 last:border-0 hover:bg-linear-to-r hover:from-indigo-50/30 hover:via-violet-50/20 hover:to-fuchsia-50/20 dark:border-zinc-800 dark:hover:from-indigo-500/5 dark:hover:via-violet-500/5 dark:hover:to-fuchsia-500/5">

                                {{-- TEMPLATE --}}
                                <td class="px-4 py-4">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-indigo-100/80 via-violet-100/70 to-fuchsia-100/60 text-indigo-600 ring-1 ring-indigo-200/60 transition duration-300 group-hover:scale-105 group-hover:shadow-md group-hover:shadow-indigo-500/10 dark:from-indigo-500/10 dark:via-violet-500/10 dark:to-fuchsia-500/10 dark:text-indigo-400 dark:ring-indigo-500/15">

                                            <flux:icon name="document-text" class="size-5" />

                                        </div>

                                        <div class="min-w-0">

                                            <div class="font-semibold text-zinc-900 dark:text-white">
                                                {{ $template->name }}
                                            </div>

                                            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-500">
                                                Dibuat
                                                {{ $template->created_at->format('d M Y H:i') }}
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- PREVIEW --}}
                                <td class="px-4 py-4">

                                    <div class="max-w-xl">

                                        <div
                                            class="rounded-lg border border-indigo-100/60 bg-linear-to-r from-indigo-50/40 via-violet-50/30 to-fuchsia-50/20 px-3 py-2 text-sm leading-relaxed text-zinc-600 transition group-hover:border-violet-200/60 group-hover:from-indigo-50/60 group-hover:via-violet-50/50 group-hover:to-fuchsia-50/30 dark:border-indigo-500/10 dark:from-indigo-500/5 dark:via-violet-500/5 dark:to-fuchsia-500/5 dark:text-zinc-400 dark:group-hover:border-violet-500/15 dark:group-hover:bg-zinc-800/70">

                                            <span class="line-clamp-3 whitespace-pre-line">
                                                {{ \Illuminate\Support\Str::limit($template->content, 150) }}
                                            </span>

                                        </div>

                                    </div>

                                </td>


                                {{-- STATUS --}}
                                <td class="px-4 py-4">

                                    @if ($template->is_active)
                                        <flux:badge color="green" icon="check"
                                            class="shadow-sm shadow-emerald-500/10">
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

                                        {{-- TOGGLE --}}
                                        <flux:button size="sm" variant="ghost"
                                            :icon="$template->is_active ? 'eye-slash' : 'eye'"
                                            wire:click="toggleActive({{ $template->id }})"
                                            class="{{ $template->is_active
                                                ? 'hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-amber-500/10 dark:hover:text-amber-400'
                                                : 'hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-500/10 dark:hover:text-emerald-400' }}">

                                            {{ $template->is_active ? 'Nonaktifkan' : 'Aktifkan' }}

                                        </flux:button>


                                        {{-- EDIT --}}
                                        <flux:button size="sm" variant="ghost" icon="pencil"
                                            wire:click="editTemplate({{ $template->id }})"
                                            class="hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-400">
                                            Edit
                                        </flux:button>


                                        {{-- DELETE --}}
                                        <flux:button size="sm" variant="ghost" icon="trash"
                                            wire:click="deleteTemplate({{ $template->id }})"
                                            wire:confirm="Hapus template ini?"
                                            class="hover:bg-fuchsia-50 hover:text-fuchsia-600 dark:hover:bg-fuchsia-500/10 dark:hover:text-fuchsia-400">
                                            Hapus
                                        </flux:button>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4">

                                    <div class="flex flex-col items-center justify-center py-20 text-center">

                                        <div
                                            class="relative flex size-16 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-100/80 via-violet-100/70 to-fuchsia-100/60 text-indigo-500 shadow-inner dark:from-indigo-500/10 dark:via-violet-500/10 dark:to-fuchsia-500/10 dark:text-indigo-400">

                                            <div
                                                class="absolute inset-0 rounded-2xl bg-linear-to-br from-indigo-400/10 via-violet-400/10 to-fuchsia-400/10 blur-xl">
                                            </div>

                                            <flux:icon name="document-text" class="relative size-7" />

                                        </div>

                                        <flux:heading size="sm" class="mt-5">
                                            Belum ada template
                                        </flux:heading>

                                        <flux:text class="mt-1 max-w-sm">
                                            Buat template pertama untuk digunakan
                                            saat menghubungi customer.
                                        </flux:text>

                                        <flux:button
                                            class="mt-5 bg-linear-to-r from-indigo-500 via-violet-500 to-fuchsia-500 shadow-lg shadow-indigo-500/15"
                                            size="sm" variant="primary" icon="plus" wire:click="createTemplate">
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

                <div
                    class="bg-linear-to-r from-indigo-50/30 via-violet-50/20 to-fuchsia-50/20 p-4 dark:from-indigo-950/20 dark:via-violet-950/10 dark:to-fuchsia-950/10">
                    {{ $templates->links() }}
                </div>
            @endif

        </flux:card>


        {{-- FORM MODAL --}}
        <flux:modal name="template-form" class="w-full max-w-2xl" :dismissible="false">

            <form wire:submit="saveTemplate" class="space-y-6">

                {{-- MODAL HEADER --}}
                <div class="flex items-start gap-4">

                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-indigo-400 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/15">

                        <flux:icon :name="$editingId ? 'pencil' : 'plus'" class="size-5" />

                    </div>

                    <div>

                        <flux:heading size="lg">
                            {{ $editingId ? 'Edit Template' : 'Add Template' }}
                        </flux:heading>

                        <flux:text class="mt-1">
                            Buat template pesan yang dapat digunakan
                            saat menghubungi customer.
                        </flux:text>

                    </div>

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

                    <flux:text class="text-sm font-semibold">
                        Isi Pesan
                    </flux:text>

                    <div class="relative">

                        <div
                            class="absolute -inset-px rounded-xl bg-linear-to-r from-indigo-400/15 via-violet-400/15 to-fuchsia-400/15 opacity-0 blur transition duration-300 focus-within:opacity-100">
                        </div>

                        <textarea x-ref="content" wire:model="content" rows="12"
                            class="relative w-full rounded-xl border border-indigo-200/60 bg-white px-4 py-3 text-sm leading-relaxed text-zinc-900 shadow-sm outline-none transition duration-300 placeholder:text-zinc-400 focus:border-violet-400 focus:ring-2 focus:ring-violet-500/15 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:focus:border-violet-500 dark:focus:ring-violet-500/15"
                            placeholder="&#123;&#123;sapaan_waktu&#125;&#125;, &#123;&#123;panggilan&#125;&#125; *&#123;&#123;nama&#125;&#125;*

Saya dari Astra Credit Companies (ACC) Prioritas Dana.

Saat ini &#123;&#123;panggilan&#125;&#125; mendapatkan kesempatan pencairan dana sebesar *Rp &#123;&#123;nominal&#125;&#125;* dengan nomor kontrak *&#123;&#123;nomor_kontrak&#125;&#125;*

Apakah berkenan untuk cek hitungan angsurannya dulu &#123;&#123;panggilan_singkat&#125;&#125;?

Info lebih lanjut:

085113292236 (SETYA)"></textarea>

                    </div>


                    @error('content')
                        <flux:text class="text-sm text-red-600">
                            {{ $message }}
                        </flux:text>
                    @enderror


                    <div class="space-y-5">

                        <flux:text class="text-xs text-zinc-500">
                            Klik variable untuk memasukkannya ke posisi cursor.
                        </flux:text>


                        {{-- DATA CUSTOMER --}}
                        <div
                            class="rounded-xl border border-indigo-100/70 bg-linear-to-br from-indigo-50/60 via-violet-50/30 to-white p-4 dark:border-indigo-500/10 dark:from-indigo-500/5 dark:via-violet-500/5 dark:to-zinc-900">

                            <div class="mb-3 flex items-center gap-2">

                                <div
                                    class="flex size-7 items-center justify-center rounded-lg bg-indigo-100/80 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">

                                    <flux:icon name="user" class="size-4" />

                                </div>

                                <flux:text class="text-xs font-semibold text-indigo-700/80 dark:text-indigo-300/80">
                                    Data Customer
                                </flux:text>

                            </div>


                            <div class="flex flex-wrap gap-2">

                                <button type="button" x-on:click="insertVariable('&#123;&#123;nama&#125;&#125;')"
                                    class="rounded-lg border border-indigo-200/70 bg-white/80 px-3 py-1.5 text-xs font-medium text-indigo-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-indigo-400 hover:bg-indigo-50 hover:shadow-md dark:border-indigo-500/20 dark:bg-zinc-900 dark:text-indigo-300 dark:hover:bg-indigo-500/10">
                                    @{{ nama }}
                                </button>

                                <button type="button"
                                    x-on:click="insertVariable('&#123;&#123;nomor_kontrak&#125;&#125;')"
                                    class="rounded-lg border border-indigo-200/70 bg-white/80 px-3 py-1.5 text-xs font-medium text-indigo-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-indigo-400 hover:bg-indigo-50 hover:shadow-md dark:border-indigo-500/20 dark:bg-zinc-900 dark:text-indigo-300 dark:hover:bg-indigo-500/10">
                                    @{{ nomor_kontrak }}
                                </button>

                                <button type="button" x-on:click="insertVariable('&#123;&#123;nominal&#125;&#125;')"
                                    class="rounded-lg border border-indigo-200/70 bg-white/80 px-3 py-1.5 text-xs font-medium text-indigo-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-indigo-400 hover:bg-indigo-50 hover:shadow-md dark:border-indigo-500/20 dark:bg-zinc-900 dark:text-indigo-300 dark:hover:bg-indigo-500/10">
                                    @{{ nominal }}
                                </button>

                                <button type="button" x-on:click="insertVariable('&#123;&#123;cabang&#125;&#125;')"
                                    class="rounded-lg border border-indigo-200/70 bg-white/80 px-3 py-1.5 text-xs font-medium text-indigo-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-indigo-400 hover:bg-indigo-50 hover:shadow-md dark:border-indigo-500/20 dark:bg-zinc-900 dark:text-indigo-300 dark:hover:bg-indigo-500/10">
                                    @{{ cabang }}
                                </button>

                            </div>

                        </div>


                        {{-- PILIHAN SALES --}}
                        <div
                            class="rounded-xl border border-violet-100/70 bg-linear-to-br from-violet-50/60 via-fuchsia-50/30 to-white p-4 dark:border-violet-500/10 dark:from-violet-500/5 dark:via-fuchsia-500/5 dark:to-zinc-900">

                            <div class="mb-3 flex items-center gap-2">

                                <div
                                    class="flex size-7 items-center justify-center rounded-lg bg-violet-100/80 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">

                                    <flux:icon name="adjustments-horizontal" class="size-4" />

                                </div>

                                <flux:text class="text-xs font-semibold text-violet-700/80 dark:text-violet-300/80">
                                    Pilihan Saat Kirim
                                </flux:text>

                            </div>


                            <div class="flex flex-wrap gap-2">

                                <button type="button"
                                    x-on:click="insertVariable('&#123;&#123;sapaan_waktu&#125;&#125;')"
                                    class="rounded-lg border border-violet-200/70 bg-white/80 px-3 py-1.5 text-xs font-medium text-violet-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-violet-400 hover:bg-violet-50 hover:shadow-md dark:border-violet-500/20 dark:bg-zinc-900 dark:text-violet-300 dark:hover:bg-violet-500/10">
                                    @{{ sapaan_waktu }}
                                </button>

                                <button type="button"
                                    x-on:click="insertVariable('&#123;&#123;panggilan&#125;&#125;')"
                                    class="rounded-lg border border-violet-200/70 bg-white/80 px-3 py-1.5 text-xs font-medium text-violet-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-violet-400 hover:bg-violet-50 hover:shadow-md dark:border-violet-500/20 dark:bg-zinc-900 dark:text-violet-300 dark:hover:bg-violet-500/10">
                                    @{{ panggilan }}
                                </button>

                                <button type="button"
                                    x-on:click="insertVariable('&#123;&#123;panggilan_singkat&#125;&#125;')"
                                    class="rounded-lg border border-violet-200/70 bg-white/80 px-3 py-1.5 text-xs font-medium text-violet-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-violet-400 hover:bg-violet-50 hover:shadow-md dark:border-violet-500/20 dark:bg-zinc-900 dark:text-violet-300 dark:hover:bg-violet-500/10">
                                    @{{ panggilan_singkat }}
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- INFO --}}
                <div
                    class="rounded-xl border border-violet-200/60 bg-linear-to-r from-indigo-50/50 via-violet-50/50 to-fuchsia-50/30 p-4 dark:border-violet-500/10 dark:from-indigo-500/5 dark:via-violet-500/5 dark:to-fuchsia-500/5">

                    <div class="flex gap-3">

                        <div
                            class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-violet-100/80 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">

                            <flux:icon name="information-circle" class="size-5" />

                        </div>

                        <div>

                            <flux:text class="text-xs leading-relaxed">
                                Variable akan otomatis diganti dengan data customer
                                ketika pesan dibuat di WhatsApp Composer.
                            </flux:text>

                        </div>

                    </div>

                </div>

                {{-- FOOTER --}}
                <flux:separator />

                <div class="flex items-center justify-end gap-3">

                    <flux:button type="button" variant="ghost" wire:click="cancelForm"
                        class="hover:bg-fuchsia-50 hover:text-fuchsia-600 dark:hover:bg-fuchsia-500/10 dark:hover:text-fuchsia-400">
                        Batal
                    </flux:button>

                    <flux:button type="submit" variant="primary" icon="{{ $editingId ? 'check' : 'plus' }}"
                        class="bg-linear-to-r from-indigo-500 via-violet-500 to-fuchsia-500 shadow-lg shadow-indigo-500/15 transition duration-300 hover:shadow-indigo-500/20">
                        {{ $editingId ? 'Update Template' : 'Simpan Template' }}
                    </flux:button>

                </div>

            </form>

        </flux:modal>

    </div>

</div>
