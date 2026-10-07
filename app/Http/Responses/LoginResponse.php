<?php

namespace App\Http\Responses;

use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = $request->user();

        $redirect = $user->role === UserRole::ADMIN
            ? '/admin/overview'
            : '/dashboard';

        return redirect($redirect)->with('swal', [
            'type' => 'toast',
            'message' => 'Selamat datang kembali, '.$user->name.'!',
            'icon' => 'success',
        ]);
    }
}
