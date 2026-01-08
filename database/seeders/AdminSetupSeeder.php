<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSetupSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'chandra@gmail.com'], // Cari berdasarkan email ini
            [
                'name' => 'Super Admin',
                'password' => 'admin1234', // Passwordnya ini
                'role' => 'admin',
                'phone_number' => '6285213869298',
            ]
        );
    }
}
