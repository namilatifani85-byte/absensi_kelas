<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AttendanceIdorTest extends TestCase
{
    public function test_attendance_policy_blocks_idor_between_students(): void
    {
        $policyFile = file_get_contents(base_path('app/Policies/AttendancePolicy.php'));

        $this->assertStringContainsString("role === 'lecturer'", $policyFile, 'Dosen berhak melihat data presensi');
        $this->assertStringContainsString("student->user_id === (int) \$user->id", $policyFile, 'Mahasiswa hanya berhak melihat presensi miliknya sendiri');
    }

    public function test_my_attendance_controller_enforces_policy_authorization(): void
    {
        $controllerFile = file_get_contents(base_path('app/Http/Controllers/MyAttendanceController.php'));

        $this->assertStringContainsString("Gate::authorize('view', \$attendance)", $controllerFile, 'Controller wajib memanggil Gate::authorize untuk evaluasi Policy');
        $this->assertStringContainsString("app(AesGcmEncryptor::class)->decrypt", $controllerFile, 'Catatan medis wajib didekripsi secara terotentikasi');
        $this->assertStringContainsString("(string) \$attendance->student_id", $controllerFile, 'AAD dekripsi wajib diikat ke student_id');
    }

    public function test_session_cookie_security_configurations(): void
    {
        $configFile = file_get_contents(base_path('config/session.php'));

        $this->assertStringContainsString("'absensikita_session'", $configFile, 'Nama cookie sesi default wajib absensikita_session');
        $this->assertStringContainsString("'secure' => env('SESSION_SECURE_COOKIE', true)", $configFile, 'Cookie Secure wajib default true untuk HTTPS');
        $this->assertStringContainsString("'http_only' => env('SESSION_HTTP_ONLY', true)", $configFile, 'Cookie HttpOnly wajib true untuk mencegah XSS session stealing');
        $this->assertStringContainsString("'same_site' => env('SESSION_SAME_SITE', 'lax')", $configFile, 'Cookie SameSite wajib lax');
    }

    public function test_app_service_provider_registers_attendance_policy(): void
    {
        $providerFile = file_get_contents(base_path('app/Providers/AppServiceProvider.php'));

        $this->assertStringContainsString("Gate::policy(Attendance::class, AttendancePolicy::class)", $providerFile, 'AppServiceProvider wajib mendaftarkan AttendancePolicy');
    }
}
