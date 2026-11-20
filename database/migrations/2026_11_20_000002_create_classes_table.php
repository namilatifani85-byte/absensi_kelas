<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel classes (data kelas perkuliahan).
     * lecturer_id: FK ke users (role lecturer) dengan RESTRICT delete.
     */
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Kode kelas unik, misal: TIF-301');
            $table->string('name', 100)->comment('Nama mata kuliah / kelas');
            $table->foreignId('lecturer_id')
                  ->constrained('users')
                  ->restrictOnDelete()
                  ->comment('Dosen pengampu; FK ke users');
            $table->string('room', 50)->default('Lab Komputer 2')->comment('Ruang perkuliahan');
            $table->string('day', 20)->default('Kamis')->comment('Hari perkuliahan');
            $table->time('time_start')->default('08:00')->comment('Jam mulai kelas');
            $table->time('time_end')->default('10:30')->comment('Jam selesai kelas');
            $table->boolean('is_active')->default(true)->comment('Status keaktifan kelas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
