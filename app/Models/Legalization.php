<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Legalization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'running_number',
        'year',
        'date',
        'alumni_name',
        'graduation_year',
        'education_level_id',
        'page_count',
        'total_price',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'running_number' => 'integer',
        'year' => 'integer',
        'graduation_year' => 'integer',
        'page_count' => 'integer',
        'total_price' => 'integer',
    ];

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class)->withTrashed();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted()
    {
        static::creating(function (self $model) {
            if (!$model->running_number) {
                $year = $model->year ?? $model->date->format('Y');
                $model->year = $year;
                $model->running_number = LegalizationSequence::getNextNumber($year);
            }
        });
    }
}
