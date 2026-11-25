# Tutorial Ubuntu untuk Tugas Absensi Kelas Aman ("AbsensiKita")

Panduan bertahap untuk **Namila Tifani (NIM: 230170095)**, berdasarkan dokumen spesifikasi proyek (*PRD-Absensi-Kelas*) dan tugas pengganti UTS Keamanan Sistem Komputer. Ini adalah panduan operasional langkah demi langkah untuk dieksekusi langsung oleh mahasiswa pada server VM Ubuntu Server 24.04 LTS.

**Cara mengikuti:** kerjakan nomor secara berurutan, satu blok perintah pada satu waktu. Jangan melompati nomor langkah. Setiap judul bernomor (misalnya `6.2.1`) adalah satu aksi terarah. Sebelum mengeksekusi perintah yang mengubah konfigurasi sistem atau firewall, perhatikan peringatan mitigasi agar koneksi SSH remote Anda tidak terputus.

---

## Peta Tugas Dosen: Sedang Mengerjakan Apa?

### Gambaran Tugas

Anda berperan sebagai *security engineer*: membangun sistem absensi kelas mini terverifikasi (**AbsensiKita**) di VM Ubuntu Server lalu menerapkan pengerasan keamanan berlapis (*defense-in-depth*). Digit terakhir NIM Anda adalah **5**, sehingga:
- **Tema Proyek:** Absensi Kelas (*Secure Mini Class Attendance System* / **AbsensiKita**).
- **Tabel Utama:** `classes` (kelas kuliah), `students` (mahasiswa), `attendances` (kehadiran harian atomik), didukung `users` dan `attendance_notes`.
- **Data Sensitif Terenkripsi (Syarat 3 / SEC-03):** Catatan keterangan izin/sakit mahasiswa (`medical_note_encrypted`) menggunakan algoritma autentikasi **AES-256-GCM** dengan format versioned envelope (`v1:<nonce>:<ct>:<tag>`) dan AAD diikat ke `student_id`.
- **Data Anti-IDOR (Syarat 10 / SEC-10):** Detail kehadiran dan riwayat absensi mahasiswa lain (`/attendances/{id}`). Mahasiswa hanya berhak melihat presensinya sendiri; akses ke presensi milik mahasiswa lain wajib ditolak dengan respons **HTTP 403 Forbidden**. Dosen memiliki hak penuh untuk memeriksa absensi seluruh mahasiswa.

Dosen meminta aplikasi dapat diakses via **HTTPS** (port 443), memiliki otentikasi login/registrasi berbasis peran (*lecturer* & *student*), presensi harian atomik 1x/hari, pencarian aman, kolom teks bebas untuk uji Stored XSS, serta minimal dua akun mahasiswa fiktif (`budi_santoso`, `siti_aminah`) dan satu akun dosen fiktif (`admin_dosen`). Tampilan Blade elegan menggunakan Tailwind CSS; penilaian difokuskan pada integritas fungsi, 10 kontrol keamanan, dan bukti komparatifnya.

---

### Sepuluh Syarat yang Dinilai (SEC-01 s/d SEC-10)

Tabel berikut memetakan 10 syarat keamanan mutlak (masing-masing bernilai 8 poin) sesuai tema Absensi Kelas. Syarat 6–10 **wajib memiliki bukti komparatif Sebelum vs. Sesudah**.

| Syarat Dosen | Lingkup Pekerjaan & Pengamanan AbsensiKita | Bukti yang Diminta | Rujukan Dokumen & Tiket |
| :--- | :--- | :--- | :--- |
| **1 / SEC-01 — Tabel Risiko** | Identifikasi 5 aset absensi kelas & 5 risiko CIA; tentukan kontrol preventif, detektif, korektif (Backup); jelaskan alasan risiko tertinggi (kerusakan database presensi). | Tabel risiko CIA lengkap dan analisis kritis di laporan PDF. | Bagian 10; Tiket `TKT-017`. |
| **2 / SEC-02 — Password Hashing** | Bcrypt bawaan Laravel, salt unik otomatis, validasi panjang 8–72 byte (menangani limit 72 byte bcrypt). | Verifikasi dua akun berbeda (`budi_santoso` & `siti_aminah`) dengan password sama (`HadirAman#01!`) menghasilkan hash berbeda di DB. | Bagian 5.2 & 9; Tiket `TKT-005-WEB`. |
| **3 / SEC-03 — Enkripsi Catatan Medis** | Enkripsi AES-256-GCM mandiri untuk catatan izin/sakit, key 256-bit di `.env` (`APP_SICK_NOTE_KEY`), nonce 12 byte acak baru, tag 16 byte diverifikasi, AAD diikat ke `student_id`. | Ciphertext format `v1:...` di DB SQLite, tampilan didekripsi pada akun mahasiswa bersangkutan & dosen; key terlindungi. | Bagian 5.2 & 9; Tiket `TKT-003-WEB`. |
| **4 / SEC-04 — HTTPS Self-Signed** | Nginx port 443 dengan TLSv1.2/1.3, sertifikat OpenSSL memuat ekstensi SAN (`IP:192.168.56.101, DNS:localhost`), private key izin 600, redirect port 80 ke 443. | Akses browser via HTTPS, detail ekstensi SAN sertifikat, dan pengalihan HTTP 301. | Bagian 6; Tiket `TKT-013`. |
| **5 / SEC-05 — Hardening Linux** | Operator non-root `namila0095`, akun layanan sistem `appsvc` (`/usr/sbin/nologin`), source code read-only bagi worker (`root:appsvc` 755), `.env` 600, SSH `PermitRootLogin no`, `APP_DEBUG=false`. | Bukti `id appsvc`, permission file 600, output `sshd -T`, dan `APP_DEBUG=false`. | Bagian 3–5; Tiket `TKT-014`. |
| **6 / SEC-06 — Firewall UFW** | Default deny incoming, default allow outgoing. Port SSH (22) dibatasi hanya ke IP klien host (`192.168.56.1`). Port 443 terbuka. | Pemindaian Nmap dari laptop Windows Sebelum vs. Sesudah, tabel status UFW. | Bagian 7; Tiket `TKT-015`. |
| **7 / SEC-07 — Backup & Restore Drill** | Ekstraksi SQLite konsisten WAL via `VACUUM INTO`, kompresi tar, enkripsi OpenSSL AES-256-CBC, pencatatan `daftar_hash.txt`, cron harian, uji restore 3 kondisi < 60 detik. | Skrip backup, baris `crontab -l`, checksum SHA-256, dan foto 3 kondisi: Awal -> Bencana -> Pulih. | Bagian 8; Tiket `TKT-016`. |
| **8 / SEC-08 — SQL Injection** | Demo rentan localhost port 8001 dibobol query `' OR '1'='1 --`. Versi aman menggunakan parameter binding Eloquent 100%. | Hasil pencarian mahasiswa/kelas sebelum vs. sesudah dengan payload identik. | Bagian 9; Tiket `TKT-007-WEB` & `TKT-018`. |
| **9 / SEC-09 — Stored XSS & CSP** | Demo rentan unescaped `{!! $note !!}` memunculkan pop-up alert JavaScript. Versi aman Blade contextual escaping `{{ $note }}` + header CSP. | Alert JavaScript pada versi rentan vs. teks netral ter-escape + header CSP pada versi aman. | Bagian 9; Tiket `TKT-010-WEB` & `TKT-019`. |
| **10 / SEC-10 — IDOR & Keamanan Sesi** | Demo rentan `/attendances/{id}` membocorkan absensi mahasiswa lain. Versi aman menolak dengan 403 Forbidden. Cookie berflag `HttpOnly=true`, `Secure=true`, `SameSite=Lax`. | Tangkapan layar akses lintas ID sebelum vs sesudah, policy check, dan flag cookie di DevTools. | Bagian 6.3 & 9; Tiket `TKT-009-WEB` & `TKT-020`. |

---

### Posisi Proyek Saat Ini

- **Aplikasi Web (TKT-001-WEB s/d TKT-012-WEB):** **100% SELESAI (`DONE`)** di Git lokal. Semua migrasi 5 tabel, controller aman, middleware role, policy anti-IDOR, enkripsi AES-GCM, view Blade, dan unit/feature tests sudah siap.
- **Tahap Pekerjaan Sekarang:** Anda memasuki **Fase Infrastruktur Linux di VM Ubuntu (`TKT-013` s/d `TKT-016`)**, dilanjutkan pengujian komparatif lab (`TKT-017` s/d `TKT-020`) dan penyusunan deliverable final (`TKT-021` s/d `TKT-023`).
- **Mulai Langsung dari:** **Bagian 3 (Persiapan VM dan Jaringan)**.

---

## 1. Tujuan dan Parameter Lingkungan

| Parameter Lingkungan | Nilai Standar Proyek AbsensiKita | Keterangan |
| :--- | :--- | :--- |
| **Sistem Operasi VM** | Ubuntu Server 24.04 LTS (RAM 2 GB) | Berjalan di VirtualBox / VMware |
| **Adapter Jaringan 1 (Host-Only)** | `192.168.56.101/24` (Interface: `enp0s8` / `ens33`) | Jalur komunikasi aman laptop <-> VM |
| **IP Host Laptop Klien (Windows)** | `192.168.56.1` | Sumber akses SSH yang diizinkan firewall |
| **Adapter Jaringan 2 (NAT)** | DHCP (Interface: `enp0s3` / `ens37`) | Internet sementara untuk `apt` dan `git clone` |
| **Akun Operator VM** | `namila0095` (dengan hak `sudo`) | Identitas Mahasiswa (Nama Depan + 4 NIM) |
| **Akun Layanan Aplikasi (Service Account)** | `appsvc` (shell: `/usr/sbin/nologin`) | Menjalankan PHP-FPM, tidak bisa login shell |
| **Direktori Aplikasi** | `/var/www/absensikita` | Lokasi root aplikasi Laravel |
| **Stack Perangkat Lunak** | Nginx, PHP 8.3-FPM, SQLite 3 (WAL Mode) | Sesuai ADR-001 & ADR-002 |
| **Port Layanan** | 443 (HTTPS Aman), 80 (Redirect ke 443), 8001 (Demo Rentan Loopback) | Port 8001 dilarang dibuka ke publik/firewall |

### Pagar Pengaman Operasional (Wajib Dipatuhi)
1. **Ambil Snapshot VM** di VirtualBox/VMware sebelum mengubah SSH atau mengaktifkan firewall UFW. Beri nama: `Snapshot-Sebelum-UFW-Hardening`.
2. **Jangan pernah menjalankan `migrate:fresh`** pada database operasional yang sudah memiliki data pengujian.
3. **Setiap kali mengambil bukti terminal VM**, awali dengan perintah:
   ```bash
   whoami; date
   ```
4. **Sensor Informasi Rahasia:** Dalam tangkapan layar laporan, selalu sensor string password mentah, private key, dan nilai token cookie aktif.

---

## 2. Urutan Pengerjaan Linier (Roadmap)

1. **Langkah 3:** Setup Jaringan Host-Only (`192.168.56.101`), Akun `namila0095` & Paket PHP 8.3 / Nginx.
2. **Langkah 4:** Setup SSH Key Ed25519 Laptop Windows -> VM & Hardening SSH (SEC-05).
3. **Langkah 5:** Deploy Kode AbsensiKita di `/var/www/absensikita`, Setup `appsvc` & SQLite WAL (SEC-02, SEC-03, SEC-05).
4. **Langkah 6:** Generate Sertifikat OpenSSL SAN & Nginx HTTPS Port 443 + HTTP Redirect (SEC-04, TKT-013).
5. **Langkah 7:** Pengujian Nmap Sebelum UFW -> Pasang Firewall UFW Default-Deny -> Nmap Sesudah (SEC-06, TKT-015).
6. **Langkah 8:** Skrip Backup SQLite VACUUM INTO Terenkripsi OpenSSL + Cron + Restore Drill (SEC-07, TKT-016).
7. **Langkah 9:** Pengujian Komparatif Sebelum vs. Sesudah: SQLi (SEC-08), XSS (SEC-09), IDOR & Sesi (SEC-10).
8. **Langkah 10:** Penyusunan Laporan PDF, Video Demo 5 Menit, dan Audit Git Submission (TKT-021 s/d TKT-023).

---

## 3. Persiapan Jaringan, Akun, dan Paket Sistem (TKT-014)

### 3.1 Verifikasi Jaringan di VM Ubuntu
Masuk ke console VM Ubuntu Anda, lalu periksa antarmuka jaringan:

```bash
whoami; date
ip -br addr
ip route
```

Pastikan interface Host-Only memiliki IP `192.168.56.101` (atau catat jika menggunakan IP lain yang sekelas). Uji ping dari PowerShell di laptop Windows:

```powershell
ping 192.168.56.101
```

*Jika ping sukses, koneksi host-only antara laptop dan VM telah terhubung.*

### 3.2 Pembuatan Akun Operator `namila0095`
Dokumen tugas dosen mewajibkan identitas operator berupa nama depan + 4 digit terakhir NIM (`namila0095`):

```bash
# Periksa apakah akun sudah ada
id namila0095

# Jika belum ada, buat akun dan masukkan ke grup sudo:
sudo adduser namila0095
sudo usermod -aG sudo namila0095

# Pindah ke akun namila0095
su - namila0095
whoami
```
*Hasil `whoami` wajib menampilkan:* `namila0095`.

### 3.3 Pembuatan Akun Layanan Terisolasi `appsvc` (Prinsip Least Privilege)
Sesuai **ADR-006** dan **TKT-014**, PHP-FPM tidak boleh dijalankan dengan akun user interaktif. Buat akun sistem tanpa shell login:

```bash
sudo useradd -r -s /usr/sbin/nologin -d /var/www/absensikita appsvc
id appsvc
getent passwd appsvc
```
*Hasil harus menunjukkan shell:* `/usr/sbin/nologin` *(membuktikan akun tidak dapat dipakai login interaktif).*

### 3.4 Instalasi Paket Dependensi Ubuntu
Pastikan koneksi NAT aktif sementara untuk mengunduh paket resmi:

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx openssh-server php8.3-cli php8.3-fpm php8.3-sqlite3 \
    php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip sqlite3 composer git \
    unzip openssl curl ufw cron nodejs npm
```

Verifikasi versi terpasang:
```bash
php -v
nginx -v
sqlite3 --version
sudo systemctl status nginx php8.3-fpm ssh cron --no-pager
```

---

## 4. Konfigurasi SSH Key dan Hardening Daemon (SEC-05 / TKT-014)

Otentikasi SSH dengan password rentan terhadap serangan brute-force. Kita akan memasang autentikasi kunci publik (Ed25519) dan menonaktifkan login root.

### 4.1 Buat Pasangan Kunci SSH di Laptop Windows
Buka **PowerShell** di Windows Anda:

```powershell
# Buat direktori .ssh jika belum ada
if (!(Test-Path "$env:USERPROFILE\.ssh")) { New-Item -ItemType Directory -Path "$env:USERPROFILE\.ssh" }

# Buat pasangan kunci Ed25519 khusus proyek AbsensiKita
ssh-keygen -t ed25519 -f "$env:USERPROFILE\.ssh\id_ed25519_absensikita" -C "namila0095@absensikita"
```
*(Tekan Enter dua kali jika tidak ingin menggunakan passphrase, atau isi passphrase pelindung kunci).*

Tampilkan public key dan salin seluruh teks satu baris yang muncul:
```powershell
Get-Content "$env:USERPROFILE\.ssh\id_ed25519_absensikita.pub"
```

### 4.2 Pasang Public Key di VM Ubuntu
Di terminal VM Ubuntu (sebagai `namila0095`):

```bash
mkdir -p ~/.ssh
chmod 700 ~/.ssh
nano ~/.ssh/authorized_keys
```
*Tempel satu baris public key dari Windows tadi. Simpan dengan `Ctrl+O`, `Enter`, lalu keluar `Ctrl+X`.*

Batasi izin file:
```bash
chmod 600 ~/.ssh/authorized_keys
```

### 4.3 Uji Login SSH Kunci dari Windows
Buka jendela PowerShell baru di Windows:

```powershell
ssh -i "$env:USERPROFILE\.ssh\id_ed25519_absensikita" namila0095@192.168.56.101
```
*Jika berhasil masuk tanpa dimintai password akun Ubuntu, kunci telah berfungsi sempurna.*

### 4.4 Terapkan Hardening SSH Daemon
Setelah login kunci berhasil, lakukan pengerasan SSH daemon di VM Ubuntu:

```bash
sudo nano /etc/ssh/sshd_config.d/absensikita_hardening.conf
```
Masukkan konfigurasi berikut:
```text
PermitRootLogin no
MaxAuthTries 3
PubkeyAuthentication yes
PasswordAuthentication yes
```
*(Catatan: Biarkan `PasswordAuthentication yes` terlebih dahulu sampai seluruh pengujian selesai, atau ubah ke `no` jika ingin full-key authentication).*

Uji sintaks konfigurasi SSH:
```bash
sudo sshd -t
sudo systemctl reload ssh
```

---

## 5. Deployment Aplikasi AbsensiKita di VM (TKT-014 / SEC-02 / SEC-03)

### 5.1 Clone Repositori dan Setup Direktori
Kita akan menempatkan kode di `/var/www/absensikita`:

```bash
# Buat folder aplikasi
sudo mkdir -p /var/www/absensikita
sudo chown -R namila0095:namila0095 /var/www/absensikita

# Clone repositori GitHub Anda
cd /var/www/absensikita
git clone https://github.com/namilatifani85-byte/absensi_kelas.git .
git branch --show-current
git log -2 --oneline
```

Instal dependensi Composer dan kompilasi aset Frontend:
```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

### 5.2 Konfigurasi Lingkungan (`.env`) & Enkripsi Kunci Medis
Siapkan file `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

Generate kunci enkripsi khusus catatan izin/sakit AES-256-GCM (**SEC-03**):
```bash
# Generate 32-byte acak base64
openssl rand -base64 32
```
*Salin string kunci yang dihasilkan.*

Buka `.env` dengan editor:
```bash
nano .env
```
Sesuaikan konfigurasi berikut:
```dotenv
APP_NAME=AbsensiKita
APP_ENV=production
APP_DEBUG=false
APP_URL=https://192.168.56.101

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/absensikita/database/database.sqlite

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Kunci Enkripsi AES-256-GCM Catatan Izin/Sakit (SEC-03)
APP_SICK_NOTE_KEY=base64:TEMPEL_KUNCI_HASIL_OPENSSL_DI_SINI
```
*Simpan file (`Ctrl+O`, `Enter`, `Ctrl+X`).*

### 5.3 Inisialisasi Database SQLite WAL dan Seeding Data Fiktif
Siapkan file database SQLite dan aktifkan mode **Write-Ahead Logging (WAL)**:

```bash
touch database/database.sqlite
sqlite3 database/database.sqlite 'PRAGMA journal_mode=WAL;'
sqlite3 database/database.sqlite 'PRAGMA journal_mode;'
```
*(Hasil query wajib:* `wal`*).*

Jalankan migrasi tabel dan seeding data fiktif:
```bash
php artisan config:clear
php artisan migrate --seed --force
```

Verifikasi data fiktif dan bukti **SEC-02** (dua hash Bcrypt berbeda untuk password sama `HadirAman#01!`):
```bash
whoami; date
sqlite3 database/database.sqlite "SELECT id, username, email, password FROM users;"
```
> **[AMBIL FOTO BUKTI SEC-02]** Screenshot output terminal ini. Terlihat bahwa password hash `budi_santoso` dan `siti_aminah` berbeda karena salt acak unik, meskipun password aslinya identik. Sensor sebagian karakter hash untuk keamanan laporan.

### 5.4 Konfigurasi Pool PHP-FPM untuk Akun `appsvc`
Agar worker PHP berjalan di bawah akun `appsvc` (bukan user default):

```bash
sudo nano /etc/php/8.3/fpm/pool.d/www.conf
```
Cari dan sesuaikan baris-baris berikut:
```ini
user = appsvc
group = appsvc
listen.owner = www-data
listen.group = www-data
listen.mode = 0660
```
Restart PHP-FPM:
```bash
sudo systemctl restart php8.3-fpm
sudo systemctl status php8.3-fpm --no-pager
```

### 5.5 Penerapan Hak Akses Least Privilege (TKT-014 / SEC-05)
Kunci direktori aplikasi agar worker PHP tidak memiliki hak tulis berlebih:

```bash
cd /var/www/absensikita

# Seluruh source code dimiliki root:appsvc dengan hak read-only untuk appsvc
sudo chown -R root:appsvc .
sudo chmod -R 755 .

# Direktori yang WAJIB writable untuk runtime aplikasi:
sudo chmod -R 775 storage bootstrap/cache database

# File rahasia (.env dan database) WAJIB mode 600 milik appsvc
sudo chown appsvc:appsvc .env database/database.sqlite*
sudo chmod 600 .env
sudo chmod 660 database/database.sqlite*
```

Uji verifikasi hak akses (*bukti audit*):
```bash
whoami; date
sudo -u appsvc test -r /var/www/absensikita/.env && echo "appsvc BISA membaca .env"
sudo -u appsvc test -w /var/www/absensikita/database/database.sqlite && echo "appsvc BISA menulis database"
sudo -u appsvc test ! -w /var/www/absensikita/app && echo "appsvc TIDAK BISA memodifikasi source code (Aman)"
ls -la .env database/database.sqlite
```
> **[AMBIL FOTO BUKTI SEC-05]** Simpan screenshot hasil verifikasi permission `600` dan pembatasan least privilege ini.

---

## 6. Konfigurasi HTTPS Nginx dengan Ekstensi SAN (TKT-013 / SEC-04)

### 6.1 Pembuatan Sertifikat OpenSSL Self-Signed dengan Ekstensi SAN
Browser modern (Chrome/Firefox/Edge) akan menolak sertifikat TLS jika tidak memiliki ekstensi **Subject Alternative Name (SAN)**.

Buat file konfigurasi OpenSSL `/tmp/san.cnf`:
```bash
nano /tmp/san.cnf
```
Isi konfigurasi berikut:
```ini
[req]
default_bits = 2048
prompt = no
default_md = sha256
req_extensions = req_ext
distinguished_name = dn

[dn]
C = ID
ST = Aceh
L = Lhokseumawe
O = AbsensiKita Academic
OU = Computer Systems Security
CN = 192.168.56.101

[req_ext]
subjectAltName = @alt_names

[alt_names]
IP.1 = 192.168.56.101
DNS.1 = localhost
DNS.2 = absensikita.test
```

Generate sertifikat dan private key:
```bash
sudo mkdir -p /etc/ssl/private /etc/ssl/certs
sudo openssl req -x509 -newkey rsa:2048 -sha256 -nodes -days 365 \
    -config /tmp/san.cnf \
    -keyout /etc/ssl/private/absensikita.key \
    -out /etc/ssl/certs/absensikita.crt

# Amankan izin private key (wajib 600)
sudo chmod 600 /etc/ssl/private/absensikita.key
sudo chmod 644 /etc/ssl/certs/absensikita.crt
rm /tmp/san.cnf
```

Verifikasi ekstensi SAN pada sertifikat:
```bash
whoami; date
sudo openssl x509 -in /etc/ssl/certs/absensikita.crt -noout -text | grep -A 2 "Subject Alternative Name"
ls -l /etc/ssl/private/absensikita.key
```
> **[AMBIL FOTO BUKTI SEC-04 TAHAP 1]** Screenshot verifikasi terminal yang menampilkan ekstensi SAN (`IP:192.168.56.101, DNS:localhost`) dan izin berkas key `600`.

### 6.2 Konfigurasi Server Block Nginx
Buat file virtual host Nginx untuk AbsensiKita:

```bash
sudo nano /etc/nginx/sites-available/absensikita
```
Masukkan blok konfigurasi Nginx berikut:
```nginx
# Server Block HTTP: Port 80 -> Redirect Permanen ke HTTPS 443
server {
    listen 80;
    listen [::]:80;
    server_name 192.168.56.101 localhost absensikita.test;
    return 301 https://$host$request_uri;
}

# Server Block HTTPS: Port 443 SSL
server {
    listen 443 ssl;
    listen [::]:443 ssl;
    server_name 192.168.56.101 localhost absensikita.test;

    root /var/www/absensikita/public;
    index index.php index.html;

    # Sertifikat dan Kunci TLS
    ssl_certificate /etc/ssl/certs/absensikita.crt;
    ssl_certificate_key /etc/ssl/private/absensikita.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Proteksi Akses Berkas Sensitif
    location ~ /\.(?!well-known) {
        deny all;
    }
    location ~* \.(sqlite|sqlite3|env|log|key)$ {
        deny all;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

Aktifkan situs dan nonaktifkan default Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/absensikita /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
sudo ss -ltnp | grep -E ':(80|443)'
```
*Pastikan output `nginx -t` adalah `syntax is ok` dan `test is successful`.*

### 6.3 Pengujian dari Laptop Klien (Windows)
Buka browser di laptop Anda dan akses:
👉 **`https://192.168.56.101`**

1. Klik tombol **Advanced** -> **Proceed to 192.168.56.101 (unsafe)** (karena sertifikat self-signed).
2. Klik ikon gembok di sebelah URL bar -> **Certificate details** -> periksa tab **Details / Subject Alternative Name**.
3. Uji pengalihan port 80: Buka `http://192.168.56.101` dan pastikan browser langsung otomatis dialihkan (*redirected*) ke `https://192.168.56.101`.
4. Buka **F12 (DevTools)** -> tab **Application / Storage** -> **Cookies** -> periksa cookie `absensikita_session`:
   - `Secure` = `True`
   - `HttpOnly` = `True`
   - `SameSite` = `Lax`

> **[AMBIL FOTO BUKTI SEC-04 TAHAP 2]** Screenshot halaman browser HTTPS yang menampilkan detail sertifikat SAN dan redirect port 80.  
> **[AMBIL FOTO BUKTI SEC-10 COOKIE]** Screenshot DevTools yang memperlihatkan flag cookie `Secure`, `HttpOnly`, dan `SameSite`.

---

## 7. Konfigurasi Firewall UFW Default-Deny (TKT-015 / SEC-06)

> [!WARNING]
> **PENTING — CEGAH TERKUNCI (LOCKOUT):**  
> Pastikan Anda telah membuat snapshot VM sebelum menjalankan `sudo ufw enable`. Jangan salah mengetik IP laptop klien (`192.168.56.1`).

### 7.1 Pemindaian Nmap SEBELUM Firewall Aktif
Dari laptop Windows Anda (PowerShell), lakukan pemindaian port awal ke VM:

```powershell
nmap -Pn -p 1-1000 192.168.56.101
```
*(Atau uji port spesifik:* `nmap -Pn -p 21,22,23,25,80,443,3306,8080 192.168.56.101`*).*

> **[AMBIL FOTO BUKTI SEC-06 SEBELUM]** Simpan tangkapan layar terminal Nmap Windows yang menampilkan port terbuka sebelum UFW aktif.

### 7.2 Konfigurasi dan Aktivasi UFW di VM Ubuntu
Di terminal VM Ubuntu sebagai `namila0095`:

```bash
whoami; date

# Atur kebijakan default
sudo ufw default deny incoming
sudo ufw default allow outgoing

# Izinkan SSH HANYA dari IP laptop host klien (192.168.56.1)
sudo ufw allow from 192.168.56.1 to any port 22 proto tcp comment 'SSH Client Laptop Only'

# Izinkan HTTPS untuk akses web aplikasi
sudo ufw allow 443/tcp comment 'HTTPS Public'

# Izinkan HTTP port 80 untuk redirect ke 443
sudo ufw allow 80/tcp comment 'HTTP Redirect'

# Aktifkan UFW (Ketik 'y' saat konfirmasi)
sudo ufw enable

# Tampilkan status firewall
sudo ufw status verbose
```

### 7.3 Pemindaian Nmap SESUDAH Firewall Aktif
Kembali ke **PowerShell Windows**, jalankan perintah pemindaian yang persis sama:

```powershell
nmap -Pn -p 1-1000 192.168.56.101
```
Uji port yang diblokir (misal port 8080):
```powershell
curl.exe --connect-timeout 3 http://192.168.56.101:8080/
```
*Hasil harus timeout / filtered.*

> **[AMBIL FOTO BUKTI SEC-06 SESUDAH]** Simpan tangkapan layar terminal Nmap Windows sesudah UFW aktif berdampingan dengan output `sudo ufw status verbose` dari terminal VM.

---

## 8. Backup Otomatis Terenkripsi dan Latihan Pemulihan Bencana (TKT-016 / SEC-07)

Sesuai **ADR-008**, penyalinan file SQLite langsung dengan perintah `cp` saat transaksi WAL aktif dapat mengakibatkan korupsi data. Kita wajib menggunakan utilitas **`VACUUM INTO`** yang menjamin snapshot database atomik dan konsisten.

### 8.1 Setup Password dan Skrip Backup
Buat password acak pelindung arsip backup di `/etc/absensikita_backup.pass`:

```bash
sudo sh -c 'openssl rand -base64 32 > /etc/absensikita_backup.pass'
sudo chmod 400 /etc/absensikita_backup.pass
sudo mkdir -p /var/backups/absensikita
sudo chmod 700 /var/backups/absensikita
```

Buat skrip backup otomatis `/usr/local/bin/backup_absensikita.sh`:
```bash
sudo nano /usr/local/bin/backup_absensikita.sh
```
Masukkan kode skrip berikut:
```bash
#!/bin/bash
set -e

BACKUP_DIR="/var/backups/absensikita"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DUMP_TMP="/tmp/absensikita_dump_${TIMESTAMP}.sqlite"
ARCHIVE="${BACKUP_DIR}/backup_${TIMESTAMP}.tar.gz.enc"
HASH_LOG="${BACKUP_DIR}/daftar_hash.txt"

# 1. Ekstraksi konsisten SQLite WAL via VACUUM INTO
sqlite3 /var/www/absensikita/database/database.sqlite "VACUUM INTO '${DUMP_TMP}';"

# 2. Kompresi dan Enkripsi menggunakan OpenSSL AES-256-CBC PBKDF2
tar -czf - -C /tmp "absensikita_dump_${TIMESTAMP}.sqlite" | \
    openssl enc -aes-256-cbc -pbkdf2 -salt -pass file:/etc/absensikita_backup.pass -out "${ARCHIVE}"

# 3. Hapus dump sementara di /tmp
rm -f "${DUMP_TMP}"

# 4. Hitung Checksum SHA-256 dan catat ke daftar_hash.txt
HASH=$(sha256sum "${ARCHIVE}" | awk '{print $1}')
echo "${TIMESTAMP} | $(basename ${ARCHIVE}) | SHA256: ${HASH}" >> "${HASH_LOG}"

# 5. Rotasi: Hapus backup yang lebih tua dari 7 hari
find "${BACKUP_DIR}" -name "backup_*.tar.gz.enc" -mtime +7 -delete

echo "[SUCCESS] Backup AbsensiKita berhasil: ${ARCHIVE}"
```

Beri izin eksekusi:
```bash
sudo chmod 700 /usr/local/bin/backup_absensikita.sh
```

### 8.2 Konfigurasi Penjadwalan Cron Harian
Daftarkan skrip ke cron job root:

```bash
sudo crontab -e
```
Tambahkan baris berikut di akhir file:
```cron
0 2 * * * /usr/local/bin/backup_absensikita.sh >> /var/log/absensikita_backup.log 2>&1
```
Verifikasi cron job terdaftar:
```bash
whoami; date
sudo crontab -l
```
> **[AMBIL FOTO BUKTI SEC-07 CRON]** Screenshot output `sudo crontab -l`.

---

### 8.3 Eksekusi Restore Drill 3 Tahap (Bukti Mutlak SEC-07)
Dosen mewajibkan bukti pemulihan data 3 kondisi nyata yang memulihkan database dalam waktu < 60 detik.

#### Tahap 1 — Kondisi Data Awal (Sebelum Bencana)
Periksa jumlah data absensi awal di database:

```bash
whoami; date
sqlite3 /var/www/absensikita/database/database.sqlite \
    "SELECT 'Jumlah Kelas:', count(*) FROM classes UNION ALL SELECT 'Jumlah Presensi:', count(*) FROM attendances;"
```
> **[AMBIL FOTO 1 — KONDISI DATA AWAL]** Screenshot terminal menampilkan jumlah data awal (misal: 2 kelas, 2 data kehadiran).

Jalankan skrip backup untuk menghasilkan arsip terenkripsi:
```bash
sudo /usr/local/bin/backup_absensikita.sh
ls -lh /var/backups/absensikita/
cat /var/backups/absensikita/daftar_hash.txt
```

Salin arsip backup terbaru ke folder laptop Windows via SCP sebagai salinan offsite terpisah:
```powershell
# Di PowerShell Windows
scp -i "$env:USERPROFILE\.ssh\id_ed25519_absensikita" namila0095@192.168.56.101:/var/backups/absensikita/backup_*.tar.gz.enc "$env:USERPROFILE\Downloads\"
Get-FileHash "$env:USERPROFILE\Downloads\backup_*.tar.gz.enc" -Algorithm SHA256
```

---

#### Tahap 2 — Kondisi Bencana (Simulasi Data Terhapus)
Simulasikan insiden kehilangan data fatal pada tabel presensi:

```bash
whoami; date
sqlite3 /var/www/absensikita/database/database.sqlite "DELETE FROM attendances;"
sqlite3 /var/www/absensikita/database/database.sqlite "SELECT 'Jumlah Presensi Sekarang:', count(*) FROM attendances;"
```
> **[AMBIL FOTO 2 — KONDISI DATA HILANG / TERHAPUS]** Screenshot terminal menampilkan `Jumlah Presensi Sekarang: 0` (membuktikan data telah terhapus).

---

#### Tahap 3 — Pemulihan Database dari Arsip Terenkripsi (Restore)
Jalankan proses dekripsi dan pemulihan:

```bash
whoami; date
# Catat waktu mulai restore
START_TIME=$(date +%s)

# Cari file backup terbaru
LATEST_BACKUP=$(ls -t /var/backups/absensikita/backup_*.tar.gz.enc | head -n1)

# Dekripsi dan ekstrak dump database
openssl enc -d -aes-256-cbc -pbkdf2 -pass file:/etc/absensikita_backup.pass -in "${LATEST_BACKUP}" | tar -xz -C /tmp/

# Hentikan sementara PHP-FPM agar tidak ada lock database
sudo systemctl stop php8.3-fpm

# Timpa kembali file database
DUMP_EXTRACTED=$(ls /tmp/absensikita_dump_*.sqlite | head -n1)
sudo cp "${DUMP_EXTRACTED}" /var/www/absensikita/database/database.sqlite

# Hapus file jurnal WAL lama jika ada
sudo rm -f /var/www/absensikita/database/database.sqlite-wal /var/www/absensikita/database/database.sqlite-shm /tmp/absensikita_dump_*.sqlite

# Kembalikan kepemilikan dan hak akses least privilege
sudo chown appsvc:appsvc /var/www/absensikita/database/database.sqlite
sudo chmod 660 /var/www/absensikita/database/database.sqlite

# Nyalakan kembali PHP-FPM
sudo systemctl start php8.3-fpm

END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))
echo "Pemulihan selesai dalam: ${DURATION} detik."

# Verifikasi integritas dan keutuhan data hasil restore
sqlite3 /var/www/absensikita/database/database.sqlite "PRAGMA integrity_check;"
sqlite3 /var/www/absensikita/database/database.sqlite \
    "SELECT 'Jumlah Kelas:', count(*) FROM classes UNION ALL SELECT 'Jumlah Presensi:', count(*) FROM attendances;"
```
> **[AMBIL FOTO 3 — KONDISI DATA BERHASIL PULIH]** Screenshot terminal menampilkan `PRAGMA integrity_check: ok`, jumlah presensi kembali seperti semula (misal: 2), dan waktu pemulihan tercatat < 60 detik.

---

## 9. Pembuktian Laboratorium Komparatif (SEC-08, SEC-09, SEC-10)

Untuk syarat 8, 9, dan 10, Anda wajib menyajikan perbandingan tangkapan layar antara **Aplikasi Rentan (Loopback Port 8001)** versus **Aplikasi Aman (HTTPS Port 443)**.

### 9.1 Menjalankan Modul Demo Rentan (TKT-011)
Di terminal VM Ubuntu (atau terminal terpisah):

```bash
cd /var/www/absensikita
bash scripts/run_vulnerable_demo.sh
```
*Server demo rentan berjalan di `127.0.0.1:8001` (hanya loopback, terisolasi dari internet/firewall).*

---

### 9.2 Uji SQL Injection (SEC-08) — Tiket TKT-018
- **Payload Uji:** `' OR '1'='1 --`

| Lingkungan Pengujian | URL Target & Tindakan | Hasil yang Diharapkan |
| :--- | :--- | :--- |
| **Versi Rentan** | Buka browser di VM: `http://127.0.0.1:8001/vulnerable/classes?search=' OR '1'='1 --` | **BOBOL:** Seluruh data kelas ditampilkan tanpa filter karena query menggunakan konkatenasi string SQL mentah. |
| **Versi Aman** | Buka browser: `https://192.168.56.101/classes?search=' OR '1'='1 --` | **AMAN:** Tidak ada data kelas yang keluar / data tidak ditemukan karena Eloquent menggunakan PDO Parameter Binding (karakter dianggap string literal biasa). |

> **[AMBIL FOTO BUKTI SEC-08]** Tangkapan layar berdampingan: Hasil rentan (menampilkan data) vs. Hasil aman (data kosong/0 hasil).

---

### 9.3 Uji Stored XSS & CSP (SEC-09) — Tiket TKT-019
- **Payload Uji:** `<script>alert("XSS-ATTENDANCE-PWNED")</script>`

| Lingkungan Pengujian | URL Target & Tindakan | Hasil yang Diharapkan |
| :--- | :--- | :--- |
| **Versi Rentan** | Masukkan payload pada form catatan izin di `http://127.0.0.1:8001/vulnerable/notes` | **BOBOL:** Browser langsung mengeksekusi script dan memunculkan pop-up kotak dialog `alert("XSS-ATTENDANCE-PWNED")` karena output menggunakan raw `{!! $note !!}`. |
| **Versi Aman** | Masukkan payload pada form catatan presensi di `https://192.168.56.101/attendances/1` | **AMAN:** Script TIDAK dieksekusi, melainkan tampil sebagai teks karakter biasa yang aman (`&lt;script&gt;...`) berkat escaping Blade `{{ $note }}` dan ditahan header CSP `default-src 'self'`. |

> **[AMBIL FOTO BUKTI SEC-09]** Tangkapan layar berdampingan: Pop-up alert pada versi rentan vs. Teks netral ter-escape pada versi aman.

---

### 9.4 Uji IDOR & Hak Akses Presensi (SEC-10) — Tiket TKT-020
- **Skenario:** Login sebagai mahasiswa `budi_santoso` (ID: 1), lalu coba akses riwayat presensi mahasiswa lain `siti_aminah` (ID: 2) secara langsung melalui URL.

| Lingkungan Pengujian | URL Target & Tindakan | Hasil yang Diharapkan |
| :--- | :--- | :--- |
| **Versi Rentan** | Login Budi, buka: `http://127.0.0.1:8001/vulnerable/attendances/2` | **BOBOL:** Budi dapat melihat detail absensi dan riwayat milik Siti tanpa verifikasi kepemilikan server-side. |
| **Versi Aman** | Login Budi, buka: `https://192.168.56.101/attendances/2` | **AMAN (403 FORBIDDEN):** Akses ditolak dengan halaman error kustom **403 Forbidden** berkat evaluasi `AttendancePolicy` di Laravel Gate. |

> **[AMBIL FOTO BUKTI SEC-10 IDOR]** Tangkapan layar berdampingan: Halaman data mahasiswa lain yang bocor di versi rentan vs. Halaman 403 Forbidden di versi aman.

---

## 10. Checklist Pengumpulan Tugas & Panduan Ujian (TKT-021 s/d TKT-023)

### Checklist Berkas Pengumpulan (Deliverables)

- [ ] **Laporan Akademik PDF (`TKT-021`):**
  - Format kertas A4, font 11–12 pt, isi maksimal 6 halaman + lampiran bukti maksimal 4 halaman.
  - Memuat identitas lengkap: Namila Tifani (NIM: 230170095), Tema: Absensi Kelas.
  - Memuat diagram arsitektur jaringan VM (`192.168.56.101`), Nginx, PHP-FPM `appsvc`, SQLite WAL.
  - Memuat 10 bukti syarat keamanan lengkap (dengan foto perbandingan syarat 6–10).
  - Menyertakan deklarasi bantuan AI/tools secara jujur dan akademis.
- [ ] **Video Demonstrasi (<= 5 Menit) (`TKT-022`):**
  - Narasi suara jelas memperkenalkan diri dan NIM.
  - Memperlihatkan `whoami; date` di terminal VM.
  - Demonstrasi login dosen & mahasiswa di browser HTTPS.
  - Demonstrasi uji perbandingan SQLi, Stored XSS, dan IDOR.
  - Menunjukkan status UFW dan eksekusi skrip restore drill.
  - Tidak menampilkan password asli atau private key.
- [ ] **Paket Repositori Git & ZIP (`TKT-023`):**
  - Minimal 10 commit Git bertahap yang rapi dan deskriptif.
  - Bersih dari file `.env`, file `.sqlite` aktif, dan file `node_modules` / `vendor`.

---

### Panduan Tanya Jawab Evaluasi Dosen (Cheatsheet)

Jika dosen penguji menanyakan konsep teknis implementasi Anda, gunakan jawaban baku berikut:

1. **Kenapa memakai AES-256-GCM, bukan AES-CBC bawaan Laravel?**  
   *Jawaban:* AES-GCM adalah mode *Authenticated Encryption with Associated Data* (AEAD). Selain menjamin kerahasiaan (*confidentiality*), GCM memiliki Authentication Tag 16 byte yang memvalidasi integritas data. Jika ciphertext dimanipulasi 1 bit saja atau AAD `student_id` tidak cocok, proses dekripsi langsung melempar error dan menolak data.
2. **Kenapa backup SQLite harus memakai `VACUUM INTO` dan bukan perintah `cp` biasa?**  
   *Jawaban:* Karena database berjalan dalam mode WAL (*Write-Ahead Logging*). Transaksi baru tersimpan sementara di file `-wal` sebelum di-checkpoint ke file utama. Menggunakan `cp` langsung saat transaksi aktif berisiko menghasilkan backup yang korup atau inkonsisten. `VACUUM INTO` adalah perintah atomik resmi SQLite yang mengunci transaksi secara aman dan menghasilkan salinan snapshot utuh.
3. **Bagaimana cara kerja mitigasi IDOR pada controller presensi?**  
   *Jawaban:* Menggunakan Laravel Policy server-side (`AttendancePolicy`). Saat rute `/attendances/{id}` diakses, Gate mengevaluasi apakah `auth()->user()->role === 'lecturer'` ATAU `attendance->student->user_id === auth()->id()`. Jika mahasiswa mencoba mengubah angka ID di URL ke presensi milik mahasiswa lain, sistem server langsung menghentikan request dan mengeluarkan HTTP 403 Forbidden.
4. **Mengapa akun layanan `appsvc` dibuat dengan shell `/usr/sbin/nologin`?**  
   *Jawaban:* Mengikuti prinsip keamanan *least privilege*. Akun layanan sistem hanya bertugas mengeksekusi worker proses PHP-FPM dan tidak boleh memiliki hak akses login interaktif melalui SSH maupun console untuk memperkecil risiko eskalasi hak akses (*privilege escalation*).

---

## 11. Matriks Penanganan Masalah Cepat (Troubleshooting)

| Gejala Error | Kemungkinan Penyebab | Tindakan Solusi |
| :--- | :--- | :--- |
| **HTTP 502 Bad Gateway di Nginx** | Service PHP-FPM mati atau permission socket salah. | Jalankan `sudo systemctl status php8.3-fpm`. Periksa `listen.owner` di `/etc/php/8.3/fpm/pool.d/www.conf` harus bernilai `www-data`. |
| **Database is locked (SQLite)** | Izin folder database tidak writable untuk `appsvc`. | Pastikan folder `database/` memiliki izin `775` dan file `database.sqlite` dimiliki oleh `appsvc:appsvc`. |
| **Koneksi SSH Timeout setelah UFW enable** | IP laptop klien berbeda dari aturan `192.168.56.1`. | Buka console VM di VirtualBox/VMware, login lokal, cek IP host Windows dengan `ipconfig`, lalu perbarui aturan: `sudo ufw allow from <IP_BARU> to any port 22 proto tcp`. |
| **Browser menampilkan "Invalid Certificate Authority"** | Menggunakan sertifikat self-signed. | Ini normal untuk lab lokal. Klik *Advanced* -> *Proceed*, atau import file `absensikita.crt` ke Trusted Root Certification Authorities di Windows Certificate Manager. |
| **Perintah `php artisan` gagal di terminal operator** | Kepemilikan file `.env` bernilai `600` milik `appsvc`. | Jalankan perintah artisan sistem menggunakan: `sudo -u appsvc php artisan <perintah>`. |

---
*Tutorial disusun khusus untuk Proyek AbsensiKita — S1 Teknik Informatika.*
