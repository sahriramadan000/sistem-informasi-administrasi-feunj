<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model CertificateStamp untuk master data stempel digital sertifikat.
 */
class CertificateStamp extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'file_path',
        'default_opacity',
        'is_active',
    ];

    protected $casts = [
        'default_opacity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope untuk stempel yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * URL publik berkas stempel.
     */
    public function getFileUrlAttribute(): string
    {
        return asset($this->file_path);
    }
}
