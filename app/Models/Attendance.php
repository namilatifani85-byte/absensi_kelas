<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceFactory> */
    use HasFactory;

    protected $fillable = [
        'class_id',
        'student_id',
        'attendance_date',
        'status',
        'excuse_notes',
        'medical_note_encrypted',
        'checked_in_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'checked_in_at'   => 'datetime',
        ];
    }

    // ─── Relasi Eloquent ────────────────────────────────────────────────────────

    /**
     * Kelas yang dihadiri.
     */
    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Mahasiswa pemilik presensi.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Dosen / admin yang memverifikasi kehadiran.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Catatan tambahan pada presensi ini (Stored XSS testing ground).
     */
    public function notes(): HasMany
    {
        return $this->hasMany(AttendanceNote::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeSick($query)
    {
        return $query->where('status', 'sick');
    }

    public function scopePermit($query)
    {
        return $query->where('status', 'permit');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }
}
