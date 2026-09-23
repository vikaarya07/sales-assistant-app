<?php

use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Models\MessageTemplate;
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

    public ?int $selectedCustomerId = null;

    public string $selectedTemplateId = '';

    public string $message = '';

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
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

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

        $customer = Customer::query()->findOrFail($this->selectedCustomerId);

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

        $customer = Customer::query()->find($this->selectedCustomerId);

        if (!$customer) {
            return null;
        }

        return $customer->whatsappUrlWithMessage($this->message);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function deleteCustomer(int $id): void
    {
        $customer = Customer::query()->findOrFail($id);

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
                $status = CustomerStatus::tryFrom($this->status);

                if ($status) {
                    $query->where('status', $status->value);
                }
            })
            ->latest()
            ->paginate(20);

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
    <div>
        <flux:heading size="xl">
            Customers
        </flux:heading>

        <flux:text class="mt-1">
            Kelola data customer Sales WhatsApp.
        </flux:text>
    </div>

    {{-- IMPORT CUSTOMER --}}
    <flux:card>
        <form wire:submit="importCustomers" class="space-y-6">

            <div class="flex items-start justify-between gap-4">
                <div>
                    <flux:heading size="lg">
                        Import Customer
                    </flux:heading>

                    <flux:text class="mt-1">
                        Copy data dari kantor lalu paste langsung di sini.
                    </flux:text>
                </div>

                <flux:badge icon="clipboard-document" variant="outline">
                    Paste Data
                </flux:badge>
            </div>

            <flux:textarea wire:model="importText" label="Data Customer" rows="8"
                placeholder="Paste data customer di sini..." />

            @error('importText')
                <flux:text class="text-red-600" variant="strong">
                    {{ $message }}
                </flux:text>
            @enderror

            <div class="flex items-center justify-between gap-3">

                <flux:text class="text-xs text-zinc-500">
                    Format: Nomor HP · Nama · Nomor Kontrak · Nominal · Cabang
                </flux:text>

                <flux:button type="submit" variant="primary" icon="arrow-up-tray">
                    Import Customer
                </flux:button>

            </div>
        </form>
    </flux:card>

    {{-- RESULT --}}
    @if ($result)
        <div class="grid gap-4 sm:grid-cols-3">

            <flux:card>
                <div class="flex items-start justify-between">

                    <div>
                        <flux:text>
                            Berhasil
                        </flux:text>

                        <flux:heading size="xl" class="mt-1">
                            {{ $result['created'] }}
                        </flux:heading>
                    </div>

                    <flux:badge color="green" icon="check">
                        Import
                    </flux:badge>

                </div>
            </flux:card>

            <flux:card>
                <div class="flex items-start justify-between">

                    <div>
                        <flux:text>
                            Duplicate
                        </flux:text>

                        <flux:heading size="xl" class="mt-1">
                            {{ $result['duplicate'] }}
                        </flux:heading>
                    </div>

                    <flux:badge color="amber" icon="document-duplicate">
                        Skip
                    </flux:badge>

                </div>
            </flux:card>

            <flux:card>
                <div class="flex items-start justify-between">

                    <div>
                        <flux:text>
                            Invalid
                        </flux:text>

                        <flux:heading size="xl" class="mt-1">
                            {{ $result['invalid'] }}
                        </flux:heading>
                    </div>

                    <flux:badge color="red" icon="exclamation-triangle">
                        Error
                    </flux:badge>

                </div>
            </flux:card>

        </div>
    @endif

    {{-- SEARCH & FILTER --}}
    <flux:card>

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">

            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" label="Cari Customer"
                    placeholder="Nama, nomor kontrak, cabang..." icon="magnifying-glass" />
            </div>

            <div class="w-full lg:w-56">

                <flux:select wire:model.live="status" label="Status" placeholder="Semua Status">

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

    {{-- CUSTOMER TABLE --}}
    <flux:card class="overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-700">

                        <th class="px-4 py-3 text-left font-medium">
                            Customer
                        </th>

                        <th class="px-4 py-3 text-left font-medium">
                            Nomor Kontrak
                        </th>

                        <th class="px-4 py-3 text-left font-medium">
                            Nominal
                        </th>

                        <th class="px-4 py-3 text-left font-medium">
                            Cabang
                        </th>

                        <th class="px-4 py-3 text-left font-medium">
                            Status
                        </th>

                        <th class="px-4 py-3 text-right font-medium">
                            Action
                        </th>

                    </tr>
                </thead>

                <tbody>

                    @forelse ($customers as $customer)
                        <tr wire:key="customer-{{ $customer->id }}"
                            class="border-b border-zinc-100 last:border-0 dark:border-zinc-800">

                            {{-- CUSTOMER --}}
                            <td class="px-4 py-4">

                                <div class="flex items-center gap-3">

                                    <flux:avatar :name="$customer->name" size="sm" />

                                    <div class="min-w-0">

                                        <div class="truncate font-medium">
                                            {{ $customer->name }}
                                        </div>

                                        <div class="text-xs text-zinc-500">
                                            +{{ $customer->phone_normalized }}
                                        </div>

                                    </div>

                                </div>

                            </td>

                            {{-- CONTRACT --}}
                            <td class="px-4 py-4">

                                <flux:text class="font-mono text-xs">
                                    {{ $customer->contract_number }}
                                </flux:text>

                            </td>

                            {{-- AMOUNT --}}
                            <td class="px-4 py-4 whitespace-nowrap">

                                <flux:text>
                                    {{ $customer->formatted_amount }}
                                </flux:text>

                            </td>

                            {{-- BRANCH --}}
                            <td class="px-4 py-4">

                                <flux:badge icon="building-office-2" variant="outline">
                                    {{ $customer->branch }}
                                </flux:badge>

                            </td>

                            {{-- STATUS --}}
                            <td class="px-4 py-4">

                                <flux:badge :color="$customer->status->color()">
                                    {{ $customer->status->label() }}
                                </flux:badge>

                            </td>

                            {{-- ACTION --}}
                            <td class="px-4 py-4">

                                <div class="flex justify-end gap-2">

                                    <flux:button size="sm" variant="primary" icon="chat-bubble-left-right"
                                        wire:click="openComposer({{ $customer->id }})">
                                        Chat
                                    </flux:button>

                                    <flux:button size="sm" variant="ghost" icon="trash"
                                        wire:click="deleteCustomer({{ $customer->id }})"
                                        wire:confirm="Hapus customer ini?">
                                        Hapus
                                    </flux:button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6">

                                <div class="flex flex-col items-center justify-center gap-2 py-16">

                                    <flux:icon name="users" class="size-10 text-zinc-400" />

                                    <flux:heading size="sm">
                                        Belum ada customer
                                    </flux:heading>

                                    <flux:text>
                                        Import data customer terlebih dahulu.
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

    {{-- WHATSAPP COMPOSER --}}
    <flux:modal name="whatsapp-composer" class="w-full max-w-2xl" :dismissible="false">

        <div class="space-y-6">

            {{-- HEADER --}}
            <div class="flex items-end justify-between gap-4">

                <div>

                    <flux:heading size="lg">
                        Kirim WhatsApp
                    </flux:heading>

                    <flux:text class="mt-1">
                        Siapkan pesan sebelum membuka WhatsApp.
                    </flux:text>

                </div>

                @if ($selectedCustomer)
                    <flux:badge icon="chat-bubble-left-right" color="green">
                        WhatsApp
                    </flux:badge>
                @endif

            </div>

            {{-- CUSTOMER INFO --}}
            @if ($selectedCustomer)
                <div class="flex items-center gap-3 rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">

                    <flux:avatar :name="$selectedCustomer->name" size="md" />

                    <div class="min-w-0">

                        <div class="font-medium">
                            {{ $selectedCustomer->name }}
                        </div>

                        <flux:text class="text-xs">
                            +{{ $selectedCustomer->phone_normalized }}
                        </flux:text>

                    </div>

                    <flux:spacer />

                    <flux:badge variant="outline">
                        {{ $selectedCustomer->branch }}
                    </flux:badge>

                </div>
            @endif

            {{-- TEMPLATE --}}
            <flux:select wire:model.live="selectedTemplateId" label="Template Pesan" placeholder="Pilih template...">

                @foreach ($templates as $template)
                    <flux:select.option :value="(string) $template->id">
                        {{ $template->name }}
                    </flux:select.option>
                @endforeach

            </flux:select>

            {{-- CUSTOMIZATION --}}
            @if ($selectedTemplateId)
                <div class="grid gap-5 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700 sm:grid-cols-2">

                    {{-- SAPAAN --}}
                    <div>

                        <flux:text class="mb-2 font-medium">
                            Sapaan
                        </flux:text>

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

                    {{-- PANGGILAN --}}
                    <div>

                        <flux:text class="mb-2 font-medium">
                            Panggilan
                        </flux:text>

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

                    <flux:text class="font-medium">
                        Preview Pesan
                    </flux:text>

                    @if ($message)
                        <flux:badge color="green" icon="check">
                            Siap dikirim
                        </flux:badge>
                    @endif

                </div>

                <div class="overflow-hidden rounded-2xl border border-zinc-200 shadow-sm dark:border-zinc-700">

                    {{-- CHAT HEADER --}}
                    <div
                        class="flex items-center gap-3 border-b border-zinc-200 bg-white px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">

                        <flux:avatar :name="$selectedCustomer?->name ?? 'Customer'" size="sm" />

                        <div class="min-w-0">

                            <div class="truncate text-sm font-semibold">
                                {{ $selectedCustomer?->name ?? 'Customer' }}
                            </div>

                            @if ($selectedCustomer)
                                <div class="text-xs text-zinc-500">
                                    +{{ $selectedCustomer->phone_normalized }}
                                </div>
                            @endif

                        </div>

                    </div>

                    {{-- CHAT BODY --}}
                    <div class="min-h-80 overflow-y-auto bg-[#efeae2] p-4 dark:bg-zinc-950">

                        @if (filled($message))
                            <div class="flex justify-end">

                                <div
                                    class="max-w-[85%] rounded-xl rounded-tr-sm bg-[#d9fdd3] px-3 py-2 shadow-sm dark:bg-emerald-900">

                                    <div
                                        class="whitespace-pre-line wrap-break-words text-[13px] leading-relaxed text-zinc-800 dark:text-zinc-100">
                                        {{ $message }}
                                    </div>

                                    <div class="mt-1 flex items-center justify-end gap-1 text-[10px] text-zinc-500">

                                        <span>
                                            {{ now()->format('H\:i') }}
                                        </span>

                                        <flux:icon name="check" variant="mini" class="text-blue-500" />

                                    </div>

                                </div>

                            </div>
                        @else
                            <div class="flex min-h-72 items-center justify-center">

                                <div class="text-center">

                                    <flux:icon name="chat-bubble-left-right" class="mx-auto size-10 text-zinc-400" />

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

                <div class="flex gap-2">

                    @if ($this->whatsappUrl)
                        <flux:button href="{{ $this->whatsappUrl }}" target="_blank" variant="primary"
                            icon="arrow-top-right-on-square" wire:click="markContacted">
                            Buka WhatsApp
                        </flux:button>
                    @else
                        <flux:button type="button" variant="primary" icon="arrow-top-right-on-square" disabled>
                            Buka WhatsApp
                        </flux:button>
                    @endif

                </div>

            </div>

        </div>

    </flux:modal>

</div>
