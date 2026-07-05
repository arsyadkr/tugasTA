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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();
            // Bulan tagihan: 1–12
            $table->unsignedTinyInteger('month');
            // Tahun tagihan: contoh 2025
            $table->unsignedSmallInteger('year');
            // Nominal SPP
            $table->unsignedInteger('amount');
            $table->enum('status', ['unpaid', 'pending', 'paid', 'overdue'])->default('unpaid');
            // Batas tanggal bayar
            $table->date('due_date');
            // Catatan dari admin jika ada
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // Satu siswa tidak boleh punya tagihan duplikat per bulan per tahun
            $table->unique(['student_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
