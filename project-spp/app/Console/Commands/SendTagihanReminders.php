<?php

namespace App\Console\Commands;

use App\Jobs\SendReminderTagihan;
use App\Models\Bill;
use Illuminate\Console\Command;

class SendTagihanReminders extends Command
{
    protected $signature   = 'spp:send-reminders {--days=7 : Hari sebelum jatuh tempo}';
    protected $description = 'Kirim reminder tagihan SPP ke orang tua siswa via WhatsApp dan Email';

    public function handle(): int
    {
        $days    = (int) $this->option('days');
        $dueDate = now()->addDays($days)->toDateString();

        $this->info("Mencari tagihan yang jatuh tempo pada: {$dueDate} ({$days} hari lagi)...");

        // Ambil tagihan yang:
        // 1. Belum lunas (status unpaid/overdue/pending)
        // 2. Jatuh tempo tepat N hari dari sekarang
        // 3. Orang tua punya nomor WA atau email
        $bills = Bill::with(['student.schoolClass'])
            ->whereNotIn('status', ['paid'])
            ->whereDate('due_date', $dueDate)
            ->whereHas('student', function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('parent_phone')
                        ->orWhereNotNull('parent_email');
                });
            })
            ->get();

        if ($bills->isEmpty()) {
            $this->info('Tidak ada tagihan yang perlu diingatkan hari ini.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$bills->count()} tagihan. Mengirim reminder...");

        $bar = $this->output->createProgressBar($bills->count());
        $bar->start();

        foreach ($bills as $bill) {
            // Dispatch ke queue agar tidak blocking
            SendReminderTagihan::dispatch($bill, $days);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ {$bills->count()} reminder berhasil di-dispatch ke queue.");

        return self::SUCCESS;
    }
}
