<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Str;

class SetSingleSessionToken
{
    public function handle(Login $event): void
    {
        if ($event->guard !== 'web' || ! $event->user instanceof User) {
            return;
        }

        $request = request();

        if (! $request->hasSession()) {
            return;
        }

        $token = Str::random(64);

        $event->user->forceFill([
            'session_login_token' => $token,
        ])->save();

        $request->session()->put(
            'single_session_token',
            $token,
        );
    }
}
