<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Signatory untuk master data penandatangan surat
 */
class Signatory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'position',
        'nip',
        'signature_path',
        'restrict_signature_access',
        'is_active',
    ];

    /**
     * Daftar user yang diizinkan memakai TTD digital pejabat ini (jika dibatasi).
     */
    public function allowedSignatureUsers()
    {
        return $this->belongsToMany(User::class, 'signatory_user_access')
            ->withTimestamps();
    }

    /**
     * Cek apakah user tertentu diizinkan mengakses/menggunakan TTD digital pejabat ini.
     */
    public function canUserUseSignature(?User $user): bool
    {
        if (!$user || !$user->canAccessAnyStudio()) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (!$this->restrict_signature_access) {
            return true;
        }

        if ($this->relationLoaded('allowedSignatureUsers')) {
            return $this->allowedSignatureUsers->contains('id', $user->id);
        }

        return $this->allowedSignatureUsers()->where('users.id', $user->id)->exists();
    }

    /**
     * Resolve path absolut file TTD di private storage (atau fallback public lama).
     */
    public function getAbsoluteSignaturePath(): ?string
    {
        if (!$this->signature_path) {
            return null;
        }

        if (str_starts_with($this->signature_path, 'private/')) {
            return storage_path('app/' . $this->signature_path);
        }

        return public_path($this->signature_path);
    }

    /**
     * URL pratinjau terproteksi ber-watermark (bukan file asli publik).
     */
    public function getSignatureUrlAttribute(): ?string
    {
        return $this->signature_path ? route('studio.signature-preview', $this) : null;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'restrict_signature_access' => 'boolean',
    ];

    /**
     * Relasi ke surat yang ditandatangani oleh orang ini
     */
    public function letters()
    {
        return $this->hasMany(Letter::class, 'signatory_id');
    }

    /**
     * Scope untuk mendapatkan penandatangan yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}