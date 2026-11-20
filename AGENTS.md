# Pedoman Agen Pengembang & Rekayasa Sistem (AGENTS.md)
## Sistem Informasi Absensi Kelas Aman (AbsensiKita)
### Tugas Pengganti UTS: Keamanan Sistem Komputer — S1 Teknik Informatika

---

> **Pemberitahuan Perpindahan Dokumen:**  
> File ini dirancang sebagai panduan instruksi utama untuk Agen AI atau asisten pengembang yang akan bekerja di lingkungan proyek Laravel. File ini berada di dalam vault dokumentasi Obsidian dan nantinya **akan dipindahkan sendiri oleh pengguna ke root direktori proyek Laravel**.  
> **JANGAN PERNAH menyalin atau memindahkan file ini secara otomatis ke workspace proyek.** Biarkan pengguna memindahkannya saat repositori proyek Laravel diinisialisasi.

---

## 1. Lokasi Dasar Dokumentasi Resmi Proyek

Karena file ini dirancang untuk dipindahkan ke luar vault Obsidian ke root proyek Laravel, **JANGAN MENGANDALKAN TAUTAN RELATIF MARKDOWN YANG AKAN RUSAK SETELAH FILE INI DIPINDAHKAN**. Gunakan path absolut berikut sebagai sumber kebenaran tunggal (*Single Source of Truth*):

- **Path Absolut Folder Dokumentasi Aktual:** `C:\obsidian\PRD-Absensi-Kelas\`
- **Path Dokumentasi Filesystem Alternatif:** `C:\Users\Administrator\Downloads\obsidian fani\obsidian\PRD-Absensi-Kelas\`
- **URL / Direktori Vault Obsidian:** `C:\obsidian`
- **Aturan Batasan Otorisasi Web:** `C:\obsidian\PRD-Absensi-Kelas\agent-workflow.md`
- **Identitas Mahasiswa Pengembang:** Namila Tifani (NIM: 230170095)
- **Tema Proyek (Digit NIM 5):** Absensi Kelas (*Secure Mini Class Attendance System* / **AbsensiKita**)
- **Tabel Utama:** `classes` (kelas), `students` (mahasiswa), `attendances` (kehadiran) didukung `users` dan `attendance_notes`
- **Data Sensitif Terenkripsi (Syarat 3):** Catatan izin / sakit mahasiswa (`medical_note_encrypted` / `sick_notes_encrypted`) via AES-256-GCM kustom
- **Data Anti-IDOR (Syarat 10):** Kehadiran / riwayat absensi mahasiswa lain (`/attendances/{id}`)
- **Identitas Operator Server VM:** `namila0095`

> [!CAUTION]
> **Protokol Kegagalan Akses Dokumentasi:**  
> Apabila folder dokumentasi di `C:\obsidian\PRD-Absensi-Kelas\` tidak dapat diakses oleh Agen AI karena perubahan path atau lingkungan mesin yang berbeda, **AGEN DILARANG KERAS MENGARANG PERSYARATAN (DO NOT HALLUCINATE REQUIREMENTS)**. Agen wajib segera berhenti dan meminta pengguna memberikan path direktori dokumentasi yang valid.

---

## 2. Status Tahapan & Batasan Otorisasi Mutlak Agen AI

> [!IMPORTANT]
> **Status Proyek Saat Ini:**  
> 1. Dokumen spesifikasi (PRD, Desain, ADR, Traceability, Evidence Plan) berstatus **BASELINE FINAL**.
> 2. Rencana kerja telah dipecah menjadi **23 Tiket Implementasi Linier** yang terdokumentasi lengkap di:  
>    `C:\obsidian\PRD-Absensi-Kelas\tickets\`
> 3. Alur kerja operasional agen telah diatur dalam:  
>    `C:\obsidian\PRD-Absensi-Kelas\agent-workflow.md`
> 4. Status tiket: 11 `TODO`, 0 `BLOCKED`, 0 `IN_PROGRESS`, 12 `DONE` (TKT-001-WEB s/d TKT-012-WEB selesai). **Tidak ada tiket yang terblokir (`DEC-OPEN-01` selesai: tema 100% Absensi Kelas sesuai digit 5).**

> [!CAUTION]
> **BATASAN MUTLAK AGEN AI: KHUSUS TIKET APLIKASI WEB (`*-WEB.md`)**  
> - **Agen AI HANYA DIIZINKAN** mengerjakan 12 tiket aplikasi web yang bertanda **`WEB`** (nama berkas berakhiran **`-WEB.md`**, yaitu `TKT-001-WEB` s/d `TKT-012-WEB`).  
> - **Agen AI DILARANG KERAS** mengeksekusi atau menyentuh tiket domain Infrastruktur Linux (`TKT-013` s/d `TKT-016`), pengujian lab interaktif (`TKT-017` s/d `TKT-020`), laporan PDF akademik (`TKT-021`), perekaman video demo (`TKT-022`), maupun rilis final audit (`TKT-023`). Domain Linux dan deliverable manual tersebut merupakan hak dan kewajiban langsung mahasiswa (`namila0095`) di server VM Ubuntu.  
> - Agen AI **hanya boleh mulai menulis kode jika diinstruksikan mengerjakan tiket `-WEB` spesifik**, dan **dilarang mengerjakan tiket berikutnya secara liar tanpa persetujuan pengguna**.

---

## 3. Peta Dokumen dan Protokol Urutan Pembacaan Agen

Sebelum agen melakukan tindakan implementasi apa pun, agen **WAJIB MEMBACA DOKUMEN SPESIFIKASI SECARA BERURUTAN** melalui path absolut dokumentasi:

1. **Langkah 1: Baca `C:\obsidian\PRD-Absensi-Kelas\README.md`**  
   $\rightarrow$ Memahami ringkasan eksekutif, status kesiapan, dan panduan navigasi.
2. **Langkah 2: Baca `C:\obsidian\PRD-Absensi-Kelas\agent-workflow.md` (Wajib)**  
   $\rightarrow$ Memahami 10 protokol operasional kerja agen, aturan pengujian, dan batasan eksekusi 12 tiket WEB.
3. **Langkah 3: Baca `C:\obsidian\PRD-Absensi-Kelas\tickets\README.md`**  
   $\rightarrow$ Memeriksa daftar 23 tiket, critical path linier, dependensi, dan status tiket.
4. **Langkah 4: Baca `C:\obsidian\PRD-Absensi-Kelas\PRD.md` (Wajib & Mendalam)**  
   $\rightarrow$ Memahami seluruh lingkup fungsional (FR), non-fungsional (NFR), model database (tabel `users`, `classes`, `students`, `attendances`, `attendance_notes`), presensi atomik 1x/hari, status idempoten, dan 10 syarat keamanan mutlak (SEC-01 s/d SEC-10).
5. **Langkah 5: Baca `C:\obsidian\PRD-Absensi-Kelas\decisions.md` (Wajib)**  
   $\rightarrow$ Memahami 9 Architecture Decision Records (ADR), terutama larangan Breeze/Jetstream, penggunaan SQLite WAL, arsitektur AES-256-GCM mandiri, dan batas 72 byte bcrypt.
6. **Langkah 6: Baca `C:\obsidian\PRD-Absensi-Kelas\design.md`**  
   $\rightarrow$ Memahami palet warna (penyesuaian WCAG AA), tata letak Blade mandiri, dan sintaks Blade Heroicons (`<x-heroicon-o-...>`).
7. **Langkah 7: Baca `C:\obsidian\PRD-Absensi-Kelas\traceability.md` & `C:\obsidian\PRD-Absensi-Kelas\evidence-plan.md`**  
   $\rightarrow$ Memahami matriks pengujian dan protokol pengambilan bukti 10 syarat keamanan.
8. **Langkah 8: Baca `C:\obsidian\PRD-Absensi-Kelas\references.md`**  
   $\rightarrow$ Memeriksa referensi resmi untuk syntax dan konfigurasi spesifik.

---

## 4. Cara Memilih Tiket dan Aturan Transisi Status

1. **Cara Memilih Tiket yang Siap Dikerjakan:**
   - Buka `C:\obsidian\PRD-Absensi-Kelas\tickets\README.md`.
   - **Pastikan tiket yang dipilih berakhiran `-WEB.md`** (Domain Rekayasa Web).
   - Pilih tiket yang tidak terblokir (`TODO`), di mana **seluruh tiket dependensi pendahulunya telah berstatus `DONE`**.
   - Tiket pertama yang siap dimulai adalah **`TKT-001-WEB`**.
2. **Aturan Transisi Status Tiket:**
   - `TODO` $\rightarrow$ `IN_PROGRESS`: Ubah status saat mulai mengerjakan scope tiket.
   - `IN_PROGRESS` $\rightarrow$ `BLOCKED`: Ubah status jika terdapat dependensi eksternal/blocker lingkungan.
   - `IN_PROGRESS` $\rightarrow$ `DONE`: **DILARANG MENANDAI DONE TANPA BUKTI NYATA**. Status `DONE` hanya boleh disematkan jika seluruh checklist *Acceptance Criteria* tercentang `[x]`, pengujian lolos, dan bagian *Catatan Implementasi & Hasil* telah diisi ringkasan kerja aktual.

---

## 5. Ringkasan Baseline Teknologi & Batasan Arsitektur

Agen wajib mematuhi batasan teknologi berikut tanpa deviasi:
- **Framework:** Laravel 12 (sesuai tutorial resmi).
- **Runtime:** PHP 8.3 dengan ekstensi OpenSSL AEAD dan SQLite3.
- **Basis Data:** SQLite (Mode WAL aktif untuk menangani transaksi presensi atomik).
- **Autentikasi:** Manual murni menggunakan `Auth::attempt()`, session guard, dan `Hash::make()`.
- **DILARANG:** Memasang Breeze, Jetstream, Fortify, Livewire, React, Vue, atau SPA.
- **Frontend & Ikon:** Blade server-rendered dengan Tailwind CSS via Vite dan satu paket ikon konsisten `blade-ui-kit/blade-heroicons`.
- **Infrastruktur Target:** Ubuntu Server 24.04 LTS (RAM 2 GB, Host-Only IP `192.168.56.101`), Nginx Reverse Proxy, PHP-FPM (User: `appsvc`), HTTPS Self-Signed dengan ekstensi SAN, Firewall UFW default-deny.
- **Isolasi Versi Rentan:** Port 8001 hanya loopback `127.0.0.1`, cookie terpisah (`absensikita_vuln_session`), database terpisah (`database/vulnerable.sqlite`), dilarang dipublikasikan ke internet atau dibuka di UFW.

---

## 6. Ringkasan 10 Syarat Keamanan Mutlak (SEC-01 – SEC-10)

1. **SEC-01 (Tabel Risiko):** 5 aset absensi kelas, 5 risiko CIA lengkap, kontrol merujuk syarat 2–10, kontrol korektif (Syarat 7 Backup), alasan risiko tertinggi (kerusakan database presensi) $\rightarrow$ [TKT-017](tickets/TKT-017-risk-assessment-table.md). *(Tugas Mahasiswa)*
2. **SEC-02 (Password Hashing):** Menggunakan Bcrypt bawaan Laravel, salt unik otomatis, validasi panjang `min:8|max:72` (menangani limit 72 byte bcrypt), verifikasi 2 akun password sama (`HadirAman#01!`) menghasilkan hash berbeda di DB $\rightarrow$ [TKT-005-WEB](tickets/TKT-005-manual-auth-password-hashing-WEB.md). *(Domain Agen AI)*
3. **SEC-03 (Enkripsi Catatan Izin/Sakit):** Wajib menggunakan **AES-256-GCM** (bukan AES-CBC default Laravel). Kunci 256-bit di `.env` (`APP_SICK_NOTE_KEY`). Nonce 12 byte acak baru tiap enkripsi. Versioned envelope `v1:<nonce>:<ct>:<tag>`. Tag 16 byte diverifikasi. AAD terikat ke `student_id`. Dekripsi hanya untuk mahasiswa bersangkutan dan dosen $\rightarrow$ [TKT-003-WEB](tickets/TKT-003-aes-256-gcm-encryption-WEB.md). *(Domain Agen AI)*
4. **SEC-04 (HTTPS Self-Signed):** Nginx port 443 dengan TLSv1.2/1.3, sertifikat OpenSSL memuat ekstensi SAN (`IP:192.168.56.101, DNS:localhost`), private key izin 600, redirect port 80 ke 443 $\rightarrow$ [TKT-013](tickets/TKT-013-self-signed-https-san-nginx.md). *(Tugas Mahasiswa - Linux)*
5. **SEC-05 (Hardening Linux):** Operator non-root `namila0095` (sudo), akun layanan `appsvc` (shell `/usr/sbin/nologin`), permission `.env` 600, source code read-only bagi worker, SSH `PermitRootLogin no` dan `MaxAuthTries 3`, apt upgrade, `APP_DEBUG=false` $\rightarrow$ [TKT-014](tickets/TKT-014-linux-hardening-least-privilege.md). *(Tugas Mahasiswa - Linux)*
6. **SEC-06 (Firewall UFW):** Default deny incoming, default allow outgoing. Port SSH 22 dibatasi ke IP host klien `192.168.56.1`. Port 443 HTTPS terbuka. Pemindaian Nmap sebelum vs sesudah $\rightarrow$ [TKT-015](tickets/TKT-015-ufw-firewall-default-deny-nmap.md). *(Tugas Mahasiswa - Linux)*
7. **SEC-07 (Backup & Restore Drill):** Skrip backup SQLite atomik via `VACUUM INTO` (mengatasi transaksi WAL), kompresi tar, enkripsi OpenSSL AES-256, hash SHA-256 dicatat di `daftar_hash.txt`, cron harian, salinan offsite terpisah. Uji restore membuktikan data pulih dalam waktu $< 60$ detik $\rightarrow$ [TKT-016](tickets/TKT-016-automated-backup-restore-drill.md). *(Tugas Mahasiswa - Linux)*
8. **SEC-08 (SQL Injection):** Versi rentan localhost dibobol dengan `' OR '1'='1 --`. Versi aman 100% prepared statements / parameter binding Eloquent pada pencarian kelas/mahasiswa. Payload identik gagal $\rightarrow$ [TKT-007-WEB](tickets/TKT-007-classes-students-search-WEB.md) & [TKT-018](tickets/TKT-018-sqli-comparative-evidence.md).
9. **SEC-09 (Stored XSS):** Versi rentan unescaped `{!! $note !!}` memunculkan alert. Versi aman Blade contextual escaping `{{ $note }}` menampilkan teks biasa, diperkuat header Content-Security-Policy (CSP) pada catatan izin/sakit $\rightarrow$ [TKT-010-WEB](tickets/TKT-010-attendance-notes-stored-xss-csp-WEB.md) & [TKT-019](tickets/TKT-019-stored-xss-comparative-evidence.md).
10. **SEC-10 (IDOR & Sesi):** Versi rentan `/attendances/{id}` bocor antar mahasiswa. Versi aman Policy server-side menolak akses user lain (403 Forbidden). Cookie berflag `HttpOnly=true`, `Secure=true`, `SameSite=Lax`. Sesi diregenerasi saat login, dibatalkan saat logout $\rightarrow$ [TKT-009-WEB](tickets/TKT-009-my-attendance-policy-session-cookies-WEB.md) & [TKT-020](tickets/TKT-020-idor-session-comparative-evidence.md).

---

## 7. Perlindungan Pekerjaan Pengguna dan Kebijakan Operasi Aman

1. **Larangan Operasi Destruktif:** Agen dilarang keras menjalankan perintah penghapusan massal (`rm -rf`, `DROP TABLE`, `git reset --hard`) tanpa izin eksplisit pengguna.
2. **Kerahasiaan Data & Kunci:** Jangan pernah memasukkan file `.env`, berkas private key `.key`, atau password asli ke dalam repositori git publik. Selalu verifikasi keberadaan `.gitignore`.
3. **Data Fiktif Murni:** Seluruh data yang diolah selama penulisan kode dan pengujian harus merupakan data fiktif (Nama: Budi Santoso, Siti Aminah; Alasan izin fiktif: Demam dan rawat jalan).
4. **Git Commit Bertahap:** Saat tahap implementasi dimulai, commit wajib dibuat secara bertahap (minimal 10 commit terpisah) dengan pesan commit yang deskriptif dan profesional sesuai rencana rilis [TKT-023](tickets/TKT-023-git-zip-final-submission-audit.md).
