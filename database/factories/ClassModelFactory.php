<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassModel>
 */
class ClassModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Pastikan ada dosen yang bisa jadi FK lecturer_id
        $lecturer = User::factory()->create(['role' => 'lecturer']);

        return [
            'code'       => strtoupper(fake()->unique()->bothify('TIF-###')),
            'name'       => fake()->randomElement([
                'Keamanan Sistem Komputer',
                'Pemrograman Web Lanjut',
                'Basis Data Terdistribusi',
                'Jaringan Komputer',
                'Rekayasa Perangkat Lunak',
            ]),
            'lecturer_id' => $lecturer->id,
            'room'        => fake()->randomElement(['Lab Komputer 1', 'Lab Komputer 2', 'Ruang 301']),
            'day'         => fake()->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']),
            'time_start'  => '08:00',
            'time_end'    => '10:30',
            'is_active'   => true,
        ];
    }
}
