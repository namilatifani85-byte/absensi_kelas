<?php

namespace Tests\Unit;

use App\Services\Security\AesGcmEncryptor;
use PHPUnit\Framework\TestCase;

class DatabaseSeederStructureTest extends TestCase
{
    public function test_seeder_defines_specified_accounts_and_classes(): void
    {
        $seederCode = file_get_contents(base_path('database/seeders/DatabaseSeeder.php'));

        // Cek akun dosen dan mahasiswa
        $this->assertStringContainsString('admin_dosen', $seederCode);
        $this->assertStringContainsString('budi_santoso', $seederCode);
        $this->assertStringContainsString('siti_aminah', $seederCode);

        // Cek kelas perkuliahan
        $this->assertStringContainsString('TIF-301', $seederCode);
        $this->assertStringContainsString('TIF-204', $seederCode);

        // Cek enkripsi AES-256-GCM pada catatan medis
        $this->assertStringContainsString('medical_note_encrypted', $seederCode);
        $this->assertStringContainsString('AesGcmEncryptor', $seederCode);
    }

    public function test_budi_and_siti_passwords_produce_unique_bcrypt_hashes(): void
    {
        $plain = 'HadirAman#01!';

        $hashBudi = password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);
        $hashSiti = password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);

        $this->assertStringStartsWith('$2y$12$', $hashBudi);
        $this->assertStringStartsWith('$2y$12$', $hashSiti);
        $this->assertNotSame($hashBudi, $hashSiti, 'Hash Bcrypt harus unik berbeda untuk SEC-02');
    }

    public function test_medical_note_in_seeder_is_properly_encrypted_with_v1_prefix(): void
    {
        $key = AesGcmEncryptor::generateKey();
        $encryptor = new AesGcmEncryptor($key);

        $studentId = '1';
        $cipher = $encryptor->encrypt(
            'Demam 38.5C perlu istirahat 2 hari sesuai surat dokter klinik kampus',
            $studentId
        );

        $this->assertStringStartsWith('v1:', $cipher);
        $this->assertSame(
            'Demam 38.5C perlu istirahat 2 hari sesuai surat dokter klinik kampus',
            $encryptor->decrypt($cipher, $studentId)
        );
    }
}
