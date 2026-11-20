<?php

use App\Http\Controllers\Vulnerable\VulnerableDemoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Vulnerable Demo Routes - AbsensiKita (ADR-005)
|--------------------------------------------------------------------------
| HANYA DIAKSES PADA LOCALHOST:8001 UNTUK KEBUTUHAN PENGUJIAN KOMPARATIF LAB
| DILARANG DIBUKA DI JARINGAN PUBLIK ATAU FIREWALL PRODUKSI
*/

Route::prefix('vulnerable')->name('vulnerable.')->group(function () {
    // SEC-08: Demo Celah SQL Injection pada Pencarian Kelas
    Route::get('/classes/search', [VulnerableDemoController::class, 'searchClass'])->name('classes.search');

    // SEC-10: Demo Celah IDOR pada Detail Presensi Mahasiswa
    // SEC-09: Demo Celah Stored XSS pada Output Catatan Unescaped
    Route::get('/attendances/{id}', [VulnerableDemoController::class, 'showAttendance'])->name('attendances.show');
    Route::post('/attendances/{id}/notes', [VulnerableDemoController::class, 'storeNote'])->name('attendances.notes.store');
});
