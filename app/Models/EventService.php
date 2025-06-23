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
        'catering_required',
        'catering_people',
        'dietary_requirements',
        'catering_notes',
        'photography_required',
        'photography_type',
        'security_required',
        'security_guards',
        'risk_assessment',
        'security_notes',
        'status',
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
