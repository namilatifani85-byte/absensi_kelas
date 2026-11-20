<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class VulnerableDemoTest extends TestCase
{
    public function test_env_vulnerable_isolates_database_and_session_cookie(): void
    {
        $envFile = file_get_contents(base_path('.env.vulnerable'));

        $this->assertStringContainsString('DB_DATABASE=database/database_vuln.sqlite', $envFile, 'Database rentan wajib terisolasi di database_vuln.sqlite');
        $this->assertStringContainsString('SESSION_COOKIE=absensikita_vuln_session', $envFile, 'Cookie sesi wajib terisolasi');
        $this->assertStringContainsString('APP_URL=http://127.0.0.1:8001', $envFile, 'Harus berjalan di port 8001');
    }

    public function test_vulnerable_controller_contains_raw_sql_concatenation_for_sqli_demo(): void
    {
        $controllerFile = file_get_contents(base_path('app/Http/Controllers/Vulnerable/VulnerableDemoController.php'));

        $this->assertStringContainsString('SELECT * FROM classes WHERE', $controllerFile);
        $this->assertStringContainsString('LIKE \'%" . $search', $controllerFile, 'Wajib mengandung raw string concatenation untuk membuktikan celah SQLi (SEC-08)');
    }

    public function test_vulnerable_show_view_contains_unescaped_blade_tags_for_xss_demo(): void
    {
        $viewFile = file_get_contents(base_path('resources/views/vulnerable/show.blade.php'));

        $this->assertStringContainsString('{!! $attendance->excuse_notes', $viewFile, 'View rentan wajib memuat unescaped blade tag {!! !!} untuk demo Stored XSS');
        $this->assertStringContainsString('{!! $note->notes !!}', $viewFile, 'View rentan wajib memuat unescaped note tag untuk demo Stored XSS');
    }

    public function test_vulnerable_controller_lacks_policy_authorization_for_idor_demo(): void
    {
        $controllerFile = file_get_contents(base_path('app/Http/Controllers/Vulnerable/VulnerableDemoController.php'));

        $this->assertStringNotContainsString('Gate::authorize', $controllerFile, 'Controller rentan dilarang memanggil Gate::authorize (membuktikan celah IDOR)');
        $this->assertStringNotContainsString('AttendancePolicy', $controllerFile);
    }

    public function test_run_vulnerable_script_binds_only_to_loopback_port_8001(): void
    {
        $scriptSh = file_get_contents(base_path('scripts/run_vulnerable_demo.sh'));
        $scriptBat = file_get_contents(base_path('scripts/run_vulnerable_demo.bat'));

        $this->assertStringContainsString('127.0.0.1', $scriptSh, 'Script wajib bind ke 127.0.0.1');
        $this->assertStringContainsString('8001', $scriptSh, 'Script wajib bind ke port 8001');
        $this->assertStringContainsString('8001', $scriptBat);
    }
}
