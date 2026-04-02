<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
protected $fillable = [
    'title', 
    'description',
    'image',
    'date_event',
    'heure',         
    'nombre_places', 
    'lieu',
    'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function supports()
    {
        return $this->hasMany(Support::class);
    }

}
