<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'System Admin',
                'email' => 'admin@edudev.local',
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make('Admin12345!'),
                'email_verified_at' => now(),
            ]
        );
    }
}
