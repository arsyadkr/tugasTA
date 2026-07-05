<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BillController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = $user->student()->with([
            'schoolClass:id,name',
            'major:id,name',
            // Hanya load tagihan yang BELUM lunas
            'bills' => function ($query) {
                $query->whereNotIn('status', ['paid'])
                    ->orderBy('year')
                    ->orderBy('month');
            },
            'bills.successfulPayment',
        ])->firstOrFail();

        $bills = $student->bills;

        // Hitung total dari SEMUA tagihan (termasuk yang sudah lunas) untuk progres
        $allBills     = $student->bills()->get();
        $totalTagihan = $student->bills()->sum('amount');
        $totalDibayar = $student->bills()
            ->where('status', 'paid')
            ->sum('amount');
        $persentase   = $totalTagihan > 0
            ? (int) round($totalDibayar / $totalTagihan * 100)
            : 0;

        $jumlahLunas    = $student->bills()->where('status', 'paid')->count();
        $jumlahTotal    = $student->bills()->count();
        $jumlahBelumLunas = $bills->count();

        return view('siswa.tagihan.index', compact(
            'student',
            'bills',
            'totalTagihan',
            'totalDibayar',
            'persentase',
            'jumlahLunas',
            'jumlahTotal',
            'jumlahBelumLunas',
        ));
    }
}
