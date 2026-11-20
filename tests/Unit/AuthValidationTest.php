<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AuthValidationTest extends TestCase
{
    public function test_bcrypt_produces_unique_hashes_for_identical_passwords(): void
    {
        $password = 'HadirAman#01!';

        // Hash manual Bcrypt bawaan PHP (cost 12 default Laravel)
        $hash1 = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $hash2 = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $this->assertStringStartsWith('$2y$12$', $hash1, 'Hash Bcrypt wajib menggunakan cost 12');
        $this->assertStringStartsWith('$2y$12$', $hash2, 'Hash Bcrypt wajib menggunakan cost 12');
        $this->assertNotSame($hash1, $hash2, 'Dua hash dengan kata sandi identik WAJIB berbeda karena salt unik (SEC-02)');

        // Verifikasi kata sandi cocok terhadap kedua hash berbeda
        $this->assertTrue(password_verify($password, $hash1));
        $this->assertTrue(password_verify($password, $hash2));
    }

    public function test_register_request_contains_72_byte_limit_rule(): void
    {
        $requestFile = file_get_contents(base_path('app/Http/Requests/Auth/RegisterRequest.php'));

        $this->assertStringContainsString('max:72', $requestFile, 'Rule max:72 wajib ada untuk mitigasi silent truncation Bcrypt (ADR-009)');
        $this->assertStringContainsString('min:8', $requestFile, 'Rule min:8 wajib ada');
        $this->assertStringContainsString('confirmed', $requestFile, 'Rule confirmed wajib ada');
    }

    public function test_auth_controller_implements_session_regeneration_and_invalidation(): void
    {
        $controllerFile = file_get_contents(base_path('app/Http/Controllers/Auth/AuthController.php'));

        $this->assertStringContainsString('session()->regenerate()', $controllerFile, 'Wajib meregenerasi sesi saat login untuk mencegah Session Fixation (SEC-10)');
        $this->assertStringContainsString('session()->invalidate()', $controllerFile, 'Wajib menginvalidasi sesi saat logout');
        $this->assertStringContainsString('session()->regenerateToken()', $controllerFile, 'Wajib meregenerasi CSRF token saat logout');
    }
}
