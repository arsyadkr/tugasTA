<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $token;
    private string $baseUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token', '');
    }

    /**
     * Kirim pesan WhatsApp ke nomor tujuan.
     * Nomor format: 628xxxxxxxxxx (tanpa + dan tanpa spasi)
     */
    public function send(string $phone, string $message): bool
    {
        if (empty($this->token)) {
            Log::warning('WhatsApp: Fonnte token not configured.');
            return false;
        }

        // Normalisasi nomor: 08xx → 628xx
        $phone = $this->normalizePhone($phone);

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->timeout(15)->post($this->baseUrl, [
                'target'  => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful() && $response->json('status') !== false) {
                Log::info('WhatsApp sent', ['phone' => $phone]);
                return true;
            }

            Log::error('WhatsApp failed', [
                'phone'    => $phone,
                'response' => $response->json(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function normalizePhone(string $phone): string
    {
        // Hapus spasi, tanda +, dan karakter non-digit
        $phone = preg_replace('/\D/', '', $phone);

        // 08xx → 628xx
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Jika belum ada prefix 62
        if (! str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
