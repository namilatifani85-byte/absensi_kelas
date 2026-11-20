@extends('layouts.app')

@section('title', '403 - Akses Ditolak (Forbidden)')

@section('content')
<div style="max-width: 36rem; margin: 3rem auto; text-align: center; background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 1rem; padding: 2.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
    <div style="display: inline-flex; align-items: center; justify-content: center; width: 4rem; height: 4rem; background-color: #FEE2E2; border-radius: 9999px; color: #DC2626; margin-bottom: 1.25rem;">
        <x-heroicon-o-shield-exclamation style="width: 2.5rem; height: 2.5rem;" />
    </div>

    <h1 style="font-size: 1.875rem; font-weight: 800; color: #0F172A; margin: 0 0 0.5rem 0;">403 Forbidden</h1>
    <h2 style="font-size: 1.125rem; font-weight: 600; color: #DC2626; margin: 0 0 1rem 0;">Akses Ditolak: Perlindungan Anti-IDOR Aktif</h2>

    <p style="font-size: 0.9375rem; color: #475569; line-height: 1.6; margin: 0 0 1.5rem 0;">
        {{ $exception->getMessage() ?: 'Anda tidak memiliki hak otorisasi untuk mengakses atau melihat rekaman presensi mahasiswa lain.' }}
    </p>

    <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 0.5rem; padding: 0.875rem; text-align: left; font-size: 0.8125rem; color: #64748B; margin-bottom: 1.5rem;">
        🛡️ <strong>Keamanan Syarat 10 (SEC-10 Safe):</strong> Sistem menerapkan kebijakan otorisasi server-side (Policy) ketat untuk mencegah manipulasi parameter ID pada URL (<em>Insecure Direct Object Reference</em>).
    </div>

    <div style="display: flex; justify-content: center; gap: 0.75rem;">
        <a href="{{ route('my-attendance.index') }}"
           class="focus-accessible"
           style="display: inline-flex; align-items: center; gap: 0.375rem; background-color: #2563EB; color: #FFFFFF; padding: 0.625rem 1.25rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600;">
            <x-heroicon-o-arrow-left style="width: 1rem; height: 1rem;" /> Kembali ke Riwayat Saya
        </a>
        <a href="{{ route('dashboard') }}"
           class="focus-accessible"
           style="display: inline-flex; align-items: center; gap: 0.375rem; background-color: #F1F5F9; color: #334155; padding: 0.625rem 1.25rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600; border: 1px solid #CBD5E1;">
            Dashboard
        </a>
    </div>
</div>
@endsection
