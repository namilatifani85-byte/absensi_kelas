<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Controller Autentikasi Manual Murni (ADR-003, SEC-02).
 * Tanpa starter kit Breeze/Fortify/Jetstream.
 */
class AuthController extends Controller
{
    /**
     * Menampilkan form login.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Memproses autentikasi login pengguna.
     * Menerapkan session()->regenerate() guna mencegah Session Fixation.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $input = $request->validated();
        $usernameOrEmail = $input['username'];
        $password = $input['password'];
        $remember = $request->boolean('remember');

        // Mendukung login menggunakan username maupun email
        $loginField = filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $usernameOrEmail,
            'password'  => $password,
        ];

        if (Auth::attempt($credentials, $remember)) {
            // Mencegah Session Fixation (ADR-003 / SEC-10)
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        return back()
            ->withErrors(['username' => 'Kredensial login tidak cocok dengan catatan kami.'])
            ->onlyInput('username');
    }

    /**
     * Menampilkan form registrasi pengguna baru.
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Memproses pendaftaran akun baru.
     * SEC-02: Hash Bcrypt otomatis via Hash::make(), batas 72 karakter.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'username' => strtolower($data['username']),
                'email'    => strtolower($data['email']),
                'password' => Hash::make($data['password']),
                'role'     => $data['role'],
            ]);

            // Jika role adalah student, otomatis buat entri profil Student
            if ($data['role'] === 'student') {
                Student::create([
                    'user_id'       => $user->id,
                    'nim'           => $data['nim'] ?? ('MHS-' . $user->id),
                    'full_name'     => $data['name'],
                    'study_program' => 'Teknik Informatika',
                    'class_year'    => (string) date('Y'),
                ]);
            }

            return $user;
        });

        // Login otomatis setelah registrasi berhasil
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Akun berhasil didaftarkan! Selamat datang di AbsensiKita.');
    }

    /**
     * Memproses logout pengguna.
     * Menghancurkan sesi dan meregenerasi token CSRF (ADR-003 / SEC-10).
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
