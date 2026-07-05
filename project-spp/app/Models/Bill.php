<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    // -------------------------------------------------------------------------
    // Mass Assignment
    // -------------------------------------------------------------------------

    protected $fillable = [
        'student_id',
        'month',
        'year',
        'amount',
        'status',
        'due_date',
        'notes',
    ];

    // -------------------------------------------------------------------------
    // Casts
    // -------------------------------------------------------------------------

    protected function casts(): array
    {
        return [
            'month'    => 'integer',
            'year'     => 'integer',
            'amount'   => 'integer',
            'due_date' => 'date',
        ];
    }

    // -------------------------------------------------------------------------
    // Konstanta Status
    // -------------------------------------------------------------------------

    // Gunakan konstanta agar tidak ada magic string berulang di seluruh codebase
    const STATUS_UNPAID    = 'unpaid';
    const STATUS_PENDING   = 'pending';
    const STATUS_PAID      = 'paid';
    const STATUS_OVERDUE   = 'overdue';

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    /**
     * Tagihan dimiliki oleh satu siswa (N:1).
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Satu tagihan memiliki banyak attempt pembayaran (1:N).
     * Satu tagihan bisa gagal lalu dicoba ulang — semua tercatat di sini.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'bill_id');
    }

    /**
     * Shortcut ke pembayaran yang berhasil (status settlement atau capture).
     * Berguna untuk cetak kartu / bukti bayar.
     */
    public function successfulPayment(): HasOne
    {
        return $this->hasOne(Payment::class, 'bill_id')
            ->whereIn('status', [Payment::STATUS_SETTLEMENT, Payment::STATUS_CAPTURE])
            ->latestOfMany();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Filter tagihan belum lunas.
     * Penggunaan: Bill::unpaid()->get()
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', [self::STATUS_UNPAID, self::STATUS_OVERDUE]);
    }

    /**
     * Filter tagihan yang sudah lunas.
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Filter berdasarkan tahun ajaran.
     * Penggunaan: Bill::ofYear(2025)->get()
     */
    public function scopeOfYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    // -------------------------------------------------------------------------
    // Helper / Accessor
    // -------------------------------------------------------------------------

    /**
     * Nama bulan dalam Bahasa Indonesia.
     * Contoh: bill->month_label → "Januari"
     */
    public function getMonthLabelAttribute(): string
    {
        $months = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $months[$this->month] ?? '-';
    }

    /**
     * Label bulan + tahun lengkap.
     * Contoh: "Januari 2025"
     */
    public function getPeriodLabelAttribute(): string
    {
        return "{$this->month_label} {$this->year}";
    }

    /**
     * Cek apakah tagihan sudah jatuh tempo dan belum dibayar.
     */
    public function isOverdue(): bool
    {
        return $this->status !== self::STATUS_PAID
            && now()->isAfter($this->due_date);
    }
}
