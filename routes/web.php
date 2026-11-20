<?php

use App\Http\Controllers\AttendanceCheckinController;
use App\Http\Controllers\AttendanceNoteController;
use App\Http\Controllers\AttendanceVerificationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\MyAttendanceController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - AbsensiKita
|--------------------------------------------------------------------------
| Seluruh rute dilindungi oleh middleware sesi dan CSRF bawaan Laravel.
*/

// Halaman Utama / Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rute Tamu (Hanya dapat diakses jika belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rute Terproteksi (Wajib Login via Session Guard)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Kelas (Daftar & Detail untuk semua user; CRUD hanya untuk Dosen)
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::middleware('role:lecturer')->group(function () {
        Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
        Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
        Route::get('/classes/{class}/edit', [ClassController::class, 'edit'])->name('classes.edit');
        Route::put('/classes/{class}', [ClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');
    });
    Route::get('/classes/{class}', [ClassController::class, 'show'])->name('classes.show');

    // Presensi Kehadiran Harian Mahasiswa (TKT-008-WEB)
    Route::get('/classes/{class}/checkin', [AttendanceCheckinController::class, 'showCheckinForm'])->name('attendances.checkin');
    Route::post('/classes/{class}/checkin', [AttendanceCheckinController::class, 'storeCheckin'])->name('attendances.storeCheckin');

    // Verifikasi Kehadiran oleh Dosen (TKT-008-WEB)
    Route::put('/attendances/{attendance}/verify', [AttendanceVerificationController::class, 'update'])->name('attendances.verify');

    // Riwayat Presensi Pribadi Mahasiswa & Detail Anti-IDOR (TKT-009-WEB)
    Route::get('/my-attendance', [MyAttendanceController::class, 'index'])->name('my-attendance.index');
    Route::get('/attendances/{attendance}', [MyAttendanceController::class, 'show'])->name('attendances.show');

    // Catatan Tambahan Keterangan Presensi (TKT-010-WEB SEC-09 Safe)
    Route::post('/attendances/{attendance}/notes', [AttendanceNoteController::class, 'store'])->name('attendances.notes.store');

    // Mahasiswa (Daftar & Detail)
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
});

// Modul Demo Kerentanan Terisolasi (TKT-011-WEB, ADR-005)
if (file_exists(__DIR__ . '/web_vulnerable.php')) {
    require __DIR__ . '/web_vulnerable.php';
}
