<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class MidtransService
{
    private string $serverKey;
    private string $clientKey;
    private bool   $isProduction;
    private string $snapBaseUrl;

    public function __construct()
    {
        $this->serverKey    = config('midtrans.server_key');
        $this->clientKey    = config('midtrans.client_key');
        $this->isProduction = config('midtrans.is_production', false);

        $this->snapBaseUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1'
            : 'https://app.sandbox.midtrans.com/snap/v1';
    }

    // -------------------------------------------------------------------------
    // createTransaction
    // -------------------------------------------------------------------------

    public function createTransaction(Payment $payment): string
    {
        $payment->loadMissing(['bill.student.user', 'bill.student']);

        $bill    = $payment->bill;
        $student = $bill->student;

        $payload = [
            'transaction_details' => [
                'order_id'     => $payment->order_id,
                'gross_amount' => $payment->amount,
            ],
            'item_details' => [
                [
                    'id'       => 'SPP-' . $bill->month . '-' . $bill->year,
                    'price'    => $payment->amount,
                    'quantity' => 1,
                    'name'     => 'SPP ' . $bill->month_label . ' ' . $bill->year,
                ],
            ],
            'customer_details' => [
                'first_name' => $student->name,
                'email'      => $student->user->email ?? ($student->nis . '@spp.test'),
                'phone'      => $student->phone ?? '',
            ],
            'callbacks' => [
                'finish' => route('siswa.dashboard'),
            ],
            'expiry' => [
                'unit'     => 'hours',
                'duration' => 24,
            ],
            'enabled_payments' => [
                'bca_va',
                'bni_va',
                'bri_va',
                'mandiri_va',
                'permata_va',
                'gopay',
                'qris',
                'shopeepay',
                'indomaret',
                'alfamart',
            ],
        ];

        $response = Http::withBasicAuth($this->serverKey, '')
            ->timeout(30)
            ->post("{$this->snapBaseUrl}/transactions", $payload);

        if ($response->failed()) {
            Log::error('Midtrans createTransaction failed', [
                'order_id' => $payment->order_id,
                'status'   => $response->status(),
                'response' => $response->json(),
            ]);

            throw new RuntimeException(
                'Gagal membuat transaksi Midtrans: ' . ($response->json('error_messages.0') ?? 'Unknown error')
            );
        }

        return $response->json('token');
    }

    // -------------------------------------------------------------------------
    // FIX: validateSignature — formula SHA512 yang benar
    // -------------------------------------------------------------------------

    /**
     * Formula resmi Midtrans:
     *   SHA512(order_id + status_code + gross_amount + server_key)
     *
     * Referensi: https://docs.midtrans.com/docs/verifying-data-and-notification
     */
    public function validateSignature(array $payload): bool
    {
        if (
            empty($payload['signature_key'])
            || empty($payload['order_id'])
            || empty($payload['status_code'])
            || empty($payload['gross_amount'])
        ) {
            return false;
        }

        $raw = $payload['order_id']
            . $payload['status_code']
            . $payload['gross_amount']
            . $this->serverKey;

        $expected = hash('sha512', $raw);

        // hash_equals mencegah timing attack
        return hash_equals($expected, $payload['signature_key']);
    }

    // -------------------------------------------------------------------------
    // FIX: validateAmount — cegah tampering gross_amount dari callback
    // -------------------------------------------------------------------------

    /**
     * Validasi bahwa amount di callback sama persis dengan amount di DB.
     * Midtrans mengirim gross_amount sebagai string, misal "200000.00"
     */
    public function validateAmount(Payment $payment, string $grossAmount): bool
    {
        // Bandingkan sebagai integer — strip desimal dari Midtrans
        return (int) $payment->amount === (int) round((float) $grossAmount);
    }

    // -------------------------------------------------------------------------
    // Getters
    // -------------------------------------------------------------------------

    public function getClientKey(): string
    {
        return $this->clientKey;
    }
    public function isProduction(): bool
    {
        return $this->isProduction;
    }
}
