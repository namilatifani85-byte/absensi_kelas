@extends('layouts.app')

@section('title', 'Detail Presensi: #' . $attendance->id)

@section('content')
<div style="max-width: 48rem; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">

    <div>
        <a href="{{ auth()->user()->isStudent() ? route('my-attendance.index') : route('classes.show', $attendance->class_id) }}"
           style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.875rem; color: #2563EB; text-decoration: none; font-weight: 500; margin-bottom: 0.5rem;">
            &larr; Kembali
        </a>
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">ID Presensi #{{ $attendance->id }}</span>
                <h1 style="font-size: 1.75rem; font-weight: 700; color: #0F172A; margin: 0.25rem 0 0 0;">Detail Rekam Kehadiran</h1>
            </div>
            <x-status-badge :status="$attendance->status" />
        </div>
    </div>

    {{-- Detail Mahasiswa & Kelas --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.5rem;">
        <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0 0 1rem 0; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
            Informasi Perkuliahan & Mahasiswa
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(13rem, 1fr)); gap: 1.25rem;">
            <div>
                <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Mata Kuliah / Kelas</div>
                <div style="font-size: 0.9375rem; font-weight: 700; color: #0F172A; margin-top: 0.25rem;">
                    {{ $attendance->classModel->name ?? '-' }}
                </div>
                <div style="font-size: 0.75rem; color: #2563EB;">Kode: {{ $attendance->classModel->code ?? '-' }}</div>
            </div>

            <div>
                <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Nama Mahasiswa</div>
                <div style="font-size: 0.9375rem; font-weight: 700; color: #0F172A; margin-top: 0.25rem;">
                    {{ $attendance->student->full_name ?? '-' }}
                </div>
                <div style="font-size: 0.75rem; color: #475569; font-family: monospace;">NIM: {{ $attendance->student->nim ?? '-' }}</div>
            </div>

            <div>
                <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Tanggal Presensi</div>
                <div style="font-size: 0.9375rem; font-weight: 600; color: #0F172A; margin-top: 0.25rem;">
                    {{ \Carbon\Carbon::parse($attendance->attendance_date)->translatedFormat('l, d F Y') }}
                </div>
                @if($attendance->checked_in_at)
                    <div style="font-size: 0.75rem; color: #64748B;">Pukul: {{ $attendance->checked_in_at->format('H:i:s') }} WIB</div>
                @endif
            </div>

            <div>
                <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Dosen Pengampu</div>
                <div style="font-size: 0.9375rem; font-weight: 600; color: #0F172A; margin-top: 0.25rem;">
                    {{ $attendance->classModel->lecturer->name ?? '-' }}
                </div>
                @if($attendance->verifier)
                    <div style="font-size: 0.75rem; color: #166534;">Diverifikasi oleh: {{ $attendance->verifier->name }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Keterangan Alasan Izin / Sakit --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.5rem;">
        <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0 0 0.75rem 0;">
            Keterangan / Alasan Presensi
        </h3>
        <div class="note-content" style="font-size: 0.9375rem; color: #334155; line-height: 1.6; background-color: #F8FAFC; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid #F1F5F9; word-break: break-word;">
            {{ $attendance->excuse_notes ?: 'Tidak ada keterangan tambahan.' }}
        </div>
    </div>

    {{-- Catatan Medis Terenkripsi (SEC-03 AEAD AES-256-GCM) --}}
    @if($attendance->medical_note_encrypted)
        <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #BFDBFE; border-radius: 0.75rem; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.25rem;">🔒</span>
                    <h3 style="font-size: 1rem; font-weight: 700; color: #1E40AF; margin: 0;">
                        Catatan Medis Terdekripsi (SEC-03 AEAD AES-256-GCM)
                    </h3>
                </div>
                <span style="font-size: 0.6875rem; background-color: #DBEAFE; color: #1E3A8A; padding: 0.25rem 0.5rem; border-radius: 9999px; font-weight: 600;">
                    Terotentikasi & Rahasia
                </span>
            </div>

            <div style="background-color: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 0.5rem; padding: 1rem; font-size: 0.9375rem; color: #1E3A8A; line-height: 1.6;">
                {{ $decryptedMedical }}
            </div>

            <div style="margin-top: 0.75rem; font-size: 0.75rem; color: #64748B; font-family: monospace; word-break: break-all; background-color: #F8FAFC; padding: 0.5rem; border-radius: 0.375rem;">
                <strong>Ciphertext di DB (Envelope):</strong> {{ $attendance->medical_note_encrypted }}
            </div>
        </div>
    @endif

    {{-- Catatan Bebas Tambahan & Mitigasi Stored XSS (SEC-09 Safe Escaping) --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0;">
                Catatan Tambahan Presensi
            </h3>
            <span style="font-size: 0.75rem; color: #166534; background-color: #DCFCE7; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-weight: 600;">
                SEC-09 Contextual Escaping
            </span>
        </div>

        {{-- Daftar Catatan yang Sudah Ada --}}
        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem;">
            @forelse($attendance->notes as $note)
                <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 0.5rem; padding: 0.875rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.375rem;">
                        <span style="font-weight: 600; font-size: 0.8125rem; color: #0F172A;">
                            {{ $note->author->name ?? 'Pengguna' }}
                            <span style="font-size: 0.6875rem; color: #64748B; font-weight: normal;">({{ $note->author->role ?? '-' }})</span>
                        </span>
                        <span style="font-size: 0.75rem; color: #64748B;">
                            {{ $note->created_at ? $note->created_at->format('d M Y, H:i') : '-' }}
                        </span>
                    </div>
                    {{-- SEC-09 Safe: Wajib {{ $note->notes }} untuk mencegah eksekusi Stored XSS --}}
                    <div class="note-content" style="font-size: 0.875rem; color: #334155; line-height: 1.5; word-break: break-word;">
                        {{ $note->notes }}
                    </div>
                </div>
            @empty
                <p style="font-size: 0.875rem; color: #64748B; margin: 0; font-style: italic;">
                    Belum ada catatan tambahan pada presensi ini.
                </p>
            @endforelse
        </div>

        {{-- Form Tambah Catatan Baru --}}
        <form action="{{ route('attendances.notes.store', $attendance) }}" method="POST" style="border-top: 1px solid #F1F5F9; padding-top: 1rem;">
            @csrf
            <label for="notes" style="display: block; font-size: 0.8125rem; font-weight: 600; color: #334155; margin-bottom: 0.375rem;">
                Tambah Catatan / Tindak Lanjut:
            </label>
            <textarea id="notes"
                      name="notes"
                      rows="2"
                      required
                      placeholder="Masukkan catatan keterangan tambahan..."
                      class="focus-accessible"
                      style="width: 100%; border: 1px solid #CBD5E1; border-radius: 0.5rem; padding: 0.625rem; font-size: 0.875rem; box-sizing: border-box;">{{ old('notes') }}</textarea>
            @error('notes')
                <p style="color: #DC2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
            @enderror
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                <span style="font-size: 0.75rem; color: #64748B;">
                    🛡️ Teks dienkode aman dengan HTML Contextual Escaping bawaan Blade.
                </span>
                <button type="submit"
                        class="focus-accessible"
                        style="padding: 0.5rem 1rem; background-color: #2563EB; color: #FFFFFF; border: none; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                    Kirim Catatan
                </button>
            </div>
        </form>
    </div>

    {{-- Banner Keamanan Anti-IDOR (SEC-10) & CSP (SEC-09) --}}
    <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 0.5rem; padding: 0.875rem; display: flex; align-items: center; gap: 0.75rem;">
        <x-heroicon-o-shield-check style="width: 1.5rem; height: 1.5rem; color: #16A34A; flex-shrink: 0;" />
        <div style="font-size: 0.8125rem; color: #475569;">
            <strong>Proteksi Ganda Aktif:</strong> Halaman ini dilindungi oleh <code>AttendancePolicy</code> (Anti-IDOR SEC-10) serta header HTTP <strong>Content-Security-Policy (CSP)</strong> &amp; Blade Entity Encoding (SEC-09 Stored XSS Mitigation).
        </div>
    </div>

</div>
@endsection
