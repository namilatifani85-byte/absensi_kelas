<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Policies\AttendancePolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * TKT-001-WEB: Mengaktifkan mode WAL, Foreign Keys, dan busy timeout pada SQLite.
     * TKT-009-WEB: Mendaftarkan Policy AttendancePolicy (SEC-10 Anti-IDOR).
     */
    public function boot(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA journal_mode=WAL;');
            DB::statement('PRAGMA foreign_keys=ON;');
            DB::statement('PRAGMA busy_timeout=5000;');
        }

        // Registrasi Policy Keamanan Anti-IDOR
        Gate::policy(Attendance::class, AttendancePolicy::class);
    }
}
