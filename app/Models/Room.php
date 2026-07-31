<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'description',
        'is_active',
        'room_group_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function reservations()
    {
        return $this->hasMany(RoomReservation::class);
    }

    public function roomGroup()
    {
        return $this->belongsTo(RoomGroup::class);
    }
}
