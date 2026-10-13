<?php

namespace App\Livewire\Traits;

trait WithSorting
{
    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    protected function allowedSortFields(): array
    {
        return ['name', 'created_at', 'branch'];
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, $this->allowedSortFields(), true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc'
                ? 'desc'
                : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }
}
