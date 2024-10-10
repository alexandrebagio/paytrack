<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function login(Request $request): array
    {
        $user = User::where(
            $request->only('email', 'identification_number')
        )->first();

        if (
            !$user ||
            !Hash::check($request->password, $user->password)
        ) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return [
            'token' => $user->createToken('*')->plainTextToken,
        ];
    }
}
