<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a random category to make the image slightly more relevant if possible, 
        // though loremflickr /book is generic.
        $category = fake()->randomElement(['Teknologi', 'Novel', 'Sains', 'Sejarah', 'Bisnis', 'Psikologi']);
        
        return [
            'title' => ucwords(fake()->words(3, true)), // "The Great Gatsby" style
            'author' => fake()->name(),
            'description' => fake()->paragraph(3),
            'category' => $category,
            'stock' => fake()->numberBetween(3, 20),
            // We use 'lock' param to ensure the image stays the same for this specific book entry
            // otherwise scrolling in Flutter might cause the image to refresh/change.
            'cover_image' => 'https://loremflickr.com/300/450/book?lock=' . fake()->unique()->numberBetween(1, 10000),
            'book_file' => null, // We don't have real PDFs for dummies
            'status' => 'Available',
        ];
    }
}
