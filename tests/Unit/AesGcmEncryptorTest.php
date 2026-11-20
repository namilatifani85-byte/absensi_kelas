<?php

namespace Tests\Unit;

use App\Exceptions\DecryptionFailedException;
use App\Services\Security\AesGcmEncryptor;
use PHPUnit\Framework\TestCase;

class AesGcmEncryptorTest extends TestCase
{
    private AesGcmEncryptor $encryptor;
    private string $key;

    protected function setUp(): void
    {
        parent::setUp();
        $this->key = AesGcmEncryptor::generateKey();
        $this->encryptor = new AesGcmEncryptor($this->key);
    }

    public function test_can_encrypt_and_decrypt_plaintext_successfully(): void
    {
        $plain = 'Mahasiswa menderita demam berdarah dan dirawat di RS Permata.';
        $aad = 'student_230170095';

        $cipher = $this->encryptor->encrypt($plain, $aad);
        $this->assertStringStartsWith('v1:', $cipher);

        $decrypted = $this->encryptor->decrypt($cipher, $aad);
        $this->assertSame($plain, $decrypted);
    }

    public function test_envelope_format_contains_four_segments(): void
    {
        $plain = 'Catatan rahasia';
        $cipher = $this->encryptor->encrypt($plain, 'student_1');

        $segments = explode(':', $cipher);
        $this->assertCount(4, $segments);
        $this->assertSame('v1', $segments[0]);

        $nonce = base64_decode($segments[1], true);
        $this->assertNotFalse($nonce);
        $this->assertSame(12, strlen($nonce), 'Nonce harus 12 byte (96 bits)');

        $tag = base64_decode($segments[3], true);
        $this->assertNotFalse($tag);
        $this->assertSame(16, strlen($tag), 'Authentication tag harus 16 byte (128 bits)');
    }

    public function test_encrypting_same_plaintext_twice_produces_different_ciphertexts(): void
    {
        $plain = 'Demam dan flu';
        $aad = 'student_10';

        $cipher1 = $this->encryptor->encrypt($plain, $aad);
        $cipher2 = $this->encryptor->encrypt($plain, $aad);

        $this->assertNotSame($cipher1, $cipher2, 'Dua enkripsi teks sama harus menghasilkan ciphertext berbeda karena nonce acak.');
    }

    public function test_modifying_ciphertext_throws_decryption_failed_exception(): void
    {
        $plain = 'Istirahat 3 hari';
        $aad = 'student_10';

        $cipher = $this->encryptor->encrypt($plain, $aad);
        $parts = explode(':', $cipher);

        // Manipulasi 1 byte pada ciphertext
        $rawCt = base64_decode($parts[2]);
        $rawCt[0] = chr(ord($rawCt[0]) ^ 0x01);
        $parts[2] = base64_encode($rawCt);
        $tamperedCipher = implode(':', $parts);

        $this->expectException(DecryptionFailedException::class);
        $this->encryptor->decrypt($tamperedCipher, $aad);
    }

    public function test_aad_mismatch_throws_decryption_failed_exception(): void
    {
        $plain = 'Surat keterangan sakit';

        // Enkripsi terikat ke student_1
        $cipher = $this->encryptor->encrypt($plain, 'student_1');

        // Dekripsi menggunakan student_2 (percobaan transfer data)
        $this->expectException(DecryptionFailedException::class);
        $this->encryptor->decrypt($cipher, 'student_2');
    }
}
