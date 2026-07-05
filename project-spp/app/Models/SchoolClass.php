<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Nama class SchoolClass (bukan Class) karena 'class' adalah
 * reserved keyword di PHP — tidak bisa digunakan sebagai nama class.
 *
 * Di database tetap menggunakan tabel 'classes'.
 * Di controller dan view dipanggil sebagai SchoolClass.
 */
class SchoolClass extends Model
{
    use HasFactory, SoftDeletes;

    // -------------------------------------------------------------------------
    // Table Name
    // -------------------------------------------------------------------------

    // Eksplisit karena nama model (SchoolClass) tidak match konvensi tabel (classes)
    protected $table = 'classes';

    // -------------------------------------------------------------------------
    // Mass Assignment
    // -------------------------------------------------------------------------

    protected $fillable = [
        'major_id',
        'grade',
        'name',
        'academic_year',
        'is_active',
    ];

    // -------------------------------------------------------------------------
    // Casts
    // -------------------------------------------------------------------------

    protected function casts(): array
    {
        return [
            'grade'     => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    /**
     * Kelas dimiliki oleh satu jurusan (N:1).
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    /**
     * Satu kelas memiliki banyak siswa (1:N).
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    // -------------------------------------------------------------------------
    // Helper / Accessor
    // -------------------------------------------------------------------------

    /**
     * Label lengkap kelas, contoh: "X RPL 1 (2024/2025)"
     */
    public function getFullLabelAttribute(): string
    {
        return "{$this->name} ({$this->academic_year})";
    }
}
