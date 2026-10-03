<?php

use Livewire\Component;

new class extends Component {
    public function heartbeat(): void
    {
        if (!auth()->check()) {
            return;
        }

        auth()
            ->user()
            ->updateQuietly([
                'last_activity_at' => now(),
            ]);
    }
};
?>

<div wire:poll.30s="heartbeat"></div>
