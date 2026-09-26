<?php

use App\Enums\CustomerStatus;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $importText = '';
    public string $search = '';
    public string $status = '';
    public array $result = [];

    public string $greeting = 'Selamat pagi';
    public string $address = 'Bapak';

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Composer
    |--------------------------------------------------------------------------
    */

    public ?int $selectedCustomerId = null;
    public string $selectedTemplateId = '';
    public string $message = '';

    /*
    |--------------------------------------------------------------------------
    | Bulk Delete
    |--------------------------------------------------------------------------
    */

    public array $selectedCustomers = [];
    public bool $selectAll = false;

    /*
    |--------------------------------------------------------------------------
    | Edit Customer
    |--------------------------------------------------------------------------
    */

    public ?int $editingCustomerId = null;

    public string $editName = '';
    public string $editPhone = '';
    public string $editContractNumber = '';
    public string $editAmount = '';
    public string $editBranch = '';
    public string $editStatus = '';

    /*
    |--------------------------------------------------------------------------
    | Import Customer
    |--------------------------------------------------------------------------
    */

    public function importCustomers(): void
    {
        $this->validate([
            'importText' => ['required', 'string'],
        ]);

        $lines = preg_split('/\r\n|\r|\n/', trim($this->importText));

        $created = 0;
        $duplicate = 0;
        $invalid = 0;

        DB::transaction(function () use ($lines, &$created, &$duplicate, &$invalid) {
            foreach ($lines as $line) {
                $line = trim($line);

                if ($line === '') {
                    continue;
                }

                $customer = $this->parseCustomerLine($line);

                if ($customer === null) {
                    $invalid++;
                    continue;
                }

                $exists = auth()->user()->customers()->where('phone_normalized', $customer['phone_normalized'])->exists();

                if ($exists) {
                    $duplicate++;
                    continue;
                }

                auth()->user()->customers()->create($customer);

                $created++;
            }
        });

        $this->result = [
            'created' => $created,
            'duplicate' => $duplicate,
            'invalid' => $invalid,
        ];

        $this->reset('importText');

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Parse Customer
    |--------------------------------------------------------------------------
    */

    private function parseCustomerLine(string $line): ?array
    {
        $columns = preg_split('/\t+/', $line);

        if (count($columns) !== 5) {
            $columns = preg_split('/\s{2,}/', $line);
        }

        if (count($columns) !== 5) {
            return null;
        }

        [$phone, $name, $contractNumber, $amount, $branch] = array_map('trim', $columns);

        if ($phone === '' || $name === '' || $contractNumber === '' || $amount === '' || $branch === '') {
            return null;
        }

        $phoneNormalized = $this->normalizePhone($phone);

        if ($phoneNormalized === null) {
            return null;
        }

        $amountValue = $this->normalizeAmount($amount);

        if ($amountValue === null) {
            return null;
        }

        return [
            'phone' => $phone,
            'phone_normalized' => $phoneNormalized,
            'name' => $name,
            'contract_number' => $contractNumber,
            'amount' => $amountValue,
            'branch' => $branch,
            'source' => 'paste',
            'status' => CustomerStatus::NEW,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Phone
    |--------------------------------------------------------------------------
    */

    private function normalizePhone(string $phone): ?string
    {
        $phone = preg_replace('/\D+/', '', $phone);

        if ($phone === '') {
            return null;
        }

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        if (!str_starts_with($phone, '62')) {
            return null;
        }

        if (!preg_match('/^628\d{7,12}$/', $phone)) {
            return null;
        }

        return $phone;
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Amount
    |--------------------------------------------------------------------------
    */

    private function normalizeAmount(string $amount): ?int
    {
        $amount = preg_replace('/\D+/', '', $amount);

        if ($amount === '') {
            return null;
        }

        return (int) $amount;
    }

    /*
    |--------------------------------------------------------------------------
    | Search & Filter
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    /*
    |--------------------------------------------------------------------------
    | Select Customer
    |--------------------------------------------------------------------------
    */

    public function updatedSelectedCustomers(): void
    {
        $this->selectAll = false;
    }

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedCustomers = $this->getCurrentPageCustomerIds();
        } else {
            $this->selectedCustomers = [];
        }
    }

    private function getCurrentPageCustomerIds(): array
    {
        return auth()
            ->user()
            ->customers()
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('contract_number', 'like', '%' . $this->search . '%')
                        ->orWhere('branch', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->latest()
            ->limit(20)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function clearSelection(): void
    {
        $this->selectedCustomers = [];
        $this->selectAll = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Delete
    |--------------------------------------------------------------------------
    */

    public function deleteSelected(): void
    {
        if (empty($this->selectedCustomers)) {
            return;
        }

        $customers = auth()->user()->customers()->whereIn('id', $this->selectedCustomers)->get();

        foreach ($customers as $customer) {
            $this->authorize('delete', $customer);
        }

        foreach ($customers as $customer) {
            $customer->delete();
        }

        $this->clearSelection();

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Customer
    |--------------------------------------------------------------------------
    */

    public function openEditCustomer(int $customerId): void
    {
        $customer = auth()->user()->customers()->findOrFail($customerId);

        $this->authorize('update', $customer);

        $this->editingCustomerId = $customer->id;

        $this->editName = $customer->name;
        $this->editPhone = $customer->phone;
        $this->editContractNumber = $customer->contract_number;
        $this->editAmount = (string) $customer->amount;
        $this->editBranch = $customer->branch;
        $this->editStatus = $customer->status->value;

        $this->resetValidation();

        $this->modal('customer-edit')->show();
    }

    public function saveCustomer(): void
    {
        $this->validate([
            'editName' => ['required', 'string', 'max:255'],

            'editPhone' => ['required', 'string'],

            'editContractNumber' => ['required', 'string', 'max:255'],

            'editAmount' => ['required', 'string'],

            'editBranch' => ['required', 'string', 'max:255'],

            'editStatus' => ['required', 'string'],
        ]);

        if (!$this->editingCustomerId) {
            return;
        }

        $customer = auth()->user()->customers()->findOrFail($this->editingCustomerId);

        $this->authorize('update', $customer);

        $phoneNormalized = $this->normalizePhone($this->editPhone);

        if ($phoneNormalized === null) {
            $this->addError('editPhone', 'Nomor WhatsApp tidak valid.');

            return;
        }

        $amount = $this->normalizeAmount($this->editAmount);

        if ($amount === null) {
            $this->addError('editAmount', 'Nominal tidak valid.');

            return;
        }

        $newStatus = CustomerStatus::tryFrom($this->editStatus);

        if (!$newStatus) {
            $this->addError('editStatus', 'Status tidak valid.');

            return;
        }

        $duplicatePhone = auth()->user()->customers()->where('phone_normalized', $phoneNormalized)->where('id', '!=', $customer->id)->exists();

        if ($duplicatePhone) {
            $this->addError('editPhone', 'Nomor WhatsApp sudah digunakan customer lain.');

            return;
        }

        $customer->update([
            'name' => $this->editName,
            'phone' => $this->editPhone,
            'phone_normalized' => $phoneNormalized,
            'contract_number' => $this->editContractNumber,
            'amount' => $amount,
            'branch' => $this->editBranch,
            'status' => $newStatus,
        ]);

        $this->closeEditCustomer();
    }

    public function closeEditCustomer(): void
    {
        $this->editingCustomerId = null;

        $this->editName = '';
        $this->editPhone = '';
        $this->editContractNumber = '';
        $this->editAmount = '';
        $this->editBranch = '';
        $this->editStatus = '';

        $this->resetValidation();

        $this->modal('customer-edit')->close();
    }

    /*
    |--------------------------------------------------------------------------
    | Quick Status
    |--------------------------------------------------------------------------
    */

    public function updateStatus(int $customerId, string $status): void
    {
        $customer = auth()->user()->customers()->findOrFail($customerId);

        $this->authorize('update', $customer);

        $newStatus = CustomerStatus::tryFrom($status);

        if (!$newStatus) {
            return;
        }

        $customer->update([
            'status' => $newStatus,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Composer
    |--------------------------------------------------------------------------
    */

    public function openComposer(int $customerId): void
    {
        $customer = auth()->user()->customers()->findOrFail($customerId);

        $this->authorize('view', $customer);

        $this->selectedCustomerId = $customer->id;
        $this->selectedTemplateId = '';
        $this->message = '';

        $this->greeting = 'Selamat pagi';
        $this->address = 'Bapak';

        $this->resetValidation();

        $this->modal('whatsapp-composer')->show();
    }

    public function closeComposer(): void
    {
        $this->selectedCustomerId = null;
        $this->selectedTemplateId = '';
        $this->message = '';

        $this->greeting = 'Selamat pagi';
        $this->address = 'Bapak';

        $this->resetValidation();

        $this->modal('whatsapp-composer')->close();
    }

    public function updatedSelectedTemplateId($templateId): void
    {
        if ($templateId === '' || !$this->selectedCustomerId) {
            $this->message = '';

            return;
        }

        $this->generateMessage();
    }

    private function generateMessage(): void
    {
        if (!$this->selectedCustomerId || $this->selectedTemplateId === '') {
            $this->message = '';

            return;
        }

        $customer = auth()->user()->customers()->findOrFail($this->selectedCustomerId);

        $this->authorize('view', $customer);

        $template = auth()->user()->messageTemplates()->where('is_active', true)->findOrFail((int) $this->selectedTemplateId);

        $shortAddress = $this->address === 'Bapak' ? 'pak' : 'bu';

        $this->message = $customer->replaceTemplateVariables($template->content, [
            '{{sapaan_waktu}}' => $this->greeting,

            '{{panggilan}}' => $this->address,

            '{{panggilan_singkat}}' => $shortAddress,
        ]);
    }

    public function updatedGreeting(): void
    {
        $this->generateMessage();
    }

    public function updatedAddress(): void
    {
        $this->generateMessage();
    }

    public function markContacted(): void
    {
        if (!$this->selectedCustomerId) {
            return;
        }

        $customer = auth()->user()->customers()->findOrFail($this->selectedCustomerId);

        $this->authorize('update', $customer);

        if ($customer->status === CustomerStatus::NEW) {
            $customer->update([
                'status' => CustomerStatus::CONTACTED,
            ]);
        }

        $customer->update([
            'last_contacted_at' => now(),
        ]);
    }

    public function sendWhatsapp(): void
    {
        $this->markContacted();
    }

    public function getWhatsappUrlProperty(): ?string
    {
        if (!$this->selectedCustomerId || blank($this->message)) {
            return null;
        }

        $customer = auth()->user()->customers()->find($this->selectedCustomerId);

        if (!$customer) {
            return null;
        }

        return $customer->whatsappUrlWithMessage($this->message);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Single
    |--------------------------------------------------------------------------
    */

    public function deleteCustomer(int $id): void
    {
        $customer = auth()->user()->customers()->findOrFail($id);

        $this->authorize('delete', $customer);

        $customer->delete();

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $customers = auth()
            ->user()
            ->customers()

            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('contract_number', 'like', '%' . $this->search . '%')
                        ->orWhere('branch', 'like', '%' . $this->search . '%');
                });
            })

            ->when($this->status, function ($query) {
                $customerStatus = CustomerStatus::tryFrom($this->status);

                if ($customerStatus) {
                    $query->where('status', $customerStatus->value);
                }
            })

            ->latest()
            ->paginate(10);

        return $this->view([
            'customers' => $customers,

            'selectedCustomer' => $this->selectedCustomerId ? auth()->user()->customers()->find($this->selectedCustomerId) : null,

            'templates' => auth()->user()->messageTemplates()->where('is_active', true)->orderBy('name')->get(),

            'statuses' => CustomerStatus::cases(),
        ]);
    }
};

?>

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="m-5 md:my-5 md:ms-2 md:me-5 rounded-xl bg-white dark:bg-zinc-800">
        <div class="mx-auto p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div
                    class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-indigo-400 to-violet-500 text-white">
                    <flux:icon name="users" class="size-6" />
                </div>

                <div>
                    <flux:heading size="xl">
                        Customers
                    </flux:heading>

                    <flux:text class="mt-1">
                        Kelola data customer Sales WhatsApp.
                    </flux:text>
                </div>
            </div>

            <flux:badge color="indigo" icon="phone-arrow-down-left" class="self-start sm:self-auto">
                Customer Management
            </flux:badge>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="m-5 md:my-5 md:ms-2 md:me-5 rounded-xl space-y-6">

        {{-- IMPORT CUSTOMER --}}
        <flux:card variant="soft" class="bg-white dark:bg-zinc-800">

            <form wire:submit="importCustomers" class="space-y-6">

                <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">

                    <div class="flex gap-4">

                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-400 to-violet-500 text-white shadow-lg shadow-indigo-500/15">
                            <flux:icon name="arrow-up-tray" class="size-5" />
                        </div>

                        <div>

                            <flux:heading size="lg">
                                Import Customer
                            </flux:heading>

                            <flux:text class="mt-1 max-w-xl">
                                Copy data dari EXCEL/SPREADSHEET lalu paste langsung
                                ke dalam aplikasi.
                            </flux:text>

                        </div>

                    </div>

                    <flux:badge color="indigo" icon="clipboard-document" variant="outline">
                        Paste Data
                    </flux:badge>

                </div>

                <div
                    class="rounded-2xl border border-dashed border-indigo-200/70 bg-indigo-50/30 p-3 transition hover:border-violet-300/70 hover:bg-violet-50/30 dark:border-violet-800/50 dark:bg-indigo-950/20 dark:hover:border-fuchsia-500/30">

                    <flux:textarea wire:model="importText" label="Data Customer" rows="7"
                        placeholder="Contoh : 85799928828 SETYA PRIORITAS DANA 07000000000000000 30.000.000 YOGYAKARTA" />

                </div>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div
                        class="flex items-start gap-2 rounded-lg bg-fuchsia-100 px-3 py-2 text-xs text-fuchsia-600 dark:bg-fuchsia-950/30 dark:text-fuchsia-300">
                        <flux:icon name="information-circle" class="mt-0.5 size-4 shrink-0" />

                        <span class="font-semibold leading-relaxed">
                            Nomor HP Nama Nomor Kontrak Nominal Cabang
                        </span>
                    </div>

                    <flux:button type="submit" variant="primary" color="violet" icon="arrow-up-tray">
                        Import Customer
                    </flux:button>

                </div>

            </form>

        </flux:card>

        {{-- RESULT --}}
        @if ($result)
            <div class="grid gap-4 sm:grid-cols-3">

                {{-- SUCCESS --}}
                <flux:card class="border-emerald-200 dark:border-emerald-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text>Berhasil</flux:text>

                            <flux:heading size="xl" class="mt-1 text-emerald-600 dark:text-emerald-400">
                                {{ $result['created'] }}
                            </flux:heading>

                            <flux:text class="mt-1 text-xs">
                                Customer berhasil diimport
                            </flux:text>
                        </div>

                        <flux:icon name="check-circle" variant="solid" class="size-8 text-emerald-500" />
                    </div>
                </flux:card>

                {{-- DUPLICATE --}}
                <flux:card class="border-amber-200 dark:border-amber-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text>Duplicate</flux:text>

                            <flux:heading size="xl" class="mt-1 text-amber-600 dark:text-amber-400">
                                {{ $result['duplicate'] }}
                            </flux:heading>

                            <flux:text class="mt-1 text-xs">
                                Data dilewati
                            </flux:text>
                        </div>

                        <flux:icon name="document-duplicate" class="size-8 text-amber-500" />
                    </div>
                </flux:card>

                {{-- INVALID --}}
                <flux:card class="border-red-200 dark:border-red-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text>Invalid</flux:text>

                            <flux:heading size="xl" class="mt-1 text-red-600 dark:text-red-400">
                                {{ $result['invalid'] }}
                            </flux:heading>

                            <flux:text class="mt-1 text-xs">
                                Data tidak valid
                            </flux:text>
                        </div>

                        <flux:icon name="exclamation-triangle" class="size-8 text-red-500" />
                    </div>
                </flux:card>

            </div>
        @endif

        {{-- SEARCH & FILTER --}}
        <flux:card variant="soft" class="bg-white dark:bg-zinc-800">

            <div class="flex flex-col gap-4 lg:flex-row lg:items-end">

                <div class="flex-1">

                    <flux:input wire:model.live.debounce.300ms="search" label="Cari Customer"
                        placeholder="Nama, nomor kontrak, cabang..." icon="magnifying-glass" />

                </div>

                <div class="w-full lg:w-60">

                    <flux:select wire:model.live="status" label="Status">

                        <flux:select.option value="">
                            Semua Status
                        </flux:select.option>

                        @foreach ($statuses as $customerStatus)
                            <flux:select.option :value="$customerStatus->value">
                                {{ $customerStatus->label() }}
                            </flux:select.option>
                        @endforeach

                    </flux:select>

                </div>

            </div>

        </flux:card>

        {{-- BULK ACTION --}}
        @if (count($selectedCustomers) > 0)
            <div
                class="rounded-2xl border border-red-200 bg-linear-to-r from-red-50 to-orange-50 p-4 shadow-sm dark:border-red-500/20 dark:from-red-500/10 dark:to-orange-500/5">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex size-9 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400">
                            <flux:icon name="check" class="size-4" />
                        </div>

                        <div>

                            <div class="font-medium">
                                {{ count($selectedCustomers) }} customer dipilih
                            </div>

                            <div class="text-xs text-zinc-500">
                                Pilih aksi yang ingin dilakukan
                            </div>

                        </div>

                    </div>

                    <div class="flex gap-2">

                        <flux:button size="sm" variant="ghost" wire:click="clearSelection">
                            Batal
                        </flux:button>

                        <flux:button size="sm" variant="danger" icon="trash" wire:click="deleteSelected"
                            wire:confirm="Hapus semua customer yang dipilih?">
                            Hapus Terpilih
                        </flux:button>

                    </div>

                </div>

            </div>
        @endif

        {{-- CUSTOMER TABLE --}}
        <flux:card variant="soft" class="bg-white dark:bg-zinc-800">

            <div class="flex items-center justify-between pb-4">

                <div class="flex items-center gap-3">

                    <div
                        class="flex size-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-500 dark:bg-indigo-500/10 dark:text-indigo-300">
                        <flux:icon name="users" class="size-6" />
                    </div>

                    <div>

                        <flux:heading size="lg">
                            Daftar Customer
                        </flux:heading>

                        <flux:text class="text-xs">
                            Kelola dan hubungi customer
                        </flux:text>

                    </div>

                </div>

                <flux:badge variant="outline">
                    {{ $customers->total() }} Customer
                </flux:badge>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead>

                        <tr
                            class="bg-linear-to-r from-indigo-100/50 via-violet-100/50 to-fuchsia-100/50 dark:from-indigo-950/30 dark:via-violet-950/20 dark:to-fuchsia-950/10">

                            <th class="w-12 px-4 py-3">

                                <input type="checkbox" wire:model.live="selectAll" wire:change="toggleSelectAll"
                                    class="size-4 rounded border-slate-300 text-indigo-500 focus:ring-indigo-400">

                            </th>

                            <th class="px-4 py-3 text-left font-medium text-slate-600">
                                Customer
                            </th>

                            <th class="px-4 py-3 text-left font-medium text-slate-600">
                                Nomor Kontrak
                            </th>

                            <th class="px-4 py-3 text-left font-medium text-slate-600">
                                Nominal
                            </th>

                            <th class="px-4 py-3 text-left font-medium text-slate-600">
                                Cabang
                            </th>

                            <th class="px-4 py-3 text-left font-medium text-slate-600">
                                Status
                            </th>

                            <th class="px-4 py-3 text-center font-medium text-slate-600">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($customers as $customer)
                            <tr wire:key="customer-{{ $customer->id }}"
                                class="group border-b border-indigo-50 transition duration-200 last:border-0 hover:bg-linear-to-r hover:from-indigo-50/30 hover:via-violet-50/20 hover:to-fuchsia-50/20 dark:border-zinc-800 dark:hover:from-indigo-500/5 dark:hover:via-violet-500/5 dark:hover:to-fuchsia-500/5">

                                {{-- CHECKBOX --}}
                                <td class="px-4 py-4">

                                    <input type="checkbox" value="{{ $customer->id }}"
                                        wire:model.live="selectedCustomers"
                                        class="size-4 rounded border-zinc-300 text-indigo-500 focus:ring-indigo-400">

                                </td>

                                {{-- CUSTOMER --}}
                                <td class="px-4 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="relative">

                                            <flux:avatar :name="$customer->name" color="auto" size="sm" />

                                        </div>

                                        <div class="min-w-0">

                                            <div class="truncate font-medium text-zinc-900 dark:text-white">
                                                {{ $customer->name }}
                                            </div>

                                            <div class="mt-0.5 font-mono text-xs text-zinc-500">
                                                +{{ $customer->phone_normalized }}
                                            </div>

                                        </div>

                                    </div>

                                </td>

                                {{-- CONTRACT --}}
                                <td class="px-4 py-4">

                                    <span
                                        class="rounded-lg bg-indigo-50 px-2 py-1 font-mono text-xs text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                                        {{ $customer->contract_number }}
                                    </span>

                                </td>

                                {{-- AMOUNT --}}
                                <td class="px-4 py-4 whitespace-nowrap">

                                    <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                        {{ $customer->formatted_amount }}
                                    </span>

                                </td>

                                {{-- BRANCH --}}
                                <td class="px-4 py-4">

                                    <flux:badge icon="building-office-2" variant="outline">
                                        {{ $customer->branch }}
                                    </flux:badge>

                                </td>

                                {{-- STATUS --}}
                                <td class="px-4 py-4">

                                    <flux:badge :color="$customer->status->color()" class="shrink-0">
                                        {{ $customer->status->label() }}
                                    </flux:badge>

                                </td>

                                {{-- ACTION --}}
                                <td class="px-4 py-4">

                                    <div class="flex justify-end gap-2">

                                        {{-- CHAT --}}
                                        <flux:button size="sm" icon="chat-bubble-left-right" variant="primary"
                                            color="emerald" wire:click="openComposer({{ $customer->id }})">
                                            Chat
                                        </flux:button>

                                        {{-- EDIT --}}
                                        <flux:button size="sm" variant="ghost" color="indigo" icon="pencil"
                                            wire:click="openEditCustomer({{ $customer->id }})">
                                        </flux:button>

                                        {{-- DELETE --}}
                                        <flux:button size="sm" variant="ghost" color="red" icon="trash"
                                            wire:click="deleteCustomer({{ $customer->id }})"
                                            wire:confirm="Hapus customer ini?">
                                        </flux:button>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7">

                                    <div class="flex flex-col items-center justify-center py-20 text-center">

                                        <div
                                            class="flex size-16 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-50 via-violet-50 to-fuchsia-50 text-indigo-400 dark:from-indigo-950/40 dark:via-violet-950/30 dark:to-fuchsia-950/20">
                                            <flux:icon name="users" class="size-7" />
                                        </div>

                                        <flux:heading size="sm" class="mt-4">
                                            Belum ada customer
                                        </flux:heading>

                                        <flux:text class="mt-1 max-w-sm">
                                            Import data customer terlebih dahulu
                                            untuk mulai menggunakan Customer Management.
                                        </flux:text>

                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            @if ($customers->hasPages())
                <flux:separator />

                <div class="p-4">
                    {{ $customers->links() }}
                </div>
            @endif

        </flux:card>

    </div>

    {{-- EDIT CUSTOMER MODAL --}}
    <flux:modal name="customer-edit" class="w-full max-w-2xl">

        <form wire:submit="saveCustomer" class="space-y-6">

            <div class="flex items-start gap-4">

                <div
                    class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-400 to-violet-500 text-white shadow-lg shadow-indigo-500/15">
                    <flux:icon name="pencil-square" class="size-5" />
                </div>

                <div>

                    <flux:heading size="lg">
                        Edit Customer
                    </flux:heading>

                    <flux:text class="mt-1">
                        Ubah informasi customer dan statusnya.
                    </flux:text>

                </div>

            </div>

            <flux:select wire:model="editStatus" label="Status">

                @foreach ($statuses as $customerStatus)
                    <flux:select.option :value="$customerStatus->value">
                        {{ $customerStatus->label() }}
                    </flux:select.option>
                @endforeach

            </flux:select>

            <div class="grid gap-5 sm:grid-cols-2">

                <flux:input wire:model="editName" label="Nama Customer" placeholder="Nama customer" />

                <flux:input wire:model="editContractNumber" label="Nomor Kontrak" placeholder="Nomor kontrak" />

                <flux:input wire:model="editAmount" label="Nominal" placeholder="30000000" />

                <flux:input wire:model="editBranch" label="Cabang" placeholder="KARAWACI" />

            </div>

            <flux:input wire:model="editPhone" label="Nomor WhatsApp" placeholder="081234567890" />

            <flux:separator />

            <div class="flex items-center justify-end gap-3">

                <flux:button type="button" variant="ghost" wire:click="closeEditCustomer">
                    Batal
                </flux:button>

                <flux:button type="submit" variant="primary" icon="check" class="shadow-lg shadow-indigo-500/15">
                    Simpan Perubahan
                </flux:button>

            </div>

        </form>

    </flux:modal>

    {{-- WHATSAPP COMPOSER --}}
    <flux:modal name="whatsapp-composer" class="w-sm md:w-full md:max-w-2xl" :dismissible="false">

        <div class="space-y-6">

            {{-- HEADER --}}
            <div class="flex items-end justify-between gap-4">

                <div class="flex gap-4">

                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-400 via-violet-400 to-fuchsia-400 text-white shadow-lg shadow-violet-500/15">
                        <flux:icon name="chat-bubble-left-right" class="size-5" />
                    </div>

                    <div>

                        <flux:heading size="lg">
                            Kirim WhatsApp
                        </flux:heading>

                        <flux:text class="mt-1">
                            Siapkan pesan sebelum membuka WhatsApp.
                        </flux:text>

                    </div>

                </div>

                @if ($selectedCustomer)
                    <flux:badge color="green" icon="check">
                        Ready
                    </flux:badge>
                @endif

            </div>

            {{-- CUSTOMER INFO --}}
            @if ($selectedCustomer)
                <div
                    class="relative overflow-hidden rounded-2xl border border-indigo-100 bg-linear-to-r from-indigo-100/60 via-violet-100/50 to-fuchsia-100/60 p-4 dark:border-violet-900/40 dark:from-indigo-500/10 dark:via-violet-500/8 dark:to-fuchsia-500/5">

                    <div class="flex items-center gap-3">

                        <flux:avatar :name="$selectedCustomer->name" size="md" color="auto" />

                        <div class="min-w-0">

                            <div class="font-semibold">
                                {{ $selectedCustomer->name }}
                            </div>

                            <flux:text class="text-xs">
                                +{{ $selectedCustomer->phone_normalized }}
                            </flux:text>

                        </div>

                        <flux:spacer />

                        <flux:badge color="blue" variant="outline">
                            {{ $selectedCustomer->branch }}
                        </flux:badge>

                    </div>

                </div>
            @endif

            {{-- TEMPLATE --}}
            <flux:select wire:model.live="selectedTemplateId" label="Template Pesan"
                placeholder="-- Pilih Template --">

                @foreach ($templates as $template)
                    <flux:select.option :value="(string) $template->id">
                        {{ $template->name }}
                    </flux:select.option>
                @endforeach

            </flux:select>

            {{-- CUSTOMIZATION --}}
            @if ($selectedTemplateId)
                <div
                    class="grid gap-5 rounded-2xl border border-indigo-100 bg-linear-to-r from-indigo-50/40 via-violet-50/30 to-fuchsia-50/20 p-5 dark:border-violet-900/40 dark:from-indigo-500/5 dark:via-violet-500/5 dark:to-fuchsia-500/5 sm:grid-cols-2">

                    {{-- GREETING --}}
                    <div>

                        <div class="mb-2 flex items-center gap-2">

                            <flux:icon name="sun" class="size-4 text-violet-400" />

                            <flux:text class="font-medium">
                                Sapaan Waktu
                            </flux:text>

                        </div>

                        <div class="flex flex-wrap gap-2">

                            <flux:button type="button" size="sm"
                                :variant="$greeting === 'Selamat pagi' ? 'primary' : 'ghost'"
                                wire:click="$set('greeting', 'Selamat pagi')">
                                Pagi
                            </flux:button>

                            <flux:button type="button" size="sm"
                                :variant="$greeting === 'Selamat siang' ? 'primary' : 'ghost'"
                                wire:click="$set('greeting', 'Selamat siang')">
                                Siang
                            </flux:button>

                            <flux:button type="button" size="sm"
                                :variant="$greeting === 'Selamat sore' ? 'primary' : 'ghost'"
                                wire:click="$set('greeting', 'Selamat sore')">
                                Sore
                            </flux:button>

                        </div>

                    </div>

                    {{-- ADDRESS --}}
                    <div>

                        <div class="mb-2 flex items-center gap-2">

                            <flux:icon name="user" class="size-4 text-fuchsia-400" />

                            <flux:text class="font-medium">
                                Panggilan
                            </flux:text>

                        </div>

                        <div class="flex flex-wrap gap-2">

                            <flux:button type="button" size="sm"
                                :variant="$address === 'Bapak' ? 'primary' : 'ghost'"
                                wire:click="$set('address', 'Bapak')">
                                Bapak
                            </flux:button>

                            <flux:button type="button" size="sm"
                                :variant="$address === 'Ibu' ? 'primary' : 'ghost'"
                                wire:click="$set('address', 'Ibu')">
                                Ibu
                            </flux:button>

                        </div>

                    </div>

                </div>
            @endif

            {{-- PREVIEW --}}
            <div>

                <div class="mb-2 flex items-center justify-between">

                    <div class="flex items-center gap-2">

                        <flux:icon name="eye" class="size-4 text-violet-500" />

                        <flux:text class="font-medium">
                            Preview Pesan
                        </flux:text>

                    </div>

                    @if ($message)
                        <flux:badge color="green" icon="check">
                            Siap dikirim
                        </flux:badge>
                    @endif

                </div>

                <div
                    class="overflow-hidden rounded-2xl border border-indigo-100 shadow-lg shadow-violet-900/5 dark:border-violet-900/40">

                    {{-- CHAT HEADER --}}
                    <div
                        class="flex items-center gap-3 border-b border-emerald-900/20 bg-emerald-600 px-4 py-3 text-white dark:border-emerald-800">

                        <flux:avatar :name="$selectedCustomer?->name ?? 'Customer'" circle size="sm"
                            color="auto" />

                        <div class="min-w-0">

                            <div class="truncate text-sm font-semibold">
                                {{ $selectedCustomer?->name ?? 'Customer' }}
                            </div>

                            @if ($selectedCustomer)
                                <div class="text-xs">
                                    +{{ $selectedCustomer->phone_normalized }}
                                </div>
                            @endif

                        </div>

                    </div>

                    {{-- CHAT BODY --}}
                    <div class="relative min-h-80 overflow-y-auto bg-[#efeae2] p-4 dark:bg-zinc-800">

                        {{-- Decorative --}}
                        <div class="pointer-events-none absolute inset-0 opacity-[0.035]"
                            style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 16px 16px;">
                        </div>

                        @if (filled($message))
                            <div class="relative flex justify-end">

                                <div
                                    class="max-w-[85%] rounded-xl rounded-tr-sm bg-[#d9fdd3] px-3 py-2 shadow-sm dark:bg-emerald-900">

                                    <div
                                        class="whitespace-pre-line wrap-break-words text-[13px] leading-relaxed text-zinc-800 dark:text-zinc-100">
                                        {{ $message }}
                                    </div>

                                    <div class="mt-1 flex items-center justify-end gap-1 text-[10px] text-zinc-500">

                                        <span>
                                            {{ now()->format('H:i') }}
                                        </span>

                                        <flux:icon name="check" variant="mini" class="text-blue-500" />

                                    </div>

                                </div>

                            </div>
                        @else
                            <div class="relative flex min-h-72 items-center justify-center">

                                <div class="text-center">

                                    <div
                                        class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-50 via-violet-50 to-fuchsia-50 text-violet-400 shadow-sm dark:from-indigo-950/40 dark:via-violet-950/30 dark:to-fuchsia-950/20">

                                        <flux:icon name="chat-bubble-left-right" class="size-7" />

                                    </div>

                                    <flux:text class="mt-3">
                                        Pilih template untuk melihat preview
                                    </flux:text>

                                </div>

                            </div>
                        @endif

                    </div>

                </div>

            </div>

            {{-- FOOTER --}}
            <flux:separator />

            <div class="flex items-center justify-end gap-3">

                <flux:button type="button" variant="ghost" wire:click="closeComposer">
                    Batal
                </flux:button>

                @if ($this->whatsappUrl)
                    <flux:button href="{{ $this->whatsappUrl }}" target="_blank" variant="primary"
                        icon="arrow-top-right-on-square" color="emerald" wire:click="markContacted">
                        Buka WhatsApp
                    </flux:button>
                @else
                    <flux:button type="button" variant="primary" icon="arrow-top-right-on-square" disabled>
                        Buka WhatsApp
                    </flux:button>
                @endif

            </div>

        </div>

    </flux:modal>

</div>
