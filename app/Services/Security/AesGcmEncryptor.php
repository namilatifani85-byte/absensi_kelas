<?php

namespace App\Services\Security;

use App\Exceptions\DecryptionFailedException;
use InvalidArgumentException;

/**
 * Layanan Kriptografi Kustom AEAD AES-256-GCM (SEC-03).
 * Sesuai ADR-004 dan Ketentuan Dokumen Tugas UTS Keamanan Sistem Komputer.
 *
 * Spesifikasi:
 * - Cipher: aes-256-gcm
 * - Kunci: 256-bit (32 bytes) diambil dari env `APP_SICK_NOTE_KEY`
 * - Nonce: 96-bit (12 bytes) acak via random_bytes(12) per enkripsi
 * - Authentication Tag: 128-bit (16 bytes) diverifikasi saat dekripsi
 * - AAD: Additional Authenticated Data (diikat ke student_id untuk anti-tampering)
 * - Format Amplop: v1:<nonce_base64>:<ciphertext_base64>:<tag_base64>
 */
class AesGcmEncryptor
{
    private const CIPHER = 'aes-256-gcm';
    private const NONCE_LENGTH = 12; // 96 bits sesuai NIST SP 800-38D
    private const TAG_LENGTH = 16;   // 128 bits authentication tag
    private const VERSION_PREFIX = 'v1';

    private string $key;

    /**
     * @param string|null $key Kunci biner 32-byte atau base64:key
     */
    public function __construct(?string $key = null)
    {
        $rawKey = $key ?? config('app.sick_note_key') ?? env('APP_SICK_NOTE_KEY');

        if (empty($rawKey)) {
            throw new InvalidArgumentException(
                'Kunci enkripsi APP_SICK_NOTE_KEY belum disetel pada lingkungan (.env).'
            );
        }

        // Jika kunci diawali "base64:", lakukan decode
        if (str_starts_with($rawKey, 'base64:')) {
            $rawKey = base64_decode(substr($rawKey, 7), true);
        }

        if ($rawKey === false || strlen($rawKey) !== 32) {
            throw new InvalidArgumentException(
                sprintf('Kunci enkripsi AES-256-GCM wajib tepat 32 byte (256 bit). Terbaca: %d byte.', strlen((string)$rawKey))
            );
        }

        $this->key = $rawKey;
    }

    /**
     * Enkripsi plaintext menggunakan AEAD AES-256-GCM.
     *
     * @param string $plaintext Data sensitif (catatan medis / sakit)
     * @param string $aad Additional Authenticated Data (misal: 'student_' . $studentId)
     * @return string Format amplop terversi: v1:<nonce>:<ciphertext>:<tag>
     */
    public function encrypt(string $plaintext, string $aad = ''): string
    {
        $nonce = random_bytes(self::NONCE_LENGTH);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $this->key,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            $aad,
            self::TAG_LENGTH
        );

        if ($ciphertext === false) {
            throw new DecryptionFailedException('Gagal mengenkripsi data dengan OpenSSL AES-256-GCM.');
        }

        return sprintf(
            '%s:%s:%s:%s',
            self::VERSION_PREFIX,
            base64_encode($nonce),
            base64_encode($ciphertext),
            base64_encode($tag)
        );
    }

    /**
     * Dekripsi amplop payload dan verifikasi integritas otentikasi tag.
     *
     * @param string $payload Format v1:<nonce>:<ciphertext>:<tag>
     * @param string $aad Additional Authenticated Data yang sama saat dienkripsi
     * @return string Plaintext asli
     * @throws DecryptionFailedException Jika tag rusak, AAD beda, atau format salah
     */
    public function decrypt(string $payload, string $aad = ''): string
    {
        $parts = explode(':', $payload);

        if (count($parts) !== 4 || $parts[0] !== self::VERSION_PREFIX) {
            throw new DecryptionFailedException(
                'Format amplop ciphertext tidak valid. Format yang didukung: v1:<nonce>:<ct>:<tag>.'
            );
        }

        $nonce = base64_decode($parts[1], true);
        $ciphertext = base64_decode($parts[2], true);
        $tag = base64_decode($parts[3], true);

        if ($nonce === false || strlen($nonce) !== self::NONCE_LENGTH) {
            throw new DecryptionFailedException('Nonce AES-256-GCM tidak valid (wajib 12 byte).');
        }

        if ($tag === false || strlen($tag) !== self::TAG_LENGTH) {
            throw new DecryptionFailedException('Authentication Tag AES-256-GCM tidak valid (wajib 16 byte).');
        }

        if ($ciphertext === false) {
            throw new DecryptionFailedException('Ciphertext tidak dapat di-decode.');
        }

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::CIPHER,
            $this->key,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            $aad
        );

        if ($plaintext === false) {
            throw new DecryptionFailedException(
                'Autentikasi tag AES-256-GCM gagal! Data telah dimanipulasi atau AAD tidak sesuai.'
            );
        }

        return $plaintext;
    }

    /**
     * Helper untuk membuat kunci 256-bit baru terformat base64.
     */
    public static function generateKey(): string
    {
        return 'base64:' . base64_encode(random_bytes(32));
    }
}
