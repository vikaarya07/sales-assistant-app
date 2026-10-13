@if ($sortField === $field)

    @if ($sortDirection === 'asc')
        <flux:icon.arrow-up class="size-3" />
    @else
        <flux:icon.arrow-down class="size-3" />
    @endif
@else
    <flux:icon.arrows-up-down class="size-3 opacity-75" />

@endif
