@echo off
REM ==============================================================================
REM Script Menjalankan Server Demo Rentan Terisolasi (ADR-005) - Windows CMD
REM ==============================================================================

echo ==========================================================
echo  AbsensiKita: Server Demo Rentan Terisolasi (Port 8001)   
echo ==========================================================

if not exist "database\database_vuln.sqlite" (
    echo Menyiapkan database/database_vuln.sqlite...
    php scripts\setup_vulnerable_db.php
)

php artisan serve --env=vulnerable --host=127.0.0.1 --port=8001
