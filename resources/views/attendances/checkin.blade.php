@extends('layouts.app')

@section('title', 'Presensi Kelas: ' . $class->name)

@section('content')
<div style="max-width: 48rem; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">

    <div>
        <a href="{{ route('classes.show', $class) }}" style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.875rem; color: #2563EB; text-decoration: none; font-weight: 500; margin-bottom: 0.5rem;">
            &larr; Kembali ke Detail Kelas
        </a>
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #0F172A; margin: 0;">Presensi Kehadiran Harian</h1>
        <p style="font-size: 0.875rem; color: #64748B; margin: 0.25rem 0 0 0;">
            Silakan konfirmasi kehadiran Anda untuk sesi perkuliahan hari ini ({{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}).
        </p>
    </div>

    {{-- Kartu Info Kelas --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(12rem, 1fr)); gap: 1rem;">
            <div>
                <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Mata Kuliah / Kelas</div>
                <div style="font-size: 1rem; font-weight: 700; color: #0F172A; margin-top: 0.25rem;">{{ $class->name }} ({{ $class->code }})</div>
            </div>
            <div>
                <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Jadwal & Ruang</div>
                <div style="font-size: 1rem; font-weight: 600; color: #0F172A; margin-top: 0.25rem;">{{ $class->day }}, {{ substr($class->time_start, 0, 5) }} - {{ substr($class->time_end, 0, 5) }} WIB ({{ $class->room }})</div>
            </div>
            <div>
                <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Identitas Mahasiswa</div>
                <div style="font-size: 1rem; font-weight: 600; color: #2563EB; margin-top: 0.25rem;">{{ $student->full_name }} ({{ $student->nim }})</div>
            </div>
        </div>
    </div>

    @if($existing)
        {{-- Peringatan Sudah Presensi --}}
        <div style="background-color: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 0.75rem; padding: 1.5rem; text-align: center;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 3rem; height: 3rem; background-color: #DBEAFE; border-radius: 9999px; color: #1D4ED8; margin-bottom: 0.75rem;">
                <x-heroicon-o-check-circle style="width: 2rem; height: 2rem;" />
            </div>
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #1E3A8A; margin: 0;">Presensi Hari Ini Telah Dicatat</h3>
            <p style="font-size: 0.875rem; color: #1E40AF; margin: 0.5rem 0 1rem 0;">
                Anda telah mengisi daftar hadir untuk kelas ini pada tanggal <strong>{{ \Carbon\Carbon::parse($today)->translatedFormat('d F Y') }}</strong>.
            </p>
            <div style="display: inline-flex; align-items: center; gap: 0.75rem; background-color: #FFFFFF; padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid #BFDBFE;">
                <span style="font-size: 0.875rem; color: #475569;">Status Anda:</span>
                <x-status-badge :status="$existing->status" />
                @if($existing->checked_in_at)
                    <span style="font-size: 0.75rem; color: #64748B;">({{ $existing->checked_in_at->format('H:i') }} WIB)</span>
                @endif
            </div>
        </div>
    @else
        {{-- Form Presensi Kehadiran --}}
        <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.5rem;">
            <form action="{{ route('attendances.storeCheckin', $class) }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
                @csrf

                {{-- Pilihan Status Kehadiran --}}
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                        Pilih Status Kehadiran <span style="color: #DC2626;">*</span>
                    </label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(9rem, 1fr)); gap: 0.75rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; border: 1px solid #E2E8F0; border-radius: 0.5rem; padding: 0.75rem; cursor: pointer;">
                            <input type="radio" name="status" value="present" {{ old('status', 'present') === 'present' ? 'checked' : '' }} required>
                            <div>
                                <div style="font-weight: 600; font-size: 0.875rem; color: #166534;">Hadir</div>
                                <div style="font-size: 0.75rem; color: #64748B;">Hadir di ruang kelas</div>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; gap: 0.5rem; border: 1px solid #E2E8F0; border-radius: 0.5rem; padding: 0.75rem; cursor: pointer;">
                            <input type="radio" name="status" value="sick" {{ old('status') === 'sick' ? 'checked' : '' }}>
                            <div>
                                <div style="font-weight: 600; font-size: 0.875rem; color: #9A3412;">Sakit</div>
                                <div style="font-size: 0.75rem; color: #64748B;">Kondisi sakit / bedrest</div>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; gap: 0.5rem; border: 1px solid #E2E8F0; border-radius: 0.5rem; padding: 0.75rem; cursor: pointer;">
                            <input type="radio" name="status" value="permit" {{ old('status') === 'permit' ? 'checked' : '' }}>
                            <div>
                                <div style="font-weight: 600; font-size: 0.875rem; color: #1E40AF;">Izin</div>
                                <div style="font-size: 0.75rem; color: #64748B;">Keperluan penting</div>
                            </div>
                        </label>
                    </div>
                    @error('status')
                        <p style="color: #DC2626; font-size: 0.8125rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alasan / Keterangan Singkat --}}
                <div>
                    <label for="excuse_notes" style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">
                        Keterangan Tambahan / Alasan Izin (Opsional)
                    </label>
                    <textarea id="excuse_notes"
                              name="excuse_notes"
                              rows="2"
                              class="focus-accessible"
                              placeholder="Contoh: Mengikuti lomba olimpiade sains / rawat jalan..."
                              style="width: 100%; border: 1px solid #CBD5E1; border-radius: 0.5rem; padding: 0.625rem; font-size: 0.875rem; box-sizing: border-box;">{{ old('excuse_notes') }}</textarea>
                    @error('excuse_notes')
                        <p style="color: #DC2626; font-size: 0.8125rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Catatan Medis Terenkripsi (SEC-03) --}}
                <div style="background-color: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 0.5rem; padding: 1rem;">
                    <label for="medical_note" style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">
                        Catatan Medis Khusus / Diagnosa Dokter (Opsional & Terenkripsi)
                    </label>
                    <p style="font-size: 0.75rem; color: #64748B; margin-bottom: 0.5rem;">
                        🔒 <strong>Keamanan Syarat 3:</strong> Informasi ini dienkripsi dengan standar kriptografi autentikasi <strong>AES-256-GCM (AEAD)</strong> dan hanya dapat dibaca oleh Anda serta Dosen pengampu.
                    </p>
                    <textarea id="medical_note"
                              name="medical_note"
                              rows="3"
                              class="focus-accessible"
                              placeholder="Masukkan catatan resep dokter, keluhan penyakit, atau informasi rawat inap fiktif..."
                              style="width: 100%; border: 1px solid #CBD5E1; border-radius: 0.5rem; padding: 0.625rem; font-size: 0.875rem; box-sizing: border-box; background-color: #FFFFFF;">{{ old('medical_note') }}</textarea>
                    @error('medical_note')
                        <p style="color: #DC2626; font-size: 0.8125rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                    <a href="{{ route('classes.show', $class) }}"
                       style="padding: 0.625rem 1.25rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; color: #475569; text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                        Batal
                    </a>
                    <button type="submit"
                            class="focus-accessible"
                            style="padding: 0.625rem 1.5rem; background-color: #2563EB; color: #FFFFFF; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                        Simpan Presensi Hari Ini
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection
