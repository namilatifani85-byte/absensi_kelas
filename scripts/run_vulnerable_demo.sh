#!/bin/bash
# ==============================================================================
# Script Menjalankan Server Demo Rentan Terisolasi (ADR-005)
# HANYA DIAKSES PADA LOCALHOST INTERFACE 127.0.0.1 PORT 8001
# ==============================================================================

echo "=========================================================="
echo " AbsensiKita: Server Demo Rentan Terisolasi (Port 8001)   "
echo "=========================================================="
echo "Menjalankan php artisan serve dengan .env.vulnerable..."

# Pastikan database database_vuln.sqlite telah siap
if [ ! -f "database/database_vuln.sqlite" ]; then
    echo "Menyiapkan database/database_vuln.sqlite..."
    php scripts/setup_vulnerable_db.php
fi

php artisan serve --env=vulnerable --host=127.0.0.1 --port=8001
