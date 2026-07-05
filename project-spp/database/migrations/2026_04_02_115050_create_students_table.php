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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            // One-to-one dengan users — satu akun hanya untuk satu siswa
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete(); // Hapus user = hapus data siswa   
            $table->foreignId('class_id')
                ->constrained('classes')
                ->restrictOnDelete(); // Kelas tidak bisa dihapus jika masih ada siswa
            $table->foreignId('major_id')
                ->constrained('majors')
                ->restrictOnDelete();
            // NIS unik — sinkron nilainya dengan users.username (enforce di aplikasi)  
            $table->string('nis', 20)->unique();
            $table->string('name');
            $table->enum('gender', ['L', 'P']);
            $table->string('phone', 15)->nullable();
            $table->text('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            // Foto profil opsional
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student');
    }
};
