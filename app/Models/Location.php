<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\Campus;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'campus_id'];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }
}
