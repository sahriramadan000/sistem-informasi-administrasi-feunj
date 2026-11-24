<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model StudioLetterhead untuk master gambar Kop Surat pada Studio Surat (SIPERMAWA).
 */
class StudioLetterhead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope untuk kop surat yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * URL publik berkas kop surat.
     */
    public function getFileUrlAttribute(): string
    {
        return asset($this->file_path);
    }
}
