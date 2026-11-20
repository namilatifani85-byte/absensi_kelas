@extends('layouts.guest')

@section('title', 'Masuk ke Akun')

@section('content')
<form action="{{ route('login') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
    @csrf

    <div style="font-size: 1.25rem; font-weight: 700; color: #0F172A; text-align: center; margin-bottom: 0.5rem;">
        Masuk ke Sistem
    </div>

    {{-- Field Username / Email --}}
    <div>
        <label for="username" style="display: block; font-size: 0.875rem; font-weight: 600; color: #0F172A; margin-bottom: 0.375rem;">
            Username atau Email
        </label>
        <input type="text"
               id="username"
               name="username"
               value="{{ old('username') }}"
               required
               autofocus
               autocomplete="username"
               placeholder="Contoh: dosen_fani atau mhs_budi"
               class="focus-accessible"
               style="width: 100%; box-sizing: border-box; padding: 0.625rem 0.875rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem; color: #0F172A; background-color: #FFFFFF;">
        @error('username')
            <span style="display: block; font-size: 0.75rem; color: #DC2626; margin-top: 0.25rem;">{{ $message }}</span>
        @enderror
    </div>

    {{-- Field Password --}}
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.375rem;">
            <label for="password" style="font-size: 0.875rem; font-weight: 600; color: #0F172A;">
                Kata Sandi
            </label>
        </div>
        <input type="password"
               id="password"
               name="password"
               required
               autocomplete="current-password"
               placeholder="••••••••"
               class="focus-accessible"
               style="width: 100%; box-sizing: border-box; padding: 0.625rem 0.875rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem; color: #0F172A; background-color: #FFFFFF;">
        @error('password')
            <span style="display: block; font-size: 0.75rem; color: #DC2626; margin-top: 0.25rem;">{{ $message }}</span>
        @enderror
    </div>

    {{-- Remember Me Checkbox --}}
    <div style="display: flex; align-items: center; gap: 0.5rem;">
        <input type="checkbox"
               id="remember"
               name="remember"
               value="1"
               style="width: 1rem; height: 1rem; border-radius: 0.25rem; border: 1px solid #CBD5E1; cursor: pointer;">
        <label for="remember" style="font-size: 0.875rem; color: #475569; cursor: pointer;">
            Ingat saya di perangkat ini
        </label>
    </div>

    {{-- Tombol Submit --}}
    <button type="submit"
            class="focus-accessible"
            style="width: 100%; padding: 0.75rem 1rem; border: none; border-radius: 0.5rem; background-color: #2563EB; color: #FFFFFF; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
        <x-heroicon-o-lock-closed style="width: 1.125rem; height: 1.125rem;" />
        <span>Masuk ke AbsensiKita</span>
    </button>

    {{-- Link Pendaftaran --}}
    <div style="text-align: center; margin-top: 0.5rem; font-size: 0.875rem; color: #475569;">
        Belum memiliki akun?
        <a href="{{ route('register') }}" style="color: #2563EB; text-decoration: none; font-weight: 600;">
            Daftar di sini
        </a>
    </div>

    {{-- Info Kredensial Uji (Sesuai Aturan Dosen) --}}
    <div style="margin-top: 0.5rem; padding: 0.75rem; background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 0.5rem; font-size: 0.75rem; color: #64748B;">
        <strong style="color: #334155;">Akun Uji Fiktif (Seeder):</strong><br>
        &bull; Dosen: <code>dosen_fani</code> / <code>HadirAman#01!</code><br>
        &bull; Mahasiswa: <code>mhs_budi</code> / <code>HadirAman#01!</code>
    </div>
</form>
@endsection
