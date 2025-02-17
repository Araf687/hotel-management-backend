<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'name',
        'address',
        'image',
        'available_rooms',
        'per_night_cost',
        'average_rating',
        'description',
      
    ];
}
