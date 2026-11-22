<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleSession
{
    public function handle(
        Request $request,
        Closure $next,
    ): Response {
        $user = $request->user();

        $sessionToken = $request->session()->get(
            'single_session_token',
        );

        $currentToken = $user?->session_login_token;

        if (
            ! $user ||
            ! is_string($sessionToken) ||
            ! is_string($currentToken) ||
            ! hash_equals($currentToken, $sessionToken)
        ) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if (
                $request->expectsJson() ||
                $request->is('livewire/*')
            ) {
                return response()->json([
                    'message' => 'Sesi login telah digantikan.',
                ], 401);
            }

            return redirect()->route('login')->with('swal', [
                'type' => 'toast',
                'message' => 'Sesi Anda telah digantikan oleh login baru.',
                'icon' => 'warning',
            ]);
        }

        return $next($request);
    }
}
