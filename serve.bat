@echo off
set "PATH=C:\php83;%PATH%"
cd /d "%~dp0"
echo Menjalankan AbsensiKita di http://127.0.0.1:8000 ...
php artisan serve --port=8000
pause
