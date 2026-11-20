@extends('layouts.app')

@section('title', 'Daftar Mahasiswa')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <div>
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #0F172A; margin: 0;">Daftar Mahasiswa</h1>
        <p style="font-size: 0.875rem; color: #475569; margin: 0.25rem 0 0 0;">
            Data mahasiswa aktif terdaftar di sistem AbsensiKita (SEC-08 Protected).
        </p>
    </div>

    {{-- Form Pencarian Mahasiswa (SEC-08: Tahan Terhadap SQL Injection) --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
        <form action="{{ route('students.index') }}" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 16rem; position: relative;">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari berdasarkan NIM, nama lengkap, atau program studi..."
                       class="focus-accessible"
                       style="width: 100%; box-sizing: border-box; padding: 0.625rem 1rem 0.625rem 2.5rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem; color: #0F172A;">
                <div style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94A3B8;">
                    <x-heroicon-o-magnifying-glass style="width: 1.125rem; height: 1.125rem;" />
                </div>
            </div>

            <button type="submit"
                    class="focus-accessible"
                    style="display: inline-flex; align-items: center; gap: 0.375rem; background-color: #2563EB; color: #FFFFFF; padding: 0.625rem 1.25rem; border-radius: 0.5rem; border: none; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                <span>Cari Mahasiswa</span>
            </button>

            @if(request('search'))
                <a href="{{ route('students.index') }}"
                   class="focus-accessible"
                   style="display: inline-flex; align-items: center; gap: 0.375rem; background-color: #F1F5F9; color: #475569; padding: 0.625rem 1rem; border-radius: 0.5rem; border: 1px solid #E2E8F0; font-size: 0.875rem; font-weight: 500; text-decoration: none;">
                    <span>Reset</span>
                </a>
            @endif
        </form>

        @if(request('search'))
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.75rem;">
                Menampilkan hasil pencarian untuk: <strong style="color: #0F172A;">"{{ request('search') }}"</strong>
                <span style="color: #059669; margin-left: 0.5rem;">(SEC-08: Parameter di-bind secara aman via PDO Prepared Statements)</span>
            </div>
        @endif
    </div>

    {{-- Tabel Mahasiswa --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="background-color: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #475569; font-size: 0.75rem; text-transform: uppercase;">
                        <th style="padding: 0.875rem 1.25rem; font-weight: 600;">NIM</th>
                        <th style="padding: 0.875rem 1.25rem; font-weight: 600;">Nama Lengkap</th>
                        <th style="padding: 0.875rem 1.25rem; font-weight: 600;">Email Kampus</th>
                        <th style="padding: 0.875rem 1.25rem; font-weight: 600;">Program Studi</th>
                        <th style="padding: 0.875rem 1.25rem; font-weight: 600;">Angkatan</th>
                        <th style="padding: 0.875rem 1.25rem; font-weight: 600; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $stud)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 1rem 1.25rem; font-weight: 700; font-family: monospace; color: #2563EB;">
                                {{ $stud->nim }}
                            </td>
                            <td style="padding: 1rem 1.25rem; font-weight: 600; color: #0F172A;">
                                {{ $stud->full_name }}
                            </td>
                            <td style="padding: 1rem 1.25rem; color: #475569;">
                                {{ $stud->user->email ?? '-' }}
                            </td>
                            <td style="padding: 1rem 1.25rem; color: #475569;">
                                {{ $stud->study_program }}
                            </td>
                            <td style="padding: 1rem 1.25rem; color: #475569;">
                                {{ $stud->class_year }}
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: right;">
                                <a href="{{ route('students.show', $stud) }}"
                                   class="focus-accessible"
                                   style="padding: 0.375rem 0.625rem; border-radius: 0.375rem; background-color: #EBF3FC; color: #1E40AF; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
                                    Lihat Profil
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 3rem 1.25rem; text-align: center; color: #64748B;">
                                <x-heroicon-o-user-group style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.75rem auto; color: #94A3B8;" />
                                <div style="font-weight: 600; color: #0F172A; margin-bottom: 0.25rem;">Data mahasiswa tidak ditemukan</div>
                                <div style="font-size: 0.8125rem;">
                                    @if(request('search'))
                                        Pencarian kata kunci "{{ request('search') }}" tidak cocok dengan data mahasiswa manapun.
                                    @else
                                        Belum ada data mahasiswa yang terdaftar di sistem.
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div style="padding: 1rem 1.25rem; border-top: 1px solid #E2E8F0;">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
