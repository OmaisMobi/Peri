<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'team_id', 'start', 'end'];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'team_id');
    }

    protected static function booted()
    {
        // Enforce permission on creation
        static::creating(function ($event) {
            $user = Auth::user();
            if (!$user || (!$user->hasRole('Admin') && !$user->can('events.create'))) {
                throw ValidationException::withMessages([
                    'create' => 'You do not have permission to create this event.',
                ]);
            }
        });

        // Enforce permission on deletion
        static::deleting(function ($event) {
            $user = Auth::user();
            if (!$user || (!$user->hasRole('Admin') && !$user->can('events.delete'))) {
                throw ValidationException::withMessages([
                    'delete' => 'You do not have permission to delete this event.',
                ]);
            }
        });
    }
}
