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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // Relasi ke tagihan — bills:payments = 1:N
            // Satu tagihan bisa punya banyak attempt pembayaran (gagal → coba lagi)
            $table->foreignId('bill_id')
                ->constrained('bills')
                ->cascadeOnDelete();
            // Redundan untuk kemudahan query tanpa JOIN ke bills
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();
            // Order ID unik yang dikirim ke Midtrans
            // Format: SPP-{student_id}-{bill_id}-{timestamp}
            $table->string('order_id')->unique();
            // Token dari Midtrans Snap API
            $table->string('snap_token')->nullable();
            // Nominal yang dibayarkan (bisa berbeda jika ada biaya admin)
            $table->unsignedInteger('amount');
            // Status mengikuti status Midtrans
            $table->enum('status', [
                'pending',
                'settlement', // Berhasil (transfer, QRIS, VA)
                'capture',    // Berhasil (kartu kredit)
                'deny',
                'cancel',
                'expire',
                'failure',
            ])->default('pending');
            // Metode pembayaran yang dipilih: gopay, qris, bca_va, bni_va, dll
            $table->string('payment_type')->nullable();
            // Transaction ID dari Midtrans (berbeda dengan order_id)
            $table->string('transaction_id')->nullable();
            // Raw response dari Midtrans disimpan untuk audit & debugging
            $table->json('midtrans_response')->nullable();
            // Waktu pembayaran berhasil dikonfirmasi
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // Indeks untuk mempercepat query berdasarkan student_id dan bill_id
            $table->index(['student_id', 'status']);
            $table->index(['bill_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
