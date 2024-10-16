<?php

namespace App\Http\Controllers;

use App\Enums\PersonType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    public function index()
    {
        //
    }

    #[Route('/api/user/store', methods: ['POST'])]
    public function store(Request $request)
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

            DB::commit();

            return $user;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    #[Route('/api/user', methods: ['GET'])]
    public function show(Request $request, User $user)
    {
        $user = $user->find($request->user()->id);

        return $user;
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
