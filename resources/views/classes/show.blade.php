@extends('layouts.app')

@section('title', 'Detail Kelas: ' . $class->name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <div>
        <a href="{{ route('classes.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.875rem; color: #2563EB; text-decoration: none; font-weight: 500; margin-bottom: 0.5rem;">
            &larr; Kembali ke Daftar Kelas
        </a>
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span style="font-size: 0.875rem; font-weight: 700; color: #2563EB;">{{ $class->code }}</span>
                <h1 style="font-size: 1.75rem; font-weight: 700; color: #0F172A; margin: 0.25rem 0 0 0;">{{ $class->name }}</h1>
            </div>

            <div style="display: flex; gap: 0.5rem; align-items: center;">
                {{-- Tombol Presensi untuk Mahasiswa --}}
                @if(auth()->user()->isStudent() && $class->is_active)
                    <a href="{{ route('attendances.checkin', $class) }}"
                       class="focus-accessible"
                       style="display: inline-flex; align-items: center; gap: 0.375rem; background-color: #2563EB; color: #FFFFFF; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600;">
                        <x-heroicon-o-check-circle style="width: 1.25rem; height: 1.25rem;" /> Presensi Hari Ini
                    </a>
                @endif

                {{-- Tombol Edit untuk Dosen Pengampu --}}
                @if(auth()->user()->isLecturer())
                    <a href="{{ route('classes.edit', $class) }}"
                       class="focus-accessible"
                       style="display: inline-flex; align-items: center; gap: 0.375rem; background-color: #FEF3C7; color: #92400E; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600;">
                        <x-heroicon-o-pencil-square style="width: 1rem; height: 1rem;" /> Edit Kelas
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Info Card --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(14rem, 1fr)); gap: 1rem;">
        <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
            <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Dosen Pengampu</div>
            <div style="font-size: 1rem; font-weight: 700; color: #0F172A; margin-top: 0.25rem;">{{ $class->lecturer->name ?? '-' }}</div>
        </div>
        <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
            <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Ruangan & Hari</div>
            <div style="font-size: 1rem; font-weight: 700; color: #0F172A; margin-top: 0.25rem;">{{ $class->room }} ({{ $class->day }})</div>
        </div>
        <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
            <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Waktu Perkuliahan</div>
            <div style="font-size: 1rem; font-weight: 700; color: #0F172A; margin-top: 0.25rem;">{{ substr($class->time_start, 0, 5) }} &ndash; {{ substr($class->time_end, 0, 5) }} WIB</div>
        </div>
        <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
            <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Total Rekam Presensi</div>
            <div style="font-size: 1rem; font-weight: 700; color: #2563EB; margin-top: 0.25rem;">{{ $class->attendances->count() }} Record</div>
        </div>
    </div>

    {{-- Daftar Kehadiran Terakhir pada Kelas Ini --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; overflow: hidden;">
        <div style="padding: 1.25rem; border-bottom: 1px solid #E2E8F0; font-weight: 700; font-size: 1rem; color: #0F172A;">
            Riwayat Kehadiran Mahasiswa
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="background-color: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #475569; font-size: 0.75rem; text-transform: uppercase;">
                        <th style="padding: 0.75rem 1.25rem;">Tanggal</th>
                        <th style="padding: 0.75rem 1.25rem;">NIM</th>
                        <th style="padding: 0.75rem 1.25rem;">Nama Mahasiswa</th>
                        <th style="padding: 0.75rem 1.25rem;">Status Kehadiran</th>
                        <th style="padding: 0.75rem 1.25rem;">Keterangan</th>
                        @if(auth()->user()->isLecturer())
                            <th style="padding: 0.75rem 1.25rem;">Aksi Verifikasi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($class->attendances as $att)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.875rem 1.25rem; font-weight: 500;">
                                {{ $att->attendance_date }}
                            </td>
                            <td style="padding: 0.875rem 1.25rem; font-family: monospace; color: #475569;">
                                {{ $att->student->nim ?? '-' }}
                            </td>
                            <td style="padding: 0.875rem 1.25rem; font-weight: 600; color: #0F172A;">
                                {{ $att->student->full_name ?? '-' }}
                            </td>
                            <td style="padding: 0.875rem 1.25rem;">
                                <x-status-badge :status="$att->status" />
                            </td>
                            <td style="padding: 0.875rem 1.25rem; color: #64748B; font-size: 0.8125rem;">
                                {{ $att->excuse_notes ?? '-' }}
                            </td>
                            @if(auth()->user()->isLecturer())
                                <td style="padding: 0.875rem 1.25rem;">
                                    <form action="{{ route('attendances.verify', $att) }}" method="POST" style="display: inline-flex; align-items: center; gap: 0.375rem;">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" style="font-size: 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.375rem; padding: 0.25rem 0.5rem; background-color: #FFFFFF;">
                                            <option value="present" {{ $att->status === 'present' ? 'selected' : '' }}>Hadir</option>
                                            <option value="sick" {{ $att->status === 'sick' ? 'selected' : '' }}>Sakit</option>
                                            <option value="permit" {{ $att->status === 'permit' ? 'selected' : '' }}>Izin</option>
                                            <option value="absent" {{ $att->status === 'absent' ? 'selected' : '' }}>Alfa</option>
                                        </select>
                                        <button type="submit"
                                                class="focus-accessible"
                                                style="font-size: 0.75rem; background-color: #F1F5F9; border: 1px solid #CBD5E1; border-radius: 0.375rem; padding: 0.25rem 0.5rem; cursor: pointer; font-weight: 600; color: #334155;">
                                            Ubah
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isLecturer() ? 6 : 5 }}" style="padding: 2rem 1.25rem; text-align: center; color: #64748B;">
                                Belum ada data presensi tercatat untuk kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
