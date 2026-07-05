<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $student = $user->student()->with([
            'schoolClass:id,name,academic_year',
            'major:id,name,code',
            'bills' => function ($query) {
                $query->select('id', 'student_id', 'amount', 'status', 'month', 'year', 'due_date')
                    ->orderBy('year')->orderBy('month');
            },
            'bills.successfulPayment',
        ])->firstOrFail();

        $bills = $student->bills;

        // ── Kalkulasi ────────────────────────────────────────────────────────
        $totalTagihanNominal = $bills->sum('amount');

        $totalDibayar = $bills->sum(function ($bill) {
            return optional($bill->successfulPayment)->amount ?? 0;
        });

        $sisaTagihan = $totalTagihanNominal - $totalDibayar;

        $persentase = $totalTagihanNominal > 0
            ? (int) round(($totalDibayar / $totalTagihanNominal) * 100)
            : 0;

        // ── Partisi ──────────────────────────────────────────────────────────
        $tagihanBelumLunas = $bills->whereIn('status', ['unpaid', 'pending', 'overdue']);
        $tagihanLunas      = $bills->where('status', 'paid');
        $tagihanOverdue    = $bills->where('status', 'overdue');

        // ── Riwayat bayar (5 terakhir) untuk panel kanan ────────────────────
        $riwayatBayar = Payment::with('bill:id,month,year')
            ->where('student_id', $student->id)
            ->whereIn('status', [Payment::STATUS_SETTLEMENT, Payment::STATUS_CAPTURE])
            ->latest('paid_at')
            ->limit(5)
            ->get();

        return view('siswa.dashboard', compact(
            'student',
            'bills',
            'totalTagihanNominal',
            'totalDibayar',
            'sisaTagihan',
            'persentase',
            'tagihanBelumLunas',
            'tagihanLunas',
            'tagihanOverdue',
            'riwayatBayar',
        ));
    }
}
