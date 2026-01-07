<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSetupSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@unilam.com'], // Cari berdasarkan email ini
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'), // Passwordnya ini
                'role' => 'admin',
                'phone_number' => '081234567890',
            ]
        );
    }
}
