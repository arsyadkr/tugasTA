<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\Bill;
use App\Models\Major;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Throwable;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::query()
            ->with([
                'user:id,username,must_change_password',
                'schoolClass:id,name,academic_year',
                'major:id,name,code',
            ])
            ->withCount('bills')
            ->latest()
            ->paginate(15);

        return view('admin.students.index', compact('students'));
    }

    public function create(): View
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get(['id', 'name', 'academic_year', 'major_id']);
        $majors  = Major::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);

        return view('admin.students.create', compact('classes', 'majors'));
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name'                 => $request->name,
                    'username'             => $request->nis,
                    'email'                => null,
                    'password'             => Hash::make($request->nis),
                    'role'                 => 'student',
                    'must_change_password' => true,
                ]);

                Student::create([
                    'user_id'  => $user->id,
                    'nis'      => $request->nis,
                    'name'     => $request->name,
                    'gender'   => $request->gender,
                    'class_id' => $request->class_id,
                    'major_id' => $request->major_id,
                    'phone'    => $request->phone,
                    'address'  => $request->address,
                ]);
            });
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Gagal menambahkan siswa. Silakan coba lagi.');
        }

        return redirect()->route('admin.students.index')
            ->with('success', "Siswa {$request->name} berhasil ditambahkan. Password default: {$request->nis}");
    }

    public function show(Student $student): View
    {
        $student->load(['user', 'schoolClass', 'major', 'bills.payments']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $student->load(['user:id,username,must_change_password', 'schoolClass', 'major']);
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get(['id', 'name', 'academic_year', 'major_id']);
        $majors  = Major::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);

        return view('admin.students.edit', compact('student', 'classes', 'majors'));
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $nisChanged = $request->nis !== $student->nis;

        try {
            DB::transaction(function () use ($request, $student, $nisChanged) {
                $student->update([
                    'nis'      => $request->nis,
                    'name'     => $request->name,
                    'gender'   => $request->gender,
                    'class_id' => $request->class_id,
                    'major_id' => $request->major_id,
                    'phone'    => $request->phone,
                    'address'  => $request->address,
                ]);

                if ($nisChanged) {
                    $student->user->update([
                        'name'                 => $request->name,
                        'username'             => $request->nis,
                        'password'             => Hash::make($request->nis),
                        'must_change_password' => true,
                    ]);
                } else {
                    $student->user->update(['name' => $request->name]);
                }
            });
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Gagal memperbarui data siswa.');
        }

        $redirect = redirect()->route('admin.students.index')
            ->with('success', "Data siswa {$request->name} berhasil diperbarui.");

        if ($nisChanged) {
            $redirect->with('warning', "NIS berubah: password {$request->name} direset ke NIS baru ({$request->nis}).");
        }

        return $redirect;
    }

    public function destroy(Student $student): RedirectResponse
    {
        $name = $student->name;

        try {
            DB::transaction(function () use ($student) {
                // Force delete payments dan bills agar tidak muncul lagi
                // di dashboard, laporan, atau riwayat admin
                Payment::where('student_id', $student->id)->forceDelete();
                Bill::where('student_id', $student->id)->forceDelete();

                // Soft delete student & user
                $student->delete();
                $student->user->delete();
            });
        } catch (Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal menghapus siswa. Silakan coba lagi.');
        }

        return redirect()->route('admin.students.index')
            ->with('success', "Siswa {$name} beserta semua tagihan dan riwayat pembayaran berhasil dihapus.");
    }

    public function resetPassword(Student $student): RedirectResponse
    {
        $student->user->update([
            'password'             => Hash::make($student->nis),
            'must_change_password' => true,
        ]);

        return back()
            ->with('success', "Password {$student->name} berhasil direset.")
            ->with('warning', "Password baru: {$student->nis}. Informasikan ke siswa.");
    }
}
