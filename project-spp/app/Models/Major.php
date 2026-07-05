<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Major extends Model
{
    use HasFactory, SoftDeletes;

    // -------------------------------------------------------------------------
    // Mass Assignment
    // -------------------------------------------------------------------------

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    // -------------------------------------------------------------------------
    // Casts
    // -------------------------------------------------------------------------

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    /**
     * Satu jurusan memiliki banyak kelas (1:N).
     */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'major_id');
    }

    /**
     * Satu jurusan memiliki banyak siswa melalui kelas (1:N indirect).
     * Berguna untuk query "semua siswa jurusan RPL".
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'major_id');
    }
}
