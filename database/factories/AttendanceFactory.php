<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id'             => Student::factory(),
            'class_id'               => ClassModel::factory(),
            'attendance_date'        => now()->toDateString(),
            'status'                 => fake()->randomElement(['present', 'sick', 'permit', 'absent']),
            'excuse_notes'           => null,
            'medical_note_encrypted' => null,
            'checked_in_at'          => now(),
            'verified_by'            => null,
        ];
    }
}
