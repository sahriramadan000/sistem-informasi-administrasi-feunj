<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationLevel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price_per_page',
    ];

    protected $casts = [
        'price_per_page' => 'integer',
    ];

    public function legalizations()
    {
        return $this->hasMany(Legalization::class);
    }
}
