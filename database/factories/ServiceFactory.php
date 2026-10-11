<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'category' => fake()->randomElement(['Preventiva', 'Estética', 'Ortodoncia', 'Implantología', 'General']),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 50, 500),
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90]),
            'allows_online_booking' => true,
            'is_featured' => false,
            'is_active' => true,
            'order' => fake()->numberBetween(1, 20),
        ];
    }
}
