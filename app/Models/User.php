<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atribut yang boleh diisi massal.
     * SEC-02: Password Bcrypt salt unik, batas 72 byte.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting atribut bawaan.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Relasi Eloquent ────────────────────────────────────────────────────────

    /**
     * Profil mahasiswa (jika role = student).
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Kelas-kelas yang diampu oleh dosen ini (jika role = lecturer).
     */
    public function lecturerClasses(): HasMany
    {
        return $this->hasMany(ClassModel::class, 'lecturer_id');
    }

    /**
     * Catatan kehadiran yang ditulis oleh user ini.
     */
    public function attendanceNotes(): HasMany
    {
        return $this->hasMany(AttendanceNote::class, 'author_id');
    }

    // ─── Role Helpers ──────────────────────────────────────────────────────────

    public function isLecturer(): bool
    {
        return $this->role === 'lecturer';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
}
