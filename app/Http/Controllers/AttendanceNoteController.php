<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceNoteRequest;
use App\Models\Attendance;
use App\Models\AttendanceNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Controller Catatan Bebas Keterangan Presensi (SEC-09 Safe).
 * Menampung input teks bebas yang diuji terhadap serangan Stored XSS.
 */
class AttendanceNoteController extends Controller
{
    /**
     * Menyimpan catatan teks bebas tambahan pada presensi.
     */
    public function store(StoreAttendanceNoteRequest $request, Attendance $attendance): RedirectResponse
    {
        // SEC-10: Evaluasi Policy kepemilikan / hak akses dosen
        Gate::authorize('view', $attendance);

        AttendanceNote::create([
            'attendance_id' => $attendance->id,
            'author_id' => Auth::id(),
            'notes' => $request->input('notes'),
        ]);

        return back()->with('success', 'Catatan keterangan berhasil ditambahkan!');
    }
}
