<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceNote;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TKT-012-WEB: Pengujian Fungsional Security Headers & Stored XSS Protection (SEC-09)
 *
 * Mencakup: keberadaan header CSP, X-Frame-Options DENY, X-Content-Type-Options nosniff,
 * dan verifikasi bahwa payload XSS disimpan sebagai teks mentah (not rendered) oleh Blade.
 */
class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // 1. Response HTTP Memuat Header Content-Security-Policy (SEC-09)
    // -----------------------------------------------------------------------

    public function test_response_contains_content_security_policy_header(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertHeader('Content-Security-Policy');
        $this->assertStringContainsString(
            "default-src 'self'",
            $response->headers->get('Content-Security-Policy') ?? '',
            "CSP wajib memuat 'default-src 'self'' untuk membatasi sumber resource."
        );
    }

    // -----------------------------------------------------------------------
    // 2. Response HTTP Memuat Header X-Frame-Options: DENY
    // -----------------------------------------------------------------------

    public function test_response_contains_x_frame_options_deny(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertHeader('X-Frame-Options', 'DENY');
    }

    // -----------------------------------------------------------------------
    // 3. Response HTTP Memuat Header X-Content-Type-Options: nosniff
    // -----------------------------------------------------------------------

    public function test_response_contains_x_content_type_options_nosniff(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    // -----------------------------------------------------------------------
    // 4. Payload XSS Disimpan Tapi Ditampilkan Escaped (Stored XSS Prevention)
    // -----------------------------------------------------------------------

    public function test_attendance_note_xss_payload_is_escaped_in_output(): void
    {
        $user       = User::factory()->create(['role' => 'student']);
        $class      = ClassModel::factory()->create();
        $student    = Student::factory()->create(['user_id' => $user->id]);

        $attendance = Attendance::factory()->create([
            'student_id'      => $student->id,
            'class_id'        => $class->id,
            'status'          => 'present',
            'attendance_date' => now()->toDateString(),
        ]);

        // Simulasi payload Stored XSS yang disimpan di database
        $xssPayload = '<script>alert("XSS")</script>';
        AttendanceNote::create([
            'attendance_id' => $attendance->id,
            'author_id'     => $user->id,
            'notes'         => $xssPayload,
        ]);

        $this->actingAs($user);

        $response = $this->get("/attendances/{$attendance->id}");
        $response->assertStatus(200);

        $body = $response->getContent();

        // Tag <script> mentah TIDAK boleh muncul di response HTML
        $this->assertStringNotContainsString(
            '<script>alert("XSS")</script>',
            $body,
            'Payload XSS wajib di-escape oleh Blade dan TIDAK boleh di-render sebagai HTML mentah.'
        );

        // Versi ter-escape WAJIB ada (Blade {{ }} = htmlspecialchars)
        $this->assertStringContainsString(
            '&lt;script&gt;',
            $body,
            'Karakter < wajib di-escape menjadi &lt; oleh Blade contextual escaping.'
        );
    }
}
