<?php

// app/Models/PayRun.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'month', // e.g., 7 for July
        'year', // e.g., 2025
        'pay_period_start_date',
        'pay_period_end_date',
        'status',
        'paid',
        'paid_date',
        'rejection_reason',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'pay_period_start_date' => 'date',
        'pay_period_end_date' => 'date',
        'month' => 'integer',
        'year' => 'integer',
        'paid' => 'boolean',
        'paid_date' => 'date',
    ];

    /**
     * Get all payroll records associated with this pay run.
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    // Helper to get a display name
    public function getDisplayNameAttribute(): string
    {
        return \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
    }
    
    /**
     * Local scope to filter roles by team_id.
     *
     * @param Builder $query
     * @param mixed|null $adminId
     * @return Builder
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
