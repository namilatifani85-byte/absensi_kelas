@extends('layouts.app')

@section('title', '[RENTAN] Pencarian Kelas - SQL Injection Demo')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- Banner Peringatan Demo Rentan --}}
    <div style="background-color: #FEF2F2; border: 2px dashed #DC2626; border-radius: 0.75rem; padding: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; color: #DC2626; font-weight: 700; font-size: 1.125rem;">
            <span>⚠️</span> MODUL DEMO CELAH RENTAN TERISOLASI (ADR-005)
        </div>
        <p style="font-size: 0.875rem; color: #991B1B; margin: 0.5rem 0 0 0;">
            Halaman ini sengaja dibuat <strong>RENTAN TERHADAP SQL INJECTION (SEC-08)</strong> untuk tujuan demonstrasi lab dan bukti komparatif.
            Dijalankan pada interface loopback <code>127.0.0.1:8001</code> dengan database terisolasi <code>database_vuln.sqlite</code>.
        </p>
    </div>

    <div>
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #0F172A; margin: 0;">Pencarian Kelas (Versi Rentan SQLi)</h1>
        <p style="font-size: 0.875rem; color: #64748B; margin: 0.25rem 0 0 0;">
            Kueri dibangun menggunakan konkatenasi string mentah tanpa Prepared Statements / Eloquent Parameter Binding.
        </p>
    </div>

    {{-- Kueri SQL yang Dieksekusi --}}
    <div style="background-color: #1E293B; color: #F8FAFC; border-radius: 0.5rem; padding: 1rem; font-family: monospace; font-size: 0.875rem; overflow-x: auto;">
        <div style="color: #94A3B8; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 0.25rem;">Raw SQL Query yang Dieksekusi:</div>
        <code>{{ $rawSql }}</code>
    </div>

    {{-- Form Pencarian dengan Contoh Payload --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.25rem;">
        <form action="{{ route('vulnerable.classes.search') }}" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="Masukkan nama kelas atau coba payload: ' OR '1'='1 --"
                   style="flex: 1; min-width: 18rem; padding: 0.625rem 1rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem;">
            <button type="submit"
                    style="padding: 0.625rem 1.25rem; background-color: #DC2626; color: #FFFFFF; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                Cari (Rentan)
            </button>
        </form>

        <div style="margin-top: 0.75rem; font-size: 0.8125rem; color: #475569;">
            <strong>Contoh Payload SQLi:</strong>
            <button type="button" onclick="document.querySelector('input[name=search]').value = '\' OR \'1\'=\'1 --'; document.forms[0].submit();"
                    style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-family: monospace; cursor: pointer; margin-left: 0.25rem;">
                ' OR '1'='1 --
            </button>
        </div>
    </div>

    {{-- Tabel Hasil Pencarian --}}
    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; overflow: hidden;">
        <div style="padding: 1.25rem; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 700; font-size: 1rem; color: #0F172A;">Hasil Kueri: {{ count($classes) }} Record Ditemukan</span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="background-color: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #475569; font-size: 0.75rem; text-transform: uppercase;">
                        <th style="padding: 0.75rem 1.25rem;">ID</th>
                        <th style="padding: 0.75rem 1.25rem;">Kode Matkul</th>
                        <th style="padding: 0.75rem 1.25rem;">Nama Kelas</th>
                        <th style="padding: 0.75rem 1.25rem;">Ruang &amp; Hari</th>
                        <th style="padding: 0.75rem 1.25rem;">Status Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $c)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.875rem 1.25rem; font-family: monospace;">{{ $c->id }}</td>
                            <td style="padding: 0.875rem 1.25rem; font-weight: 700; color: #DC2626;">{{ $c->code }}</td>
                            <td style="padding: 0.875rem 1.25rem; font-weight: 600; color: #0F172A;">{{ $c->name }}</td>
                            <td style="padding: 0.875rem 1.25rem; color: #475569;">{{ $c->room ?? '-' }} ({{ $c->day ?? '-' }})</td>
                            <td style="padding: 0.875rem 1.25rem;">
                                <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-weight: 600; background-color: {{ $c->is_active ? '#DCFCE7' : '#F1F5F9' }}; color: {{ $c->is_active ? '#166534' : '#475569' }};">
                                    {{ $c->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 2rem 1.25rem; text-align: center; color: #64748B;">
                                Tidak ada data yang cocok dengan kueri pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
