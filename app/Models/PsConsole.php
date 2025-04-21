<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PsConsole extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'room_id', 'timer'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
