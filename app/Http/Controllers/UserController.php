<?php

namespace App\Http\Controllers;

use App\Enums\PersonType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                    'email_verified_at' => Carbon::now(), //TODO - Email de verificação de conta nova
                    'password' => bcrypt($request->password),
                    ...$request->only('name', 'email', 'type', 'identification_number'),
                ]
            );

            $user->wallet()->create(['cash' => 0]);

            DB::commit();

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

    public function update(Request $request, User $user)
    {
        //
    }

    public function destroy(User $user)
    {
        //
    }
}
