<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department',
        'location_id',
        'campus',
        'title',
        'details',
        'expected_attendance',
        'organizer_name',
        'organizer_email',
        'organizer_phone',
        'start_time',
        'end_time',
        'end_date',
        'security_note',
        'status',
    ];

    protected $casts = [
        'campus' => \App\Enums\Campus::class,
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function services()
    {
        return $this->hasMany(EventService::class);
    }

    public function cancellationRequest()
    {
        return $this->hasOne(CancellationRequest::class);
    }

    public function notes()
    {
        return $this->hasMany(EventNote::class);
    }
}
