<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriveFolder extends Model
{
    protected $fillable = [
        'year',
        'letter_type_id',
        'folder_id',
        'folder_name',
        'parent_folder_id',
    ];

    protected $casts = [
        'year' => 'integer',
        'letter_type_id' => 'integer',
    ];

    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id');
    }
}
