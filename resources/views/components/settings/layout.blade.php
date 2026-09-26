<div class="flex items-start max-md:flex-col">

    <div
        class="me-10 w-full pb-4 md:w-55 rounded-xl bg-linear-to-b from-indigo-100 via-violet-100 to-fuchsia-100 dark:from-indigo-950/50 dark:via-violet-950/40 dark:to-fuchsia-950/30 p-3">
        <flux:navlist aria-label="{{ __('Settings') }}">
            <flux:navlist.item :href="route('profile.edit')" wire:navigate>
                {{ __('Profile') }}
            </flux:navlist.item>

            <flux:navlist.item :href="route('security.edit')" wire:navigate>
                {{ __('Security') }}
            </flux:navlist.item>

            <flux:navlist.item :href="route('appearance.edit')" wire:navigate>
                {{ __('Appearance') }}
            </flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden bg-violet-200 dark:bg-violet-800" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>
            {{ $heading ?? '' }}
        </flux:heading>

        <flux:subheading>
            {{ $subheading ?? '' }}
        </flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>

</div>
