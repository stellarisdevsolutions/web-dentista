<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title),
            'category' => fake()->randomElement(['Prevención y Cuidados', 'Ortodoncia', 'Estética Dental', 'Salud Infantil']),
            'excerpt' => fake()->paragraph(2),
            'content' => '<p>'.fake()->paragraph(4).'</p>',
            'cover_image' => null,
            'published_at' => now(),
            'is_published' => true,
        ];
    }
}
