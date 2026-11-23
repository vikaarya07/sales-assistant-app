<?php

use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Livewire\Traits\WithSorting;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;
    use WithSorting;

    public string $importText = '';
    public string $search = '';
    public array $selectedStatuses = [];
    public array $result = [];
    public string $greeting = 'Selamat pagi';
    public string $address = 'Bapak';
    public string $createdDate = '';

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Composer
    |--------------------------------------------------------------------------
    */

    public ?int $selectedCustomerId = null;
    public string $selectedTemplateId = '';
    public string $message = '';
    public string $originalMessage = '';

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
    public array $editStatus = [];

    /*
    |--------------------------------------------------------------------------
    | Manual Customer
    |--------------------------------------------------------------------------
    */

    public bool $showManualCustomer = false;
    public string $manualName = '';
    public string $manualPhone = '';
    public string $manualContractNumber = '';
    public string $manualAmount = '';
    public string $manualBranch = '';

    public ?int $deletingCustomerId = null;

    /*
    |--------------------------------------------------------------------------
    | Import Customer
    |--------------------------------------------------------------------------
    */

    public function importCustomers(): void
    {
        $this->validate(
            [
                'importText' => ['required', 'string'],
            ],
            [
                'importText.required' => 'Data customer wajib diisi.',
                'importText.string' => 'Data customer tidak valid.',
            ],
        );

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

                $newCustomer = auth()->user()->customers()->create($customer);

                $newCustomer->syncStatuses([CustomerStatus::NEW->value]);

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

        if ($created > 0) {
            $this->dispatch('swal', type: 'success', message: "{$created} customer berhasil diimport.");
        } elseif ($duplicate > 0 && $invalid === 0) {
            $this->dispatch('swal', type: 'warning', message: 'Tidak ada customer baru yang diimport. Semua data sudah terdaftar.');
        } else {
            $this->dispatch('swal', type: 'warning', message: 'Tidak ada data customer yang berhasil diimport.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Manual Customer
    |--------------------------------------------------------------------------
    */

    public function openManualCustomer(): void
    {
        $this->reset(['manualName', 'manualPhone', 'manualContractNumber', 'manualAmount', 'manualBranch']);

        $this->resetValidation();

        $this->modal('customer-manual')->show();
    }

    public function saveManualCustomer(): void
    {
        $this->validate(
            [
                'manualName' => ['required', 'string', 'max:255'],
                'manualPhone' => ['required', 'string'],
                'manualContractNumber' => ['required', 'string', 'digits:17'],
                'manualAmount' => ['required', 'string'],
                'manualBranch' => ['required', 'string', 'max:255'],
            ],
            [
                'manualName.required' => 'Nama customer wajib diisi.',
                'manualName.string' => 'Nama customer harus berupa teks.',
                'manualName.max' => 'Nama customer maksimal 255 karakter.',
                'manualPhone.required' => 'Nomor WhatsApp wajib diisi.',
                'manualPhone.string' => 'Nomor WhatsApp harus berupa teks.',
                'manualContractNumber.required' => 'Nomor kontrak wajib diisi.',
                'manualContractNumber.string' => 'Nomor kontrak harus berupa angka.',
                'manualContractNumber.digits' => 'Nomor kontrak harus tepat 17 digit.',
                'manualAmount.required' => 'Nominal wajib diisi.',
                'manualAmount.string' => 'Nominal tidak valid.',
                'manualBranch.required' => 'Cabang wajib diisi.',
                'manualBranch.string' => 'Nama cabang tidak valid.',
                'manualBranch.max' => 'Nama cabang maksimal 255 karakter.',
            ],
        );

        $phoneNormalized = $this->normalizePhone($this->manualPhone);

        if ($phoneNormalized === null) {
            $this->addError('manualPhone', 'Nomor WhatsApp tidak valid. Gunakan nomor Indonesia yang benar.');

            return;
        }

        $amount = $this->unformatIdr($this->manualAmount);

        if ($amount === '') {
            $this->addError('manualAmount', 'Nominal tidak valid.');

            return;
        }

        if ((int) $amount > 30000000) {
            $this->addError('manualAmount', 'Nominal maksimal Rp30.000.000.');

            return;
        }

        $exists = auth()->user()->customers()->where('phone_normalized', $phoneNormalized)->exists();

        if ($exists) {
            $this->addError('manualPhone', 'Nomor WhatsApp sudah digunakan customer lain.');

            return;
        }

        $newCustomer = auth()
            ->user()
            ->customers()
            ->create([
                'phone' => $this->manualPhone,
                'phone_normalized' => $phoneNormalized,
                'name' => $this->manualName,
                'contract_number' => $this->manualContractNumber,
                'amount' => (int) $amount,
                'branch' => $this->manualBranch,
                'source' => 'manual',
                'status' => CustomerStatus::NEW,
            ]);

        $newCustomer->syncStatuses([CustomerStatus::NEW->value]);

        $this->closeManualCustomer();
        $this->resetPage();

        $this->result = [
            'created' => 1,
            'duplicate' => 0,
            'invalid' => 0,
        ];

        $this->dispatch('swal', type: 'success', message: 'Customer berhasil ditambahkan.');
    }

    public function closeManualCustomer(): void
    {
        $this->reset(['manualName', 'manualPhone', 'manualContractNumber', 'manualAmount', 'manualBranch']);

        $this->resetValidation();

        $this->modal('customer-manual')->close();
    }

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    public function formatIdr(string|int|null $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $number = preg_replace('/\D/', '', (string) $value);

        return $number === '' ? '' : number_format((int) $number, 0, ',', '.');
    }

    public function unformatIdr(string|int|null $value): string
    {
        return preg_replace('/\D/', '', (string) $value) ?? '';
    }

    public function updatedManualAmount(): void
    {
        $this->manualAmount = $this->formatIdr($this->manualAmount);
    }

    public function updatedEditAmount(): void
    {
        $this->editAmount = $this->formatIdr($this->editAmount);
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

        // Nomor kontrak wajib tepat 17 digit.
        if (!preg_match('/^\d{17}$/', $contractNumber)) {
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

        if ($amountValue > 30000000) {
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

    public function updatedSelectedStatuses(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedCreatedDate(): void
    {
        $this->resetPage();
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
        $validStatuses = array_map(fn(CustomerStatus $status) => $status->value, CustomerStatus::cases());

        $selectedStatuses = array_values(array_intersect($this->selectedStatuses, $validStatuses));

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
            ->when($selectedStatuses !== [], fn($query) => $query->whereHas('statusAssignments', fn($statusQuery) => $statusQuery->whereIn('status', $selectedStatuses)))
            ->when($this->createdDate, function ($query) {
                $query->whereDate('created_at', $this->createdDate);
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

    public function confirmDeleteSelected(): void
    {
        if (empty($this->selectedCustomers)) {
            return;
        }

        $this->dispatch('confirm', title: 'Hapus customer terpilih?', message: 'Apakah Anda yakin ingin menghapus ' . count($this->selectedCustomers) . ' customer yang dipilih?', action: 'delete-selected-confirmed');
    }

    #[On('delete-selected-confirmed')]
    public function deleteSelected(): void
    {
        if (empty($this->selectedCustomers)) {
            return;
        }

        $customers = auth()->user()->customers()->whereIn('id', $this->selectedCustomers)->get();

        foreach ($customers as $customer) {
            $this->authorize('delete', $customer);
        }

        $count = $customers->count();

        foreach ($customers as $customer) {
            $customer->delete();
        }

        $this->clearSelection();
        $this->resetPage();

        $this->dispatch('swal', type: 'success', message: "{$count} customer berhasil dihapus.");
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
        $this->editAmount = $this->formatIdr($customer->amount);
        $this->editBranch = $customer->branch;

        $this->editStatus = $customer
            ->statusAssignments()
            ->pluck('status')
            ->map(function ($status): string {
                return $status instanceof CustomerStatus ? $status->value : (string) $status;
            })
            ->unique()
            ->values()
            ->all();

        if ($this->editStatus === []) {
            $this->editStatus = [$customer->status instanceof CustomerStatus ? $customer->status->value : (string) $customer->status];
        }

        $this->resetValidation();

        $this->modal('customer-edit')->show();
    }

    public function saveCustomer(): void
    {
        if ($this->editingCustomerId === null) {
            return;
        }

        $customer = auth()->user()->customers()->findOrFail($this->editingCustomerId);

        $this->authorize('update', $customer);

        $validated = $this->validate(
            [
                'editName' => ['required', 'string', 'max:255'],
                'editPhone' => ['required', 'string', 'max:20'],
                'editContractNumber' => ['required', 'string', 'digits:17'],
                'editAmount' => ['required', 'string'],
                'editBranch' => ['required', 'string', 'max:255'],
                'editStatus' => ['required', 'array', 'min:1'],
                'editStatus.*' => ['required', 'string', 'distinct', Rule::enum(CustomerStatus::class)],
            ],
            [
                'editName.required' => 'Nama customer wajib diisi.',
                'editPhone.required' => 'Nomor WhatsApp wajib diisi.',
                'editContractNumber.required' => 'Nomor kontrak wajib diisi.',
                'editContractNumber.digits' => 'Nomor kontrak harus tepat 17 digit.',
                'editAmount.required' => 'Nominal wajib diisi.',
                'editBranch.required' => 'Cabang wajib diisi.',
                'editStatus.required' => 'Pilih minimal satu status.',
                'editStatus.min' => 'Pilih minimal satu status.',
                'editStatus.*.distinct' => 'Status tidak boleh duplikat.',
                'editStatus.*.enum' => 'Terdapat status customer yang tidak valid.',
            ],
        );

        $phoneNormalized = $this->normalizePhone($validated['editPhone']);

        if ($phoneNormalized === null) {
            $this->addError('editPhone', 'Nomor WhatsApp tidak valid. Gunakan nomor Indonesia yang benar.');

            return;
        }

        $amount = $this->unformatIdr($validated['editAmount']);

        if ($amount === '') {
            $this->addError('editAmount', 'Nominal tidak valid.');

            return;
        }

        if ((int) $amount > 30000000) {
            $this->addError('editAmount', 'Nominal maksimal Rp30.000.000.');

            return;
        }

        $duplicate = auth()->user()->customers()->where('phone_normalized', $phoneNormalized)->where('id', '!=', $customer->id)->exists();

        if ($duplicate) {
            $this->addError('editPhone', 'Nomor WhatsApp sudah digunakan customer lain.');

            return;
        }

        $statusValues = collect($validated['editStatus'])->unique()->values()->all();

        foreach ($statusValues as $status) {
            if (CustomerStatus::tryFrom($status) === null) {
                $this->addError('editStatus', 'Terdapat status customer yang tidak valid.');

                return;
            }
        }

        if ($statusValues === []) {
            $this->addError('editStatus', 'Pilih minimal satu status.');

            return;
        }

        DB::transaction(function () use ($customer, $validated, $phoneNormalized, $amount, $statusValues): void {
            $customer->update([
                'name' => $validated['editName'],
                'phone' => $validated['editPhone'],
                'phone_normalized' => $phoneNormalized,
                'contract_number' => $validated['editContractNumber'],
                'amount' => (int) $amount,
                'branch' => $validated['editBranch'],
            ]);

            $customer->syncStatuses($statusValues);
        });

        $this->closeEditCustomer();

        $this->dispatch('swal', type: 'success', message: 'Data customer dan status berhasil diperbarui.');
    }

    public function closeEditCustomer(): void
    {
        $this->editingCustomerId = null;
        $this->editName = '';
        $this->editPhone = '';
        $this->editContractNumber = '';
        $this->editAmount = '';
        $this->editBranch = '';
        $this->editStatus = [];

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

        if ($newStatus === null) {
            return;
        }

        $customer->syncStatuses([$newStatus->value]);

        $this->dispatch('swal', type: 'success', message: 'Status customer berhasil diperbarui.');
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
        $this->originalMessage = '';

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
        $this->originalMessage = '';

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
            '{{nama}}' => $customer->name,
            '{{nomor_kontrak}}' => $customer->contract_number,
            '{{nomor_telepon}}' => $customer->phone,
            '{{nominal}}' => $customer->formatted_amount,
            '{{cabang}}' => $customer->branch,

            '{{sapaan_waktu}}' => $this->greeting,
            '{{panggilan}}' => $this->address,
            '{{panggilan_singkat}}' => $shortAddress,
        ]);

        $this->originalMessage = $this->message;
    }

    public function resetMessage(): void
    {
        $this->message = $this->originalMessage;
    }

    public function clearStatusFilter(): void
    {
        $this->selectedStatuses = [];

        $this->resetPage();
        $this->clearSelection();
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

        DB::transaction(function () use ($customer): void {
            $customer->refresh();

            $currentStatus = $customer->status instanceof CustomerStatus ? $customer->status->value : $customer->status;

            if ($currentStatus === CustomerStatus::NEW->value) {
                $customer->syncStatuses([CustomerStatus::BLAST->value]);
            }

            // Pastikan status assignment yang masih NEW ikut disinkronkan.
            $hasNewAssignment = $customer->statusAssignments()->where('status', CustomerStatus::NEW->value)->exists();

            if ($hasNewAssignment) {
                $customer->syncStatuses([CustomerStatus::BLAST->value]);
            }

            $customer->update([
                'last_contacted_at' => now(),
            ]);
        });

        $this->dispatch('swal', type: 'success', message: 'Customer berhasil ditandai sudah dihubungi.');
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

    public function confirmDeleteCustomer(int $id): void
    {
        $customer = auth()->user()->customers()->findOrFail($id);

        $this->authorize('delete', $customer);

        $this->deletingCustomerId = $customer->id;

        $this->dispatch('confirm', title: 'Hapus Customer?', message: "{$customer->name} akan dihapus secara permanen.", confirmText: 'Ya, hapus', cancelText: 'Batal', action: 'delete-customer');
    }

    #[On('delete-customer')]
    public function deleteCustomer(): void
    {
        if (!$this->deletingCustomerId) {
            return;
        }

        $customer = auth()->user()->customers()->findOrFail($this->deletingCustomerId);

        $this->authorize('delete', $customer);

        $customer->delete();

        $this->deletingCustomerId = null;

        $this->resetPage();

        $this->dispatch('swal', type: 'success', title: 'Berhasil', message: 'Customer berhasil dihapus.');
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
            ->with('statusAssignments')
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('contract_number', 'like', '%' . $this->search . '%')
                        ->orWhere('branch', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedStatuses !== [], function ($query) {
                $validStatuses = array_map(fn(CustomerStatus $status) => $status->value, CustomerStatus::cases());

                $selectedStatuses = array_values(array_intersect($this->selectedStatuses, $validStatuses));

                if ($selectedStatuses !== []) {
                    $query->whereHas('statusAssignments', fn($statusQuery) => $statusQuery->whereIn('status', $selectedStatuses));
                }
            })
            ->when($this->createdDate, function ($query) {
                $query->whereDate('created_at', $this->createdDate);
            })
            ->latest()
            ->orderBy($this->sortField, $this->sortDirection)
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

                    <flux:button type="button" size="sm" variant="primary" color="cyan" icon="plus"
                        wire:click="openManualCustomer">
                        Input Manual
                    </flux:button>

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
            <div class="space-y-3">
                {{-- Search --}}
                <div class="flex max-w-2xl items-end gap-2">
                    <div class="min-w-0 flex-1">
                        <flux:input wire:model.live.debounce.300ms="search" label="Cari Customer"
                            placeholder="Nama, nomor kontrak, nomor WhatsApp, atau cabang..." icon="magnifying-glass" />
                    </div>

                    @if ($search)
                        <flux:button type="button" variant="ghost" icon="arrow-path" wire:click="$set('search', '')">
                            Reset
                        </flux:button>
                    @endif
                </div>

                {{-- Filters --}}
                <div class="flex flex-wrap items-end gap-2">
                    {{-- Tanggal --}}
                    <div class="flex items-end gap-2">
                        <flux:input type="date" wire:model.live="createdDate" label="Tanggal Dibuat" />

                        @if ($createdDate)
                            <flux:button type="button" variant="ghost" icon="arrow-path"
                                wire:click="$set('createdDate', '')">
                                Reset
                            </flux:button>
                        @endif
                    </div>

                    {{-- STATUS MULTI-SELECT --}}
                    <div wire:key="customer-status-filter" x-data="{ open: false }" x-on:click.outside="open = false"
                        x-on:keydown.escape.window="open = false" class="flex flex-col gap-2">
                        <span class="text-sm font-medium">
                            Status
                        </span>

                        <div class="relative w-64">
                            {{-- TRIGGER --}}
                            <flux:button type="button" variant="outline" x-on:click="open = !open"
                                x-bind:aria-expanded="open">
                                <span class="flex min-w-0 items-center gap-2">
                                    <flux:icon name="funnel" class="size-4 shrink-0 text-indigo-500" />

                                    <span class="truncate font-medium text-zinc-700 dark:text-zinc-200">
                                        @if (count($selectedStatuses) === 0)
                                            Semua Status
                                        @else
                                            {{ count($selectedStatuses) }} status dipilih
                                        @endif
                                    </span>
                                </span>

                                <flux:icon name="chevron-down" x-bind:class="{ 'rotate-180': open }"
                                    class="size-4 shrink-0 text-zinc-400 transition-transform duration-200" />
                            </flux:button>

                            {{-- DROPDOWN --}}
                            <div x-cloak x-show="open" x-transition
                                class="absolute left-0 top-full z-50 mt-2 w-full overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-xl shadow-zinc-900/10 dark:border-zinc-700 dark:bg-zinc-900">
                                {{-- HEADER --}}
                                <div
                                    class="border-b border-zinc-100 bg-zinc-50/80 px-3 py-3 dark:border-zinc-800 dark:bg-zinc-800/50">
                                    <div class="flex flex-col gap-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                            Filter Status
                                        </span>
                                        <div class="flex items-center justify-between gap-2">
                                            <span
                                                class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
                                                {{ count($selectedStatuses) }}/{{ count($statuses) }}
                                            </span>
                                            <flux:button type="button" size="xs" variant="outline"
                                                color="rose" icon="x-mark" wire:click="clearStatusFilter">
                                                Hapus Semua
                                            </flux:button>
                                        </div>
                                    </div>

                                </div>

                                {{-- CHECKBOX LIST --}}
                                <div class="grid max-h-64 grid-cols-2 gap-1 overflow-y-auto p-2">
                                    @foreach ($statuses as $customerStatus)
                                        <label wire:key="status-filter-{{ $customerStatus->value }}"
                                            class="flex min-w-0 cursor-pointer items-center gap-2 rounded-lg px-2 py-2 transition hover:bg-indigo-50 dark:hover:bg-indigo-500/10">
                                            <input type="checkbox" value="{{ $customerStatus->value }}"
                                                wire:model.live="selectedStatuses"
                                                class="size-4 shrink-0 rounded border-zinc-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800">

                                            <span class="min-w-0 flex-1 text-xs text-zinc-700 dark:text-zinc-200">
                                                {{ $customerStatus->label() }}
                                            </span>

                                            @if (in_array($customerStatus->value, $selectedStatuses, true))
                                                <flux:icon name="check"
                                                    class="size-4 shrink-0 text-indigo-600 dark:text-indigo-400" />
                                            @endif
                                        </label>
                                    @endforeach
                                </div>

                                {{-- FOOTER --}}
                                <div
                                    class="border-t border-zinc-100 bg-zinc-50/80 p-3 dark:border-zinc-800 dark:bg-zinc-800/50">
                                    <button type="button" x-on:click="open = false"
                                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-3 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900">
                                        <flux:icon name="check-circle" class="size-4" />
                                        Selesai
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

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

                        <flux:button size="sm" variant="danger" icon="trash"
                            wire:click="confirmDeleteSelected">
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

                            {{-- CHECKBOX --}}
                            <th class="w-12 px-4 py-3">
                                <input type="checkbox" wire:model.live="selectAll" wire:change="toggleSelectAll"
                                    class="size-4 rounded border-slate-300 text-indigo-500 focus:ring-indigo-400">
                            </th>

                            {{-- CUSTOMER --}}
                            <flux:table.column class="max-w-52 cursor-pointer whitespace-normal"
                                wire:click="sortBy('name')">
                                <div class="flex items-center gap-1">
                                    <span class="max-w-40 whitespace-normal wrap-break-words">
                                        Customer
                                    </span>

                                    @include('components.sort-icon', [
                                        'field' => 'name',
                                    ])
                                </div>
                            </flux:table.column>

                            {{-- DATE --}}
                            <flux:table.column class="cursor-pointer" wire:click="sortBy('created_at')">
                                <div class="flex items-center gap-1">
                                    Dibuat

                                    @include('components.sort-icon', [
                                        'field' => 'created_at',
                                    ])
                                </div>
                            </flux:table.column>

                            {{-- CONTRACT --}}
                            <flux:table.column>
                                Nomor Kontrak
                            </flux:table.column>

                            {{-- AMOUNT --}}
                            <flux:table.column>
                                Nominal
                            </flux:table.column>

                            {{-- BRANCH --}}
                            <flux:table.column class="cursor-pointer" wire:click="sortBy('branch')">
                                <div class="flex items-center gap-1">
                                    Cabang

                                    @include('components.sort-icon', [
                                        'field' => 'branch',
                                    ])
                                </div>
                            </flux:table.column>

                            {{-- STATUS --}}
                            <flux:table.column>
                                Status
                            </flux:table.column>

                            {{-- ACTION --}}
                            <flux:table.column align="end">
                                Action
                            </flux:table.column>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($customers as $customer)
                            <tr wire:key="customer-{{ $customer->id }}"
                                class="group border-b border-indigo-50 transition duration-200 last:border-0 hover:bg-linear-to-r hover:from-indigo-50/30 hover:via-violet-50/20 hover:to-fuchsia-50/20 dark:border-zinc-800 dark:hover:from-indigo-500/5 dark:hover:via-violet-500/5 dark:hover:to-fuchsia-500/5">

                                {{-- CHECKBOX --}}
                                <td class="px-2 py-2">

                                    <input type="checkbox" value="{{ $customer->id }}"
                                        wire:model.live="selectedCustomers"
                                        class="size-4 rounded border-zinc-300 text-indigo-500 focus:ring-indigo-400">

                                </td>

                                {{-- CUSTOMER --}}
                                <td class="px-2 py-2">
                                    <div class="flex items-center gap-3">
                                        <div class="relative shrink-0">
                                            <flux:avatar :name="$customer->name" color="auto" size="sm" />
                                        </div>

                                        <div class="min-w-0 max-w-52">
                                            <div
                                                class="whitespace-normal wrap-break-words font-medium text-zinc-900 dark:text-white">
                                                {{ $customer->name }}
                                            </div>

                                            <div class="mt-0.5 break-all font-mono text-xs text-zinc-500 whitespace-nowrap">
                                                +{{ $customer->phone_normalized }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- DATE --}}
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="font-medium text-zinc-800 dark:text-zinc-200">
                                        {{ $customer->created_at->locale('id')->translatedFormat('l, d F Y') }}
                                    </div>

                                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $customer->created_at->format('H:i') }}
                                    </div>
                                </td>

                                {{-- CONTRACT --}}
                                <td class="px-2 py-2">

                                    <span
                                        class="rounded-lg bg-indigo-50 px-2 py-1 font-mono text-sm text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                                        {{ $customer->contract_number }}
                                    </span>

                                </td>

                                {{-- AMOUNT --}}
                                <td class="px-2 py-2 whitespace-nowrap">

                                    <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                        {{ $customer->formatted_amount }}
                                    </span>

                                </td>

                                {{-- BRANCH --}}
                                <td class="px-2 py-2">

                                    <flux:badge icon="building-office-2" variant="outline">
                                        {{ $customer->branch }}
                                    </flux:badge>

                                </td>

                                {{-- STATUS --}}
                                <td class="px-2 py-2">
                                    @php
                                        $statusOrder = array_flip(
                                            array_map(
                                                fn($status) => $status->value,
                                                \App\Enums\CustomerStatus::cases(),
                                            ),
                                        );

                                        $statusAssignments = $customer->statusAssignments
                                            ->sortBy(
                                                fn($assignment) => $statusOrder[$assignment->status->value] ??
                                                    PHP_INT_MAX,
                                            )
                                            ->values();

                                        $statusCount = $statusAssignments->count();

                                        $columns = match (true) {
                                            $statusCount <= 2 => 1,
                                            $statusCount <= 4 => 2,
                                            default => 3,
                                        };
                                    @endphp

                                    @if ($statusCount > 0)
                                        <div @class([
                                            'grid w-fit gap-1',
                                            'grid-cols-1' => $columns === 1,
                                            'grid-cols-2' => $columns === 2,
                                            'grid-cols-3' => $columns === 3,
                                        ])>
                                            @foreach ($statusAssignments as $assignment)
                                                <flux:badge size="xs"
                                                    wire:key="customer-{{ $customer->id }}-status-{{ $assignment->status->value }}"
                                                    :color="$assignment->status->color()"
                                                    class="w-16 justify-center whitespace-nowrap text-[9px] font-bold! leading-tight">
                                                    {{ $assignment->status->label() }}
                                                </flux:badge>
                                            @endforeach
                                        </div>
                                    @else
                                        <flux:badge size="xs" :color="$customer->status->color()"
                                            class="w-16 justify-center whitespace-nowrap text-[9px] font-bold! leading-tight">
                                            {{ $customer->status->label() }}
                                        </flux:badge>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td class="px-2 py-2">

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
                                            wire:click="confirmDeleteCustomer({{ $customer->id }})"
                                            wire:loading.attr="disabled">
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

    {{-- MANUAL CUSTOMER MODAL --}}
    <flux:modal name="customer-manual" class="w-full max-w-2xl" :dismissible="false">

        <form wire:submit="saveManualCustomer" class="space-y-6">

            {{-- HEADER --}}
            <div class="flex items-start gap-4">

                <div
                    class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-400 to-violet-500 text-white shadow-lg shadow-indigo-500/15">
                    <flux:icon name="user-plus" class="size-5" />
                </div>

                <div>
                    <flux:heading size="lg">
                        Tambah Customer
                    </flux:heading>

                    <flux:text class="mt-1">
                        Input satu customer secara manual.
                    </flux:text>
                </div>

            </div>

            {{-- FORM --}}
            <div class="space-y-5">

                {{-- NAMA + PHONE --}}
                <div class="grid gap-5 sm:grid-cols-2">

                    <flux:input wire:model="manualName" label="Nama Customer" placeholder="Setya Prioritas Dana"
                        autofocus />

                    <flux:input wire:model="manualPhone" label="Nomor WhatsApp" placeholder="85799928828"
                        type="tel" />

                </div>

                {{-- KONTRAK + NOMINAL --}}
                <div class="grid gap-5 sm:grid-cols-2">

                    <flux:input wire:model.live="manualContractNumber" label="Nomor Kontrak"
                        placeholder="17 digit nomor kontrak" inputmode="numeric" maxlength="17" />

                    <flux:input wire:model.live="manualAmount" label="Nominal" placeholder="30.000.000"
                        inputmode="numeric" />

                </div>

                {{-- CABANG --}}
                <flux:input wire:model="manualBranch" label="Cabang" placeholder="YOGYAKARTA" />

            </div>

            {{-- INFO --}}
            <div
                class="flex items-center gap-2 rounded-xl bg-indigo-50 px-3 py-2.5 text-xs text-indigo-600 dark:bg-indigo-950/30 dark:text-indigo-300">
                <flux:icon name="information-circle" class="mt-0.5 size-4 shrink-0" />

                <span class="leading-relaxed">
                    Customer baru akan otomatis memiliki status
                    <flux:badge :color="CustomerStatus::NEW->color()" class="shrink-0">
                        {{ CustomerStatus::NEW->label() }}
                    </flux:badge>
                </span>
            </div>

            <flux:separator />

            {{-- FOOTER --}}
            <div class="flex items-center justify-end gap-3">

                <flux:button type="button" variant="ghost" wire:click="closeManualCustomer">
                    Batal
                </flux:button>

                <flux:button type="submit" variant="primary" color="cyan" icon="check"
                    class="shadow-lg shadow-indigo-500/15">
                    Simpan Customer
                </flux:button>

            </div>

        </form>

    </flux:modal>

    {{-- EDIT CUSTOMER MODAL --}}
    <flux:modal name="customer-edit" class="w-full max-w-2xl" :dismissible="false">

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

            {{-- STATUS --}}
            <div class="space-y-2" x-data="{ open: false }">
                <flux:heading size="sm">Status Customer</flux:heading>

                <div class="relative">
                    <button type="button" @click="open = !open"
                        class="flex w-full items-center justify-between gap-3 rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-left text-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-800">
                        <span class="min-w-0">
                            <span class="block font-medium text-zinc-800 dark:text-zinc-100">
                                Pilih status customer
                            </span>

                            <span class="mt-0.5 block truncate text-xs text-zinc-500">
                                {{ count($editStatus) }} status dipilih
                            </span>
                        </span>

                        <flux:icon name="chevron-down" class="size-4 shrink-0 transition-transform"
                            x-bind:class="{ 'rotate-180': open }" />
                    </button>

                    {{-- Dropdown tetap --}}
                    <div x-cloak x-show="open" @click.outside="open = false" x-transition
                        class="absolute inset-x-0 z-20 mt-2 max-h-64 overflow-y-auto rounded-lg border border-zinc-200 bg-white p-2 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
                        {{-- Isi dropdown: 2 kolom --}}
                        <div class="grid grid-cols-2 gap-1">
                            @foreach ($statuses as $customerStatus)
                                <label wire:key="edit-customer-status-{{ $customerStatus->value }}"
                                    class="flex min-w-0 cursor-pointer items-center gap-2 rounded-md px-2 py-2 transition hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                    <input type="checkbox" value="{{ $customerStatus->value }}"
                                        wire:model.live="editStatus"
                                        class="size-4 shrink-0 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800">

                                    <span class="min-w-0 flex-1 text-xs text-zinc-700 dark:text-zinc-200">
                                        {{ $customerStatus->label() }}
                                    </span>

                                    @if (in_array($customerStatus->value, $editStatus, true))
                                        <flux:icon name="check"
                                            class="size-4 shrink-0 text-indigo-600 dark:text-indigo-400" />
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                @error('editStatus')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <flux:input wire:model="editName" label="Nama Customer" placeholder="Setya Prioritas Dana" />
                    @error('editName')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <flux:input wire:model="editPhone" label="Nomor WhatsApp" placeholder="85799928828" />
                    @error('editPhone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <flux:input wire:model.live="editContractNumber" label="Nomor Kontrak"
                        placeholder="17 digit nomor kontrak" inputmode="numeric" maxlength="17" />
                    @error('editContractNumber')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <flux:input wire:model.live="editAmount" label="Nominal" placeholder="30.000.000"
                        inputmode="numeric" />
                    @error('editAmount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <flux:input wire:model="editBranch" label="Cabang" placeholder="YOGYAKARTA" />
                @error('editBranch')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @error('editStatus')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            @error('editStatus.*')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <flux:input wire:model="editBranch" label="Cabang" placeholder="YOGYAKARTA" />

            <flux:separator />

            <div class="flex items-center justify-end gap-3">

                <flux:button type="button" variant="ghost" wire:click="closeEditCustomer">
                    Batal
                </flux:button>

                <flux:button type="submit" variant="primary" color="violet" icon="check"
                    class="shadow-lg shadow-indigo-500/15" wire:loading.attr="disabled" wire:target="saveCustomer">
                    <span wire:loading.remove wire:target="saveCustomer">
                        Simpan Perubahan
                    </span>

                    <span wire:loading wire:target="saveCustomer">
                        Menyimpan...
                    </span>
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
                        <flux:badge color="green" icon="pencil-square">
                            Bisa Diedit
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
                    <div class="relative min-h-80 bg-[#efeae2] p-4 dark:bg-zinc-800">

                        {{-- Decorative --}}
                        <div class="pointer-events-none absolute inset-0 opacity-[0.035]"
                            style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 16px 16px;">
                        </div>

                        @if (filled($message))
                            <div class="relative flex justify-end">

                                <div
                                    class="w-full max-w-[75%] rounded-xl rounded-tr-sm bg-[#d9fdd3] p-3 shadow-sm dark:bg-emerald-900">

                                    <flux:textarea wire:model.live.debounce.300ms="message" rows="auto"
                                        placeholder="Tulis pesan WhatsApp..." class="composer-message" />

                                    {{-- FOOTER --}}
                                    <div
                                        class="mt-2
                                        flex items-center justify-between border-t border-black/5 pt-2">

                                        <span class="text-[10px] text-zinc-500">
                                            Pesan dapat diedit
                                        </span>

                                        <div class="flex items-center gap-1 text-[10px] text-zinc-500">

                                            {{ now()->format('H:i') }}

                                            <flux:icon name="check" variant="mini" class="text-blue-500" />

                                        </div>

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

                @if ($message)
                    <div class="flex items-center gap-2">

                        <flux:button type="button" variant="ghost" wire:click="closeComposer">
                            Batal
                        </flux:button>

                        <flux:button type="button" variant="ghost" color="amber" icon="arrow-path"
                            wire:click="resetMessage" :disabled="$message === $originalMessage">
                            Reset
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

                @endif
            </div>

        </div>

    </flux:modal>

</div>
