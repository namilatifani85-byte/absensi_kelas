@extends('layouts.app')

@section('title', 'Daftar Kelas Perkuliahan')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- Header & Aksi --}}
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #0F172A; margin: 0;">Daftar Kelas Perkuliahan</h1>
            <p style="font-size: 0.875rem; color: #475569; margin: 0.25rem 0 0 0;">
                Kelola data kelas, jadwal perkuliahan, dan cari mata kuliah (SEC-08 Protected).
            </p>
        </div>

        @if(auth()->user()->isLecturer())
            <a href="{{ route('classes.create') }}"
               class="focus-accessible"
               style="display: inline-flex; align-items: center; gap: 0.5rem; background-color: #2563EB; color: #FFFFFF; padding: 0.625rem 1.25rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; text-decoration: none;">
                <x-heroicon-o-plus-circle style="width: 1.25rem; height: 1.25rem;" />
                <span>Tambah Kelas Baru</span>
            </a>
        @endif
    </div>

    {{-- Form Pencarian Kelas (SEC-08: Tahan Terhadap SQL Injection) --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
        <form action="{{ route('classes.index') }}" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 16rem; position: relative;">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari berdasarkan nama kelas, kode (contoh: TIF-301), atau ruang..."
                       class="focus-accessible"
                       style="width: 100%; box-sizing: border-box; padding: 0.625rem 1rem 0.625rem 2.5rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem; color: #0F172A;">
                <div style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94A3B8;">
                    <x-heroicon-o-magnifying-glass style="width: 1.125rem; height: 1.125rem;" />
                </div>
            </div>

            <button type="submit"
                    class="focus-accessible"
                    style="display: inline-flex; align-items: center; gap: 0.375rem; background-color: #2563EB; color: #FFFFFF; padding: 0.625rem 1.25rem; border-radius: 0.5rem; border: none; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                <span>Cari Kelas</span>
            </button>

            @if(request('search'))
                <a href="{{ route('classes.index') }}"
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

    {{-- Tabel Data Kelas --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="background-color: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #475569; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <th style="padding: 0.875rem 1.25rem; font-weight: 600;">Kode</th>
                        <th style="padding: 0.875rem 1.25rem; font-weight: 600;">Nama Mata Kuliah</th>
                        <th style="padding: 0.875rem 1.25rem; font-weight: 600;">Dosen Pengampu</th>
                        <th style="padding: 0.875rem 1.25rem; font-weight: 600;">Ruang / Hari</th>
                        <th style="padding: 0.875rem 1.25rem; font-weight: 600;">Jadwal</th>
                        <th style="padding: 0.875rem 1.25rem; font-weight: 600; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 1rem 1.25rem; font-weight: 700; color: #2563EB;">
                                {{ $class->code }}
                            </td>
                            <td style="padding: 1rem 1.25rem;">
                                <div style="font-weight: 600; color: #0F172A;">{{ $class->name }}</div>
                            </td>
                            <td style="padding: 1rem 1.25rem; color: #475569;">
                                {{ $class->lecturer->name ?? 'Dosen Belum Ditentukan' }}
                            </td>
                            <td style="padding: 1rem 1.25rem; color: #475569;">
                                <span style="font-weight: 500; color: #0F172A;">{{ $class->day }}</span>
                                <div style="font-size: 0.75rem; color: #64748B;">{{ $class->room }}</div>
                            </td>
                            <td style="padding: 1rem 1.25rem; color: #475569; font-size: 0.8125rem;">
                                {{ substr($class->time_start, 0, 5) }} &ndash; {{ substr($class->time_end, 0, 5) }} WIB
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: right;">
                                <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                    <a href="{{ route('classes.show', $class) }}"
                                       class="focus-accessible"
                                       style="padding: 0.375rem 0.625rem; border-radius: 0.375rem; background-color: #EBF3FC; color: #1E40AF; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
                                        Detail
                                    </a>

                                    @if(auth()->user()->isLecturer())
                                        <a href="{{ route('classes.edit', $class) }}"
                                           class="focus-accessible"
                                           style="padding: 0.375rem 0.625rem; border-radius: 0.375rem; background-color: #FEF3C7; color: #92400E; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
                                            Edit
                                        </a>

                                        <form action="{{ route('classes.destroy', $class) }}"
                                              method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini?');"
                                              style="display: inline; margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="focus-accessible"
                                                    style="padding: 0.375rem 0.625rem; border-radius: 0.375rem; border: none; background-color: #FEE2E2; color: #991B1B; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 3rem 1.25rem; text-align: center; color: #64748B;">
                                <x-heroicon-o-folder-open style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.75rem auto; color: #94A3B8;" />
                                <div style="font-weight: 600; color: #0F172A; margin-bottom: 0.25rem;">Data kelas tidak ditemukan</div>
                                <div style="font-size: 0.8125rem;">
                                    @if(request('search'))
                                        Pencarian kata kunci "{{ request('search') }}" tidak cocok dengan data kelas manapun.
                                    @else
                                        Belum ada data kelas yang terdaftar di sistem.
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classes->hasPages())
            <div style="padding: 1rem 1.25rem; border-top: 1px solid #E2E8F0;">
                {{ $classes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
