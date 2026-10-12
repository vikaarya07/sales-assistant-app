<?php

use App\Enums\CustomerStatus;
use App\Models\Customer;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public string $searchCustomer = '';
    public string $selectedCustomerId = '';
    public string $selectedTemplateId = '';

    public string $principal = '30000000';
    public string $tenor = '24';
    public string $installment = '1590000';

    public string $greeting = 'Selamat pagi';
    public string $address = 'Bapak';
    public string $message = '';

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

    #[Computed]
    public function selectedCustomer(): ?Customer
    {
        if (blank($this->selectedCustomerId)) {
            return null;
        }

        return auth()->user()
            ->customers()
            ->find($this->selectedCustomerId);
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

<div class="m-5 space-y-6 md:ms-2 md:me-5">
    <flux:card class="bg-white dark:bg-zinc-800">
        <div class="flex items-center gap-3">
            <div class="flex size-12 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                <flux:icon name="calculator" class="size-6" />
            </div>
            <div>
                <flux:heading size="xl">Hitung Bunga Bulanan</flux:heading>
                <flux:text class="mt-1">
                    Hitung simulasi dan siapkan pesan WhatsApp untuk customer.
                </flux:text>
            </div>
        </div>
    </flux:card>

    <flux:card class="space-y-5 bg-white dark:bg-zinc-800">
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
            <flux:text class="text-sm text-amber-600">
                Periksa nominal, tenor, dan angsuran. Total pembayaran tidak boleh kurang dari pokok pinjaman.
            </flux:text>
        @endif
    </flux:card>

    <flux:card class="space-y-5 bg-white dark:bg-zinc-800">
        <flux:heading size="lg">Pesan WhatsApp</flux:heading>

        <div class="grid gap-3 sm:grid-cols-2">

            <flux:select wire:model.live="selectedCustomerId" label="Customer" placeholder="Pilih customer"
                class="w-full">

                @foreach ($customers as $customer)
                    <flux:select.option value="{{ $customer->id }}">
                        {{ $customer->name }} — {{ $customer->phone_normalized }}
                    </flux:select.option>
                @endforeach

            </flux:select>

            <flux:input wire:model.live.debounce.300ms="searchCustomer" label="Cari Customer" class="w-full"
                placeholder="Nama, nomor WhatsApp, atau nomor kontrak" icon="magnifying-glass" />

        </div>

        {{-- TEMPLATE --}}
        <flux:select wire:model.live="selectedTemplateId" label="Template Pesan" placeholder="-- Pilih Template --">

            @foreach ($templates as $template)
                <flux:select.option :value="(string) $template->id">
                    {{ $template->name }}
                </flux:select.option>
            @endforeach

        </flux:select>

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

                    <flux:button type="button" size="sm" :variant="$address === 'Bapak' ? 'primary' : 'ghost'"
                        wire:click="$set('address', 'Bapak')">
                        Bapak
                    </flux:button>

                    <flux:button type="button" size="sm" :variant="$address === 'Ibu' ? 'primary' : 'ghost'"
                        wire:click="$set('address', 'Ibu')">
                        Ibu
                    </flux:button>

                </div>

            </div>

        </div>

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

        <div class="flex flex-wrap justify-end gap-2">
            <flux:button type="button" variant="ghost" icon="arrow-path" wire:click="generateMessage">
                Buat Ulang Pesan
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
    </flux:card>
</div>
