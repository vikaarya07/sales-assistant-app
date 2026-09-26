<section class="mx-auto max-w-7xl space-y-6 px-6 py-8 lg:px-8">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">
        {{ __('Appearance settings') }}
    </flux:heading>

    <x-settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">

        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance"
            class="bg-linear-to-r from-indigo-100 via-violet-100 to-fuchsia-100 dark:from-indigo-950/50 dark:via-violet-950/40 dark:to-fuchsia-950/30 border border-indigo-200/60 dark:border-violet-800/50">

            <flux:radio value="light" icon="sun">
                {{ __('Light') }}
            </flux:radio>

            <flux:radio value="dark" icon="moon">
                {{ __('Dark') }}
            </flux:radio>

            <flux:radio value="system" icon="computer-desktop">
                {{ __('System') }}
            </flux:radio>

        </flux:radio.group>

    </x-settings.layout>
</section>
