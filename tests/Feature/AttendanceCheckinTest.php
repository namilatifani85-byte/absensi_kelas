<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TKT-012-WEB: Pengujian Fungsional Presensi Kehadiran (SEC-03, SEC-08)
 *
 * Mencakup: presensi berhasil, penolakan duplikat pada hari sama (idempotent),
 * verifikasi enkripsi AES-256-GCM catatan medis di database,
 * dan penolakan akses dosen ke form presensi.
 */
class AttendanceCheckinTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // 1. Mahasiswa Berhasil Melakukan Presensi
    // -----------------------------------------------------------------------

    public function test_student_can_checkin_to_active_class(): void
    {
        $user    = User::factory()->create(['role' => 'student']);
        $class   = ClassModel::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post("/classes/{$class->id}/checkin", [
            'status' => 'present',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'class_id'   => $class->id,
            'status'     => 'present',
        ]);
    }

    // -----------------------------------------------------------------------
    // 2. Presensi Duplikat di Hari yang Sama Ditolak (Idempotent)
    // -----------------------------------------------------------------------

    public function test_duplicate_checkin_same_day_is_rejected(): void
    {
        $user    = User::factory()->create(['role' => 'student']);
        $class   = ClassModel::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // Presensi pertama — berhasil
        $this->post("/classes/{$class->id}/checkin", ['status' => 'present']);

        // Presensi kedua pada hari yang sama — wajib ditolak
        $response = $this->post("/classes/{$class->id}/checkin", ['status' => 'present']);
        $response->assertRedirect();

        // Database hanya boleh memiliki 1 record presensi (bukan duplikat)
        $this->assertDatabaseCount('attendances', 1);
    }

    // -----------------------------------------------------------------------
    // 3. Catatan Izin/Sakit Tersimpan Terenkripsi AES-256-GCM (SEC-03)
    // -----------------------------------------------------------------------

    public function test_medical_note_is_encrypted_in_database(): void
    {
        $user    = User::factory()->create(['role' => 'student']);
        $class   = ClassModel::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post("/classes/{$class->id}/checkin", [
            'status'       => 'sick',
            'medical_note' => 'Demam tinggi dan rawat jalan di klinik Sehat Sentosa.',
        ]);

        $response->assertRedirect();

        // Kolom terenkripsi wajib berformat envelope AES-GCM: v1:<nonce>:<ct>:<tag>
        $attendance = Attendance::where('student_id', $student->id)->first();
        $this->assertNotNull($attendance, 'Record presensi harus tersimpan di database.');
        $this->assertStringStartsWith(
            'v1:',
            $attendance->medical_note_encrypted ?? '',
            'Catatan medis wajib terenkripsi dengan format envelope v1:<nonce>:<ct>:<tag>'
        );
    }

    // -----------------------------------------------------------------------
    // 4. Dosen Tidak Dapat Melakukan Presensi (Mahasiswa Only)
    // -----------------------------------------------------------------------

    public function test_lecturer_cannot_checkin(): void
    {
        $lecturer = User::factory()->create(['role' => 'lecturer']);
        $class    = ClassModel::factory()->create();

        $this->actingAs($lecturer);

        // Dosen mengakses form presensi — wajib ditolak (403 atau redirect)
        $response = $this->get("/classes/{$class->id}/checkin");

        // Respons tidak boleh 200 OK untuk dosen
        $this->assertNotSame(200, $response->getStatusCode(),
            'Dosen tidak boleh mengakses form presensi mahasiswa.'
        );
    }
}
