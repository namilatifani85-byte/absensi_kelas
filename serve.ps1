$env:PATH = "C:\php83;" + $env:PATH
Set-Location $PSScriptRoot
Write-Host "Menjalankan AbsensiKita di http://127.0.0.1:8000 ..." -ForegroundColor Green
php artisan serve --port=8000
