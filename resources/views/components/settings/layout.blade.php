<div class="flex items-start max-md:flex-col">

    <div
        class="me-10 w-full pb-4 md:w-55 rounded-xl bg-linear-to-b from-indigo-100/70 via-violet-100/60 to-fuchsia-100/50 dark:from-indigo-950/50 dark:via-violet-950/40 dark:to-fuchsia-950/30 p-3">
        <flux:navlist aria-label="{{ __('Settings') }}">
            <flux:navlist.item :href="route('profile.edit')" wire:navigate
                class="text-indigo-700 hover:bg-indigo-200/40 hover:text-indigo-900 dark:text-indigo-300 dark:hover:bg-indigo-900/40">
                {{ __('Profile') }}
            </flux:navlist.item>

            <flux:navlist.item :href="route('security.edit')" wire:navigate
                class="text-violet-700 hover:bg-violet-200/40 hover:text-violet-900 dark:text-violet-300 dark:hover:bg-violet-900/40">
                {{ __('Security') }}
            </flux:navlist.item>

            <flux:navlist.item :href="route('appearance.edit')" wire:navigate
                class="text-fuchsia-700 hover:bg-fuchsia-200/40 hover:text-fuchsia-900 dark:text-fuchsia-300 dark:hover:bg-fuchsia-900/40">
                {{ __('Appearance') }}
            </flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden bg-violet-200/60 dark:bg-violet-800/40" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading class="text-indigo-900 dark:text-indigo-100">
            {{ $heading ?? '' }}
        </flux:heading>

        <flux:subheading class="text-violet-700/80 dark:text-violet-300/80">
            {{ $subheading ?? '' }}
        </flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>

</div>
