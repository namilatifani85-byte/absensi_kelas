<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StoredXssProtectionTest extends TestCase
{
    public function test_security_headers_middleware_defines_required_headers(): void
    {
        $middlewareFile = file_get_contents(base_path('app/Http/Middleware/SecurityHeadersMiddleware.php'));

        $this->assertStringContainsString('Content-Security-Policy', $middlewareFile, 'CSP header wajib disetel');
        $this->assertStringContainsString("default-src 'self'", $middlewareFile);
        $this->assertStringContainsString("script-src 'self'", $middlewareFile);
        $this->assertStringContainsString('X-Content-Type-Options', $middlewareFile);
        $this->assertStringContainsString('nosniff', $middlewareFile);
        $this->assertStringContainsString('X-Frame-Options', $middlewareFile);
        $this->assertStringContainsString('DENY', $middlewareFile);
        $this->assertStringContainsString('Referrer-Policy', $middlewareFile);
    }

    public function test_bootstrap_app_registers_security_headers_middleware(): void
    {
        $bootstrapFile = file_get_contents(base_path('bootstrap/app.php'));

        $this->assertStringContainsString('SecurityHeadersMiddleware::class', $bootstrapFile, 'SecurityHeadersMiddleware wajib terdaftar di bootstrap/app.php');
    }

    public function test_blade_views_use_contextual_escaping_and_avoid_raw_unescaped_tags(): void
    {
        $showFile = file_get_contents(base_path('resources/views/attendances/show.blade.php'));

        // Pastikan tidak ada sintaks unescaped {!! ... !!} pada data input pengguna
        $this->assertStringNotContainsString('{!! $note->notes !!}', $showFile, 'Dilarang menggunakan unescaped syntax pada notes');
        $this->assertStringNotContainsString('{!! $attendance->excuse_notes !!}', $showFile, 'Dilarang menggunakan unescaped syntax pada excuse_notes');

        // Pastikan menggunakan sintaks aman bawaan Blade {{ ... }}
        $this->assertStringContainsString('{{ $note->notes }}', $showFile, 'Wajib menggunakan contextual escaping {{ $note->notes }}');
        $this->assertStringContainsString('{{ $attendance->excuse_notes', $showFile);
    }

    public function test_xss_payload_is_properly_escaped_into_safe_html_entities(): void
    {
        $scriptPayload = "<script>alert('XSS-Test')</script>";
        $imgPayload = "<img src=x onerror=alert(1)>";

        // Fungsi bawaan Blade e() membungkus htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
        $escapedScript = htmlspecialchars($scriptPayload, ENT_QUOTES, 'UTF-8');
        $escapedImg = htmlspecialchars($imgPayload, ENT_QUOTES, 'UTF-8');

        $this->assertSame("&lt;script&gt;alert(&#039;XSS-Test&#039;)&lt;/script&gt;", $escapedScript);
        $this->assertSame("&lt;img src=x onerror=alert(1)&gt;", $escapedImg);

        // Pastikan tidak ada tag executable HTML yang tersisa
        $this->assertStringNotContainsString('<script>', $escapedScript);
        $this->assertStringNotContainsString('onerror=', $escapedImg);
    }

    public function test_attendance_note_controller_authorizes_and_stores_notes(): void
    {
        $controllerFile = file_get_contents(base_path('app/Http/Controllers/AttendanceNoteController.php'));

        $this->assertStringContainsString("Gate::authorize('view', \$attendance)", $controllerFile, 'Wajib mengevaluasi policy');
        $this->assertStringContainsString('AttendanceNote::create', $controllerFile);
        $this->assertStringContainsString("'notes' => \$request->input('notes')", $controllerFile);
    }
}
