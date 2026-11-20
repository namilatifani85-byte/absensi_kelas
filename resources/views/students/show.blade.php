@extends('layouts.app')

@section('title', 'Profil Mahasiswa: ' . $student->full_name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 48rem; margin: 0 auto;">

    <div>
        <a href="{{ route('students.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.875rem; color: #2563EB; text-decoration: none; font-weight: 500; margin-bottom: 0.5rem;">
            &larr; Kembali ke Daftar Mahasiswa
        </a>
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #0F172A; margin: 0;">Profil Mahasiswa</h1>
        <p style="font-size: 0.875rem; color: #475569; margin: 0.25rem 0 0 0;">
            Informasi akademik dan rekap kehadiran mahasiswa.
        </p>
    </div>

    {{-- Detail Card --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(14rem, 1fr)); gap: 1.25rem;">
            <div>
                <div style="font-size: 0.75rem; color: #64748B; font-weight: 600; text-transform: uppercase;">NIM</div>
                <div style="font-size: 1.125rem; font-weight: 700; font-family: monospace; color: #2563EB; margin-top: 0.25rem;">{{ $student->nim }}</div>
            </div>

            <div>
                <div style="font-size: 0.75rem; color: #64748B; font-weight: 600; text-transform: uppercase;">Nama Lengkap</div>
                <div style="font-size: 1.125rem; font-weight: 700; color: #0F172A; margin-top: 0.25rem;">{{ $student->full_name }}</div>
            </div>

            <div>
                <div style="font-size: 0.75rem; color: #64748B; font-weight: 600; text-transform: uppercase;">Program Studi</div>
                <div style="font-size: 1rem; color: #0F172A; margin-top: 0.25rem;">{{ $student->study_program }}</div>
            </div>

            <div>
                <div style="font-size: 0.75rem; color: #64748B; font-weight: 600; text-transform: uppercase;">Tahun Angkatan</div>
                <div style="font-size: 1rem; color: #0F172A; margin-top: 0.25rem;">{{ $student->class_year }}</div>
            </div>

            <div>
                <div style="font-size: 0.75rem; color: #64748B; font-weight: 600; text-transform: uppercase;">Alamat Email</div>
                <div style="font-size: 1rem; color: #0F172A; margin-top: 0.25rem;">{{ $student->user->email ?? '-' }}</div>
            </div>

            <div>
                <div style="font-size: 0.75rem; color: #64748B; font-weight: 600; text-transform: uppercase;">Total Kehadiran Tercatat</div>
                <div style="font-size: 1rem; font-weight: 700; color: #065F46; margin-top: 0.25rem;">{{ $student->attendances->count() }} Kali</div>
            </div>
        </div>
    </div>

</div>
@endsection
