<?php

namespace App\Http\Controllers;

use App\Enums\PersonType;
use App\Mail\UserCreated;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    #[Route('/api/user/store', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'required|min:3|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|max:255',
                'type' => ['required', Rule::enum(PersonType::class)],
                'identification_number' => 'required|min:11|max:14|unique:users',
            ]);

            $user = User::create(
                [
                    'remember_token' => Str::random(10),
                    'password' => bcrypt($request->password),
                    ...$request->only('name', 'email', 'type', 'identification_number'),
                ]
            );

            $user->wallet()->create(['cash' => 0]);

            DB::commit();

            $message = (new UserCreated($user))->onQueue('mail');
            Mail::to($user->email)->queue($message);

            return response()->json(['data' => $user]);
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    #[Route('/api/user', methods: ['GET'])]
    public function show(Request $request, User $user): JsonResponse
    {
        $user = $user->find($request->user()->id);

        return response()->json(['data' => $user]);
    }

    #[Route('/api/user/confirmation/{rememberToken}', methods: ['GET'])]
    public function confirmation(string $rememberToken, Request $request): JsonResponse
    {
        try {
            $request->merge(['remember_token' => $rememberToken]);
            $request->validate([
                'remember_token' => 'required|max:255|min:1',
            ]);

            $user = User::where('remember_token', $rememberToken)->firstOrFail();
            $user->email_verified_at = Carbon::now();
            $user->remember_token = null;
            $user->save();

            return response()->json(['data' => $user, 'message' => 'User verified']);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function email()
    {
        $user = User::first();

        return view('mail.UserCreated', ['user' => $user]);
    }
}
