<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
