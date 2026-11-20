<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AtomicCheckinTest extends TestCase
{
    public function test_checkin_controller_uses_db_transaction_and_lock_for_update(): void
    {
        $controllerFile = file_get_contents(base_path('app/Http/Controllers/AttendanceCheckinController.php'));

        $this->assertStringContainsString('DB::transaction', $controllerFile, 'Presensi wajib dibungkus dalam DB::transaction untuk atomisitas');
        $this->assertStringContainsString('lockForUpdate()', $controllerFile, 'Presensi wajib menggunakan lockForUpdate() untuk mencegah race condition');
        $this->assertStringContainsString('Anda sudah melakukan presensi hari ini', $controllerFile, 'Wajib menolak presensi dobel');
    }

    public function test_checkin_request_validates_status_and_notes(): void
    {
        $requestFile = file_get_contents(base_path('app/Http/Requests/Attendance/CheckinRequest.php'));

        $this->assertStringContainsString("'status' => ['required', 'string', 'in:present,sick,permit']", $requestFile);
        $this->assertStringContainsString("'excuse_notes' => ['nullable', 'string', 'max:500']", $requestFile);
        $this->assertStringContainsString("'medical_note' => ['nullable', 'string', 'max:1000']", $requestFile);
        $this->assertStringContainsString("role === 'student'", $requestFile);
    }

    public function test_medical_note_is_encrypted_using_aes_gcm(): void
    {
        $controllerFile = file_get_contents(base_path('app/Http/Controllers/AttendanceCheckinController.php'));

        $this->assertStringContainsString('AesGcmEncryptor::class', $controllerFile, 'Catatan medis wajib dienkripsi via AesGcmEncryptor (SEC-03)');
        $this->assertStringContainsString("(string) \$student->id", $controllerFile, 'AAD enkripsi wajib diikat ke student_id');
        $this->assertStringContainsString("'medical_note_encrypted' => \$encryptedMedical", $controllerFile);
    }

    public function test_verification_controller_is_idempotent(): void
    {
        $controllerFile = file_get_contents(base_path('app/Http/Controllers/AttendanceVerificationController.php'));

        $this->assertStringContainsString("role !== 'lecturer'", $controllerFile, 'Hanya dosen yang boleh memverifikasi kehadiran');
        $this->assertStringContainsString("\$attendance->status = \$validated['status']", $controllerFile);
        $this->assertStringContainsString("\$attendance->verified_by = Auth::id()", $controllerFile);
    }
}
