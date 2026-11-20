<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller Verifikasi Presensi Mahasiswa oleh Dosen.
 * Operasi pembaruan status bersifat idempoten: memanggil aksi berulang kali
 * dengan parameter yang sama menghasilkan state akhir yang identik (TKT-008-WEB).
 */
class AttendanceVerificationController extends Controller
{
    /**
     * Memperbarui status kehadiran mahasiswa secara idempoten.
     */
    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        if (Auth::user()->role !== 'lecturer') {
            abort(403, 'Hanya dosen pengampu yang berhak memverifikasi presensi.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:present,sick,permit,absent'],
        ], [
            'status.required' => 'Status kehadiran wajib dipilih.',
            'status.in' => 'Status kehadiran tidak valid.',
        ]);

        // Pembaruan idempoten
        $attendance->status = $validated['status'];
        $attendance->verified_by = Auth::id();
        $attendance->save();

        return back()->with('success', 'Status presensi mahasiswa berhasil diperbarui!');
    }
}
