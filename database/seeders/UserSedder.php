<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class UserSedder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('123'),
            'type' => 'F',
            'identification_number' => '74846134016',
        ]);

        Wallet::create([
            'cash' => 200,
            'user_id' => $user->id,
        ]);

        $user = User::factory()->create([
            'name' => 'Test User2',
            'email' => 'test2@example.com',
            'password' => bcrypt('123'),
            'type' => 'J',
            'identification_number' => '10984458000186',
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'cash' => 50,
        ]);
    }
}
