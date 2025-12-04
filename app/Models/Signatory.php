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