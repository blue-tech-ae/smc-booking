<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'campus'];

    protected $casts = [
        'campus' => \App\Enums\Campus::class,
    ];

    public function setDescriptionAttribute(?string $value): void
    {
        $allowed = '<p><a><b><strong><i><em><u><ul><ol><li><br><span>';
        $this->attributes['description'] = $value ? strip_tags($value, $allowed) : null;
    }
    
    public function events()
    {
        return $this->hasMany(Event::class, 'location', 'name');
    }
}
