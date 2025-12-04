<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model LetterPurpose untuk master data keperluan surat
 */
class LetterPurpose extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke surat yang menggunakan keperluan ini
     */
    public function letters()
    {
        return $this->hasMany(Letter::class, 'letter_purpose_id');
    }

    /**
     * Scope untuk mendapatkan keperluan yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
