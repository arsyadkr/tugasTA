<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\ReminderLog;
use App\Jobs\SendReminderTagihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReminderController extends Controller
{
    // List log reminder
    public function index(Request $request): View
    {
        $logs = ReminderLog::with(['student', 'bill'])
            ->latest()
            ->paginate(20);

        // Statistik
        $stats = [
            'total'   => ReminderLog::count(),
            'sent'    => ReminderLog::where('status', 'sent')->count(),
            'failed'  => ReminderLog::where('status', 'failed')->count(),
            'wa'      => ReminderLog::where('channel', 'whatsapp')->where('status', 'sent')->count(),
            'email'   => ReminderLog::where('channel', 'email')->where('status', 'sent')->count(),
        ];

        // Tagihan yang akan jatuh tempo 7 hari lagi
        $upcoming = Bill::with(['student'])
            ->whereNotIn('status', ['paid'])
            ->whereBetween('due_date', [now(), now()->addDays(7)])
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        return view('admin.reminders.index', compact('logs', 'stats', 'upcoming'));
    }

    // Kirim reminder manual untuk satu tagihan
    public function send(Bill $bill): RedirectResponse
    {
        if ($bill->status === 'paid') {
            return back()->with('error', 'Tagihan ini sudah lunas, tidak perlu reminder.');
        }

        $daysLeft = (int) now()->diffInDays($bill->due_date, false);

        SendReminderTagihan::dispatch($bill, max($daysLeft, 0));

        return back()->with('success', "Reminder untuk tagihan {$bill->period_label} – {$bill->student->name} berhasil dikirim.");
    }

    // Kirim reminder massal untuk semua tagihan mendekati jatuh tempo
    public function sendBulk(Request $request): RedirectResponse
    {
        $days = (int) $request->get('days', 7);

        $bills = Bill::with(['student'])
            ->whereNotIn('status', ['paid'])
            ->whereDate('due_date', now()->addDays($days)->toDateString())
            ->whereHas('student', fn($q) => $q->where(function ($q2) {
                $q2->whereNotNull('parent_phone')->orWhereNotNull('parent_email');
            }))
            ->get();

        foreach ($bills as $bill) {
            SendReminderTagihan::dispatch($bill, $days);
        }

        return back()->with('success', "{$bills->count()} reminder berhasil dikirim ke queue.");
    }
}
