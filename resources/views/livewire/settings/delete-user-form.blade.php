<section class="mt-10 space-y-6">

    <div class="relative mb-5">
        <flux:heading class="text-indigo-800 dark:text-indigo-200">
            {{ __('Delete account') }}
        </flux:heading>

        <flux:subheading class="text-violet-700/75 dark:text-violet-300/75">
            {{ __('Delete your account and all of its resources') }}
        </flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button variant="danger" x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="bg-linear-to-r from-indigo-500/80 via-violet-500/80 to-fuchsia-500/70 hover:from-indigo-600 hover:via-violet-600 hover:to-fuchsia-600">
            {{ __('Delete account') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">

        <form method="POST" wire:submit="deleteUser" class="space-y-6">

            <div>
                <flux:heading size="lg" class="text-indigo-900 dark:text-indigo-100">
                    {{ __('Are you sure you want to delete your account?') }}
                </flux:heading>

                <flux:subheading class="text-violet-700/75 dark:text-violet-300/75">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </flux:subheading>
            </div>

            <flux:input wire:model="password" :label="__('Password')" type="password" viewable
                class="focus-within:border-violet-300 dark:focus-within:border-violet-700" />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">

                <flux:modal.close>
                    <flux:button variant="filled">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit"
                    class="bg-linear-to-r from-indigo-500/80 via-violet-500/80 to-fuchsia-500/70 hover:from-indigo-600 hover:via-violet-600 hover:to-fuchsia-600">
                    {{ __('Delete account') }}
                </flux:button>

            </div>
        </form>
    </flux:modal>
</section>
