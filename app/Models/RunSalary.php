<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunSalary extends Model
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
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
