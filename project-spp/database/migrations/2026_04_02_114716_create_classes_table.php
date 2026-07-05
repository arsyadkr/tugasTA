<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')
                ->constrained('majors')
                ->restrictOnDelete(); // Jurusan tidak bisa dihapus jika masih ada kelas
            $table->unsignedTinyInteger('grade'); // Tingkat kelas: 10, 11, 12
            $table->string('name'); // Nama kelas, contoh: X RPL 1
            $table->string('academic_year', 9); // Tahun ajaran, contoh: 2024/2025
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name', 'academic_year']); // Satu nama kelas tidak boleh duplikat dalam satu tahun ajaran  
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
