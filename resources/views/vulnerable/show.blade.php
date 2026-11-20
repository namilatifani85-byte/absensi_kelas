@extends('layouts.app')

@section('title', '[RENTAN] Detail Presensi - IDOR & Stored XSS Demo')

@section('content')
<div style="max-width: 48rem; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- Banner Peringatan Demo Rentan --}}
    <div style="background-color: #FEF2F2; border: 2px dashed #DC2626; border-radius: 0.75rem; padding: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; color: #DC2626; font-weight: 700; font-size: 1.125rem;">
            <span>⚠️</span> MODUL DEMO CELAH RENTAN (IDOR SEC-10 &amp; STORED XSS SEC-09)
        </div>
        <p style="font-size: 0.875rem; color: #991B1B; margin: 0.5rem 0 0 0;">
            1. <strong>Celah IDOR:</strong> Endpoint <code>/vulnerable/attendances/{id}</code> tidak memiliki pengecekan <code>Policy</code>. Siapa pun dapat mengubah parameter ID di URL untuk melihat riwayat mahasiswa lain.<br>
            2. <strong>Celah Stored XSS:</strong> Konten catatan di-render menggunakan sintaks mentah unescaped <code>{!! '$note->notes' !!}</code> tanpa perlindungan Content-Security-Policy (CSP).
        </p>
    </div>

    <div>
        <span style="font-size: 0.75rem; font-weight: 700; color: #DC2626; text-transform: uppercase;">MODE RENTAN - ID PRESENSI #{{ $attendance->id }}</span>
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #0F172A; margin: 0.25rem 0 0 0;">Detail Rekam Kehadiran Mahasiswa</h1>
    </div>

    {{-- Detail Mahasiswa --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.5rem;">
        <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0 0 1rem 0; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
            Informasi Mahasiswa (Target IDOR)
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(13rem, 1fr)); gap: 1.25rem;">
            <div>
                <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Nama Mahasiswa</div>
                <div style="font-size: 1rem; font-weight: 700; color: #DC2626; margin-top: 0.25rem;">
                    {{ $attendance->student->full_name ?? '-' }}
                </div>
                <div style="font-size: 0.75rem; color: #475569; font-family: monospace;">NIM: {{ $attendance->student->nim ?? '-' }}</div>
            </div>

            <div>
                <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Mata Kuliah</div>
                <div style="font-size: 1rem; font-weight: 600; color: #0F172A; margin-top: 0.25rem;">
                    {{ $attendance->classModel->name ?? '-' }} ({{ $attendance->classModel->code ?? '-' }})
                </div>
            </div>

            <div>
                <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Tanggal Presensi</div>
                <div style="font-size: 0.9375rem; font-weight: 600; color: #0F172A; margin-top: 0.25rem;">
                    {{ $attendance->attendance_date }}
                </div>
            </div>

            <div>
                <div style="font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: 600;">Status Kehadiran</div>
                <div style="margin-top: 0.25rem;">
                    <x-status-badge :status="$attendance->status" />
                </div>
            </div>
        </div>
    </div>

    {{-- Keterangan Alasan Izin (Sengaja Unescaped untuk Demo XSS) --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #FCA5A5; border-radius: 0.75rem; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #991B1B; margin: 0;">
                Keterangan Presensi (Render Unescaped {!! !!})
            </h3>
            <span style="font-size: 0.75rem; background-color: #FEE2E2; color: #991B1B; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-weight: 700;">
                XSS Vulnerable
            </span>
        </div>
        {{-- VULNERABLE SEC-09: Unescaped blade tag {!! !!} --}}
        <div class="note-vuln" style="font-size: 0.9375rem; color: #334155; line-height: 1.6; background-color: #FFF5F5; padding: 0.875rem; border-radius: 0.5rem; border: 1px solid #FECACA;">
            {!! $attendance->excuse_notes ?: '<em>Tidak ada keterangan.</em>' !!}
        </div>
    </div>

    {{-- Daftar Catatan Tambahan (Sengaja Unescaped untuk Demo Stored XSS) --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #FCA5A5; border-radius: 0.75rem; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #991B1B; margin: 0;">
                Catatan Tambahan Presensi (Stored XSS Ground)
            </h3>
            <span style="font-size: 0.75rem; background-color: #FEE2E2; color: #991B1B; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-weight: 700;">
                Unescaped Output
            </span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem;">
            @forelse($attendance->notes as $note)
                <div style="background-color: #FFF5F5; border: 1px solid #FECACA; border-radius: 0.5rem; padding: 0.875rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.375rem;">
                        <span style="font-weight: 600; font-size: 0.8125rem; color: #991B1B;">
                            {{ $note->author->name ?? 'Pengguna' }}
                        </span>
                        <span style="font-size: 0.75rem; color: #64748B;">
                            {{ $note->created_at ? $note->created_at->format('d M Y, H:i') : '-' }}
                        </span>
                    </div>
                    {{-- VULNERABLE SEC-09: Unescaped blade tag {!! !!} memicu eksekusi payload script --}}
                    <div class="note-vuln-content" style="font-size: 0.875rem; color: #334155; line-height: 1.5;">
                        {!! $note->notes !!}
                    </div>
                </div>
            @empty
                <p style="font-size: 0.875rem; color: #64748B; margin: 0; font-style: italic;">
                    Belum ada catatan pada presensi ini.
                </p>
            @endforelse
        </div>

        {{-- Form Tambah Catatan Rentan --}}
        <form action="{{ route('vulnerable.attendances.notes.store', $attendance->id) }}" method="POST" style="border-top: 1px solid #F1F5F9; padding-top: 1rem;">
            @csrf
            <label for="notes" style="display: block; font-size: 0.8125rem; font-weight: 600; color: #991B1B; margin-bottom: 0.375rem;">
                Kirim Catatan Uji Coba XSS (Payload):
            </label>
            <textarea id="notes"
                      name="notes"
                      rows="2"
                      required
                      placeholder="Masukkan payload XSS, contoh: &lt;script&gt;alert('XSS-Vuln')&lt;/script&gt;"
                      style="width: 100%; border: 1px solid #FCA5A5; border-radius: 0.5rem; padding: 0.625rem; font-size: 0.875rem; box-sizing: border-box; background-color: #FFF5F5; font-family: monospace;"></textarea>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                <button type="button"
                        onclick="document.getElementById('notes').value = '<script>alert(\'XSS-Vuln-Executed!\')<\/script>';"
                        style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-family: monospace; cursor: pointer;">
                    Isi Payload &lt;script&gt;
                </button>
                <button type="submit"
                        style="padding: 0.5rem 1rem; background-color: #DC2626; color: #FFFFFF; border: none; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                    Kirim Catatan Rentan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
