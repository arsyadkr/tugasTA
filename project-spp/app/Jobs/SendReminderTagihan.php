<?php

namespace App\Jobs;

use App\Mail\ReminderTagihanMail;
use App\Models\Bill;
use App\Models\ReminderLog;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendReminderTagihan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly Bill $bill,
        public readonly int  $daysLeft,
    ) {}

    public function handle(WhatsAppService $wa): void
    {
        $bill    = $this->bill;
        $student = $bill->student->loadMissing(['schoolClass', 'major']);

        // ── Kirim WhatsApp ke orang tua ───────────────────────────────────────
        if (! empty($student->parent_phone)) {
            $this->sendWhatsApp($wa, $student, $bill);
        }

        // ── Kirim Email ke orang tua ──────────────────────────────────────────
        if (! empty($student->parent_email)) {
            $this->sendEmail($student, $bill);
        }
    }

    private function sendWhatsApp(WhatsAppService $wa, $student, Bill $bill): void
    {
        // Cek sudah pernah kirim WA untuk bill ini
        $alreadySent = ReminderLog::where('bill_id', $bill->id)
            ->where('channel', 'whatsapp')
            ->where('status', 'sent')
            ->exists();

        if ($alreadySent) {
            Log::info("WA reminder already sent for bill #{$bill->id}");
            return;
        }

        $message = $this->buildWhatsAppMessage($student, $bill);

        $success = $wa->send($student->parent_phone, $message);

        ReminderLog::create([
            'bill_id'    => $bill->id,
            'student_id' => $student->id,
            'channel'    => 'whatsapp',
            'status'     => $success ? 'sent' : 'failed',
        ]);
    }

    private function sendEmail($student, Bill $bill): void
    {
        // Cek sudah pernah kirim email untuk bill ini
        $alreadySent = ReminderLog::where('bill_id', $bill->id)
            ->where('channel', 'email')
            ->where('status', 'sent')
            ->exists();

        if ($alreadySent) {
            Log::info("Email reminder already sent for bill #{$bill->id}");
            return;
        }

        try {
            Mail::to($student->parent_email)
                ->send(new ReminderTagihanMail($student, $bill, $this->daysLeft));

            ReminderLog::create([
                'bill_id'    => $bill->id,
                'student_id' => $student->id,
                'channel'    => 'email',
                'status'     => 'sent',
            ]);

            Log::info("Email reminder sent for bill #{$bill->id} to {$student->parent_email}");
        } catch (Throwable $e) {
            ReminderLog::create([
                'bill_id'       => $bill->id,
                'student_id'    => $student->id,
                'channel'       => 'email',
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error("Email reminder failed for bill #{$bill->id}", [
                'error' => $e->getMessage()
            ]);
        }
    }

    private function buildWhatsAppMessage($student, Bill $bill): string
    {
        $daysText = $this->daysLeft === 7 ? '7 hari lagi' : "{$this->daysLeft} hari lagi";
        $dueDate  = $bill->due_date->translatedFormat('d F Y');
        $amount   = 'Rp ' . number_format($bill->amount, 0, ',', '.');

        return "🔔 *REMINDER TAGIHAN SPP*\n"
            . "SMK Muhammadiyah 2 Tangerang\n\n"
            . "Yth. Bapak/Ibu Orang Tua/Wali murid dari:\n"
            . "*{$student->name}* ({$student->nis})\n"
            . "Kelas: {$student->schoolClass->name}\n\n"
            . "━━━━━━━━━━━━━━━━━━\n"
            . "📋 *Detail Tagihan:*\n"
            . "Periode  : {$bill->period_label}\n"
            . "Nominal  : *{$amount}*\n"
            . "Jatuh Tempo: *{$dueDate}*\n"
            . "Status   : ⏳ {$daysText}\n"
            . "━━━━━━━━━━━━━━━━━━\n\n"
            . "Mohon segera melakukan pembayaran melalui portal siswa:\n"
            . config('app.url') . "/login/siswa\n\n"
            . "Login dengan NIS: *{$student->nis}*\n"
            . "Metode bayar: QRIS, GoPay, OVO, DANA, Transfer Bank, Kartu Kredit\n\n"
            . "_Jika sudah membayar, abaikan pesan ini_ 🙏";
    }

    public function failed(Throwable $e): void
    {
        Log::error("SendReminderTagihan job failed for bill #{$this->bill->id}", [
            'error' => $e->getMessage()
        ]);
    }
}
