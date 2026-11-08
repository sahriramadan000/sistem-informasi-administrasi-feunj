<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model LetterType untuk master data jenis surat
 */
class LetterType extends Model
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
        'description',
        'is_active',
        'requires_subject',
        'requires_purpose',
        'requires_drive_archive',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'requires_subject' => 'boolean',
        'requires_purpose' => 'boolean',
        'requires_drive_archive' => 'boolean',
    ];

    /**
     * Relasi ke surat yang menggunakan jenis ini
     */
    public function letters()
    {
        return $this->hasMany(Letter::class, 'letter_type_id');
    }

    /**
     * Scope untuk mendapatkan jenis surat yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk mendapatkan jenis surat yang memerlukan arsip Google Drive
     */
    public function scopeRequiresDriveArchive($query)
    {
        return $query->where('requires_drive_archive', true);
    }
}