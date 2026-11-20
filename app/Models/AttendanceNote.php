<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceNote extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceNoteFactory> */
    use HasFactory;

    protected $table = 'attendance_notes';

    /**
     * SEC-09: Kolom notes adalah teks bebas. Di Blade versi aman wajib {{ $note->notes }}.
     */
    protected $fillable = [
        'attendance_id',
        'author_id',
        'notes',
    ];

    // ─── Relasi Eloquent ────────────────────────────────────────────────────────

    /**
     * Presensi yang dicatat.
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * Penulis catatan (User: dosen atau mahasiswa).
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
