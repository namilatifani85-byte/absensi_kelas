<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel students (relasi 1:1 dengan users yang memiliki role student).
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->comment('FK 1:1 ke users, CASCADE delete');
            $table->string('nim', 20)->unique()->comment('Nomor Induk Mahasiswa unik');
            $table->string('full_name', 100)->comment('Nama lengkap mahasiswa');
            $table->string('study_program', 100)->default('Teknik Informatika')->comment('Program studi');
            $table->string('class_year', 4)->default('2023')->comment('Tahun angkatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
