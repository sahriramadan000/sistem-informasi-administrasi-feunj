<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomReservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'reservation_date',
        'start_time',
        'end_time',
        'event_name',
        'requester_name',
        'contact_person',
        'facilities',
        'remarks',
        'status',
        'approved_by',
        'rejection_reason',
        'created_by',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
