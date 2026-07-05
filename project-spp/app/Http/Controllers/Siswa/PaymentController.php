<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentSettledNotification;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        private readonly MidtransService $midtrans
    ) {}

    // -------------------------------------------------------------------------
    // initiate — siswa klik tombol Bayar
    // -------------------------------------------------------------------------

    public function initiate(Bill $bill): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = $user->student;

        if ($bill->student_id !== $student->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($bill->status === Bill::STATUS_PAID) {
            return response()->json(['message' => 'Tagihan ini sudah lunas.'], 422);
        }

        // Reuse pending token yang masih fresh
        $existingPending = Payment::where('bill_id', $bill->id)
            ->where('status', Payment::STATUS_PENDING)
            ->whereNotNull('snap_token')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->latest()
            ->first();

        if ($existingPending) {
            return response()->json([
                'snap_token'    => $existingPending->snap_token,
                'client_key'    => $this->midtrans->getClientKey(),
                'is_production' => $this->midtrans->isProduction(),
            ]);
        }

        try {
            $snapToken = null;

            DB::transaction(function () use ($bill, $student, &$snapToken) {
                $payment = Payment::create([
                    'bill_id'    => $bill->id,
                    'student_id' => $student->id,
                    'order_id'   => Payment::generateOrderId($student->id, $bill->id),
                    'amount'     => $bill->amount,
                    'status'     => Payment::STATUS_PENDING,
                ]);

                $snapToken = $this->midtrans->createTransaction($payment);
                $payment->update(['snap_token' => $snapToken]);
            });

            return response()->json([
                'snap_token'    => $snapToken,
                'client_key'    => $this->midtrans->getClientKey(),
                'is_production' => $this->midtrans->isProduction(),
            ]);
        } catch (Throwable $e) {
            Log::error('Payment initiate failed', [
                'bill_id' => $bill->id,
                'error'   => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Gagal membuat transaksi. Silakan coba lagi.'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // checkStatus — dipanggil dari JS setelah Snap onSuccess
    // Ini solusi untuk kasus callback Midtrans lambat/belum masuk
    // JS langsung tanya ke Midtrans API via server kita untuk cek status
    // -------------------------------------------------------------------------

    public function checkStatus(Bill $bill): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = $user->student;

        if ($bill->student_id !== $student->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Ambil payment terbaru untuk bill ini
        $payment = Payment::where('bill_id', $bill->id)
            ->whereNotNull('snap_token')
            ->latest()
            ->first();

        if (! $payment) {
            return response()->json(['status' => 'not_found']);
        }

        // Jika sudah settlement di DB → langsung return
        if ($payment->isSuccessful()) {
            return response()->json(['status' => 'settlement']);
        }

        // Cek status ke Midtrans API secara langsung
        try {
            $serverKey  = config('midtrans.server_key');
            $isProduction = config('midtrans.is_production', false);
            $baseUrl    = $isProduction
                ? 'https://api.midtrans.com/v2'
                : 'https://api.sandbox.midtrans.com/v2';

            $response = Http::withBasicAuth($serverKey, '')
                ->timeout(10)
                ->get("{$baseUrl}/{$payment->order_id}/status");

            if ($response->failed()) {
                return response()->json(['status' => 'pending']);
            }

            $data              = $response->json();
            $transactionStatus = $data['transaction_status'] ?? 'pending';
            $fraudStatus       = $data['fraud_status'] ?? 'accept';

            $isSettlement = $transactionStatus === 'settlement';
            $isCapture    = $transactionStatus === 'capture' && $fraudStatus === 'accept';

            if ($isSettlement || $isCapture) {
                // Update langsung tanpa menunggu callback
                DB::transaction(function () use ($payment, $data, $transactionStatus) {
                    $status = $transactionStatus === 'settlement'
                        ? Payment::STATUS_SETTLEMENT
                        : Payment::STATUS_CAPTURE;

                    $payment->update([
                        'status'            => $status,
                        'payment_type'      => $data['payment_type'] ?? null,
                        'transaction_id'    => $data['transaction_id'] ?? null,
                        'midtrans_response' => $data,
                        'paid_at'           => now(),
                    ]);

                    $payment->bill->update(['status' => Bill::STATUS_PAID]);

                    Log::info('Payment settled via checkStatus', [
                        'order_id' => $payment->order_id,
                    ]);
                });

                // Notifikasi admin
                $this->notifyAdmins($payment);

                return response()->json(['status' => 'settlement']);
            }

            return response()->json(['status' => $transactionStatus]);
        } catch (Throwable $e) {
            Log::error('checkStatus error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'pending']);
        }
    }

    // -------------------------------------------------------------------------
    // cancel — siswa batalkan transaksi pending
    // -------------------------------------------------------------------------

    public function cancel(Bill $bill): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = $user->student;

        if ($bill->student_id !== $student->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $payment = Payment::where('bill_id', $bill->id)
            ->where('status', Payment::STATUS_PENDING)
            ->latest()
            ->first();

        if (! $payment) {
            return response()->json(['message' => 'Tidak ada transaksi yang bisa dibatalkan.'], 422);
        }

        try {
            // Cancel ke Midtrans
            $serverKey    = config('midtrans.server_key');
            $isProduction = config('midtrans.is_production', false);
            $baseUrl      = $isProduction
                ? 'https://api.midtrans.com/v2'
                : 'https://api.sandbox.midtrans.com/v2';

            Http::withBasicAuth($serverKey, '')
                ->timeout(10)
                ->post("{$baseUrl}/{$payment->order_id}/cancel");

            // Update status lokal
            $payment->update(['status' => Payment::STATUS_CANCEL]);
            $bill->update(['status' => Bill::STATUS_UNPAID]);

            return response()->json(['message' => 'Transaksi berhasil dibatalkan.']);
        } catch (Throwable $e) {
            Log::error('Payment cancel error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal membatalkan transaksi.'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // callback — Midtrans POST ke /midtrans/callback
    // -------------------------------------------------------------------------

    public function callback(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Midtrans callback received', [
            'order_id'           => $payload['order_id'] ?? null,
            'transaction_status' => $payload['transaction_status'] ?? null,
            'payment_type'       => $payload['payment_type'] ?? null,
        ]);

        if (! $this->midtrans->validateSignature($payload)) {
            Log::warning('Midtrans callback: invalid signature', ['ip' => $request->ip()]);
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $payment = Payment::where('order_id', $payload['order_id'])
            ->lockForUpdate()
            ->with('bill.student')
            ->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment not found.'], 404);
        }

        if ($payment->isSuccessful()) {
            return response()->json(['message' => 'Already processed.'], 200);
        }

        if (! $this->midtrans->validateAmount($payment, $payload['gross_amount'] ?? '0')) {
            Log::critical('Midtrans: AMOUNT MISMATCH', [
                'order_id' => $payment->order_id,
                'expected' => $payment->amount,
                'received' => $payload['gross_amount'] ?? null,
            ]);
            return response()->json(['message' => 'Amount mismatch.'], 422);
        }

        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus       = $payload['fraud_status'] ?? 'accept';

        try {
            DB::transaction(function () use ($payment, $payload, $transactionStatus, $fraudStatus) {
                $isSettlement = $transactionStatus === 'settlement';
                $isCapture    = $transactionStatus === 'capture' && $fraudStatus === 'accept';

                if ($isSettlement || $isCapture) {
                    $status = $isSettlement
                        ? Payment::STATUS_SETTLEMENT
                        : Payment::STATUS_CAPTURE;

                    $payment->update([
                        'status'            => $status,
                        'payment_type'      => $payload['payment_type'] ?? null,
                        'transaction_id'    => $payload['transaction_id'] ?? null,
                        'midtrans_response' => $payload,
                        'paid_at'           => now(),
                    ]);

                    $payment->bill->update(['status' => Bill::STATUS_PAID]);

                    Log::info('Payment settled via callback', [
                        'order_id' => $payment->order_id,
                    ]);

                    $this->notifyAdmins($payment);
                } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny', 'failure'])) {
                    $statusMap = [
                        'expire'  => Payment::STATUS_EXPIRE,
                        'cancel'  => Payment::STATUS_CANCEL,
                        'deny'    => Payment::STATUS_DENY,
                        'failure' => Payment::STATUS_FAILURE,
                    ];

                    $payment->update([
                        'status'            => $statusMap[$transactionStatus],
                        'midtrans_response' => $payload,
                    ]);

                    $payment->bill->update(['status' => Bill::STATUS_UNPAID]);
                } elseif ($transactionStatus === 'pending') {
                    $payment->update([
                        'status'            => Payment::STATUS_PENDING,
                        'midtrans_response' => $payload,
                    ]);
                }
            });
        } catch (Throwable $e) {
            Log::error('Callback processing error', [
                'order_id' => $payment->order_id,
                'error'    => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Processing error.'], 500);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function notifyAdmins(Payment $payment): void
    {
        try {
            $payment->loadMissing(['bill.student']);

            User::where('role', 'admin')
                ->get()
                ->each(fn(User $admin) => $admin->notify(new PaymentSettledNotification($payment)));
        } catch (Throwable $e) {
            Log::error('Failed to notify admins', [
                'order_id' => $payment->order_id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
