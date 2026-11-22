<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionStatusController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $sessionToken = $request->session()->get(
            'single_session_token',
        );

        $currentToken = $user?->session_login_token;

        $valid = is_string($sessionToken)
            && is_string($currentToken)
            && hash_equals($currentToken, $sessionToken);

        return response()->json([
            'valid' => $valid,
        ]);
    }
}
