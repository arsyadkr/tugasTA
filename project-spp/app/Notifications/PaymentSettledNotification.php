<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentSettledNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Payment $payment
    ) {}

    /**
     * Channel yang dipakai: database
     * Bisa ditambah 'mail' atau 'broadcast' di sini nanti
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Data yang disimpan ke tabel notifications.
     * Diakses via: $notification->data['key']
     */
    public function toDatabase(object $notifiable): array
    {
        $bill    = $this->payment->bill;
        $student = $bill->student;

        return [
            'payment_id'   => $this->payment->id,
            'order_id'     => $this->payment->order_id,
            'student_id'   => $student->id,
            'student_name' => $student->name,
            'student_nis'  => $student->nis,
            'period'       => $bill->period_label,
            'amount'       => $this->payment->amount,
            'payment_type' => $this->payment->payment_type_label,
            'paid_at'      => $this->payment->paid_at?->toIso8601String(),
            'message'      => "{$student->name} telah membayar SPP {$bill->period_label} sebesar Rp " .
                number_format($this->payment->amount, 0, ',', '.'),
        ];
    }
}
