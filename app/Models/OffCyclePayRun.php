<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Make sure this is imported

class OffCyclePayRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'status',
        'team_id',
        'paid',
        'paid_date',
        'rejection_reason'
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'paid' => 'boolean',
        'paid_date' => 'date',
    ];

    public function offCyclePayrolls(): HasMany // Explicitly type-hint HasMany
    {
        return $this->hasMany(OffCyclePayroll::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
