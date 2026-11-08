<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'is_active',
        'can_access_letters',
        'can_access_archives',
        'can_access_legalizations',
        'can_access_room_reservations',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
        'can_access_letters' => 'boolean',
        'can_access_archives' => 'boolean',
        'can_access_legalizations' => 'boolean',
        'can_access_room_reservations' => 'boolean',
    ];

    /**
     * Relasi ke surat yang dibuat oleh user ini
     */
    public function letters()
    {
        return $this->hasMany(Letter::class, 'created_by');
    }

    /**
     * Cek apakah user memiliki role admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user memiliki role operator
     */
    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * Cek apakah user memiliki role viewer
     */
    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    /**
     * Cek apakah user punya akses ke lebih dari 1 modul
     */
    public function hasMultipleModules(): bool
    {
        $count = 0;
        if ($this->can_access_letters) $count++;
        if ($this->can_access_archives) $count++;
        if ($this->can_access_legalizations) $count++;
        if ($this->can_access_room_reservations) $count++;
        if ($this->isAdmin()) $count++;

        return $count >= 2;
    }
}
