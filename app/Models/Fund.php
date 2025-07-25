<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'name',
        'brackets',
        'is_active',
    ];
    protected $casts = [
        'brackets' => 'array',
        'is_active' => 'boolean',
    ];
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('team_id')
            ->withTimestamps();
    }
}
