<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TKT-012-WEB: Pengujian Fungsional Autentikasi & Keamanan Sesi (SEC-02)
 *
 * Mencakup: login sukses, kegagalan kredensial salah, penolakan password > 72 byte,
 * dan regenerasi session ID saat login (Anti Session Fixation).
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // 1. Login dengan Kredensial Valid
    // -----------------------------------------------------------------------

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'budi.santoso@example.com',
            'password' => bcrypt('RahasiaAman@99'),
            'role'     => 'student',
        ]);

        $response = $this->post('/login', [
            'email'    => 'budi.santoso@example.com',
            'password' => 'RahasiaAman@99',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    // -----------------------------------------------------------------------
    // 2. Login Gagal – Password Salah
    // -----------------------------------------------------------------------

    public function test_login_fails_with_invalid_password(): void
    {
        User::factory()->create([
            'email'    => 'siti.aminah@example.com',
            'password' => bcrypt('Benarnya@123'),
            'role'     => 'student',
        ]);

        $response = $this->post('/login', [
            'email'    => 'siti.aminah@example.com',
            'password' => 'SalahPassword!',
        ]);

        // Wajib redirect balik (tidak masuk) dengan error sesi
        $response->assertRedirect();
        $this->assertGuest();
    }

    // -----------------------------------------------------------------------
    // 3. Validasi Password > 72 Byte Ditolak (SEC-02 Bcrypt Limit)
    // -----------------------------------------------------------------------

    public function test_register_rejects_password_exceeding_72_bytes(): void
    {
        // Password 73 karakter ASCII = 73 byte — melampaui batas 72 byte bcrypt
        $longPassword = str_repeat('A', 70) . '@1a';   // 73 karakter (melampaui max:72)

        $response = $this->post('/register', [
            'name'                  => 'Mahasiswa Baru',
            'email'                 => 'mahasiswabaru@example.com',
            'password'              => $longPassword,
            'password_confirmation' => $longPassword,
            'role'                  => 'student',
        ]);

        // Validasi max:72 wajib menolak input ini
        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    // -----------------------------------------------------------------------
    // 4. Session ID Diregenerasi Saat Login (Anti Session Fixation)
    // -----------------------------------------------------------------------

    public function test_session_id_regenerates_on_login(): void
    {
        User::factory()->create([
            'email'    => 'dosen.aman@example.com',
            'password' => bcrypt('DosenHadir#2024'),
            'role'     => 'lecturer',
        ]);

        // Mulai sesi kosong (tanpa autentikasi)
        $this->get('/login');
        $sessionBefore = session()->getId();

        // Login — sesi wajib diregenerasi (Session Fixation Prevention)
        $this->post('/login', [
            'email'    => 'dosen.aman@example.com',
            'password' => 'DosenHadir#2024',
        ]);

        $sessionAfter = session()->getId();

        // Session ID setelah login wajib berbeda dari session sebelum login
        $this->assertNotSame(
            $sessionBefore,
            $sessionAfter,
            'Session ID wajib diregenerasi saat login untuk mencegah Session Fixation.'
        );
    }
}
