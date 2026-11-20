<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Dilempar saat proses dekripsi AEAD AES-256-GCM gagal karena tag autentikasi
 * tidak cocok, ciphertext rusak/dimanipulasi, atau Additional Authenticated Data (AAD) tidak sesuai.
 */
class DecryptionFailedException extends RuntimeException
{
    //
}
