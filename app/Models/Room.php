<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    public function psConsoles()
    {
        return $this->hasMany(PsConsole::class);
    }

    public function user()
{
    return $this->belongsTo(User::class);
}

}
