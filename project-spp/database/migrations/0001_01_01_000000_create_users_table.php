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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Username diisi NIS untuk siswa, nama unik untuk admin
            $table->string('username')->unique()->nullable();
            // Email hanya wajib untuk admin, nullable untuk siswa
            $table->string('email')->unique()->nullable();
            $table->string('name');
            $table->string('password');
            // Role menggunakan enum: admin atau student
            $table->enum('role', ['admin', 'student'])->default('student');
            // Flag force ganti password setelah login pertama
            $table->boolean('must_change_password')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Agar data tidak hilang permanen saat dihapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
