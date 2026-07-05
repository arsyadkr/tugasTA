<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ExtraBill;
use App\Models\ExtraPayment;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class ExtraPaymentController extends Controller
{
    public function __construct(
        private readonly MidtransService $midtrans
    ) {}

    // ── Index: tampilkan semua tagihan extra milik siswa ──────────────────────

    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = $user->student()->with(['schoolClass:id,name,grade'])->firstOrFail();

        $grade = $student->schoolClass->grade ?? 0;

        // Ambil tagihan sesuai grade siswa
        $gradeTypeMap = [10 => 'kunjungan_industri', 11 => 'gts', 12 => 'pkl'];
        $allowedType  = $gradeTypeMap[$grade] ?? null;

        $bills = ExtraBill::with('successfulPayment')
            ->where('student_id', $student->id)
            ->when($allowedType, fn($q) => $q->where('type', $allowedType))
            ->get();

        return view('siswa.extra.index', compact('student', 'bills', 'grade', 'allowedType'));
    }

    // ── Initiate: buat transaksi Midtrans ─────────────────────────────────────

    public function initiate(ExtraBill $extraBill): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = $user->student;

        if ($extraBill->student_id !== $student->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($extraBill->status === 'paid') {
            return response()->json(['message' => 'Tagihan ini sudah lunas.'], 422);
        }

        // Reuse pending token
        $existing = ExtraPayment::where('extra_bill_id', $extraBill->id)
            ->where('status', 'pending')
            ->whereNotNull('snap_token')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->latest()->first();

        if ($existing) {
            return response()->json([
                'snap_token'    => $existing->snap_token,
                'client_key'    => $this->midtrans->getClientKey(),
                'is_production' => $this->midtrans->isProduction(),
            ]);
        }

        try {
            $snapToken = null;

            DB::transaction(function () use ($extraBill, $student, &$snapToken) {
                $payment = ExtraPayment::create([
                    'extra_bill_id' => $extraBill->id,
                    'student_id'    => $student->id,
                    'order_id'      => ExtraPayment::generateOrderId($student->id, $extraBill->id),
                    'amount'        => $extraBill->amount,
                    'status'        => ExtraPayment::STATUS_PENDING,
                ]);

                // Generate snap token via MidtransService
                // Buat payload manual karena ini bukan Bill biasa
                $snapToken = $this->createSnapToken($payment, $extraBill, $student);
                $payment->update(['snap_token' => $snapToken]);
            });

            return response()->json([
                'snap_token'    => $snapToken,
                'client_key'    => $this->midtrans->getClientKey(),
                'is_production' => $this->midtrans->isProduction(),
            ]);
        } catch (Throwable $e) {
            Log::error('ExtraPayment initiate failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal membuat transaksi.'], 500);
        }
    }

    // ── Check status setelah Snap onSuccess ───────────────────────────────────

    public function checkStatus(ExtraBill $extraBill): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = $user->student;

        if ($extraBill->student_id !== $student->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $payment = ExtraPayment::where('extra_bill_id', $extraBill->id)
            ->whereNotNull('snap_token')->latest()->first();

        if (! $payment) {
            return response()->json(['status' => 'not_found']);
        }

        if ($payment->isSuccessful()) {
            return response()->json(['status' => 'settlement']);
        }

        try {
            $serverKey    = config('midtrans.server_key');
            $isProduction = config('midtrans.is_production', false);
            $baseUrl      = $isProduction
                ? 'https://api.midtrans.com/v2'
                : 'https://api.sandbox.midtrans.com/v2';

            $res  = Http::withBasicAuth($serverKey, '')->timeout(10)
                ->get("{$baseUrl}/{$payment->order_id}/status");

            if ($res->failed()) {
                return response()->json(['status' => 'pending']);
            }

            $data   = $res->json();
            $status = $data['transaction_status'] ?? 'pending';
            $fraud  = $data['fraud_status'] ?? 'accept';

            if ($status === 'settlement' || ($status === 'capture' && $fraud === 'accept')) {
                DB::transaction(function () use ($payment, $data, $status) {
                    $payment->update([
                        'status'            => $status,
                        'payment_type'      => $data['payment_type'] ?? null,
                        'transaction_id'    => $data['transaction_id'] ?? null,
                        'midtrans_response' => $data,
                        'paid_at'           => now(),
                    ]);
                    $payment->extraBill->update(['status' => 'paid']);
                });
                return response()->json(['status' => 'settlement']);
            }

            return response()->json(['status' => $status]);
        } catch (Throwable $e) {
            return response()->json(['status' => 'pending']);
        }
    }

    // ── Private: buat snap token untuk extra payment ──────────────────────────

    private function createSnapToken(ExtraPayment $payment, ExtraBill $extraBill, $student): string
    {
        $serverKey    = config('midtrans.server_key');
        $isProduction = config('midtrans.is_production', false);
        $baseUrl      = $isProduction
            ? 'https://app.midtrans.com/snap/v1'
            : 'https://app.sandbox.midtrans.com/snap/v1';

        $payload = [
            'transaction_details' => [
                'order_id'     => $payment->order_id,
                'gross_amount' => $payment->amount,
            ],
            'item_details' => [
                [
                    'id'       => $extraBill->type,
                    'price'    => $payment->amount,
                    'quantity' => 1,
                    'name'     => $extraBill->title,
                ],
            ],
            'customer_details' => [
                'first_name' => $student->name,
                'email'      => $student->user->email ?? ($student->nis . '@spp.test'),
                'phone'      => $student->phone ?? '',
            ],
            'callbacks' => [
                'finish' => route('siswa.extra.index'),
            ],
            'expiry' => ['unit' => 'hours', 'duration' => 24],
        ];

        $response = \Illuminate\Support\Facades\Http::withBasicAuth($serverKey, '')
            ->timeout(30)
            ->post("{$baseUrl}/transactions", $payload);

        if ($response->failed()) {
            throw new \RuntimeException('Gagal membuat transaksi: ' . ($response->json('error_messages.0') ?? 'Unknown'));
        }

        return $response->json('token');
    }
}
