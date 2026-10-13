<?php

use App\Enums\CustomerStatus;
use App\Models\Customer;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public string $searchCustomer = '';
    public string $selectedCustomerId = '';
    public string $selectedTemplateId = '';

    public string $principal = '';
    public string $tenor = '';
    public string $installment = '';

    public string $greeting = 'Selamat pagi';
    public string $address = 'Bapak';
    public string $message = '';

    public bool $showWhatsappModal = false;

    public function updatedSearchCustomer(): void
    {
        // Pencarian customer tidak mengubah pilihan yang sudah ada.
    }

    public function updatedSelectedCustomerId(): void
    {
        $this->generateMessage();
    }

    public function updatedSelectedTemplateId(): void
    {
        $this->generateMessage();
    }

    public function updatedPrincipal(): void
    {
        $this->principal = $this->formatIdr($this->principal);

        $this->generateMessage();
    }

    public function updatedTenor(): void
    {
        $this->generateMessage();
    }

    public function updatedInstallment(): void
    {
        $this->installment = $this->formatIdr($this->installment);

        $this->generateMessage();
    }

    public function updatedGreeting(): void
    {
        $this->generateMessage();
    }

    public function updatedAddress(): void
    {
        $this->generateMessage();
    }

    public function formatIdr(string|int|null $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $number = preg_replace('/\D/', '', (string) $value);

        return $number === '' ? '' : number_format((int) $number, 0, ',', '.');
    }

    private function getCalculation(): ?array
    {
        $principal = (int) preg_replace('/\D/', '', $this->principal);
        $tenor = (int) $this->tenor;
        $installment = (int) preg_replace('/\D/', '', $this->installment);

        if ($principal <= 0 || !in_array($tenor, [6, 9, 12, 15, 18, 21, 24], true) || $installment <= 0) {
            return null;
        }

        $totalPayment = $installment * $tenor;
        $totalInterest = $totalPayment - $principal;

        if ($totalInterest < 0) {
            return null;
        }

        return [
            'principal' => $principal,
            'tenor' => $tenor,
            'installment' => $installment,
            'total_payment' => $totalPayment,
            'total_interest' => $totalInterest,
            'monthly_rate' => ($totalInterest / $principal / $tenor) * 100,
        ];
    }

    private function formatRupiah(int $amount): string
    {
        return 'Rp' . number_format($amount, 0, ',', '.');
    }

    public function generateMessage(): void
    {
        $this->message = '';

        if (blank($this->selectedCustomerId) || blank($this->selectedTemplateId)) {
            return;
        }

        $calculation = $this->getCalculation();

        if (!$calculation) {
            return;
        }

        $customer = auth()->user()->customers()->find($this->selectedCustomerId);

        if (!$customer) {
            return;
        }

        $this->authorize('view', $customer);

        $template = auth()->user()->messageTemplates()->where('is_active', true)->find($this->selectedTemplateId);

        if (!$template) {
            return;
        }

        $shortAddress = $this->address === 'Bapak' ? 'pak' : 'bu';

        $variables = [
            '{{sapaan_waktu}}' => $this->greeting,
            '{{panggilan}}' => $this->address,
            '{{panggilan_singkat}}' => $shortAddress,
            '{{nominal_pinjaman}}' => $this->formatRupiah($calculation['principal']),
            '{{tenor}}' => (string) $calculation['tenor'],
            '{{angsuran_bulanan}}' => $this->formatRupiah($calculation['installment']),
            '{{total_pembayaran}}' => $this->formatRupiah($calculation['total_payment']),
            '{{total_bunga}}' => $this->formatRupiah($calculation['total_interest']),
            '{{bunga_persen_bulanan}}' => number_format($calculation['monthly_rate'], 2, ',', '.') . '%',
        ];

        $this->message = $customer->replaceTemplateVariables($template->content, $variables);
    }

    public function markContacted(): void
    {
        if (blank($this->selectedCustomerId) || blank($this->message)) {
            return;
        }

        $customer = auth()->user()->customers()->findOrFail($this->selectedCustomerId);

        $this->authorize('update', $customer);

        if ($customer->status === CustomerStatus::NEW) {
            $customer->update([
                'status' => CustomerStatus::BLAST,
            ]);
        }

        $customer->update([
            'last_contacted_at' => now(),
        ]);
    }

    public function getWhatsappUrlProperty(): ?string
    {
        if (blank($this->message) || blank($this->selectedCustomerId)) {
            return null;
        }

        $customer = auth()->user()->customers()->find($this->selectedCustomerId);

        if (!$customer) {
            return null;
        }

        $this->authorize('view', $customer);

        return $customer->whatsappUrlWithMessage($this->message);
    }

    public function openWhatsappModal(): void
    {
        $this->showWhatsappModal = true;
        $this->generateMessage();
    }

    public function closeWhatsappModal(): void
    {
        $this->showWhatsappModal = false;
    }

    #[Computed]
    public function selectedCustomer(): ?Customer
    {
        if (blank($this->selectedCustomerId)) {
            return null;
        }

        return auth()->user()->customers()->find($this->selectedCustomerId);
    }

    public function render()
    {
        $customers = auth()
            ->user()
            ->customers()
            ->when($this->searchCustomer, function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where('name', 'like', '%' . $this->searchCustomer . '%')
                        ->orWhere('phone', 'like', '%' . $this->searchCustomer . '%')
                        ->orWhere('contract_number', 'like', '%' . $this->searchCustomer . '%');
                });
            })
            ->orderBy('name')
            ->limit(50)
            ->get();

        return $this->view([
            'customers' => $customers,
            'templates' => auth()->user()->messageTemplates()->where('is_active', true)->orderBy('name')->get(),
            'calculation' => $this->getCalculation(),
        ]);
    }
};
?>

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="m-5 md:my-5 md:ms-2 md:me-5 rounded-xl bg-white dark:bg-zinc-800">
        <div class="mx-auto p-6">
            <div class="flex items-center gap-3">
                <div
                    class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-indigo-400 to-violet-500 text-white">
                    <flux:icon name="calculator" class="size-6" />
                </div>

                <div>
                    <flux:heading size="xl">
                        Hitung Bunga Bulanan
                    </flux:heading>

                    <flux:text class="mt-1">
                        Hitung simulasi dan siapkan pesan WhatsApp untuk customer.
                    </flux:text>
                </div>
            </div>
        </div>
    </div>

    <flux:card class="m-5 md:my-5 md:ms-2 md:me-5 rounded-xl bg-white dark:bg-zinc-800 space-y-5" variant="soft">
        <flux:heading size="lg">Simulasi Pinjaman</flux:heading>

        <div class="grid gap-5 sm:grid-cols-3">

            <flux:input wire:model.live.debounce.400ms="principal" label="Nominal Pinjaman (Rp)" inputmode="numeric"
                placeholder="30.000.000" />

            <flux:select wire:model.live="tenor" label="Tenor (bulan)" placeholder="Pilih tenor">
                <flux:select.option value="6">6 Bulan</flux:select.option>
                <flux:select.option value="9">9 Bulan</flux:select.option>
                <flux:select.option value="12">12 Bulan</flux:select.option>
                <flux:select.option value="15">15 Bulan</flux:select.option>
                <flux:select.option value="18">18 Bulan</flux:select.option>
                <flux:select.option value="21">21 Bulan</flux:select.option>
                <flux:select.option value="24">24 Bulan</flux:select.option>
            </flux:select>

            <flux:input wire:model.live.debounce.400ms="installment" label="Angsuran per Bulan (Rp)" inputmode="numeric"
                placeholder="1.590.000" />

        </div>

        @if ($calculation)
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-900">
                    <flux:text>Total Pembayaran</flux:text>
                    <flux:heading size="lg" class="mt-1">
                        {{ 'Rp' . number_format($calculation['total_payment'], 0, ',', '.') }}
                    </flux:heading>
                </div>

                <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-900">
                    <flux:text>Total Bunga</flux:text>
                    <flux:heading size="lg" class="mt-1">
                        {{ 'Rp' . number_format($calculation['total_interest'], 0, ',', '.') }}
                    </flux:heading>
                </div>

                <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-900">
                    <flux:text>Bunga Flat per Bulan</flux:text>
                    <flux:heading size="lg" class="mt-1">
                        {{ number_format($calculation['monthly_rate'], 2, ',', '.') }}%
                    </flux:heading>
                </div>
            </div>
        @else
            <flux:text class="text-sm font-medium text-rose-600">
                Periksa nominal, tenor, dan angsuran. Total pembayaran tidak boleh kurang dari pokok pinjaman.
            </flux:text>
        @endif
    </flux:card>

    <div class="m-5 md:my-5 md:ms-2 md:me-5 flex justify-end">
        <flux:button type="button" variant="primary" color="violet" icon="plus-circle" wire:click="openWhatsappModal">
            Buat Pesan WhatsApp
        </flux:button>
    </div>

    {{-- MODAL PESAN WHATSAPP --}}
    <flux:modal wire:model="showWhatsappModal" class="w-sm md:w-full md:max-w-2xl" :dismissible="false">
        <div class="space-y-5">
            <div>
                <flux:heading size="lg">
                    Pesan WhatsApp
                </flux:heading>

                <flux:text class="mt-1">
                    Siapkan pesan dan lihat preview sebelum mengirim.
                </flux:text>
            </div>

            {{-- PILIH CUSTOMER --}}
            <div class="grid gap-3 sm:grid-cols-2">
                <flux:select wire:model.live="selectedCustomerId" label="Customer" placeholder="Pilih customer">
                    @foreach ($customers as $customer)
                        <flux:select.option value="{{ $customer->id }}">
                            {{ $customer->name }} — {{ $customer->phone_normalized }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model.live.debounce.300ms="searchCustomer" label="Cari Customer"
                    placeholder="Nama, nomor WhatsApp, atau kontrak" icon="magnifying-glass" />
            </div>

            {{-- TEMPLATE --}}
            <flux:select wire:model.live="selectedTemplateId" label="Template Pesan" placeholder="Pilih template">
                @foreach ($templates as $template)
                    <flux:select.option :value="(string) $template->id">
                        {{ $template->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            {{-- SAPAAN DAN PANGGILAN --}}
            <div
                class="grid gap-5 rounded-xl border border-indigo-100 bg-indigo-50/40 p-4 dark:border-violet-900/40 dark:bg-violet-500/5 sm:grid-cols-2">
                <div>
                    <flux:text class="mb-2 font-medium">
                        Sapaan Waktu
                    </flux:text>

                    <div class="flex flex-wrap gap-2">
                        @foreach ([
        'Selamat pagi' => 'Pagi',
        'Selamat siang' => 'Siang',
        'Selamat sore' => 'Sore',
    ] as $value => $label)
                            <flux:button type="button" size="sm"
                                :variant="$greeting === $value ? 'primary' : 'ghost'"
                                wire:click="$set('greeting', '{{ $value }}')">
                                {{ $label }}
                            </flux:button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <flux:text class="mb-2 font-medium">
                        Panggilan
                    </flux:text>

                    <div class="flex flex-wrap gap-2">
                        @foreach (['Bapak', 'Ibu'] as $value)
                            <flux:button type="button" size="sm"
                                :variant="$address === $value ? 'primary' : 'ghost'"
                                wire:click="$set('address', '{{ $value }}')">
                                {{ $value }}
                            </flux:button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- PREVIEW CHAT --}}
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

                <div class="overflow-hidden rounded-2xl border border-indigo-100 shadow-sm dark:border-violet-900/40">
                    {{-- HEADER CHAT --}}
                    <div
                        class="flex items-center gap-3 border-b border-emerald-900/20 bg-emerald-600 px-4 py-3 text-white">
                        <flux:avatar :name="$this->selectedCustomer?->name ?? 'Customer'" circle size="sm"
                            color="auto" />

                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold">
                                {{ $this->selectedCustomer?->name ?? 'Customer' }}
                            </div>

                            @if ($this->selectedCustomer)
                                <div class="text-xs">
                                    +{{ $this->selectedCustomer->phone_normalized }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ISI CHAT --}}
                    <div class="relative min-h-64 max-h-96 overflow-y-auto bg-[#efeae2] p-4 dark:bg-zinc-800">
                        <div class="pointer-events-none absolute inset-0 opacity-[0.035]"
                            style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 16px 16px;">
                        </div>

                        @if (filled($message))
                            <div class="relative flex justify-end">
                                <div
                                    class="w-full max-w-[90%] rounded-xl rounded-tr-sm bg-[#d9fdd3] px-3 py-2 shadow-sm dark:bg-emerald-900">
                                    <flux:textarea wire:model.live="message" rows="8"
                                        class="border-0! bg-transparent! shadow-none!" />

                                    <div class="mt-1 flex items-center justify-end gap-1 text-[10px] text-zinc-500">
                                        <span>{{ now()->format('H:i') }}</span>
                                        <flux:icon name="check" variant="mini" class="text-blue-500" />
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="relative flex min-h-56 items-center justify-center">
                                <div class="text-center">
                                    <flux:icon name="chat-bubble-left-right" class="mx-auto size-8 text-violet-400" />

                                    <flux:text class="mt-3">
                                        Pilih customer dan template untuk melihat preview.
                                    </flux:text>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex flex-wrap justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="closeWhatsappModal">
                    Tutup
                </flux:button>

                <flux:button type="button" variant="ghost" icon="arrow-path" wire:click="generateMessage">
                    Buat Ulang
                </flux:button>

                @if ($this->whatsappUrl)
                    <flux:button href="{{ $this->whatsappUrl }}" target="_blank" variant="primary" color="emerald"
                        icon="arrow-top-right-on-square" wire:click="markContacted">
                        Buka WhatsApp
                    </flux:button>
                @else
                    <flux:button type="button" disabled icon="paper-airplane">
                        Buka WhatsApp
                    </flux:button>
                @endif
            </div>
        </div>
    </flux:modal>

</div>
