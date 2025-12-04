<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model ClassificationLetter untuk master data klasifikasi surat
 */
class ClassificationLetter extends Model
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
     * Relasi ke surat yang menggunakan klasifikasi ini
     */
    public function letters()
    {
        return $this->hasMany(Letter::class, 'classification_id');
    }

    /**
     * Scope untuk mendapatkan klasifikasi yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}