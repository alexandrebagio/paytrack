<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationController extends Controller
{
    #[Route('/api/login', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required_without:identification_number', 'email'],
            'identification_number' => ['required_without:email'],
            'password' => ['required'],
        ]);

        $user = User::where(
            $request->only('email', 'identification_number')
        )->first();

        if (
            !$user ||
            !Hash::check($request->password, $user->password)
        ) {
            throw ValidationException::withMessages(
                [
                    'email' => 'The provided credentials are incorrect.',
                    'identification_number' => 'The provided credentials are incorrect.',
                ],
            );
        }

        return response()->json([
            'data' => [
                'type' => 'Bearer',
                'token' => $user->createToken('*')->plainTextToken,
            ],
        ]);
    }
}
