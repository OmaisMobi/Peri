<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class Biometric extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'user_id', 'reason', 'timedate', 'period', 'status', 'rejection_reason'];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($biometric) {
            $biometric->attendance()->delete();
        });
    }
    public function attendance(): HasOne
    {
        return $this->hasOne(Attendance::class, 'user_id', 'user_id')
            ->where('finger', $this->timedate)
            ->where('note', 'biometric request');
    }
    protected static function booted()
    {
        static::creating(function ($bio) {
            if (Auth::check()) {
                $currentUser = Auth::user();
                $bio->user_id = $currentUser->id;
            }
        });
    }
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
