<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Unilam',
            'email' => 'admin@unilam.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone_number' => '6285178093168',
        ]);

        Book::create([
            'title' => 'Flutter for Beginners',
            'author' => 'Google Developers',
            'description' => 'Learn Flutter from scratch with this comprehensive guide.',
            'category' => 'Technology',
            'stock' => 10,
            'cover_image' => 'https://via.placeholder.com/150',
            'status' => 'Available',
        ]);

        Book::create([
            'title' => 'Laravel Architectural Patterns',
            'author' => 'Taylor Otwell',
            'description' => 'A deep dive into Laravel internals and design patterns.',
            'category' => 'Technology',
            'stock' => 5,
            'cover_image' => 'https://loremflickr.com/300/450/tech?lock=2',
            'status' => 'Available',
        ]);

        // Generate 50 dummy books
        Book::factory(50)->create();
    }
}
