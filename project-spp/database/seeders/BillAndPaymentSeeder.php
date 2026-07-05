<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BillAndPaymentSeeder extends Seeder
{
    private const SPP_AMOUNT = 200000;
    private const MONTHS     = [1, 2, 3, 4, 5, 6];
    private const YEAR       = 2025;

    public function run(): void
    {
        $students = Student::all();

        // FIX PERFORMANCE: Satu transaction mencakup semua siswa
        // Jika ada satu siswa yang gagal, semua di-rollback — data konsisten
        DB::transaction(function () use ($students) {
            foreach ($students as $student) {
                $this->generateBillsForStudent($student);
            }
        });
    }

    private function generateBillsForStudent(Student $student): void
    {
        $selectedMonths = collect(self::MONTHS)
            ->shuffle()
            ->take(rand(3, 6))
            ->sort()
            ->values();

        foreach ($selectedMonths as $index => $month) {
            // FIX IDEMPOTENT: Key pakai composite [student_id + month + year]
            // Sesuai unique constraint di migration
            $exists = Bill::where('student_id', $student->id)
                ->where('month', $month)
                ->where('year', self::YEAR)
                ->exists();

            if ($exists) {
                continue;
            }

            $status  = $this->determineStatus($index, count($selectedMonths));
            $dueDate = Carbon::create(self::YEAR, $month, 10);

            $bill = Bill::create([
                'student_id' => $student->id,
                'month'      => $month,
                'year'       => self::YEAR,
                'amount'     => self::SPP_AMOUNT,
                'status'     => $status,
                'due_date'   => $dueDate,
            ]);

            // Payment hanya dibuat untuk bill berstatus paid
            if ($status === Bill::STATUS_PAID) {
                $this->createPayment($bill, $student);
            }
        }
    }

    private function createPayment(Bill $bill, Student $student): void
    {
        $paidAt = Carbon::create(self::YEAR, $bill->month, rand(1, 9));

        $paymentTypes = ['bca_va', 'bni_va', 'bri_va', 'gopay', 'qris', 'mandiri_va'];
        $paymentType  = $paymentTypes[array_rand($paymentTypes)];

        // FIX ORDER_ID: Pakai prefix 'SEED-' + uniqid() untuk menghindari collision
        // dengan order_id yang nanti digenerate oleh Midtrans di production
        $orderId = 'SEED-' . strtoupper(uniqid('', true));

        Payment::create([
            'bill_id'        => $bill->id,
            'student_id'     => $student->id,
            'order_id'       => $orderId,
            'snap_token'     => null,

            // FIX CONSISTENCY: amount payment HARUS sama dengan amount bill
            // Ini yang dipakai untuk kalkulasi total_dibayar di dashboard
            'amount'         => $bill->amount,

            // FIX CONSISTENCY: status harus STATUS_SETTLEMENT (bukan random)
            // Dashboard menghitung totalDibayar dari status ini
            'status'         => Payment::STATUS_SETTLEMENT,

            'payment_type'   => $paymentType,
            'transaction_id' => 'TXN-' . strtoupper(uniqid()),

            // Midtrans response dummy — struktur mengikuti response asli Midtrans
            'midtrans_response' => [
                'transaction_status' => 'settlement',
                'payment_type'       => $paymentType,
                'order_id'           => $orderId,
                'gross_amount'       => (string) $bill->amount,
                'transaction_time'   => $paidAt->toDateTimeString(),
                'settlement_time'    => $paidAt->addHours(1)->toDateTimeString(),
            ],

            // FIX CONSISTENCY: paid_at wajib diisi untuk bill yang paid
            // Dipakai di view dan laporan sebagai timestamp pembayaran
            'paid_at' => $paidAt,
        ]);
    }

    private function determineStatus(int $index, int $total): string
    {
        $isEarlyMonth = $index < ($total * 0.6);

        if ($isEarlyMonth) {
            return rand(1, 10) <= 8 ? Bill::STATUS_PAID : Bill::STATUS_UNPAID;
        }

        $roll = rand(1, 10);
        if ($roll <= 3) return Bill::STATUS_PAID;
        if ($roll <= 8) return Bill::STATUS_UNPAID;
        return Bill::STATUS_OVERDUE;
    }
}
