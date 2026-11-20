<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel attendance_notes (catatan kehadiran teks bebas untuk pengujian Stored XSS SEC-09).
     */
    public function up(): void
    {
        Schema::create('attendance_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')
                  ->constrained('attendances')
                  ->cascadeOnDelete()
                  ->comment('FK ke attendances, CASCADE delete');
            $table->foreignId('author_id')
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->comment('Penulis catatan; FK ke users');
            $table->text('notes')->comment('Isi catatan teks bebas (fitur uji Stored XSS)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_notes');
    }
};
