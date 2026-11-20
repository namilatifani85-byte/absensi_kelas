<?php

namespace App\Http\Controllers\Vulnerable;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Controller Demo Kerentanan Terisolasi (ADR-005).
 * DIBATASI HANYA UNTUK UJI KOMPARATIF PADA LOCALHOST:8001.
 *
 * Mengandung 3 Celah Sengaja:
 * 1. SEC-08 SQL Injection: Konkatenasi string mentah tanpa parameter binding pada searchClass().
 * 2. SEC-09 Stored XSS: Output unescaped {!! $notes !!} tanpa header CSP pada showAttendance().
 * 3. SEC-10 IDOR: Pengambilan rekaman absensi tanpa evaluasi Policy / otorisasi kepemilikan.
 */
class VulnerableDemoController extends Controller
{
    /**
     * 1. Celah SQL Injection (SEC-08 Rentan):
     * Menggunakan interpolasi string mentah langsung ke kueri SQL.
     * Payload: ' OR '1'='1 -- akan membocorkan seluruh isi tabel classes.
     */
    public function searchClass(Request $request): View
    {
        $search = $request->query('search', '');

        if ($search !== '') {
            // VULNERABLE: String concatenation rentan SQL Injection
            $sql = "SELECT * FROM classes WHERE name LIKE '%" . $search . "%' OR code LIKE '%" . $search . "%'";
            try {
                $classes = DB::select($sql);
            } catch (\Throwable $e) {
                $classes = [];
            }
        } else {
            $classes = DB::select("SELECT * FROM classes WHERE is_active = 1");
        }

        return view('vulnerable.search', [
            'classes' => $classes,
            'search' => $search,
            'rawSql' => isset($sql) ? $sql : "SELECT * FROM classes WHERE is_active = 1",
        ]);
    }

    /**
     * 2. Celah IDOR (SEC-10 Rentan) & Stored XSS (SEC-09 Rentan):
     * Mengambil data presensi apa pun berdasarkan ID tanpa verifikasi kepemilikan (Tanpa Policy Otorisasi).
     */
    public function showAttendance($id): View
    {
        // VULNERABLE IDOR: Tidak mengevaluasi otorisasi kepemilikan mahasiswa.
        // Mahasiswa manapun dapat mengakses ID absensi mahasiswa lain.
        $attendance = Attendance::with(['classModel.lecturer', 'student.user', 'notes.author'])->findOrFail($id);

        return view('vulnerable.show', compact('attendance'));
    }

    /**
     * 3. Penyimpanan Catatan Teks Bebas untuk Demo Stored XSS.
     */
    public function storeNote(Request $request, $id): RedirectResponse
    {
        $attendance = Attendance::findOrFail($id);

        // VULNERABLE: Menyimpan input teks mentah tanpa sanitasi
        AttendanceNote::create([
            'attendance_id' => $attendance->id,
            'author_id' => Auth::id() ?? 1,
            'notes' => $request->input('notes', ''),
        ]);

        return back()->with('success', 'Catatan berhasil disimpan pada mode rentan.');
    }
}
