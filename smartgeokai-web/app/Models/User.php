<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'full_name',
        'username',
        'nip',
        'password',
        'role',
        'is_active',
        'province_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Memeriksa apakah pengguna boleh mengakses panel admin.
     * Admin dan Super Admin sama-sama diizinkan.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin'], true);
    }

    /**
     * Memeriksa apakah pengguna adalah Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Relasi wilayah provinsi pengguna.
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Scope untuk mengambil akun Admin yang aktif.
     */
    public function scopeAdmins($query)
    {
        return $query
            ->where('role', 'admin')
            ->where('is_active', true);
    }

    /**
     * Scope untuk mengambil akun Super Admin yang aktif.
     */
    public function scopeSuperAdmins($query)
    {
        return $query
            ->where('role', 'superadmin')
            ->where('is_active', true);
    }

    /**
     * Scope untuk mengambil semua pengguna web yang aktif.
     */
    public function scopeWebAdmins($query)
    {
        return $query
            ->whereIn('role', ['admin', 'superadmin'])
            ->where('is_active', true);
    }
}
