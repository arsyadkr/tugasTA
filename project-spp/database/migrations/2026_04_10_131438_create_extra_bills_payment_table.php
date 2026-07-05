<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel tagihan pembayaran non-SPP
        // Kunjungan Industri → kelas 10
        // GTS (Go To School)  → kelas 11
        // PKL                 → kelas 12
        Schema::create('extra_bills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // Jenis pembayaran
            $table->enum('type', ['kunjungan_industri', 'gts', 'pkl']);

            $table->string('title'); // Label tampilan, misal "Kunjungan Industri 2025"
            $table->unsignedInteger('amount');
            $table->enum('status', ['unpaid', 'pending', 'paid', 'overdue'])->default('unpaid');
            $table->date('due_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Satu siswa tidak boleh punya tagihan duplikat per jenis
            $table->unique(['student_id', 'type']);
        });

        // Tabel transaksi pembayaran non-SPP
        Schema::create('extra_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('extra_bill_id')
                ->constrained('extra_bills')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->string('order_id')->unique();
            $table->string('snap_token')->nullable();
            $table->unsignedInteger('amount');

            $table->enum('status', [
                'pending',
                'settlement',
                'capture',
                'deny',
                'cancel',
                'expire',
                'failure',
            ])->default('pending');

            $table->string('payment_type')->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('midtrans_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_payments');
        Schema::dropIfExists('extra_bills');
    }
};
