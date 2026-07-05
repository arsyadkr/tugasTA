<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExtraBill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'type',
        'title',
        'amount',
        'status',
        'due_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'   => 'integer',
            'due_date' => 'date',
        ];
    }

    // ── Konstanta ──────────────────────────────────────────────────────────────

    const TYPE_KUNJUNGAN = 'kunjungan_industri';
    const TYPE_GTS       = 'gts';
    const TYPE_PKL       = 'pkl';

    const STATUS_UNPAID  = 'unpaid';
    const STATUS_PENDING = 'pending';
    const STATUS_PAID    = 'paid';
    const STATUS_OVERDUE = 'overdue';

    // Grade yang boleh akses per jenis
    const GRADE_MAP = [
        self::TYPE_KUNJUNGAN => 10,
        self::TYPE_GTS       => 11,
        self::TYPE_PKL       => 12,
    ];

    // Label tampilan per jenis
    const LABEL_MAP = [
        self::TYPE_KUNJUNGAN => 'Kunjungan Industri',
        self::TYPE_GTS       => 'GTS (Go To School)',
        self::TYPE_PKL       => 'PKL (Praktek Kerja Lapangan)',
    ];

    // ── Relasi ─────────────────────────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ExtraPayment::class);
    }

    public function successfulPayment(): HasOne
    {
        return $this->hasOne(ExtraPayment::class)
            ->whereIn('status', ['settlement', 'capture'])
            ->latestOfMany();
    }

    // ── Accessor ───────────────────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return self::LABEL_MAP[$this->type] ?? $this->type;
    }
}
