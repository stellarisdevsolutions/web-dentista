<?php

namespace Database\Factories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => Appointment::generateUniqueCode(),
            'patient_name' => fake()->name(),
            'patient_email' => fake()->safeEmail(),
            'patient_phone' => fake()->phoneNumber(),
            'appointment_date' => fake()->date(),
            'appointment_time' => fake()->time('H:i'),
            'duration_minutes' => 45,
            'is_first_visit' => fake()->boolean(),
            'symptoms' => fake()->sentence(),
            'status' => 'pending',
        ];
    }
}
