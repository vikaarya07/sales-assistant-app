@props(['title', 'description'])

<div class="flex w-full flex-row text-center items-center gap-3">
    <span class="flex size-20! items-center justify-center rounded-md">
        <x-app-logo-icon class="fill-current text-black dark:text-white" />
    </span>
    <div class="flex w-full flex-col text-left">
        <flux:heading size="xl" level="1">{{ $title }}</flux:heading>
        <flux:subheading>{{ $description }}</flux:subheading>
    </div>
</div>
