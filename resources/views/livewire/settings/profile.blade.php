<section class="mx-auto max-w-7xl space-y-6 px-6 py-8 lg:px-8">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">
        {{ __('Profile settings') }}
    </flux:heading>

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name, username, and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">

            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />


            <flux:field>
                <flux:label>{{ __('Username') }}</flux:label>
                <flux:input.group>
                    <flux:input.group.prefix>@</flux:input.group.prefix>
                    <flux:input wire:model.live="username" type="text" required autocomplete="username" />
                </flux:input.group>
                <flux:error name="website" />
            </flux:field>

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email"
                    disabled />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer"
                                wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" color="violet" type="submit">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:settings.delete-user-form />
        @endif
    </x-settings.layout>
</section>
