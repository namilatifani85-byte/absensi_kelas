<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AbsensiKita') - Sistem Presensi Kelas Aman</title>

    <style>
        /* Fallback CSS Mandiri agar tampilan tetap rapi sebelum Vite build */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #F4F5FA;
            color: #0F172A;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .canvas-bg { background-color: #F4F5FA; }
        .card-surface { background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); }
        .hero-surface { background-color: #121624; color: #FFFFFF; }
        .focus-accessible:focus { outline: 2px solid #2563EB; outline-offset: 2px; }

        /* Badge Status Kehadiran WCAG AAA */
        .badge-present { background-color: #E8F7F0; color: #065F46; }
        .badge-permit  { background-color: #FDEEE9; color: #9A3412; }
        .badge-sick    { background-color: #EBF3FC; color: #1E40AF; }
        .badge-absent  { background-color: #FEE2E2; color: #991B1B; }
    </style>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="canvas-bg min-h-screen flex flex-col">

    {{-- Top Navbar --}}
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm" style="background-color: #FFFFFF; border-bottom: 1px solid #E2E8F0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="max-width: 80rem; margin: 0 auto; padding: 0.75rem 1.5rem;">
            <div class="flex justify-between items-center h-16" style="display: flex; justify-content: space-between; align-items: center;">

                {{-- Brand Logo --}}
                <div class="flex items-center gap-3" style="display: flex; align-items: center; gap: 0.75rem;">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 text-xl font-bold text-slate-900 focus-accessible" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: #0F172A; font-size: 1.25rem; font-weight: 700;">
                        <x-heroicon-o-academic-cap class="w-7 h-7 text-blue-600" style="width: 1.75rem; height: 1.75rem; color: #2563EB;" />
                        <span>Absensi<span style="color: #2563EB;">Kita</span></span>
                    </a>

                    @auth
                        @if(auth()->user()->isLecturer())
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background-color: #EBF3FC; color: #1E40AF; font-size: 0.75rem; padding: 0.25rem 0.625rem; border-radius: 9999px; font-weight: 600;">
                                Dosen
                            </span>
                        @else
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background-color: #E8F7F0; color: #065F46; font-size: 0.75rem; padding: 0.25rem 0.625rem; border-radius: 9999px; font-weight: 600;">
                                Mahasiswa
                            </span>
                        @endif
                    @endauth
                </div>

                {{-- Navigation Links --}}
                <div class="flex items-center gap-6" style="display: flex; align-items: center; gap: 1.5rem;">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-slate-700 hover:text-blue-600 focus-accessible" style="font-size: 0.875rem; text-decoration: none; color: #475569;">
                            Dashboard
                        </a>

                        @if(auth()->user()->isLecturer())
                            <a href="{{ url('/classes') }}" class="text-sm font-medium text-slate-700 hover:text-blue-600 focus-accessible" style="font-size: 0.875rem; text-decoration: none; color: #475569;">
                                Kelola Kelas
                            </a>
                            <a href="{{ url('/students') }}" class="text-sm font-medium text-slate-700 hover:text-blue-600 focus-accessible" style="font-size: 0.875rem; text-decoration: none; color: #475569;">
                                Daftar Mahasiswa
                            </a>
                        @else
                            <a href="{{ url('/classes') }}" class="text-sm font-medium text-slate-700 hover:text-blue-600 focus-accessible" style="font-size: 0.875rem; text-decoration: none; color: #475569;">
                                Daftar Kelas
                            </a>
                            <a href="{{ url('/my-attendance') }}" class="text-sm font-medium text-slate-700 hover:text-blue-600 focus-accessible" style="font-size: 0.875rem; text-decoration: none; color: #475569;">
                                Presensi Saya
                            </a>
                        @endif

                        {{-- User & Logout --}}
                        <div class="flex items-center gap-3 pl-4 border-l border-slate-200" style="display: flex; align-items: center; gap: 0.75rem; padding-left: 1rem; border-left: 1px solid #E2E8F0;">
                            <span class="text-sm font-medium text-slate-800" style="font-size: 0.875rem; color: #0F172A;">
                                {{ auth()->user()->name }}
                            </span>
                            <form action="{{ url('/logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="text-sm px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 focus-accessible" style="font-size: 0.875rem; padding: 0.375rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; background: #FFFFFF; cursor: pointer; color: #475569;">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ url('/login') }}" class="text-sm px-4 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 focus-accessible" style="font-size: 0.875rem; padding: 0.5rem 1rem; border-radius: 0.5rem; background-color: #2563EB; color: #FFFFFF; text-decoration: none; font-weight: 500;">
                            Masuk
                        </a>
                    @endauth
                </div>

            </div>
        </div>
    </nav>

    {{-- Flash Alerts Container --}}
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6" style="flex: 1; max-width: 80rem; width: 100%; margin: 0 auto; padding: 1.5rem 1.5rem;">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl border" style="background-color: #E8F7F0; border: 1px solid #A7F3D0; color: #065F46; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <x-heroicon-o-check-circle class="w-6 h-6 flex-shrink-0" style="width: 1.5rem; height: 1.5rem; color: #065F46;" />
                <div>
                    <strong style="font-weight: 600;">Berhasil:</strong> {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl border" style="background-color: #FEE2E2; border: 1px solid #FECACA; color: #991B1B; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 flex-shrink-0" style="width: 1.5rem; height: 1.5rem; color: #991B1B;" />
                <div>
                    <strong style="font-weight: 600;">Peringatan:</strong> {{ session('error') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl border" style="background-color: #FEE2E2; border: 1px solid #FECACA; color: #991B1B; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem;">
                <div style="font-weight: 600; margin-bottom: 0.5rem;">Terdapat kesalahan pada input:</div>
                <ul style="margin: 0; padding-left: 1.25rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Page Content Slot --}}
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-slate-200 mt-auto py-6" style="background-color: #FFFFFF; border-top: 1px solid #E2E8F0; margin-top: auto; padding: 1.5rem 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-slate-500" style="max-width: 80rem; margin: 0 auto; text-align: center; font-size: 0.875rem; color: #475569;">
            <p style="margin: 0;"><strong>AbsensiKita</strong> &mdash; Sistem Informasi Absensi Kelas Aman (Tugas Pengganti UTS Keamanan Sistem Komputer)</p>
            <p style="margin: 0.25rem 0 0 0; font-size: 0.75rem; color: #64748B;">Namila Tifani (NIM: 230170095) &bull; Teknik Informatika &bull; Universitas Malikussaleh</p>
        </div>
    </footer>

</body>
</html>
