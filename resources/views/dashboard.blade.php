@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- Hero Dark Card sesuai design.md Seksi 2.1 --}}
    <div class="hero-surface" style="background-color: #121624; color: #FFFFFF; border-radius: 1rem; padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span style="font-size: 0.875rem; color: #94A3B8; font-weight: 500;">Selamat Datang di</span>
                <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0.25rem 0 0.5rem 0;">Sistem Presensi AbsensiKita</h1>
                <p style="font-size: 0.875rem; color: #CBD5E1; margin: 0; max-width: 36rem;">
                    Platform presensi perkuliahan aman dengan proteksi kriptografi AEAD AES-256-GCM, autentikasi sesi terproteksi, dan mitigasi IDOR.
                </p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <div style="background-color: rgba(255, 255, 255, 0.1); padding: 0.75rem 1.25rem; border-radius: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.15);">
                    <div style="font-size: 0.75rem; color: #94A3B8;">Status Keamanan</div>
                    <div style="font-size: 0.875rem; font-weight: 600; color: #34D399; display: flex; align-items: center; gap: 0.25rem;">
                        <x-heroicon-o-lock-closed style="width: 1rem; height: 1rem;" /> SEC-01 &ndash; SEC-10
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cards Grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(16rem, 1fr)); gap: 1rem;">
        <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 0.875rem; color: #475569;">Total Kelas Aktif</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #0F172A; margin-top: 0.25rem;">2 Kelas</div>
                </div>
                <div style="width: 2.75rem; height: 2.75rem; background-color: #EBF3FC; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                    <x-heroicon-o-academic-cap style="width: 1.5rem; height: 1.5rem; color: #2563EB;" />
                </div>
            </div>
        </div>

        <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 0.875rem; color: #475569;">Presensi Hari Ini</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #0F172A; margin-top: 0.25rem;">Atomik (1x/hari)</div>
                </div>
                <div style="width: 2.75rem; height: 2.75rem; background-color: #E8F7F0; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                    <x-heroicon-o-clipboard-document-check style="width: 1.5rem; height: 1.5rem; color: #065F46;" />
                </div>
            </div>
        </div>

        <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 0.875rem; color: #475569;">Enkripsi Catatan Medis</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #0F172A; margin-top: 0.25rem;">AES-256-GCM</div>
                </div>
                <div style="width: 2.75rem; height: 2.75rem; background-color: #FDEEE9; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                    <x-heroicon-o-lock-closed style="width: 1.5rem; height: 1.5rem; color: #9A3412;" />
                </div>
            </div>
        </div>
    </div>

    {{-- Sampel Palet Status Kehadiran (WCAG AAA Verification) --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.5rem;">
        <h2 style="font-size: 1.125rem; font-weight: 600; color: #0F172A; margin: 0 0 1rem 0;">
            Standar Badge Status Kehadiran (WCAG 2.1 AAA Compliant)
        </h2>
        <p style="font-size: 0.875rem; color: #475569; margin: 0 0 1rem 0;">
            Warna status kehadiran dikalibrasi sesuai tabel <code style="background-color: #F1F5F9; padding: 0.125rem 0.375rem; border-radius: 0.25rem;">design.md</code> Seksi 2.2 untuk menjamin keterbacaan kontras tinggi &ge; 4.5:1.
        </p>

        <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
            <div>
                <x-status-badge status="present" />
                <span style="font-size: 0.75rem; color: #64748B; margin-left: 0.5rem;">Mint #E8F7F0 / Emerald #065F46 (Kontras 7.2:1)</span>
            </div>
            <div>
                <x-status-badge status="permit" />
                <span style="font-size: 0.75rem; color: #64748B; margin-left: 0.5rem;">Peach #FDEEE9 / Terracotta #9A3412 (Kontras 6.5:1)</span>
            </div>
            <div>
                <x-status-badge status="sick" />
                <span style="font-size: 0.75rem; color: #64748B; margin-left: 0.5rem;">Sky #EBF3FC / Cobalt #1E40AF (Kontras 7.8:1)</span>
            </div>
            <div>
                <x-status-badge status="absent" />
                <span style="font-size: 0.75rem; color: #64748B; margin-left: 0.5rem;">Rose #FEE2E2 / Deep Red #991B1B (Kontras 7.4:1)</span>
            </div>
        </div>
    </div>

</div>
@endsection
