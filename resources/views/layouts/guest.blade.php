<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk') - AbsensiKita</title>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #F4F5FA;
            color: #0F172A;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-surface {
            background-color: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }
        .focus-accessible:focus { outline: 2px solid #2563EB; outline-offset: 2px; }
    </style>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
</head>
<body style="background-color: #F4F5FA; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem;">

    <div style="width: 100%; max-width: 28rem;">
        {{-- Branding Header --}}
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 4rem; height: 4rem; background-color: #EBF3FC; border-radius: 1rem; margin-bottom: 1rem;">
                <x-heroicon-o-academic-cap style="width: 2.25rem; height: 2.25rem; color: #2563EB;" />
            </div>
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #0F172A; margin: 0;">Absensi<span style="color: #2563EB;">Kita</span></h1>
            <p style="font-size: 0.875rem; color: #475569; margin-top: 0.5rem;">Sistem Presensi Kelas Aman & Terproteksi</p>
        </div>

        {{-- Flash Alerts --}}
        @if(session('error'))
            <div style="background-color: #FEE2E2; border: 1px solid #FECACA; color: #991B1B; padding: 0.875rem 1rem; border-radius: 0.75rem; margin-bottom: 1rem; font-size: 0.875rem;">
                <strong>Gagal:</strong> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div style="background-color: #E8F7F0; border: 1px solid #A7F3D0; color: #065F46; padding: 0.875rem 1rem; border-radius: 0.75rem; margin-bottom: 1rem; font-size: 0.875rem;">
                <strong>Berhasil:</strong> {{ session('success') }}
            </div>
        @endif

        {{-- Card Container --}}
        <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 1rem; padding: 2rem;">
            @yield('content')
        </div>

        <div style="text-align: center; margin-top: 1.5rem; font-size: 0.75rem; color: #64748B;">
            Tugas Pengganti UTS Keamanan Sistem Komputer &bull; NIM: 230170095
        </div>
    </div>

</body>
</html>
