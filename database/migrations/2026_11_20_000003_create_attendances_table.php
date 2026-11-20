<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel attendances (presensi kehadiran mahasiswa).
     * SEC-03: medical_note_encrypted menyimpan catatan sensitif terenkripsi AES-256-GCM.
     * Constraint unik composite: (class_id, student_id, attendance_date) anti-dobel presensi.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')
                  ->constrained('classes')
                  ->cascadeOnDelete()
                  ->comment('FK ke classes, CASCADE delete');
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->cascadeOnDelete()
                  ->comment('FK ke students, CASCADE delete');
            $table->date('attendance_date')->comment('Tanggal presensi (YYYY-MM-DD)');
            $table->string('status', 20)->default('present')->comment('present, sick, permit, absent');
            $table->text('excuse_notes')->nullable()->comment('Keterangan alasan izin/sakit teks bebas');
            $table->text('medical_note_encrypted')->nullable()->comment('Enkripsi AES-256-GCM format v1:<nonce>:<ct>:<tag>');
            $table->timestamp('checked_in_at')->nullable()->comment('Waktu presensi dicatat');
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Dosen yang memverifikasi kehadiran');
            $table->timestamps();

            // Constraint unik composite mencegah mahasiswa absen ganda pada tanggal dan kelas yang sama
            $table->unique(['class_id', 'student_id', 'attendance_date'], 'uq_attendances_class_student_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
