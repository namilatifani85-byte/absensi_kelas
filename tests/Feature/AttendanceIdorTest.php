<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TKT-012-WEB: Pengujian Fungsional Anti-IDOR Hak Akses Presensi (SEC-10)
 *
 * Mencakup: akses presensi milik sendiri (200), penolakan lintas mahasiswa (403),
 * dan hak akses dosen untuk melihat semua presensi (200).
 */
class AttendanceIdorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Buat user, student, kelas, dan record presensi untuk mahasiswa tertentu.
     *
     * @return array{user: User, student: Student, attendance: Attendance}
     */
    private function createStudentWithAttendance(): array
    {
        $user      = User::factory()->create(['role' => 'student']);
        $class     = ClassModel::factory()->create();
        $student   = Student::factory()->create(['user_id' => $user->id]);

        $attendance = Attendance::factory()->create([
            'student_id'      => $student->id,
            'class_id'        => $class->id,
            'status'          => 'present',
            'attendance_date' => now()->toDateString(),
        ]);

        return compact('user', 'student', 'attendance');
    }

    // -----------------------------------------------------------------------
    // 1. Mahasiswa Dapat Melihat Presensi Miliknya Sendiri (HTTP 200)
    // -----------------------------------------------------------------------

    public function test_student_can_view_own_attendance(): void
    {
        ['user' => $user, 'attendance' => $attendance] = $this->createStudentWithAttendance();

        $this->actingAs($user);

        $response = $this->get("/attendances/{$attendance->id}");

        $response->assertStatus(200);
    }

    // -----------------------------------------------------------------------
    // 2. Mahasiswa Tidak Dapat Melihat Presensi Mahasiswa Lain (HTTP 403)
    // -----------------------------------------------------------------------

    public function test_student_cannot_view_other_student_attendance(): void
    {
        // Mahasiswa A — pemilik presensi
        ['attendance' => $attendance] = $this->createStudentWithAttendance();

        // Mahasiswa B — penyerang IDOR
        $attacker = User::factory()->create(['role' => 'student']);
        Student::factory()->create(['user_id' => $attacker->id]);

        $this->actingAs($attacker);

        // Mahasiswa B mencoba mengakses presensi Mahasiswa A
        $response = $this->get("/attendances/{$attendance->id}");

        // Wajib ditolak dengan 403 Forbidden (Anti-IDOR SEC-10)
        $response->assertStatus(403);
    }

    // -----------------------------------------------------------------------
    // 3. Dosen Dapat Melihat Semua Presensi (HTTP 200)
    // -----------------------------------------------------------------------

    public function test_lecturer_can_view_any_attendance(): void
    {
        ['attendance' => $attendance] = $this->createStudentWithAttendance();

        $lecturer = User::factory()->create(['role' => 'lecturer']);

        $this->actingAs($lecturer);

        $response = $this->get("/attendances/{$attendance->id}");

        $response->assertStatus(200);
    }
}
