<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class BillController extends Controller
{
    public function index(Request $request): View
    {
        $query = Bill::query()
            ->with(['student.schoolClass', 'student.major'])
            ->latest();

        // Default: hanya tampilkan yang BELUM lunas
        // Admin bisa filter 'paid' untuk lihat yang sudah lunas
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default tampil unpaid + overdue + pending (semua yang belum lunas)
            $query->whereNotIn('status', ['paid']);
        }

        if ($request->filled('major_id')) {
            $query->whereHas('student', fn($q) => $q->where('major_id', $request->major_id));
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $bills   = $query->paginate(20)->withQueryString();
        $majors  = Major::where('is_active', true)->orderBy('name')->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();

        $summary = [
            'total'   => Bill::count(),
            'paid'    => Bill::where('status', Bill::STATUS_PAID)->count(),
            'unpaid'  => Bill::whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_OVERDUE, Bill::STATUS_PENDING])->count(),
            'nominal' => Bill::where('status', Bill::STATUS_PAID)->sum('amount'),
        ];

        return view('admin.tagihan.index', compact('bills', 'majors', 'classes', 'summary'));
    }

    public function create(): View
    {
        $students = Student::with(['schoolClass', 'major'])
            ->where('is_active', true)->orderBy('name')->get();
        $classes  = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $majors   = Major::where('is_active', true)->orderBy('name')->get();

        return view('admin.tagihan.create', compact('students', 'classes', 'majors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'type'       => ['required', 'in:individual,bulk'],
            'student_id' => ['required_if:type,individual', 'nullable', 'exists:students,id'],
            'class_id'   => ['required_if:type,bulk', 'nullable', 'exists:classes,id'],
            'month'      => ['required', 'integer', 'min:1', 'max:12'],
            'year'       => ['required', 'integer', 'min:2020', 'max:2099'],
            'amount'     => ['required', 'integer', 'min:1000'],
            'due_date'   => ['required', 'date'],
            'notes'      => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($request) {
                if ($request->type === 'individual') {
                    $this->createBill($request->student_id, $request->only(['month', 'year', 'amount', 'due_date', 'notes']));
                } else {
                    Student::where('class_id', $request->class_id)
                        ->where('is_active', true)
                        ->each(fn($s) => $this->createBill($s->id, $request->only(['month', 'year', 'amount', 'due_date', 'notes'])));
                }
            });
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Gagal membuat tagihan: ' . $e->getMessage());
        }

        return redirect()->route('admin.tagihan.index')
            ->with('success', 'Tagihan berhasil dibuat.');
    }

    public function destroy(Bill $bill): RedirectResponse
    {
        if ($bill->status === Bill::STATUS_PAID) {
            return back()->with('error', 'Tagihan yang sudah lunas tidak bisa dihapus.');
        }
        $bill->delete();
        return back()->with('success', 'Tagihan berhasil dihapus.');
    }

    private function createBill(int $studentId, array $data): void
    {
        Bill::updateOrCreate(
            ['student_id' => $studentId, 'month' => $data['month'], 'year' => $data['year']],
            ['amount' => $data['amount'], 'status' => Bill::STATUS_UNPAID, 'due_date' => $data['due_date'], 'notes' => $data['notes'] ?? null]
        );
    }
}
