<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = $user->student;

        $payments = Payment::with('bill')
            ->where('student_id', $student->id)
            ->whereIn('status', [Payment::STATUS_SETTLEMENT, Payment::STATUS_CAPTURE])
            ->latest('paid_at')
            ->paginate(15);

        $totalDibayar = Payment::where('student_id', $student->id)
            ->whereIn('status', [Payment::STATUS_SETTLEMENT, Payment::STATUS_CAPTURE])
            ->sum('amount');

        return view('siswa.riwayat.index', compact('payments', 'student', 'totalDibayar'));
    }
}
