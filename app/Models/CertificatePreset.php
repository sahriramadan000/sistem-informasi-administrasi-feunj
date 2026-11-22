<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model CertificatePreset untuk menyimpan template koordinat & gaya Certificate Studio.
 */
class CertificatePreset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'letter_type_id',
        'config',
        'created_by',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
