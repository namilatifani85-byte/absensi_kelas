@extends('layouts.app')

@section('title', 'Riwayat Presensi Pribadi')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <div>
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #0F172A; margin: 0;">Riwayat Presensi Saya</h1>
        <p style="font-size: 0.875rem; color: #64748B; margin: 0.25rem 0 0 0;">
            Daftar seluruh rekam kehadiran Anda pada setiap mata kuliah yang diikuti.
        </p>
    </div>

    @if($isLecturer)
        <div style="background-color: #FEF3C7; border: 1px solid #FCD34D; border-radius: 0.75rem; padding: 1.25rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #92400E; margin: 0;">Akun Dosen Terdeteksi</h3>
            <p style="font-size: 0.875rem; color: #B45309; margin: 0.25rem 0 0.75rem 0;">
                Halaman ini khusus untuk rekam jejak presensi mahasiswa. Untuk memverifikasi presensi mahasiswa pada kelas yang Anda ampu, silakan buka menu Kelas.
            </p>
            <a href="{{ route('classes.index') }}" style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.875rem; font-weight: 600; color: #92400E; text-decoration: underline;">
                Buka Manajemen Kelas &rarr;
            </a>
        </div>
    @else
        {{-- Kartu Ringkasan Kehadiran --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(11rem, 1fr)); gap: 1rem;">
            <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
                <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Mahasiswa</div>
                <div style="font-size: 1rem; font-weight: 700; color: #0F172A; margin-top: 0.25rem;">{{ $student->full_name }}</div>
                <div style="font-size: 0.75rem; color: #2563EB; font-family: monospace;">NIM: {{ $student->nim }}</div>
            </div>
            <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
                <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Total Presensi</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #0F172A; margin-top: 0.25rem;">{{ $attendances->total() }}</div>
            </div>
            <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
                <div style="font-size: 0.75rem; color: #166534; text-transform: uppercase; font-weight: 600;">Hadir</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #166534; margin-top: 0.25rem;">
                    {{ $attendances->where('status', 'present')->count() }}
                </div>
            </div>
            <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
                <div style="font-size: 0.75rem; color: #9A3412; text-transform: uppercase; font-weight: 600;">Sakit / Izin</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #9A3412; margin-top: 0.25rem;">
                    {{ $attendances->whereIn('status', ['sick', 'permit'])->count() }}
                </div>
            </div>
        </div>

        {{-- Tabel Riwayat Presensi --}}
        <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; overflow: hidden;">
            <div style="padding: 1.25rem; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 700; font-size: 1rem; color: #0F172A;">Daftar Rekam Kehadiran</span>
                <span style="font-size: 0.75rem; color: #64748B;">Perlindungan Policy SEC-10 Aktif</span>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background-color: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #475569; font-size: 0.75rem; text-transform: uppercase;">
                            <th style="padding: 0.75rem 1.25rem;">Tanggal Presensi</th>
                            <th style="padding: 0.75rem 1.25rem;">Mata Kuliah / Kelas</th>
                            <th style="padding: 0.75rem 1.25rem;">Dosen</th>
                            <th style="padding: 0.75rem 1.25rem;">Status</th>
                            <th style="padding: 0.75rem 1.25rem;">Keterangan</th>
                            <th style="padding: 0.75rem 1.25rem; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $att)
                            <tr style="border-bottom: 1px solid #F1F5F9;">
                                <td style="padding: 0.875rem 1.25rem; font-weight: 500;">
                                    {{ \Carbon\Carbon::parse($att->attendance_date)->translatedFormat('d M Y') }}
                                    @if($att->checked_in_at)
                                        <div style="font-size: 0.75rem; color: #64748B;">{{ $att->checked_in_at->format('H:i') }} WIB</div>
                                    @endif
                                </td>
                                <td style="padding: 0.875rem 1.25rem;">
                                    <div style="font-weight: 600; color: #0F172A;">{{ $att->classModel->name ?? '-' }}</div>
                                    <div style="font-size: 0.75rem; color: #2563EB;">{{ $att->classModel->code ?? '-' }}</div>
                                </td>
                                <td style="padding: 0.875rem 1.25rem; color: #475569;">
                                    {{ $att->classModel->lecturer->name ?? '-' }}
                                </td>
                                <td style="padding: 0.875rem 1.25rem;">
                                    <x-status-badge :status="$att->status" />
                                </td>
                                <td style="padding: 0.875rem 1.25rem; color: #64748B; font-size: 0.8125rem;">
                                    {{ $att->excuse_notes ? \Illuminate\Support\Str::limit($att->excuse_notes, 35) : '-' }}
                                    @if($att->medical_note_encrypted)
                                        <span title="Memiliki catatan medis terenkripsi" style="display: inline-block; font-size: 0.6875rem; background-color: #EFF6FF; color: #1D4ED8; padding: 0.125rem 0.375rem; border-radius: 0.25rem; margin-left: 0.25rem;">
                                            🔒 Medis
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 0.875rem 1.25rem; text-align: right;">
                                    <a href="{{ route('attendances.show', $att) }}"
                                       class="focus-accessible"
                                       style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.8125rem; color: #2563EB; font-weight: 600; text-decoration: none; padding: 0.375rem 0.75rem; border: 1px solid #BFDBFE; border-radius: 0.375rem; background-color: #EFF6FF;">
                                        Lihat Detail &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 2.5rem 1.25rem; text-align: center; color: #64748B;">
                                    Belum ada rekam jejak presensi untuk akun Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($attendances->hasPages())
                <div style="padding: 1rem 1.25rem; border-top: 1px solid #E2E8F0;">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
