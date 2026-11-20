<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Services\Security\AesGcmEncryptor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Controller Riwayat Presensi Pribadi Mahasiswa & Detail Terproteksi Policy (SEC-10).
 */
class MyAttendanceController extends Controller
{
    /**
     * Menampilkan daftar riwayat kehadiran milik mahasiswa yang sedang login.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Jika Dosen mengakses /my-attendance, alihkan ke daftar kelas
        if ($user->role === 'lecturer') {
            return view('attendances.my', [
                'attendances' => collect(),
                'student' => null,
                'isLecturer' => true,
            ]);
        }

        $student = $user->student;
        if (! $student) {
            abort(403, 'Akses riwayat presensi pribadi hanya untuk profil mahasiswa terdaftar.');
        }

        $attendances = Attendance::with(['classModel.lecturer'])
            ->where('student_id', $student->id)
            ->orderBy('attendance_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('attendances.my', [
            'attendances' => $attendances,
            'student' => $student,
            'isLecturer' => false,
        ]);
    }

    /**
     * Menampilkan detail presensi terproteksi otorisasi server-side Policy (Anti-IDOR).
     */
    public function show(Attendance $attendance): View
    {
        // SEC-10: Evaluasi Policy. Menolak akses jika bukan pemilik sah atau dosen pengampu (HTTP 403)
        Gate::authorize('view', $attendance);

        $attendance->load(['classModel.lecturer', 'student.user', 'verifier', 'notes.author']);

        // SEC-03: Dekripsi catatan medis terautentikasi (AAD = student_id)
        $decryptedMedical = null;
        if (! empty($attendance->medical_note_encrypted)) {
            try {
                /** @var AesGcmEncryptor $encryptor */
                $encryptor = app(AesGcmEncryptor::class);
                $decryptedMedical = $encryptor->decrypt(
                    $attendance->medical_note_encrypted,
                    (string) $attendance->student_id
                );
            } catch (\Throwable $e) {
                $decryptedMedical = '[Data terenkripsi tidak dapat didekripsi / tag integritas tidak valid]';
            }
        }

        return view('attendances.show', compact('attendance', 'decryptedMedical'));
    }
}
