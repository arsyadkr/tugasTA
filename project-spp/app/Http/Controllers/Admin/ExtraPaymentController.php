<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExtraBill;
use App\Models\ExtraPayment;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class ExtraPaymentController extends Controller
{
    // Mapping type → grade yang diizinkan
    private const GRADE_MAP = [
        'kunjungan_industri' => 10,
        'gts'                => 11,
        'pkl'                => 12,
    ];

    private const LABEL_MAP = [
        'kunjungan_industri' => 'Kunjungan Industri',
        'gts'                => 'GTS (Go To School)',
        'pkl'                => 'PKL (Praktek Kerja Lapangan)',
    ];

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        // Default tampilkan yang belum lunas
        $query = ExtraBill::with(['student.schoolClass', 'student.major'])
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereNotIn('status', ['paid']);
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }

        $bills   = $query->paginate(20)->withQueryString();
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();

        // Summary per jenis
        $summary = [];
        foreach (self::GRADE_MAP as $type => $grade) {
            $summary[$type] = [
                'label'  => self::LABEL_MAP[$type],
                'grade'  => $grade,
                'total'  => ExtraBill::where('type', $type)->count(),
                'paid'   => ExtraBill::where('type', $type)->where('status', 'paid')->count(),
                'unpaid' => ExtraBill::where('type', $type)->whereNotIn('status', ['paid'])->count(),
            ];
        }

        return view('admin.extra.index', compact('bills', 'classes', 'summary'));
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create(): View
    {
        return view('admin.extra.create', [
            'gradeMap'  => self::GRADE_MAP,
            'labelMap'  => self::LABEL_MAP,
            'classes'   => SchoolClass::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'type'     => ['required', 'in:kunjungan_industri,gts,pkl'],
            'title'    => ['required', 'string', 'max:100'],
            'amount'   => ['required', 'integer', 'min:1000'],
            'due_date' => ['required', 'date'],
            'notes'    => ['nullable', 'string', 'max:255'],
        ]);

        $type        = $request->type;
        $targetGrade = self::GRADE_MAP[$type];

        // Ambil semua siswa di grade yang sesuai
        $students = Student::whereHas('schoolClass', fn($q) => $q->where('grade', $targetGrade))
            ->where('is_active', true)
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', "Tidak ada siswa kelas " . $targetGrade . " yang aktif.");
        }

        $created = 0;
        $skipped = 0;

        try {
            DB::transaction(function () use ($students, $request, $type, &$created, &$skipped) {
                foreach ($students as $student) {
                    // Skip jika sudah ada tagihan jenis ini untuk siswa ini
                    $exists = ExtraBill::where('student_id', $student->id)
                        ->where('type', $type)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    ExtraBill::create([
                        'student_id' => $student->id,
                        'type'       => $type,
                        'title'      => $request->title,
                        'amount'     => $request->amount,
                        'status'     => ExtraBill::STATUS_UNPAID,
                        'due_date'   => $request->due_date,
                        'notes'      => $request->notes,
                    ]);

                    $created++;
                }
            });
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Gagal membuat tagihan: ' . $e->getMessage());
        }

        $msg = "Tagihan {$request->title} berhasil dibuat untuk {$created} siswa.";
        if ($skipped > 0) {
            $msg .= " {$skipped} siswa dilewati (tagihan sudah ada).";
        }

        return redirect()->route('admin.extra.index')->with('success', $msg);
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(ExtraBill $extra): RedirectResponse
    {
        if ($extra->status === 'paid') {
            return back()->with('error', 'Tagihan yang sudah lunas tidak bisa dihapus.');
        }

        ExtraPayment::where('extra_bill_id', $extra->id)->forceDelete();
        $extra->delete();

        return back()->with('success', 'Tagihan berhasil dihapus.');
    }

    // ── Payments list ─────────────────────────────────────────────────────────

    public function payments(Request $request): View
    {
        $query = ExtraPayment::with(['student.schoolClass', 'student.major', 'extraBill'])
            ->whereIn('status', ['settlement', 'capture'])
            ->latest('paid_at');

        if ($request->filled('type')) {
            $query->whereHas('extraBill', fn($q) => $q->where('type', $request->type));
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }

        $payments = $query->paginate(20)->withQueryString();
        $classes  = SchoolClass::where('is_active', true)->get();

        return view('admin.extra.payments', compact('payments', 'classes'));
    }
}
