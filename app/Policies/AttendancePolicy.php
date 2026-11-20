<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

/**
 * Policy Otorisasi Presensi (SEC-10 Anti-IDOR).
 * Mencegah kebocoran data presensi antar mahasiswa pada endpoint /attendances/{id}.
 */
class AttendancePolicy
{
    /**
     * Menentukan apakah pengguna berhak melihat detail presensi tertentu.
     *
     * Aturan Otorisasi Server-Side:
     * 1. Pengguna dengan peran Dosen ('lecturer') diizinkan melihat presensi mahasiswa.
     * 2. Pengguna dengan peran Mahasiswa ('student') HANYA boleh melihat rekaman miliknya sendiri
     *    (student.user_id === user.id). Akses record milik mahasiswa lain DITOLAK (HTTP 403).
     */
    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->role === 'lecturer') {
            return true;
        }

        return $attendance->student !== null && (int) $attendance->student->user_id === (int) $user->id;
    }
}
