<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $editingId = null;

    public ?int $deletingId = null;

    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = UserRole::PRIORITAS_DANA->value;
    public string $status = UserStatus::ACTIVE->value;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->resetValidation();

        $member = $this->memberQuery()->whereKey($id)->firstOrFail();

        $this->editingId = $member->id;
        $this->name = $member->name;
        $this->username = $member->username ?? '';
        $this->email = $member->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = $member->role->value;
        $this->status = $member->status->value;

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate($this->rules());

        if ($this->editingId) {
            $this->updateMember();

            $message = 'Member berhasil diperbarui.';
        } else {
            $this->createMember();

            $message = 'Member berhasil ditambahkan.';
        }

        $this->closeModal();

        $this->dispatch('swal', type: 'success', title: 'Berhasil', message: $message);
    }

    public function toggleStatus(int $id): void
    {
        $member = $this->memberQuery()->whereKey($id)->firstOrFail();

        $member->status = $member->status === UserStatus::ACTIVE ? UserStatus::INACTIVE : UserStatus::ACTIVE;

        $member->save();

        $message = $member->status === UserStatus::ACTIVE ? 'Member berhasil diaktifkan.' : 'Member berhasil dinonaktifkan.';

        $this->dispatch('swal', type: 'success', title: 'Berhasil', message: $message);
    }

    public function toggleFormStatus(): void
    {
        $this->status = $this->status === UserStatus::ACTIVE->value ? UserStatus::INACTIVE->value : UserStatus::ACTIVE->value;
    }

    public function closeModal(): void
    {
        $this->showModal = false;

        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->resetValidation();

        $this->editingId = null;
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = UserRole::PRIORITAS_DANA->value;
        $this->status = UserStatus::ACTIVE->value;
    }

    protected function createMember(): void
    {
        User::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'status' => $this->status,
        ]);
    }

    protected function updateMember(): void
    {
        $member = $this->memberQuery()->whereKey($this->editingId)->firstOrFail();

        $member->name = $this->name;
        $member->username = $this->username;
        $member->email = $this->email;
        $member->role = $this->role;
        $member->status = $this->status;

        if ($this->password !== '') {
            $member->password = Hash::make($this->password);
        }

        $member->save();
    }

    protected function memberQuery()
    {
        return User::query()->whereIn('role', $this->memberRoles());
    }

    protected function memberRoles(): array
    {
        return [UserRole::PRIORITAS_DANA->value, UserRole::LANDING_PAGE->value, UserRole::MULTIGUNA->value];
    }

    protected function rules(): array
    {
        $usernameRule = Rule::unique('users', 'username');
        $emailRule = Rule::unique('users', 'email');

        if ($this->editingId) {
            $usernameRule->ignore($this->editingId);
            $emailRule->ignore($this->editingId);
        }

        return [
            'name' => ['required', 'string', 'max:255'],

            'username' => ['required', 'string', 'max:50', $usernameRule],

            'email' => ['required', 'email', 'max:255', $emailRule],

            'role' => ['required', Rule::in($this->memberRoles())],

            'status' => ['required', Rule::in([UserStatus::ACTIVE->value, UserStatus::INACTIVE->value])],

            'password' => $this->editingId ? ['nullable', 'string', 'min:8', 'confirmed'] : ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function getMembersProperty()
    {
        return $this->memberQuery()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query
                        ->where('name', 'like', "%{$this->search}%")
                        ->orWhere('username', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(10);
    }

    public function getMemberRoleOptionsProperty(): array
    {
        return collect($this->memberRoles())->map(fn(string $role) => UserRole::from($role))->all();
    }

    public function confirmDelete(int $id): void
    {
        $member = $this->memberQuery()->whereKey($id)->firstOrFail();

        $this->deletingId = $member->id;

        $this->dispatch('confirm', title: 'Hapus Member?', message: "{$member->name} akan dihapus secara permanen.", confirmText: 'Ya, hapus', cancelText: 'Batal', action: 'delete-member');
    }

    #[On('delete-member')]
    public function delete(): void
    {
        if (!$this->deletingId) {
            return;
        }

        $member = $this->memberQuery()->whereKey($this->deletingId)->firstOrFail();

        $member->delete();

        $this->deletingId = null;

        $this->dispatch('swal', type: 'success', title: 'Berhasil', message: 'Member berhasil dihapus.');
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
                        Member
                    </flux:heading>

                    <flux:text class="mt-1">
                        Kelola akun member yang dapat menggunakan Sales Assistant.
                    </flux:text>
                </div>
            </div>

            <flux:button variant="primary" color="violet" icon="plus" wire:click="create">
                Tambah Member
            </flux:button>
        </div>
    </div>

    {{-- Member Table --}}
    <flux:card class="m-5 overflow-hidden border-none! md:me-5 md:ms-2 md:my-5">

        {{-- Search --}}
        <div class="pb-5">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                placeholder="Cari nama, username, atau email..." />
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-y border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">
                            Member
                        </th>

                        <th class="px-4 py-3 text-left font-medium">
                            Username
                        </th>

                        <th class="px-4 py-3 text-left font-medium">
                            Email
                        </th>

                        <th class="px-4 py-3 text-left font-medium">
                            Role
                        </th>

                        <th class="px-4 py-3 text-left font-medium">
                            Status
                        </th>

                        <th class="px-4 py-3 text-center font-medium">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($this->members as $member)
                        <tr wire:key="member-{{ $member->id }}">

                            {{-- Member --}}
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <flux:avatar :name="$member->name" :initials="$member->initials()"
                                        color="auto" />

                                    <div class="min-w-0">
                                        <div class="truncate font-medium">
                                            {{ $member->name }}
                                        </div>

                                        <div class="text-xs text-zinc-500">
                                            Bergabung {{ $member->created_at?->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Username --}}
                            <td class="px-4 py-4">
                                {{ $member->username }}
                            </td>

                            {{-- Email --}}
                            <td class="px-4 py-4">
                                {{ $member->email }}
                            </td>

                            {{-- Role --}}
                            <td class="px-4 py-4">
                                <flux:badge :color="$member->role->color()">
                                    {{ $member->role->label() }}
                                </flux:badge>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-4">
                                <flux:badge :color="$member->status->color()">
                                    {{ $member->status->label() }}
                                </flux:badge>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" variant="ghost"
                                        :icon="$member->status === UserStatus::ACTIVE ? 'pause' : 'play'"
                                        wire:click="toggleStatus({{ $member->id }})">
                                        {{ $member->status === UserStatus::ACTIVE ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </flux:button>

                                    <flux:button size="sm" variant="ghost" icon="pencil"
                                        wire:click="edit({{ $member->id }})" tooltip="Edit member" />

                                    <flux:button size="sm" variant="ghost" color="red" icon="trash"
                                        wire:click="confirmDelete({{ $member->id }})" tooltip="Hapus member" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-zinc-500">
                                Tidak ada member ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($this->members->hasPages())
            <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">
                {{ $this->members->links() }}
            </div>
        @endif

    </flux:card>

    {{-- Add / Edit Modal --}}
    <flux:modal wire:model="showModal" name="member-form" class="md:w-150" :dismissible="false">
        <div class="space-y-6">

            {{-- Modal Header --}}
            <div>
                <flux:heading size="lg">
                    {{ $editingId ? 'Edit Member' : 'Tambah Member' }}
                </flux:heading>

                <flux:text class="mt-1">
                    {{ $editingId ? 'Perbarui informasi akun member.' : 'Buat akun member baru.' }}
                </flux:text>
            </div>

            {{-- Form --}}
            <div class="grid gap-5 sm:grid-cols-2">

                <flux:input wire:model="name" label="Nama" placeholder="Nama lengkap" required />

                <flux:input wire:model="email" type="email" label="Email" placeholder="email@example.com"
                    class="sm:col-span-2" required />

                <flux:input wire:model="username" label="Username" placeholder="username" required />

                <flux:select wire:model="role" label="Role" class="sm:col-span-2">
                    @foreach ($this->memberRoleOptions as $role)
                        <flux:select.option value="{{ $role->value }}">
                            {{ $role->label() }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="password" type="password" label="Password"
                    placeholder="{{ $editingId ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}"
                    autocomplete="new-password" :required="!$editingId" />

                <flux:input wire:model="password_confirmation" type="password" label="Konfirmasi Password"
                    placeholder="Ulangi password" autocomplete="new-password" :required="!$editingId" />

                <div class="sm:col-span-2">
                    <flux:switch wire:click="toggleFormStatus" :checked="$status === UserStatus::ACTIVE->value"
                        label="Status akun" description="Member yang nonaktif tidak dapat menggunakan akun." />
                </div>
            </div>

            {{-- Validation --}}
            @if ($errors->any())
                <flux:callout variant="danger" icon="exclamation-triangle">
                    <flux:callout.heading>
                        Periksa kembali data
                    </flux:callout.heading>

                    <flux:callout.text>
                        Ada data yang belum sesuai.
                        Silakan periksa field yang ditandai.
                    </flux:callout.text>
                </flux:callout>
            @endif

            {{-- Actions --}}
            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="closeModal">
                    Batal
                </flux:button>

                <flux:button variant="primary" color="violet" wire:click="save" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Simpan Perubahan' : 'Tambah Member' }}
                    </span>

                    <span wire:loading wire:target="save">
                        Menyimpan...
                    </span>
                </flux:button>
            </div>

        </div>
    </flux:modal>

</div>
