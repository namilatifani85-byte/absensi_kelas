<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceNote;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use App\Services\Security\AesGcmEncryptor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed basis data awal AbsensiKita (data fiktif sesuai aturan dosen & TKT-006-WEB).
     *
     * SEC-02:
     * - Dosen: admin_dosen / DosenAman#2026!
     * - Mahasiswa 1: budi_santoso (NIM: 230170001) / HadirAman#01!
     * - Mahasiswa 2: siti_aminah (NIM: 230170002) / HadirAman#01!
     * Kata sandi Budi dan Siti identik secara plaintext ('HadirAman#01!'),
     * tetapi hash Bcrypt di basis data unik dan berbeda satu sama lain karena salt otomatis.
     *
     * SEC-03:
     * - Catatan medis izin/sakit Budi dienkripsi AEAD AES-256-GCM terikat ke student_id Budi.
     */
    public function run(): void
    {
        // Inisialisasi Enkriptor AEAD AES-256-GCM (SEC-03)
        $enc = app(AesGcmEncryptor::class);

        // 1. Akun Dosen Pengampu (Fiktif)
        $lecturer = User::create([
            'username' => 'admin_dosen',
            'name'     => 'Dr. Ir. Hendra Gunawan, M.T.',
            'email'    => 'dosen@absensikita.local',
            'password' => Hash::make('DosenAman#2026!'),
            'role'     => 'lecturer',
        ]);

        // 2. Akun Mahasiswa 1 (Fiktif) - Password sama dengan Siti untuk verifikasi salt Bcrypt (SEC-02)
        $userBudi = User::create([
            'username' => 'budi_santoso',
            'name'     => 'Budi Santoso',
            'email'    => 'budi@absensikita.local',
            'password' => Hash::make('HadirAman#01!'),
            'role'     => 'student',
        ]);

        $studBudi = Student::create([
            'user_id'       => $userBudi->id,
            'nim'           => '230170001',
            'full_name'     => 'Budi Santoso',
            'study_program' => 'Teknik Informatika',
            'class_year'    => '2023',
        ]);

        // 3. Akun Mahasiswa 2 (Fiktif) - Password sama dengan Budi untuk verifikasi salt Bcrypt (SEC-02)
        $userSiti = User::create([
            'username' => 'siti_aminah',
            'name'     => 'Siti Aminah',
            'email'    => 'siti@absensikita.local',
            'password' => Hash::make('HadirAman#01!'),
            'role'     => 'student',
        ]);

        $studSiti = Student::create([
            'user_id'       => $userSiti->id,
            'nim'           => '230170002',
            'full_name'     => 'Siti Aminah',
            'study_program' => 'Teknik Informatika',
            'class_year'    => '2023',
        ]);

        // 4. Kelas Perkuliahan (Fiktif)
        $class1 = ClassModel::create([
            'code'        => 'TIF-301',
            'name'        => 'Keamanan Sistem Komputer',
            'lecturer_id' => $lecturer->id,
            'room'        => 'Lab Komputer 2',
            'day'         => 'Kamis',
            'time_start'  => '08:00',
            'time_end'    => '10:30',
            'is_active'   => true,
        ]);

        $class2 = ClassModel::create([
            'code'        => 'TIF-204',
            'name'        => 'Jaringan Komputer',
            'lecturer_id' => $lecturer->id,
            'room'        => 'Ruang Teori 3',
            'day'         => 'Senin',
            'time_start'  => '13:00',
            'time_end'    => '15:30',
            'is_active'   => true,
        ]);

        // 5. Presensi Awal dengan Catatan Medis Terenkripsi AES-256-GCM (SEC-03)
        $today = date('Y-m-d');

        // Budi: Sakit dengan catatan medis sensitif terenkripsi
        $attendanceBudi = Attendance::create([
            'class_id'               => $class1->id,
            'student_id'             => $studBudi->id,
            'attendance_date'        => $today,
            'status'                 => 'sick',
            'excuse_notes'           => 'Demam dan flu berat',
            'medical_note_encrypted' => $enc->encrypt(
                'Demam 38.5C perlu istirahat 2 hari sesuai surat dokter klinik kampus',
                (string) $studBudi->id
            ),
            'checked_in_at'          => now(),
            'verified_by'            => $lecturer->id,
        ]);

        // Catatan teks bebas untuk presensi Budi (uji Stored XSS SEC-09)
        AttendanceNote::create([
            'attendance_id' => $attendanceBudi->id,
            'author_id'     => $lecturer->id,
            'notes'         => 'Surat keterangan sakit dari klinik kampus telah diverifikasi sah oleh dosen pengampu.',
        ]);

        // Siti: Hadir
        Attendance::create([
            'class_id'               => $class1->id,
            'student_id'             => $studSiti->id,
            'attendance_date'        => $today,
            'status'                 => 'present',
            'excuse_notes'           => null,
            'medical_note_encrypted' => null,
            'checked_in_at'          => now(),
            'verified_by'            => $lecturer->id,
        ]);

        $this->command->info('✅ Seeder AbsensiKita selesai:');
        $this->command->info('   - Dosen: admin_dosen / DosenAman#2026!');
        $this->command->info('   - Mahasiswa 1: budi_santoso (NIM: 230170001) / HadirAman#01!');
        $this->command->info('   - Mahasiswa 2: siti_aminah (NIM: 230170002) / HadirAman#01!');
        $this->command->info('   - Kelas: TIF-301 & TIF-204');
        $this->command->info('   - Presensi: Budi (Sakit, Terenkripsi AES-256-GCM) & Siti (Hadir)');
        $this->command->info('   - SEC-02: Hash Bcrypt Budi & Siti berbeda meski password sama.');
    }
}
