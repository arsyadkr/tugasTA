<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // ← WAJIB ada ini

class User extends Authenticatable
{
    // Notifiable WAJIB di-use agar method notifications() dan
    // unreadNotifications() tersedia — ini yang menyebabkan P1013
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'username',
        'email',
        'name',
        'password',
        'role',
        'must_change_password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'must_change_password' => 'boolean',
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
        ];
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
}
