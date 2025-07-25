<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'name'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
