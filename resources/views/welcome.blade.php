@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div style="max-width: 48rem; margin: 2rem auto; text-align: center;">

    <div style="display: inline-flex; align-items: center; justify-content: center; width: 4.5rem; height: 4.5rem; background-color: #EBF3FC; border-radius: 1.25rem; margin-bottom: 1.5rem;">
        <x-heroicon-o-academic-cap style="width: 2.75rem; height: 2.75rem; color: #2563EB;" />
    </div>

    <h1 style="font-size: 2.25rem; font-weight: 800; color: #0F172A; margin: 0 0 1rem 0;">
        Selamat Datang di <span style="color: #2563EB;">AbsensiKita</span>
    </h1>

    <p style="font-size: 1.125rem; color: #475569; margin: 0 auto 2rem auto; line-height: 1.6; max-width: 36rem;">
        Sistem Informasi Presensi Perkuliahan Mandiri yang Dirancang dengan Prinsip Keamanan Sistem Komputer Modern.
    </p>

    <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 3rem;">
        @auth
            <a href="{{ url('/dashboard') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background-color: #2563EB; color: #FFFFFF; padding: 0.75rem 1.5rem; border-radius: 0.75rem; text-decoration: none; font-weight: 600;">
                <x-heroicon-o-home style="width: 1.25rem; height: 1.25rem;" /> Buka Dashboard
            </a>
        @else
            <a href="{{ url('/login') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background-color: #2563EB; color: #FFFFFF; padding: 0.75rem 1.75rem; border-radius: 0.75rem; text-decoration: none; font-weight: 600;">
                <x-heroicon-o-lock-closed style="width: 1.25rem; height: 1.25rem;" /> Masuk ke Sistem
            </a>
        @endauth
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(14rem, 1fr)); gap: 1.25rem; text-align: left;">
        <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: #2563EB; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">
                <x-heroicon-o-shield-check style="width: 1.25rem; height: 1.25rem;" />
                <span>Kriptografi AEAD</span>
            </div>
            <p style="font-size: 0.8125rem; color: #475569; margin: 0; line-height: 1.5;">
                Catatan izin/sakit mahasiswa diamankan dengan enkripsi native AES-256-GCM (SEC-03).
            </p>
        </div>

        <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: #065F46; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">
                <x-heroicon-o-finger-print style="width: 1.25rem; height: 1.25rem;" />
                <span>Anti-Dobel Presensi</span>
            </div>
            <p style="font-size: 0.8125rem; color: #475569; margin: 0; line-height: 1.5;">
                Transaksi presensi atomik 1x per hari per kelas dengan indeks unik komposit SQLite.
            </p>
        </div>

        <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: #9A3412; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">
                <x-heroicon-o-eye-slash style="width: 1.25rem; height: 1.25rem;" />
                <span>Otorisasi Anti-IDOR</span>
            </div>
            <p style="font-size: 0.8125rem; color: #475569; margin: 0; line-height: 1.5;">
                Riwayat presensi mahasiswa lain terproteksi ketat di level Policy server-side (SEC-10).
            </p>
        </div>
    </div>

</div>
@endsection
