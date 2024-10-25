<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'email', 'cpf', 'event_id'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }
}
