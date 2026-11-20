<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attendance\CheckinRequest;
use App\Models\Attendance;
use App\Models\ClassModel;
use App\Services\Security\AesGcmEncryptor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

/**
 * Controller Presensi Kehadiran Harian Mahasiswa.
 * Mengimplementasikan transaksi atomik DB::transaction() dan lockForUpdate()
 * untuk mencegah kondisi balapan (race condition) dan presensi ganda (TKT-008-WEB).
 */
class AttendanceCheckinController extends Controller
{
    /**
     * Menampilkan form presensi harian untuk mahasiswa.
     */
    public function showCheckinForm(ClassModel $class): View|RedirectResponse
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            abort(403, 'Akses khusus mahasiswa. Profil mahasiswa tidak terdaftar untuk akun ini.');
        }

        if (! $class->is_active) {
            return redirect()->route('classes.show', $class)
                ->withErrors(['status' => 'Kelas ini sedang tidak aktif. Presensi tidak dapat dilakukan.']);
        }

        $today = now()->toDateString();
        $existing = Attendance::where('class_id', $class->id)
            ->where('student_id', $student->id)
            ->where('attendance_date', $today)
            ->first();

        return view('attendances.checkin', compact('class', 'student', 'existing', 'today'));
    }

    /**
     * Memproses penyimpanan data presensi harian secara atomik.
     */
    public function storeCheckin(CheckinRequest $request, ClassModel $class): RedirectResponse
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            abort(403, 'Akses khusus mahasiswa. Profil mahasiswa tidak terdaftar.');
        }

        if (! $class->is_active) {
            return back()->withErrors(['status' => 'Kelas tidak aktif. Presensi dibatalkan.']);
        }

        $today = now()->toDateString();

        return DB::transaction(function () use ($request, $class, $student, $today) {
            // Kunci baris presensi untuk mencegah kondisi balapan submit bersamaan
            $existing = Attendance::where('class_id', $class->id)
                ->where('student_id', $student->id)
                ->where('attendance_date', $today)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return back()->withErrors(['status' => 'Anda sudah melakukan presensi hari ini.']);
            }

            // Enkripsi catatan medis via AES-256-GCM kustom (SEC-03) jika diisi
            $encryptedMedical = null;
            if ($request->filled('medical_note')) {
                /** @var AesGcmEncryptor $encryptor */
                $encryptor = app(AesGcmEncryptor::class);
                $encryptedMedical = $encryptor->encrypt(
                    $request->input('medical_note'),
                    (string) $student->id // AAD diikat ke student_id
                );
            }

            Attendance::create([
                'class_id' => $class->id,
                'student_id' => $student->id,
                'attendance_date' => $today,
                'status' => $request->input('status'),
                'excuse_notes' => $request->input('excuse_notes'),
                'medical_note_encrypted' => $encryptedMedical,
                'checked_in_at' => now(),
            ]);

            if (Route::has('my-attendance.index')) {
                return redirect()->route('my-attendance.index')
                    ->with('success', 'Presensi berhasil dicatat!');
            }

            return redirect()->route('classes.show', $class)
                ->with('success', 'Presensi berhasil dicatat!');
        });
    }
}
