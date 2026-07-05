<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExtraPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'extra_bill_id',
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

    protected function casts(): array
    {
        return [
            'amount'            => 'integer',
            'midtrans_response' => 'array',
            'paid_at'           => 'datetime',
        ];
    }

    const STATUS_PENDING    = 'pending';
    const STATUS_SETTLEMENT = 'settlement';
    const STATUS_CAPTURE    = 'capture';
    const STATUS_DENY       = 'deny';
    const STATUS_CANCEL     = 'cancel';
    const STATUS_EXPIRE     = 'expire';
    const STATUS_FAILURE    = 'failure';

    const SUCCESS_STATUSES = ['settlement', 'capture'];

    // ── Relasi ─────────────────────────────────────────────────────────────────

    public function extraBill(): BelongsTo
    {
        return $this->belongsTo(ExtraBill::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // ── Helper ─────────────────────────────────────────────────────────────────

    public function isSuccessful(): bool
    {
        return in_array($this->status, self::SUCCESS_STATUSES);
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        $labels = [
            'gopay' => 'GoPay',
            'qris' => 'QRIS',
            'shopeepay' => 'ShopeePay',
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'mandiri_va' => 'Mandiri Virtual Account',
            'permata_va' => 'Permata Virtual Account',
            'credit_card' => 'Kartu Kredit',
            'indomaret' => 'Indomaret',
            'alfamart' => 'Alfamart',
        ];
        return $labels[$this->payment_type] ?? ucfirst(str_replace('_', ' ', $this->payment_type ?? '-'));
    }

    public static function generateOrderId(int $studentId, int $billId): string
    {
        return 'EXTRA-' . $studentId . '-' . $billId . '-' . now()->format('YmdHis');
    }
}
