<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi registrasi akun baru.
     * SEC-02 & ADR-009: Batas maksimal password tepat 72 karakter (mencegah silent truncation Bcrypt).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email'    => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'role'     => ['required', 'in:student,lecturer'],
            'nim'      => ['nullable', 'required_if:role,student', 'string', 'max:20', 'unique:students,nim'],
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
        ];
    }

    /**
     * Pesan kustom validasi berbahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'name.required'        => 'Nama lengkap wajib diisi.',
            'username.required'    => 'Username wajib diisi.',
            'username.unique'      => 'Username ini sudah digunakan.',
            'username.alpha_dash'  => 'Username hanya boleh memuat huruf, angka, tanda minus, dan garis bawah.',
            'email.required'       => 'Alamat email wajib diisi.',
            'email.email'          => 'Format alamat email tidak valid.',
            'email.unique'         => 'Alamat email ini sudah terdaftar.',
            'role.required'        => 'Peran akun wajib dipilih.',
            'role.in'              => 'Peran harus berupa Mahasiswa atau Dosen.',
            'nim.required_if'      => 'NIM wajib diisi bagi akun mahasiswa.',
            'nim.unique'           => 'NIM ini sudah terdaftar.',
            'password.required'    => 'Kata sandi wajib diisi.',
            'password.min'         => 'Kata sandi minimal 8 karakter.',
            'password.max'         => 'Kata sandi tidak boleh lebih dari 72 karakter (batas komputasi Bcrypt).',
            'password.confirmed'   => 'Konfirmasi kata sandi tidak cocok.',
        ];
    }
}
