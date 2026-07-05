<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\Payment;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payment::query()
            ->with(['student.schoolClass', 'student.major', 'bill'])
            ->whereIn('status', [Payment::STATUS_SETTLEMENT, Payment::STATUS_CAPTURE])
            ->latest('paid_at');

        if ($request->filled('major_id')) {
            $query->whereHas('student', fn($q) => $q->where('major_id', $request->major_id));
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }

        if ($request->filled('month')) {
            $query->whereMonth('paid_at', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('paid_at', $request->year);
        }

        $payments       = $query->paginate(20)->withQueryString();
        $totalPemasukan = Payment::whereIn('status', [Payment::STATUS_SETTLEMENT, Payment::STATUS_CAPTURE])->sum('amount');
        $majors         = Major::where('is_active', true)->get();
        $classes        = SchoolClass::where('is_active', true)->get();

        return view('admin.pembayaran.index', compact('payments', 'majors', 'classes', 'totalPemasukan'));
    }
}
