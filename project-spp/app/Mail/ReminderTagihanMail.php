<?php

namespace App\Mail;

use App\Models\Bill;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderTagihanMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Student $student,
        public readonly Bill    $bill,
        public readonly int     $daysLeft,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reminder: Tagihan SPP {$this->bill->period_label} – {$this->student->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reminder-tagihan',
        );
    }
}
