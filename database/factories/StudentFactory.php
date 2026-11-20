<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'       => User::factory()->create(['role' => 'student'])->id,
            'nim'           => fake()->unique()->numerify('23017####'),
            'full_name'     => fake()->randomElement([
                'Budi Santoso',
                'Siti Aminah',
                'Andi Pratama',
                'Dewi Lestari',
                'Reza Firmansyah',
            ]),
            'study_program' => 'Teknik Informatika',
            'class_year'    => '2023',
        ];
    }
}
