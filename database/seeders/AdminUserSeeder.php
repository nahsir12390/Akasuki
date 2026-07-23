<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrNew(['email' => 'naruto@gmail.com']);

        $user->forceFill([
            'name' => 'Naruto Admin',
            'password' => Hash::make('nasiru12390'),
            'email_verified_at' => now(),
            'is_admin' => true,
        ])->save();
    }
}
