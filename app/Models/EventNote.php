<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_service_id',
        'user_id',
        'note',
    ];

    public function service()
    {
        return $this->belongsTo(EventService::class, 'event_service_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
