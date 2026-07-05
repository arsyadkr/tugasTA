<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    // -------------------------------------------------------------------------
    // Mass Assignment
    // -------------------------------------------------------------------------

    protected $fillable = [
        'bill_id',
        'student_id',
        'order_id',
        'snap_token',
        'amount',
        'status',
        'payment_type',
        'transaction_id',
        'midtrans_response',
        'paid_at',
    ];

    // -------------------------------------------------------------------------
    // Casts
    // -------------------------------------------------------------------------

    protected function casts(): array
    {
        return [
            'amount'             => 'integer',
            'midtrans_response'  => 'array',   // JSON otomatis encode/decode
            'paid_at'            => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Konstanta Status (mengikuti status Midtrans)
    // -------------------------------------------------------------------------

    const STATUS_PENDING    = 'pending';
    const STATUS_SETTLEMENT = 'settlement'; // Lunas via transfer/QRIS/VA
    const STATUS_CAPTURE    = 'capture';    // Lunas via kartu kredit
    const STATUS_DENY       = 'deny';
    const STATUS_CANCEL     = 'cancel';
    const STATUS_EXPIRE     = 'expire';
    const STATUS_FAILURE    = 'failure';

    // Status yang dianggap berhasil / lunas
    const SUCCESS_STATUSES = [
        self::STATUS_SETTLEMENT,
        self::STATUS_CAPTURE,
    ];

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    /**
     * Pembayaran terkait dengan satu tagihan (N:1).
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    /**
     * Pembayaran dilakukan oleh satu siswa (N:1).
     * Relasi langsung tanpa melewati bill — untuk query riwayat cepat.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Filter pembayaran yang berhasil (settlement atau capture).
     * Penggunaan: Payment::successful()->get()
     */
    public function scopeSuccessful($query)
    {
        return $query->whereIn('status', self::SUCCESS_STATUSES);
    }

    /**
     * Filter pembayaran yang masih pending.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // -------------------------------------------------------------------------
    // Helper / Accessor
    // -------------------------------------------------------------------------

    /**
     * Cek apakah pembayaran ini dianggap lunas.
     */
    public function isSuccessful(): bool
    {
        return in_array($this->status, self::SUCCESS_STATUSES);
    }

    /**
     * Cek apakah pembayaran masih bisa dibayar (belum expire/cancel).
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Label metode pembayaran yang lebih ramah ditampilkan.
     * Contoh: "bca_va" → "BCA Virtual Account"
     */
    public function getPaymentTypeLabelAttribute(): string
    {
        $labels = [
            'gopay'           => 'GoPay',
            'qris'            => 'QRIS',
            'shopeepay'       => 'ShopeePay',
            'bca_va'          => 'BCA Virtual Account',
            'bni_va'          => 'BNI Virtual Account',
            'bri_va'          => 'BRI Virtual Account',
            'mandiri_va'      => 'Mandiri Virtual Account',
            'permata_va'      => 'Permata Virtual Account',
            'credit_card'     => 'Kartu Kredit',
            'indomaret'       => 'Indomaret',
            'alfamart'        => 'Alfamart',
            'akulaku'         => 'Akulaku',
        ];

        return $labels[$this->payment_type] ?? ucfirst(str_replace('_', ' ', $this->payment_type ?? '-'));
    }

    /**
     * Generate order_id unik untuk dikirim ke Midtrans.
     * Format: SPP-{student_id}-{bill_id}-{timestamp}
     * Dipanggil sebelum create payment baru.
     */
    public static function generateOrderId(int $studentId, int $billId): string
    {
        return 'SPP-' . $studentId . '-' . $billId . '-' . now()->format('YmdHis');
    }
}
