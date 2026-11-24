<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'sekolah_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Latihan Mandiri: Menyeragamkan email menjadi huruf kecil sebelum disimpan
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value !== null ? strtolower(trim($value)) : null,
        );
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'role' => Role::class,
        ];
    }

    // Relasi pengguna dengan sekolah
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    // Memeriksa role admin
    public function isAdmin(): bool
    {
        return $this->role === Role::ADMIN;
    }

    // Memeriksa role dinas
    public function isDinas(): bool
    {
        return $this->role === Role::DINAS;
    }

    // Memeriksa role sekolah
    public function isSekolah(): bool
    {
        return $this->role === Role::SEKOLAH;
    }

    // Memeriksa status aktif pengguna
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    // Mengubah role menjadi label
    public function roleLabel(): string
    {
        return $this->role?->label() ?? 'Tidak diketahui';
    }
}
