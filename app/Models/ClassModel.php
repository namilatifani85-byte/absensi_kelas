<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends Model
{
    /** @use HasFactory<\Database\Factories\ClassModelFactory> */
    use HasFactory;

    /**
     * Nama tabel eksplisit (karena 'class' adalah reserved keyword di PHP).
     *
     * @var string
     */
    protected $table = 'classes';

    protected $fillable = [
        'code',
        'name',
        'lecturer_id',
        'room',
        'day',
        'time_start',
        'time_end',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relasi Eloquent ────────────────────────────────────────────────────────

    /**
     * Dosen pengampu kelas.
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Seluruh data kehadiran pada kelas ini.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }
}
