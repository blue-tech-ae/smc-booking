<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EventNote;
use App\Models\User;
use Illuminate\Validation\ValidationException;

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

     protected static function boot()
    {
        parent::boot();

        static::saving(function (self $service) {
            $roleName = ucfirst($service->service_type);

            if ($service->assigned_to) {
                $user = User::find($service->assigned_to);
                if (!$user || !$user->hasRole($roleName)) {
                    throw ValidationException::withMessages([
                        'assigned_to' => ['Assigned user must have the ' . $roleName . ' role.'],
                    ]);
                }
            } else {
                $user = User::role($roleName)->first();
                if ($user) {
                    $service->assigned_to = $user->id;
                }
            }
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function notes()
    {
        return $this->hasMany(EventNote::class);
    }
    
    
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
