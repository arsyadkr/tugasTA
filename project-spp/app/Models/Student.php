<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    // -------------------------------------------------------------------------
    // Mass Assignment
    // -------------------------------------------------------------------------

    protected $fillable = [
        'user_id',
        'class_id',
        'major_id',
        'nis',
        'name',
        'gender',
        'phone',
        'address',
        'birth_date',
        'birth_place',
        'photo',
        'parent_name',
        'parent_phone',
        'parent_email',
        'is_active',
    ];

    // -------------------------------------------------------------------------
    // Casts
    // -------------------------------------------------------------------------

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_active'  => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    /**
     * Siswa dimiliki oleh satu akun user (1:1 inverse).
     * Digunakan untuk akses data auth dari konteks siswa.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Siswa terdaftar di satu kelas (N:1).
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Siswa berada di satu jurusan (N:1).
     * Meski bisa di-resolve via schoolClass->major, relasi langsung
     * ini mempermudah query dan mengurangi JOIN yang tidak perlu.
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    /**
     * Satu siswa memiliki banyak tagihan SPP (1:N).
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class, 'student_id');
    }

    /**
     * Satu siswa memiliki banyak riwayat pembayaran (1:N).
     * Relasi langsung tanpa melewati bills — untuk query riwayat cepat.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'student_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Filter hanya siswa aktif.
     * Penggunaan: Student::active()->get()
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // -------------------------------------------------------------------------
    // Helper / Accessor
    // -------------------------------------------------------------------------

    /**
     * URL foto profil siswa. Fallback ke avatar default jika tidak ada foto.
     */
    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/default-avatar.png');
    }

    /**
     * Total tagihan yang belum dibayar.
     */
    public function unpaidBillsCount(): int
    {
        return $this->bills()->whereIn('status', ['unpaid', 'overdue'])->count();
    }

    /**
     * Total nominal tagihan yang belum dibayar.
     */
    public function totalUnpaid(): int
    {
        return $this->bills()
            ->whereIn('status', ['unpaid', 'overdue'])
            ->sum('amount');
    }
}
