<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nim',
        'full_name',
        'study_program',
        'class_year',
    ];

    // ─── Relasi Eloquent ────────────────────────────────────────────────────────

    /**
     * Akun user login mahasiswa.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Riwayat presensi kehadiran mahasiswa ini.
     * SEC-10: Anti-IDOR policy membatasi akses hanya untuk pemilik atau dosen pengampu.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
