<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Nomor WA orang tua (format: 628xxxxxxxxxx)
            $table->string('parent_name')->nullable()->after('address');
            $table->string('parent_phone')->nullable()->after('parent_name');
            $table->string('parent_email')->nullable()->after('parent_phone');
        });

        // Tabel log reminder agar tidak kirim dobel
        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('bills')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('channel', ['whatsapp', 'email']);
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Cegah kirim dobel ke channel yang sama untuk bill yang sama
            $table->unique(['bill_id', 'channel'], 'reminder_logs_bill_channel_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['parent_name', 'parent_phone', 'parent_email']);
        });
    }
};
