<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationController extends Controller
{
    #[Route('/api/login', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required_without:identification_number', 'email'],
            'identification_number' => ['required_without:email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json(['message' => 'Welcome!']);
        }

        throw ValidationException::withMessages(
            [
                'email' => 'The provided credentials are incorrect.',
                'identification_number' => 'The provided credentials are incorrect.',
            ],
        );
    }

    #[Route('/api/logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout sucessful']);
    }
}
