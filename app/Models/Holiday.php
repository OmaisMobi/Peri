<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'starting_date',
        'ending_date',
        'type',
        'remarks',
        'apply',
        'departments',
        'users',
        'shifts',
    ];

    protected $casts = [
        'apply' => 'string',
        'departments' => 'array',
        'users' => 'array',
        'shifts' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($holiday) {
            if ($holiday->apply === 'shift') {
                $holiday->users = null;
                $holiday->departments = null;
            } elseif ($holiday->apply === 'user') {
                $holiday->shifts = null;
                $holiday->departments = null;
            } elseif ($holiday->apply === 'department') {
                $holiday->shifts = null;
                $holiday->users = null;
            } elseif ($holiday->apply === 'all') {
                $holiday->shifts = null;
                $holiday->departments = null;
                $holiday->users = null;
            }
        });
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
