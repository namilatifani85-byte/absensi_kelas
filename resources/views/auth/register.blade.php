@extends('layouts.guest')

@section('title', 'Daftar Akun Baru')

@section('content')
<form action="{{ route('register') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.125rem;">
    @csrf

    <div style="font-size: 1.25rem; font-weight: 700; color: #0F172A; text-align: center; margin-bottom: 0.25rem;">
        Pendaftaran Akun
    </div>

    {{-- Nama Lengkap --}}
    <div>
        <label for="name" style="display: block; font-size: 0.875rem; font-weight: 600; color: #0F172A; margin-bottom: 0.25rem;">
            Nama Lengkap
        </label>
        <input type="text"
               id="name"
               name="name"
               value="{{ old('name') }}"
               required
               placeholder="Contoh: Muhammad Ali"
               class="focus-accessible"
               style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem; color: #0F172A;">
        @error('name')
            <span style="display: block; font-size: 0.75rem; color: #DC2626; margin-top: 0.25rem;">{{ $message }}</span>
        @enderror
    </div>

    {{-- Username --}}
    <div>
        <label for="username" style="display: block; font-size: 0.875rem; font-weight: 600; color: #0F172A; margin-bottom: 0.25rem;">
            Username
        </label>
        <input type="text"
               id="username"
               name="username"
               value="{{ old('username') }}"
               required
               placeholder="Contoh: ali_2023"
               class="focus-accessible"
               style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem; color: #0F172A;">
        @error('username')
            <span style="display: block; font-size: 0.75rem; color: #DC2626; margin-top: 0.25rem;">{{ $message }}</span>
        @enderror
    </div>

    {{-- Email --}}
    <div>
        <label for="email" style="display: block; font-size: 0.875rem; font-weight: 600; color: #0F172A; margin-bottom: 0.25rem;">
            Alamat Email
        </label>
        <input type="email"
               id="email"
               name="email"
               value="{{ old('email') }}"
               required
               placeholder="Contoh: ali@mhs.unimal.ac.id"
               class="focus-accessible"
               style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem; color: #0F172A;">
        @error('email')
            <span style="display: block; font-size: 0.75rem; color: #DC2626; margin-top: 0.25rem;">{{ $message }}</span>
        @enderror
    </div>

    {{-- Peran Akun --}}
    <div>
        <label for="role" style="display: block; font-size: 0.875rem; font-weight: 600; color: #0F172A; margin-bottom: 0.25rem;">
            Peran Akun
        </label>
        <select id="role"
                name="role"
                required
                class="focus-accessible"
                style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem; color: #0F172A; background-color: #FFFFFF;">
            <option value="student" {{ old('role', 'student') === 'student' ? 'selected' : '' }}>Mahasiswa</option>
            <option value="lecturer" {{ old('role') === 'lecturer' ? 'selected' : '' }}>Dosen Pengampu</option>
        </select>
        @error('role')
            <span style="display: block; font-size: 0.75rem; color: #DC2626; margin-top: 0.25rem;">{{ $message }}</span>
        @enderror
    </div>

    {{-- NIM (Wajib untuk mahasiswa) --}}
    <div id="nim_wrapper">
        <label for="nim" style="display: block; font-size: 0.875rem; font-weight: 600; color: #0F172A; margin-bottom: 0.25rem;">
            NIM (Nomor Induk Mahasiswa)
        </label>
        <input type="text"
               id="nim"
               name="nim"
               value="{{ old('nim') }}"
               placeholder="Contoh: 230170095"
               class="focus-accessible"
               style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem; color: #0F172A;">
        @error('nim')
            <span style="display: block; font-size: 0.75rem; color: #DC2626; margin-top: 0.25rem;">{{ $message }}</span>
        @enderror
    </div>

    {{-- Kata Sandi (SEC-02: min 8, max 72) --}}
    <div>
        <label for="password" style="display: block; font-size: 0.875rem; font-weight: 600; color: #0F172A; margin-bottom: 0.25rem;">
            Kata Sandi <span style="font-weight: 400; font-size: 0.75rem; color: #64748B;">(8 &ndash; 72 karakter)</span>
        </label>
        <input type="password"
               id="password"
               name="password"
               required
               maxlength="72"
               placeholder="Minimal 8 karakter (Bcrypt safe)"
               class="focus-accessible"
               style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem; color: #0F172A;">
        @error('password')
            <span style="display: block; font-size: 0.75rem; color: #DC2626; margin-top: 0.25rem;">{{ $message }}</span>
        @enderror
    </div>

    {{-- Konfirmasi Kata Sandi --}}
    <div>
        <label for="password_confirmation" style="display: block; font-size: 0.875rem; font-weight: 600; color: #0F172A; margin-bottom: 0.25rem;">
            Konfirmasi Kata Sandi
        </label>
        <input type="password"
               id="password_confirmation"
               name="password_confirmation"
               required
               maxlength="72"
               placeholder="Ketik ulang kata sandi"
               class="focus-accessible"
               style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem; color: #0F172A;">
    </div>

    {{-- Tombol Submit --}}
    <button type="submit"
            class="focus-accessible"
            style="width: 100%; padding: 0.75rem 1rem; border: none; border-radius: 0.5rem; background-color: #2563EB; color: #FFFFFF; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-top: 0.5rem;">
        <x-heroicon-o-user-plus style="width: 1.125rem; height: 1.125rem;" />
        <span>Daftar Akun AbsensiKita</span>
    </button>

    {{-- Link Login --}}
    <div style="text-align: center; margin-top: 0.5rem; font-size: 0.875rem; color: #475569;">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" style="color: #2563EB; text-decoration: none; font-weight: 600;">
            Masuk di sini
        </a>
    </div>
</form>
@endsection
