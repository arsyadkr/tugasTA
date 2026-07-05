<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\Payment;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $year  = $request->get('year', now()->year);

        $query = Payment::with(['student.schoolClass', 'student.major', 'bill'])
            ->whereIn('status', [Payment::STATUS_SETTLEMENT, Payment::STATUS_CAPTURE])
            ->whereYear('paid_at', $year);

        if ($request->filled('major_id')) {
            $query->whereHas('student', fn($q) => $q->where('major_id', $request->major_id));
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }

        $payments = $query->orderBy('paid_at')->get();

        $rekapBulanan = $payments
            ->groupBy(fn($p) => $p->paid_at->month)
            ->map(fn($g) => ['count' => $g->count(), 'total' => $g->sum('amount')]);

        $majors  = Major::where('is_active', true)->get();
        $classes = SchoolClass::where('is_active', true)->get();

        return view('admin.laporan.index', compact('payments', 'rekapBulanan', 'majors', 'classes', 'year'));
    }

    public function export(Request $request): Response
    {
        $year  = $request->get('year', now()->year);

        $query = Payment::with(['student.schoolClass', 'student.major', 'bill'])
            ->whereIn('status', [Payment::STATUS_SETTLEMENT, Payment::STATUS_CAPTURE])
            ->whereYear('paid_at', $year);

        if ($request->filled('major_id')) {
            $query->whereHas('student', fn($q) => $q->where('major_id', $request->major_id));
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }

        $payments = $query->orderBy('paid_at')->get();
        $filename = 'laporan-spp-' . $year . '-' . now()->format('Ymd') . '.csv';

        $handle = fopen('php://temp', 'r+');
        fputs($handle, "\xEF\xBB\xBF"); // BOM UTF-8 agar Excel bisa baca

        fputcsv($handle, ['No', 'Tanggal Bayar', 'NIS', 'Nama Siswa', 'Kelas', 'Jurusan', 'Periode', 'Metode', 'Nominal (Rp)']);

        foreach ($payments as $i => $p) {
            fputcsv($handle, [
                $i + 1,
                $p->paid_at?->format('d/m/Y H:i'),
                $p->student->nis ?? '-',
                $p->student->name ?? '-',
                $p->student->schoolClass->name ?? '-',
                $p->student->major->name ?? '-',
                ($p->bill->month_label ?? '') . ' ' . ($p->bill->year ?? ''),
                $p->payment_type_label,
                $p->amount,
            ]);
        }

        fputcsv($handle, ['', '', '', '', '', '', '', 'TOTAL', $payments->sum('amount')]);

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
