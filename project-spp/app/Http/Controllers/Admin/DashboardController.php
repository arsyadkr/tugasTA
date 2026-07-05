<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ── Stat Cards ───────────────────────────────────────────────────────
        $totalSiswa       = Student::count();
        $totalTagihan     = Bill::count();
        $tagihanLunas     = Bill::where('status', Bill::STATUS_PAID)->count();
        $tagihanBelumLunas = Bill::whereIn('status', [
            Bill::STATUS_UNPAID,
            Bill::STATUS_PENDING,
            Bill::STATUS_OVERDUE,
        ])->count();

        // ── Siswa terbaru (5 terakhir ditambahkan) ───────────────────────────
        $siswaTerbaru = Student::with([
            'user:id,must_change_password',
            'schoolClass:id,name',
        ])
            ->withCount('bills')
            ->latest()
            ->limit(5)
            ->get();

        // ── Rekap jumlah siswa per kelas ─────────────────────────────────────
        $rekapKelas = SchoolClass::withCount('students')
            ->where('is_active', true)
            ->orderByDesc('students_count')
            ->get();

        // ── Pembayaran terbaru (5 terakhir settlement) ───────────────────────
        $pembayaranTerbaru = Payment::with([
            'student:id,name',
            'bill:id,month,year',
        ])
            ->whereIn('status', [Payment::STATUS_SETTLEMENT, Payment::STATUS_CAPTURE])
            ->latest('paid_at')
            ->limit(5)
            ->get();

        // ── Pemasukan bulanan (sum amount per bulan tahun ini) ───────────────
        $pemasukanBulanan = Payment::selectRaw('MONTH(paid_at) as month, SUM(amount) as total')
            ->whereIn('status', [Payment::STATUS_SETTLEMENT, Payment::STATUS_CAPTURE])
            ->whereYear('paid_at', now()->year)
            ->groupByRaw('MONTH(paid_at)')
            ->orderByRaw('MONTH(paid_at)')
            ->get();

        return view('admin.dashboard', compact(
            'totalSiswa',
            'totalTagihan',
            'tagihanLunas',
            'tagihanBelumLunas',
            'siswaTerbaru',
            'rekapKelas',
            'pembayaranTerbaru',
            'pemasukanBulanan',
        ));
    }
}
