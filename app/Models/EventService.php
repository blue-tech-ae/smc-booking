<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EventNote;

class EventService extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'service_type',
        'assigned_to',
        'details',
        'status',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function notes()
    {
        return $this->hasMany(EventNote::class);
    }
}
