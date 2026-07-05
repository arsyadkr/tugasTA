<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function print(Payment $payment): Response
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = $user->student;

        // SECURITY: pastikan payment milik siswa yang login
        if ($payment->student_id !== $student->id) {
            abort(403, 'Akses ditolak.');
        }

        if (! $payment->isSuccessful()) {
            abort(403, 'Bukti hanya tersedia untuk pembayaran yang berhasil.');
        }

        $payment->loadMissing(['bill', 'student.schoolClass', 'student.major']);

        $pdf = Pdf::loadView('siswa.bukti.pdf', [
            'payment' => $payment,
            'student' => $payment->student,
            'bill'    => $payment->bill,
            'tanggal' => now()->translatedFormat('d F Y H:i'),
        ]);

        $pdf->setPaper('A5', 'portrait');

        return $pdf->download('bukti-spp-' . $payment->order_id . '.pdf');
    }
}
